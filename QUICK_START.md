# 🚀 QUICK START GUIDE

## Langkah Cepat untuk Menjalankan Project

### 1️⃣ Setup Database & Migrasi
```powershell
# Jika pakai SQLite (recommended untuk testing)
type nul > database/database.sqlite

# Update .env
DB_CONNECTION=sqlite

# Jalankan migrasi
php artisan migrate
```

### 2️⃣ Jalankan Server Laravel
```powershell
php artisan serve
```

Server: `http://localhost:8000`

### 3️⃣ Testing

#### A. Test Manual dengan Browser
Buka: `http://localhost:8000/test-api`

Fitur:
- ✅ Form untuk kirim data sensor
- ✅ Button untuk ambil semua data, data terbaru, statistik
- ✅ Button kirim data random
- ✅ Tampilan response JSON langsung

#### B. Test dengan cURL (PowerShell)
```powershell
# Kirim data (POST)
Invoke-WebRequest -Uri "http://localhost:8000/api/sensor-data" -Method POST -ContentType "application/json" -Body '{"temperature": 28.5, "humidity": 65.3, "device_id": "test-01", "location": "Lab IoT"}'

# Ambil data (GET)
Invoke-WebRequest -Uri "http://localhost:8000/api/sensor-data" -Method GET

# Data terbaru
Invoke-WebRequest -Uri "http://localhost:8000/api/sensor-data/latest" -Method GET

# Statistik
Invoke-WebRequest -Uri "http://localhost:8000/api/sensor-data/stats" -Method GET
```

#### C. Test dengan Postman
Import collection dengan endpoints berikut:

**POST** `http://localhost:8000/api/sensor-data`
Body (JSON):
```json
{
    "temperature": 28.5,
    "humidity": 65.3,
    "device_id": "wokwi-01",
    "location": "Lab IoT"
}
```

### 4️⃣ Lihat Dashboard
Buka: `http://localhost:8000`

Dashboard akan menampilkan:
- 📊 Card suhu & kelembapan real-time
- 📈 Grafik data sensor
- 📋 Tabel 20 data terbaru
- 🔄 Auto-refresh setiap 10 detik

---

## 📡 API Endpoints Summary

| Method | URL | Deskripsi |
|--------|-----|-----------|
| POST | `/api/sensor-data` | Kirim data dari sensor |
| GET | `/api/sensor-data` | Ambil semua data (max 50) |
| GET | `/api/sensor-data/latest` | Data terbaru |
| GET | `/api/sensor-data/stats` | Statistik (avg, min, max) |

---

## 🔌 Integrasi Wokwi

### Langkah:

1. **Buat project di Wokwi**
   - Pilih ESP32
   - Tambah DHT22 sensor

2. **Expose Local Server**
   
   **Opsi A - Menggunakan ngrok:**
   ```bash
   ngrok http 8000
   ```
   
   **Opsi B - Menggunakan Laragon (jika pakai Laragon):**
   - Menu Laragon → Share → Share Project
   - Copy URL yang diberikan

3. **Update Code Wokwi**
   
   Ganti di file `wokwi-esp32-code.ino`:
   ```cpp
   const char* apiEndpoint = "http://YOUR_NGROK_URL/api/sensor-data";
   ```

4. **Run Simulation di Wokwi**
   - Data akan otomatis terkirim setiap 10 detik
   - Cek dashboard Laravel untuk melihat data masuk

---

## 📂 File-file Penting

```
iot/
├── app/
│   ├── Http/Controllers/
│   │   └── SensorController.php         ← Controller utama
│   └── Models/
│       └── SensorData.php               ← Model data sensor
│
├── database/migrations/
│   └── *_create_sensor_data_table.php   ← Schema database
│
├── resources/views/
│   ├── dashboard.blade.php              ← Dashboard utama
│   └── test-api.blade.php              ← Halaman testing API
│
├── routes/
│   ├── api.php                          ← API routes
│   └── web.php                          ← Web routes
│
├── wokwi-esp32-code.ino                 ← Code untuk Wokwi
├── API_DOCUMENTATION.md                 ← Dokumentasi API lengkap
└── README_IOT.md                        ← README project
```

---

## ✅ Checklist

Sebelum mulai integrasi dengan Wokwi, pastikan:

- [ ] Server Laravel running (`php artisan serve`)
- [ ] Migration sudah dijalankan
- [ ] Test API berhasil (via `/test-api` atau Postman)
- [ ] Dashboard bisa dibuka dan menampilkan data
- [ ] Local server sudah di-expose (ngrok/share)

---

## 🆘 Troubleshooting Cepat

**Q: Dashboard tidak muncul data?**
- Kirim data dummy dulu via `/test-api`

**Q: API error 500?**
- Check `.env` database config
- Pastikan migration sudah jalan: `php artisan migrate:status`

**Q: Wokwi tidak bisa kirim data?**
- Pastikan API endpoint sudah di-expose (ngrok)
- Check URL di code ESP32 sudah benar
- Pastikan ada `http://` atau `https://` di URL

**Q: CSRF token error?**
- API routes tidak perlu CSRF, pastikan pakai `/api/...` bukan `/...`

---

## 📞 Support

Jika ada error atau pertanyaan:
1. Check file `API_DOCUMENTATION.md` untuk detail lengkap
2. Check file `README_IOT.md` untuk panduan lengkap
3. Debug dengan browser console (F12)

---

**Selamat Coding! 🎉**
