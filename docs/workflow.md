# Alur Kerja Sistem

## Daftar Workflow

| # | Workflow | Kelengkapan | Status |
|---|----------|-------------|--------|
| A | Konfigurasi Awal Sistem | 100% | ✅ Production-ready |
| B | Registrasi Pasien & Create MWL | 90% | ✅ Minor gap |
| C | Worklist Management | 80% | ⚠️ Gap sedang |
| D | PACS Monitor & Delivery | 75% | ⚠️ Gap sedang |
| E | Study Browser & Image Viewer | 75% | ⚠️ Gap sedang |
| F | Dashboard & Monitoring | 65% | ⚠️ Gap lumayan |
| G | User Management & RBAC | 20% | ❌ Mayoritas belum |
| H | Audit Trail | 50% | ⚠️ Logging ✅, viewer ❌ |
| I | Notifications & Reporting | 0% | ❌ Belum ada |

**Overall**: ~70% — siap untuk siklus dasar, perlu beberapa fitur sebelum produksi penuh.

---

## Workflow A: Konfigurasi Awal Sistem

**Kelengkapan**: 100% ✅

**Aktor**: Admin

**Alur**:

```
Login
  ├─ Setup Server DCM4CHEE     (Servers → +Add → isi URL/AET/credentials)
  ├─ Test Connection            (Test Connection → verifikasi respon)
  ├─ Setup Device/Modality      (Devices → +Add → isi AET/hostname/port)
  ├─ C-ECHO Device              (Devices → C-ECHO → verifikasi konektivitas)
  ├─ Konfigurasi MWL Default    (Settings → MWL Config → AET/dokter/ruang per server)
  ├─ Buat Template Pemeriksaan  (Settings → Templates → modality/deskripsi/ruang)
  └─ Setting Sistem             (Settings → nama RS, alamat, timezone)
```

### Yang sudah ada:
- CRUD Server + password terenkripsi ✅
- Test Connection ke DCM4CHEE ✅
- CRUD Device + status online/offline ✅
- C-ECHO via `echoscu` DCMTK ✅
- MWL Default Config per-server ✅
- Examination Templates dengan sort order ✅
- System Settings key-value store ✅

### Yang belum ada:
- *(Tidak ada — workflow ini lengkap)*

---

## Workflow B: Registrasi Pasien & Create MWL

**Kelengkapan**: 90% ✅

**Aktor**: Petugas Registrasi / Admin

**Alur**:

```
Registrasi
  ├─ Pilih Server DCM4CHEE
  ├─ Cari Pasien (nama atau ID)
  │   ├─ Jika sudah ada → pilih dari hasil pencarian
  │   └─ Jika belum ada → isi form pasien baru (nama, ID, tgl lahir, gender, NIK, alamat, telp)
  ├─ Pilih Template Pemeriksaan → otomatis isi modality, deskripsi, ruang
  ├─ Isi detail pemeriksaan:
  │   ├─ Dokter Pengirim
  │   ├─ Modality
  │   ├─ Deskripsi Pemeriksaan
  │   ├─ Ruang
  │   ├─ Prioritas (Routine / Urgent / Stat)
  │   └─ Jadwal Tanggal & Jam
  └─ Submit → sistem melakukan:
      ├─ (Jika pasien baru) → Create Patient ke DCM4CHEE
      ├─ Create MWL ke DCM4CHEE (POST workitems)
      ├─ Generate Accession Number otomatis
      └─ Simpan WorklistItem ke DB lokal → redirect ke Worklist
```

### Yang sudah ada:
- Pencarian pasien dari PACS (by name/ID) ✅
- Create pasien baru ke PACS ✅
- Template pemeriksaan dengan auto-fill ✅
- Generate accession number ✅
- Create MWL ke DCM4CHEE (JSON DICOM) ✅
- Tracking status di database lokal ✅
- Prioritas pemeriksaan (routine/urgent/stat) ✅
- Jadwal tanggal + jam ✅

