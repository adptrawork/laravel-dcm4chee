<?php

namespace Database\Seeders;

use App\Models\ExaminationTemplate;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::set('hospital_name', 'Radiology Center', 'general');

        $templates = [
            ['name' => 'Thorax AP', 'modality' => 'DX', 'description' => 'Thorax AP', 'sort_order' => 1],
            ['name' => 'Thorax PA', 'modality' => 'DX', 'description' => 'Thorax PA', 'sort_order' => 2],
            ['name' => 'Thorax AP/Lat', 'modality' => 'DX', 'description' => 'Thorax AP Lateral', 'sort_order' => 3],
            ['name' => 'Abdomen AP', 'modality' => 'DX', 'description' => 'Abdomen AP', 'sort_order' => 4],
            ['name' => 'Skull AP/Lat', 'modality' => 'DX', 'description' => 'Skull AP Lateral', 'sort_order' => 5],
            ['name' => 'Cervical Spine AP/Lat', 'modality' => 'DX', 'description' => 'Cervical Spine AP/Lat', 'sort_order' => 6],
            ['name' => 'Thoracic Spine AP/Lat', 'modality' => 'DX', 'description' => 'Thoracic Spine AP/Lat', 'sort_order' => 7],
            ['name' => 'Lumbar Spine AP/Lat', 'modality' => 'DX', 'description' => 'Lumbar Spine AP/Lat', 'sort_order' => 8],
            ['name' => 'Pelvis AP', 'modality' => 'DX', 'description' => 'Pelvis AP', 'sort_order' => 9],
            ['name' => 'CT Head Non Contrast', 'modality' => 'CT', 'description' => 'CT Head Non Contrast', 'sort_order' => 10],
            ['name' => 'CT Head With Contrast', 'modality' => 'CT', 'description' => 'CT Head With Contrast', 'sort_order' => 11],
            ['name' => 'CT Thorax', 'modality' => 'CT', 'description' => 'CT Thorax', 'sort_order' => 12],
            ['name' => 'CT Abdomen', 'modality' => 'CT', 'description' => 'CT Abdomen', 'sort_order' => 13],
            ['name' => 'MRI Head', 'modality' => 'MR', 'description' => 'MRI Head', 'sort_order' => 14],
            ['name' => 'MRI Knee', 'modality' => 'MR', 'description' => 'MRI Knee', 'sort_order' => 15],
            ['name' => 'US Abdomen', 'modality' => 'US', 'description' => 'Ultrasound Abdomen', 'sort_order' => 16],
            ['name' => 'US Obstetric', 'modality' => 'US', 'description' => 'Ultrasound Obstetric', 'sort_order' => 17],
        ];

        foreach ($templates as $t) {
            ExaminationTemplate::create($t);
        }
    }
}
