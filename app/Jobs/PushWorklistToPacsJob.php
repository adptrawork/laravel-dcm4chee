<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use App\Services\Dcm4chee\MwlBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PushWorklistToPacsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    public function handle(): void
    {
        if ($this->order->status === Order::STATUS_CANCELLED) {
            return;
        }

        $existing = $this->order->worklistItem;
        if ($existing) {
            if ($existing->status !== WorklistItem::STATUS_FAILED) {
                return;
            }
            $existing->delete();
        }

        $this->order->load('patient', 'procedure');

        $server = $this->order->server ?: Server::where('enabled', true)->first();

        if (! $server || ! $server->enabled) {
            $this->fail('No enabled PACS server found');

            return;
        }

        $patient = $this->order->patient;
        $client = new Client($server);

        // Push patient to PACS first (required for MWL)
        $sex = $patient->sex ? strtoupper(substr($patient->sex, 0, 1)) : null;
        $patientJson = MwlBuilder::buildPatient(
            $patient->name,
            $patient->patient_id,
            $patient->date_of_birth?->format('Y-m-d'),
            $sex,
        );
        // ponytail: Accept: application/json for /patients POST — DCM4CHEE returns 406 with application/dicom+json
        $patientResponse = $client->raw('POST', 'patients', [
            'body' => $patientJson,
            'headers' => ['Accept' => 'application/json'],
        ]);
        // 200 = created, 409 = already exists (fine), anything else = problem
        if (! $patientResponse->successful() && $patientResponse->status() !== 409) {
            $this->fail("Patient push failed: HTTP {$patientResponse->status()}");

            return;
        }

        // Now push MWL item
        $mwlJson = MwlBuilder::buildMwl($this->order);
        $response = $client->raw('POST', 'mwlitems', [
            'body' => $mwlJson,
            'headers' => ['Accept' => 'application/json'],
        ]);

        $modality = $this->order->modality ?? $this->order->procedure?->modality ?? 'OT';
        $procDesc = $this->order->procedure?->name ?? $this->order->clinical_notes ?? '';

        $wlData = [
            'order_id' => $this->order->id,
            'server_id' => $server->id,
            'accession_number' => $this->order->accession_number,
            'patient_name' => $patient->name,
            'patient_id' => $patient->patient_id,
            'modality' => $modality,
            'procedure_description' => $procDesc,
            'requesting_physician' => $this->order->requesting_physician ?? '',
            'scheduled_date' => $this->order->scheduled_date,
        ];

        if (! $response->successful()) {
            $this->order->worklistItem()->create($wlData + [
                'status' => WorklistItem::STATUS_FAILED,
                'error_message' => "HTTP {$response->status()}: {$response->body()}",
            ]);
            $from = $this->order->status;
            $this->order->updateQuietly(['status' => Order::STATUS_PENDING]);
            Order::logStatus($this->order, Order::STATUS_PENDING, 'MWL push failed', $from);
            $response->throw();
        }

        $this->order->worklistItem()->create($wlData + [
            'status' => WorklistItem::STATUS_MW_PUBLISHED,
            'sent_at' => now(),
        ]);
        $from = $this->order->status;
        $this->order->updateQuietly(['status' => Order::STATUS_SCHEDULED]);
        Order::logStatus($this->order, Order::STATUS_SCHEDULED, 'MWL published', $from);
    }
}