### Yang belum ada:
- Edit MWL yang sudah dibuat ❌
- Cancellation MWL dari halaman registrasi ❌
- Print label / kartu pasien ❌
- Validasi duplikat MWL (pasien + modality + tanggal sama) ❌

---

## Workflow C: Worklist Management

**Kelengkapan**: 80% ⚠️

**Aktor**: Petugas / Koordinator

**Alur**:

```
Worklist
  ├─ Pilih Server
  ├─ Filter status (waiting / in_progress / completed / sent / failed / cancelled)
  ├─ Lihat daftar MWL
  ├─ [Refresh] → Sync dari DCM4CHEE (update status SPS)
  ├─ Klik item → Update Status:
  │   ├─ waiting → in_progress
  │   ├─ in_progress → completed
  │   ├─ waiting → cancelled (juga DELETE MWL dari DCM4CHEE)
  │   └─ completed → sent (manual)
  └─ Cancel MWL → hapus dari DCM4CHEE + update status lokal
```

### Yang sudah ada:
- Tampilkan MWL dari database lokal ✅
- Filter by status ✅
- Pilih server ✅
- Refresh/sync dari DCM4CHEE ✅
- Update status manual ✅
- Cancel MWL (delete from DCM4CHEE) ✅
- Redirect from successful registration ✅

### Yang belum ada:
- Bulk update status ❌
- Export to CSV/Excel ❌
- Search by patient name / date / accession ❌
- Edit detail MWL (jadwal ulang, ganti modality) ❌

---

## Workflow D: PACS Monitor & Delivery

**Kelengkapan**: 75% ⚠️

**Aktor**: Admin / Koordinator

**Alur**:

```
PACS Monitor
  ├─ Pilih Server
  ├─ Filter status
  ├─ Lihat daftar item dengan status pengiriman
  ├─ Update status manual:
  │   ├─ → Sent (tandai sudah terkirim)
  │   └─ → Failed (tandai gagal)
  └─ Retry → kembalikan status ke "waiting" untuk diproses ulang
```

### Yang sudah ada:
- Daftar WorklistItem dengan status ✅
- Filter by server + status ✅
- Update status ke sent/failed ✅
- Retry item gagal ✅

### Yang belum ada:
- Auto-retry dengan jadwal ❌
- Notifikasi untuk failed items ❌
- Real-time monitoring DICOM communication ❌
- Log detail error per-item yang lebih informatif ❌

---

## Workflow E: Study Browser & Image Viewer

**Kelengkapan**: 75% ⚠️

**Aktor**: Dokter / Radiografer

**Alur**:

```
Study Browser
  ├─ Pilih Server
  ├─ Cari Studies (by patient name, patient ID, tanggal studi, accession number)
  ├─ Lihat daftar studi
  ├─ Klik studi → lihat daftar Series
  ├─ Klik series → lihat daftar Instance
  └─ Per Instance:
      ├─ [Metadata] → lihat raw DICOM JSON
      └─ [View] → render image JPEG
```

### Yang sudah ada:
- Search studies (multi-kriteria) ✅
- List studies + detail ✅
- List series per study ✅
- List instances per series ✅
- Metadata viewer (raw DICOM JSON) ✅
- Image rendering (JPEG) ✅

### Yang belum ada:
- Advanced image viewer (OHIF/CornerstoneJS: pan, zoom, window/level) ❌
- Navigasi next/prev instance ❌
- Download DICOM file asli ❌
- Download as PNG/JPEG ❌
- Multi-instance view (grid) ❌
- Study-level actions (delete, anonymize, move) ❌
- Measurement tools ❌

---

## Workflow F: Dashboard & Monitoring

**Kelengkapan**: 65% ⚠️

**Aktor**: Semua user

**Tampilan**:

