<?php

namespace Database\Seeders;

use App\Models\Procedure;
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        $procedures = [
            ['code' => 'CT-HEAD', 'name' => 'CT Head', 'modality' => 'CT', 'body_part' => 'Head'],
            ['code' => 'CT-CHEST', 'name' => 'CT Chest', 'modality' => 'CT', 'body_part' => 'Chest'],
            ['code' => 'CT-ABDO', 'name' => 'CT Abdomen', 'modality' => 'CT', 'body_part' => 'Abdomen'],
            ['code' => 'MR-BRAIN', 'name' => 'MRI Brain', 'modality' => 'MR', 'body_part' => 'Head'],
            ['code' => 'MR-KNEE', 'name' => 'MRI Knee', 'modality' => 'MR', 'body_part' => 'Knee'],
            ['code' => 'XR-CHEST', 'name' => 'X-Ray Chest', 'modality' => 'DX', 'body_part' => 'Chest'],
            ['code' => 'XR-ABDO', 'name' => 'X-Ray Abdomen', 'modality' => 'DX', 'body_part' => 'Abdomen'],
            ['code' => 'US-ABDO', 'name' => 'US Abdomen', 'modality' => 'US', 'body_part' => 'Abdomen'],
        ];

        foreach ($procedures as $p) {
            Procedure::firstOrCreate(
                ['code' => $p['code']],
                $p + ['is_active' => true],
            );
        }
    }
}
