# Alur Kerja RIS-lite

```
┌─────────────┐     ┌──────────┐     ┌───────────┐     ┌──────────┐
│  Admin      │     │  Pasien  │     │  Order    │     │  Report  │
│  (Setup)    │ →   │  (Input) │ →   │  (Proses) │ →   │  (Hasil) │
└─────────────┘     └──────────┘     └───────────┘     └──────────┘
```

## 1. Setup Awal (Admin)

| Menu | Yang Diisi | Keterangan |
|------|-----------|------------|
| Administration → **Servers** | Nama, Base URL, AE Title, Username/Password | Koneksi ke PACS dcm4chee |
| Administration → **Devices** | Nama, AE Title, Hostname, Port, Modality | CT, MRI, US, dsb. |
| Administration → **Procedures** | Kode, Nama, Modality, Durasi | Jenis pemeriksaan |
| Administration → **Users** | Nama, Email, Role | Operator, Radiolog, Admin |

## 2. Input Data Pasien

**Clinical → Patients → Create**

Kolom: MRN (auto), Nama, Tanggal Lahir, Jenis Kelamin, Telepon, Email, Alamat, KTP.

## 3. Buat Order Pemeriksaan

**Clinical → Orders → Create** (Wizard 3 langkah)

| Langkah | Isi |
|---------|-----|
| **Pasien** | Cari & pilih pasien. Bisa buat pasien baru langsung dari form. |
| **Pemeriksaan** | Pilih modality → otomatis filter alat aktif. Isi dokter perujuk, jadwal, prioritas, catatan klinis. |
| **Konfirmasi** | Lihat ringkasan, Accession No. digenerate otomatis. |

**Setelah create:**
- Job `PushWorklistToPacsJob` jalan di background
- Push data pasien ke PACS → Push MWL → Order jadi `scheduled`
- Kalau gagal → order kembali ke `pending`, cek kolom `error_message` di worklist item

## 4. Worklist Item (Status Tracking)

**Clinical → Worklist Items**

| Status | Arti |
|--------|------|
| `registered` | Tercatat, belum dikirim |
| `mw_published` | MWL sudah di PACS, alat bisa ambil |
| `taken_by_modality` | Alat sudah ambil jadwal |
| `acquired` | Citra sudah diambil |
| `sent_to_pacs` | Citra sudah masuk PACS |
| `reported` | Sudah dilaporkan |
| `verified` | Sudah diverifikasi |
| `cancelled` / `failed` | Batal / Gagal |

**Update status manual** lewat tombol action di tabel (Publish MWL → Taken by Modality → Acquired → Sent to PACS → Reported → Verify).

## 5. Sync Status Otomatis

```bash
php artisan worklist:sync-status
# atau dry-run dulu:
php artisan worklist:sync-status --dry-run
```
Memeriksa PACS via QIDO-RS, kalau studi sudah ada → update status ke `sent_to_pacs` + catat `study_instance_uid`.

## 6. Report / Hasil Pemeriksaan

**Clinical → Reports → Create**

- Pilih Order → Accession No. & Study UID terisi otomatis
- Pilih Radiolog
- Isi: Clinical History, Findings, Impression, Conclusion
- **Finalize** → Order status jadi `reported`
- **Amend** → Report bisa diedit ulang (status jadi `amended`)

## Relasi Data

```
Server ──> Device ──> Order ──> WorklistItem
                            └──> Report

Patient ──> Order
Procedure ──> Order
```