```
Dashboard
  ├─ Health Check PACS → UP / DOWN
  ├─ Ringkasan MWL:
  │   ├─ Waiting: N
  │   ├─ In Progress: N
  │   ├─ Completed: N
  │   ├─ Sent: N
  │   └─ Failed: N
  ├─ Recent MWL Items (10 terakhir)
  └─ Recent Audit Logs (5 terakhir)
```

### Yang sudah ada:
- Health check PACS ✅
- Statistik MWL per status ✅
- Recent items list ✅
- Recent audit logs ✅

### Yang belum ada:
- Chart visualisasi (line chart trend, pie chart distribusi) ❌
- Real-time refresh ❌
- Statistik per modality ❌
- Statistik per user ❌
- Periode filter (hari ini, minggu ini, bulan ini) ❌

---

## Workflow G: User Management & RBAC

**Kelengkapan**: 20% ❌

**Aktor**: Super Admin

**Alur yang seharusnya**:

```
Settings → Users
  ├─ Daftar user
  ├─ Create user (nama, email, password, role)
  ├─ Edit user
  ├─ Disable/Delete user
  └─ Assign Role (Admin / Operator / Radiografer / Dokter)
```

### Yang sudah ada:
- Login/Register (Laravel Breeze) ✅
- Edit profil sendiri ✅
- Ganti password ✅
- Tabel roles/permissions (Spatie) ✅

### Yang belum ada:
- CRUD User dari admin panel ❌
- Role management UI (buat/edit role) ❌
- Assign role ke user ❌
- Permission management ❌
- Permission-based menu/fitur gating ❌

---

## Workflow H: Audit Trail

**Kelengkapan**: 50% ⚠️

### Yang sudah ada:
- Semua request ke DCM4CHEE tercatat di tabel `audit_logs` ✅
- Menyimpan method, endpoint, request body, response status, duration ✅
- Relasi ke user + server ✅

### Yang belum ada:
- Halaman viewer audit log ❌
- Filter by user/server/endpoint/status ❌
- Export audit log ❌
- Retention policy ❌

---

## Workflow I: Notifications & Reporting

**Kelengkapan**: 0% ❌

### Yang belum ada:
- Email notifikasi untuk failed MWL ❌
- Report harian/mingguan registrasi pasien ❌
- Report jumlah pemeriksaan per modality ❌
- Report kinerja pengiriman MWL ❌
- Export data (CSV, Excel, PDF) ❌

---

## Matriks Prioritas Pengembangan

| Prioritas | Fitur | Workflow | Dampak |
|-----------|-------|----------|--------|
| 🔴 **P1** | User Management + RBAC | G | Keamanan & akses kontrol |
| 🔴 **P1** | Audit Log Viewer + Filter | H | Compliance & troubleshooting |
| 🟡 **P2** | Edit & Update MWL | B, C | Fleksibilitas operasional |
| 🟡 **P2** | Advanced Image Viewer | E | Interpretasi hasil |
| 🟡 **P2** | Export Worklist | C | Pelaporan offline |
| 🟢 **P3** | Dashboard Charts | F | Visualisasi data |
| 🟢 **P3** | Notifications | I | Alert real-time |
| 🟢 **P3** | Reporting & Analytics | I | Manajemen |

---

## Siklus Lengkap (End-to-End)

```
A. Konfigurasi Awal
    │
    ▼
B. Registrasi Pasien → Create MWL → DCM4CHEE
    │                            │
    │                            ▼
    │                     C. Worklist (sync status)
    │                            │
    │                            ▼
    │                     D. PACS Monitor (delivery)
    │                            │
    │                            ▼
    │                     Modality ambil dari PACS
    │                            │
    │                            ▼
    │                     Pasien diperiksa
    │                            │
    │                            ▼
    └────────────────── E. Study Browser (hasil)
                                │
                                ▼
                        F. Dashboard (ringkasan)
```

Sistem saat ini dapat menjalankan siklus di atas untuk fungsionalitas dasar. Pengembangan selanjutnya difokuskan pada prioritas P1 untuk kesiapan produksi penuh.
