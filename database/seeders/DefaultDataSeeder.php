<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Order;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Server;
use App\Models\User;
use App\Models\WorklistItem;
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        $server = Server::first();

        if ($server) {
            foreach ([
                ['name' => 'CT Scanner 1', 'ae_title' => 'CT-SCN1', 'hostname' => '192.168.1.10', 'port' => 11112, 'modality' => 'CT', 'status' => 'active'],
                ['name' => 'MRI 1', 'ae_title' => 'MR-SCN1', 'hostname' => '192.168.1.11', 'port' => 11113, 'modality' => 'MR', 'status' => 'active'],
                ['name' => 'X-Ray 1', 'ae_title' => 'DX-SCN1', 'hostname' => '192.168.1.12', 'port' => 11114, 'modality' => 'DX', 'status' => 'active'],
                ['name' => 'US 1', 'ae_title' => 'US-SCN1', 'hostname' => '192.168.1.13', 'port' => 11115, 'modality' => 'US', 'status' => 'active'],
            ] as $d) {
                Device::firstOrCreate(['ae_title' => $d['ae_title']], ['server_id' => $server->id] + $d);
            }
        }

        foreach ([
            ['code' => 'CT-HEAD', 'name' => 'CT Head', 'modality' => 'CT', 'body_part' => 'Head'],
            ['code' => 'CT-CHEST', 'name' => 'CT Chest', 'modality' => 'CT', 'body_part' => 'Chest'],
            ['code' => 'CT-ABDO', 'name' => 'CT Abdomen', 'modality' => 'CT', 'body_part' => 'Abdomen'],
            ['code' => 'MR-BRAIN', 'name' => 'MRI Brain', 'modality' => 'MR', 'body_part' => 'Head'],
            ['code' => 'MR-KNEE', 'name' => 'MRI Knee', 'modality' => 'MR', 'body_part' => 'Knee'],
            ['code' => 'XR-CHEST', 'name' => 'X-Ray Chest', 'modality' => 'DX', 'body_part' => 'Chest'],
            ['code' => 'XR-ABDO', 'name' => 'X-Ray Abdomen', 'modality' => 'DX', 'body_part' => 'Abdomen'],
            ['code' => 'US-ABDO', 'name' => 'US Abdomen', 'modality' => 'US', 'body_part' => 'Abdomen'],
        ] as $p) {
            Procedure::firstOrCreate(['code' => $p['code']], $p + ['is_active' => true]);
        }

        $patients = [];
        foreach ([
            ['patient_id' => 'MRN-001', 'name' => 'Budi Santoso', 'date_of_birth' => '1985-03-15', 'sex' => 'M', 'phone' => '081234567890'],
            ['patient_id' => 'MRN-002', 'name' => 'Siti Rahayu', 'date_of_birth' => '1992-07-22', 'sex' => 'F', 'phone' => '081234567891'],
            ['patient_id' => 'MRN-003', 'name' => 'Ahmad Hidayat', 'date_of_birth' => '1978-11-08', 'sex' => 'M', 'phone' => '081234567892'],
            ['patient_id' => 'MRN-004', 'name' => 'Dewi Lestari', 'date_of_birth' => '1995-01-30', 'sex' => 'F', 'phone' => '081234567893'],
            ['patient_id' => 'MRN-005', 'name' => 'Rudi Hermawan', 'date_of_birth' => '1965-09-12', 'sex' => 'M', 'phone' => '081234567894'],
        ] as $p) {
            $patients[] = Patient::firstOrCreate(['patient_id' => $p['patient_id']], $p);
        }

        $procedures = Procedure::all()->keyBy('code');
        $devices = Device::all()->keyBy('modality');
        $admin = User::first();

        $accession = 1;
        foreach ([
            ['patient' => $patients[0], 'procedure' => 'CT-HEAD', 'modality' => 'CT', 'status' => Order::STATUS_COMPLETED, 'priority' => 'urgent', 'scheduled_date' => '2026-07-10'],
            ['patient' => $patients[1], 'procedure' => 'XR-CHEST', 'modality' => 'DX', 'status' => Order::STATUS_SCHEDULED, 'priority' => 'routine', 'scheduled_date' => '2026-07-15'],
            ['patient' => $patients[2], 'procedure' => 'MR-BRAIN', 'modality' => 'MR', 'status' => Order::STATUS_PENDING, 'priority' => 'routine', 'scheduled_date' => '2026-07-16'],
            ['patient' => $patients[3], 'procedure' => 'US-ABDO', 'modality' => 'US', 'status' => Order::STATUS_IN_PROGRESS, 'priority' => 'stat', 'scheduled_date' => '2026-07-14'],
            ['patient' => $patients[4], 'procedure' => 'CT-CHEST', 'modality' => 'CT', 'status' => Order::STATUS_REPORTED, 'priority' => 'routine', 'scheduled_date' => '2026-07-11'],
        ] as $o) {
            $order = Order::createQuietly([
                'accession_number' => 'ACC-20260711-'.str_pad((string) $accession++, 4, '0', STR_PAD_LEFT),
                'server_id' => $server->id,
                'patient_id' => $o['patient']->id,
                'procedure_id' => $procedures[$o['procedure']]->id,
                'device_id' => $devices->get($o['modality'])?->id,
                'modality' => $o['modality'],
                'status' => $o['status'],
                'priority' => $o['priority'],
                'scheduled_date' => $o['scheduled_date'],
                'requesting_physician' => 'Dr. Wijaya',
                'clinical_notes' => match ($o['procedure']) {
                    'CT-HEAD' => 'Sakit kepala sejak 1 minggu, CT scan tanpa kontras.',
                    'XR-CHEST' => 'Batuk kronis, screening TB.',
                    'MR-BRAIN' => 'Riwayat epilepsi, follow-up MRI.',
                    'US-ABDO' => 'Nyeri perut kanan atas, curiga batu empedu.',
                    'CT-CHEST' => 'Nodul paru, follow-up.',
                    default => 'Pemeriksaan rutin.',
                },
            ]);

            if (in_array($o['status'], [Order::STATUS_SCHEDULED, Order::STATUS_IN_PROGRESS, Order::STATUS_COMPLETED, Order::STATUS_REPORTED])) {
                $wlStatus = match ($o['status']) {
                    Order::STATUS_SCHEDULED => WorklistItem::STATUS_MW_PUBLISHED,
                    Order::STATUS_IN_PROGRESS => WorklistItem::STATUS_ACQUIRING,
                    Order::STATUS_COMPLETED => WorklistItem::STATUS_SENT_TO_PACS,
                    Order::STATUS_REPORTED => WorklistItem::STATUS_REPORTED,
                    default => WorklistItem::STATUS_REGISTERED,
                };
                WorklistItem::createQuietly([
                    'order_id' => $order->id,
                    'patient_name' => $o['patient']->name,
                    'patient_id' => $o['patient']->patient_id,
                    'modality' => $o['modality'],
                    'procedure_code' => $procedures[$o['procedure']]->code,
                    'procedure_description' => $procedures[$o['procedure']]->name,
                    'status' => $wlStatus,
                    'scheduled_date' => $o['scheduled_date'],
                    'requesting_physician' => 'Dr. Wijaya',
                ]);
            }
        }
    }
}
