<?php

declare(strict_types=1);

namespace App\Services\Dcm4chee;

use App\Models\Device;
use App\Models\Order;

final class MwlBuilder
{
    public static function buildPatient(string $name, string $patientId, ?string $birthDate = null, ?string $sex = null): array
    {
        $json = [];
        $json['00100010'] = ['vr' => 'PN', 'Value' => [['Alphabetic' => $name]]];
        $json['00100020'] = ['vr' => 'LO', 'Value' => [$patientId]];
        if ($birthDate) {
            $json['00100030'] = ['vr' => 'DA', 'Value' => [str_replace('-', '', $birthDate)]];
        }
        if ($sex) {
            $json['00100040'] = ['vr' => 'CS', 'Value' => [$sex]];
        }

        return $json;
    }

    public static function buildMwl(Order $order): array
    {
        $patient = $order->patient;
        $json = [];
        $json['00100010'] = ['vr' => 'PN', 'Value' => [['Alphabetic' => $patient->name]]];
        $json['00100020'] = ['vr' => 'LO', 'Value' => [$patient->patient_id]];
        if ($patient->date_of_birth) {
            $json['00100030'] = ['vr' => 'DA', 'Value' => [$patient->date_of_birth->format('Ymd')]];
        }
        if ($patient->sex) {
            $json['00100040'] = ['vr' => 'CS', 'Value' => [$patient->sex]];
        }
        $json['00080050'] = ['vr' => 'SH', 'Value' => [$order->accession_number]];
        if ($order->requesting_physician) {
            $json['00080090'] = ['vr' => 'PN', 'Value' => [['Alphabetic' => $order->requesting_physician]]];
        }
        $sps = ['00400020' => ['vr' => 'CS', 'Value' => ['SCHEDULED']]];
        $modality = $order->modality ?? $order->procedure?->modality;
        if ($modality) {
            $sps['00400007'] = ['vr' => 'LO', 'Value' => [$modality]];
        }
        if ($order->requesting_physician) {
            $json['00321032'] = ['vr' => 'PN', 'Value' => [['Alphabetic' => $order->requesting_physician]]];
        }
        $desc = $order->procedure?->name;
        if ($desc) {
            $json['00321060'] = ['vr' => 'LO', 'Value' => [$desc]];
        }
        $stationAe = $order->device?->ae_title
            ?? Device::where('modality', $modality)->where('status', 'active')->value('ae_title')
            ?? 'DCM4CHEE';
        $sps['00400001'] = ['vr' => 'AE', 'Value' => [$stationAe]];
        $sps['00400009'] = ['vr' => 'SH', 'Value' => ['SPS-'.$order->accession_number]];
        $date = $order->scheduled_date?->format('Ymd') ?? now()->format('Ymd');
        $sps['00400002'] = ['vr' => 'DA', 'Value' => [$date]];
        $sps['00400003'] = ['vr' => 'TM', 'Value' => ['000000']];
        $json['00400100'] = ['vr' => 'SQ', 'Value' => [$sps]];

        return $json;
    }
}
