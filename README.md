# RIS-lite

RIS (Radiology Information System) minimal terintegrasi dengan PACS dcm4chee.

## Flow Sistem

```
Admin Setup → Input Pasien → Order Pemeriksaan → Kirim MWL ke PACS
                                                    ↓
Radiolog ← Hasil ← Sync Status PACS ← Modality Ambil Gambar
```

## Quick Start

```bash
cp .env.example .env
composer install
php artisan migrate
php artisan db:seed --class=DefaultDataSeeder
php artisan serve
```

## Menu

| Menu | Akses | Fungsi |
|------|-------|--------|
| **Patients** | All | Input & cari pasien |
| **Orders** | All | Buat order pemeriksaan (wizard) |
| **Worklist Items** | All | Tracking status MWL |
| **Reports** | Radiolog | Input hasil pemeriksaan |
| **Servers** | Admin | Konfigurasi koneksi PACS |
| **Devices** | Admin | Daftar alat/modality |
| **Procedures** | Admin | Jenis pemeriksaan |

## Perintah

```bash
php artisan worklist:sync-status              # polling status dari PACS
php artisan worklist:sync-status --dry-run    # cek dulu tanpa ubah data
php artisan queue:work                        # proses job MWL
```

## Struktur

- `app/Jobs/PushWorklistToPacsJob` — push patient + MWL ke PACS
- `app/Console/Commands/SyncWorklistStatus` — polling QIDO-RS
- `app/Services/Dcm4chee/` — client, helper, study/series service
- `app/Filament/Resources/` — form & table tiap resource

Dokumentasi lengkap: [docs/ALUR-WORKFLOW.md](docs/ALUR-WORKFLOW.md)
