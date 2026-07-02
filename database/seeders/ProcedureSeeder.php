<?php

namespace Database\Seeders;

use App\Models\Procedure;
use Illuminate\Database\Seeder;

class ProcedureSeeder extends Seeder
{
    public function run(): void
    {
        $procedures = [
            ['code' => 'THORAX_AP', 'name' => 'Thorax AP', 'modality' => 'DX', 'description' => 'Thorax Antero-Posterior', 'body_part' => 'Chest', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 1],
            ['code' => 'THORAX_PA', 'name' => 'Thorax PA', 'modality' => 'DX', 'description' => 'Thorax Postero-Anterior', 'body_part' => 'Chest', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 2],
            ['code' => 'THORAX_LAT', 'name' => 'Thorax Lateral', 'modality' => 'DX', 'description' => 'Thorax Lateral', 'body_part' => 'Chest', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 3],
            ['code' => 'ABDOMEN_3POS', 'name' => 'Abdomen 3 Posisi', 'modality' => 'DX', 'description' => 'Abdomen 3 Posisi (Supine/Standing/Lateral)', 'body_part' => 'Abdomen', 'estimated_duration' => 10, 'default_room' => '1', 'sort_order' => 4],
            ['code' => 'ABDOMEN_AP', 'name' => 'Abdomen AP', 'modality' => 'DX', 'description' => 'Abdomen Antero-Posterior', 'body_part' => 'Abdomen', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 5],
            ['code' => 'SKULL_AP', 'name' => 'Skull AP', 'modality' => 'DX', 'description' => 'Skull Antero-Posterior', 'body_part' => 'Head', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 6],
            ['code' => 'SKULL_LAT', 'name' => 'Skull Lateral', 'modality' => 'DX', 'description' => 'Skull Lateral', 'body_part' => 'Head', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 7],
            ['code' => 'PELVIS_AP', 'name' => 'Pelvis AP', 'modality' => 'DX', 'description' => 'Pelvis Antero-Posterior', 'body_part' => 'Pelvis', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 8],
            ['code' => 'CERVICAL_AP', 'name' => 'Cervical AP', 'modality' => 'DX', 'description' => 'Cervical Spine Antero-Posterior', 'body_part' => 'Spine', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 9],
            ['code' => 'CERVICAL_LAT', 'name' => 'Cervical Lateral', 'modality' => 'DX', 'description' => 'Cervical Spine Lateral', 'body_part' => 'Spine', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 10],
            ['code' => 'THORACIC_AP', 'name' => 'Thoracic AP', 'modality' => 'DX', 'description' => 'Thoracic Spine AP', 'body_part' => 'Spine', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 11],
            ['code' => 'THORACIC_LAT', 'name' => 'Thoracic Lateral', 'modality' => 'DX', 'description' => 'Thoracic Spine Lateral', 'body_part' => 'Spine', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 12],
            ['code' => 'LUMBAR_AP', 'name' => 'Lumbar AP', 'modality' => 'DX', 'description' => 'Lumbar Spine AP', 'body_part' => 'Spine', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 13],
            ['code' => 'LUMBAR_LAT', 'name' => 'Lumbar Lateral', 'modality' => 'DX', 'description' => 'Lumbar Spine Lateral', 'body_part' => 'Spine', 'estimated_duration' => 5, 'default_room' => '1', 'sort_order' => 14],
            ['code' => 'EXTREMITY_AP', 'name' => 'Extremitas AP', 'modality' => 'DX', 'description' => 'Extremitas Antero-Posterior', 'body_part' => 'Extremity', 'estimated_duration' => 5, 'default_room' => '2', 'sort_order' => 15],
            ['code' => 'CT_HEAD_NC', 'name' => 'CT Head Non Contrast', 'modality' => 'CT', 'description' => 'CT Kepala tanpa Kontras', 'body_part' => 'Head', 'estimated_duration' => 15, 'default_room' => 'CT', 'sort_order' => 16],
            ['code' => 'CT_HEAD_C', 'name' => 'CT Head With Contrast', 'modality' => 'CT', 'description' => 'CT Kepala dengan Kontras', 'body_part' => 'Head', 'estimated_duration' => 20, 'default_room' => 'CT', 'requires_contrast' => true, 'contrast_detail' => 'Non-Ionic Contrast 50-100ml IV', 'sort_order' => 17],
            ['code' => 'CT_ABDOMEN_NC', 'name' => 'CT Abdomen Non Contrast', 'modality' => 'CT', 'description' => 'CT Abdomen tanpa Kontras', 'body_part' => 'Abdomen', 'estimated_duration' => 15, 'default_room' => 'CT', 'sort_order' => 18],
            ['code' => 'CT_ABDOMEN_C', 'name' => 'CT Abdomen With Contrast', 'modality' => 'CT', 'description' => 'CT Abdomen dengan Kontras', 'body_part' => 'Abdomen', 'estimated_duration' => 25, 'default_room' => 'CT', 'requires_contrast' => true, 'contrast_detail' => 'Non-Ionic Contrast 100-150ml IV', 'sort_order' => 19],
            ['code' => 'MRI_BRAIN_NC', 'name' => 'MRI Brain Non Contrast', 'modality' => 'MR', 'description' => 'MRI Kepala tanpa Kontras', 'body_part' => 'Head', 'estimated_duration' => 30, 'default_room' => 'MR', 'sort_order' => 20],
            ['code' => 'MRI_BRAIN_C', 'name' => 'MRI Brain With Contrast', 'modality' => 'MR', 'description' => 'MRI Kepala dengan Kontras', 'body_part' => 'Head', 'estimated_duration' => 40, 'default_room' => 'MR', 'requires_contrast' => true, 'contrast_detail' => 'Gadolinium 0.1-0.2mmol/kg IV', 'sort_order' => 21],
            ['code' => 'US_ABDOMEN', 'name' => 'US Abdomen', 'modality' => 'US', 'description' => 'Ultrasonografi Abdomen', 'body_part' => 'Abdomen', 'estimated_duration' => 20, 'default_room' => 'US', 'sort_order' => 22],
            ['code' => 'US_OBSTETRIC', 'name' => 'US Obstetric', 'modality' => 'US', 'description' => 'Ultrasonografi Obstetri', 'body_part' => 'Abdomen', 'estimated_duration' => 25, 'default_room' => 'US', 'sort_order' => 23],
            ['code' => 'MAMMOGRAPHY', 'name' => 'Mammography', 'modality' => 'MG', 'description' => 'Mammografi Screening', 'body_part' => 'Breast', 'estimated_duration' => 15, 'default_room' => 'MG', 'sort_order' => 24],
        ];

        foreach ($procedures as $p) {
            Procedure::create($p);
        }
    }
}
