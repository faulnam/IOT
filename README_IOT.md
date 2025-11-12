# 🌡️ IoT Dashboard - Monitoring Suhu & Kelembapan

Aplikasi web untuk memonitor data suhu dan kelembapan dari sensor DHT22 yang dikirim dari Wokwi (ESP32) ke Laravel REST API.

![Laravel](https://img.shields.io/badge/Laravel-11-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![License](https://img.shields.io/badge/License-MIT-green)

## 📋 Fitur

- ✅ REST API untuk menerima data dari IoT devices (Wokwi)
- ✅ Dashboard real-time dengan tampilan modern
- ✅ Grafik visualisasi data (Chart.js)
- ✅ Auto-refresh setiap 10 detik
- ✅ Tabel data historis
- ✅ API endpoints lengkap (GET, POST)
- ✅ Responsive design dengan Tailwind CSS

## 🛠️ Teknologi yang Digunakan

### Backend
- Laravel 11
- MySQL/SQLite
- RESTful API

### Frontend
- Blade Template
- Tailwind CSS
- Chart.js
- Font Awesome

### IoT Device
- ESP32 (Wokwi Simulator)
- DHT22 Temperature & Humidity Sensor

## 📦 Struktur File yang Dibuat

```
iot/
├── app/
│   ├── Http/Controllers/
│   │   └── SensorController.php        # Controller untuk API & Web
│   └── Models/
│       └── SensorData.php              # Model untuk tabel sensor_data
├── database/
│   └── migrations/
│       └── 2024_11_12_000000_create_sensor_data_table.php
├── resources/
│   └── views/
│       └── dashboard.blade.php         # Tampilan dashboard
├── routes/
│   ├── api.php                         # API routes
│   └── web.php                         # Web routes
├── API_DOCUMENTATION.md                # Dokumentasi API lengkap
├── wokwi-esp32-code.ino               # Code untuk ESP32 di Wokwi
└── README_IOT.md                       # File ini
```

## 🚀 Cara Instalasi

### 1. Clone atau Setup Project

Pastikan Anda sudah berada di folder project Laravel ini.

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

Copy file `.env.example` menjadi `.env`:
```bash
copy .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iot_database
DB_USERNAME=root
DB_PASSWORD=
```

> **Atau gunakan SQLite untuk testing:**
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Jika pakai SQLite, buat file database:
```bash
type nul > database/database.sqlite
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migration

```bash
php artisan migrate
```

### 6. Jalankan Server

```bash
php artisan serve
```

Server akan berjalan di: `http://localhost:8000`

## 📡 API Endpoints

### Base URL: `http://localhost:8000/api`

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/sensor-data` | Kirim data sensor (untuk Wokwi) |
| GET | `/sensor-data` | Ambil semua data sensor |
| GET | `/sensor-data/latest` | Ambil data terbaru |
| GET | `/sensor-data/stats` | Ambil statistik data |

### Contoh Request (POST)

```bash
curl -X POST http://localhost:8000/api/sensor-data \
  -H "Content-Type: application/json" \
  -d '{
    "temperature": 28.5,
    "humidity": 65.3,
    "device_id": "wokwi-01",
    "location": "Lab IoT"
  }'
```

**Lihat dokumentasi lengkap di:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

## 🌐 Cara Mengakses Dashboard

1. Pastikan server Laravel sudah running
2. Buka browser
3. Akses: `http://localhost:8000`

Dashboard akan menampilkan:
- 📊 Data suhu dan kelembapan real-time
- 📈 Grafik data sensor
- 📋 Tabel data historis 20 record terakhir
- 🔄 Auto-refresh setiap 10 detik

## 🔌 Integrasi dengan Wokwi

### 1. Setup di Wokwi

1. Buka [Wokwi.com](https://wokwi.com)
2. Buat project baru dengan ESP32
3. Tambahkan DHT22 sensor
4. Copy code dari file `wokwi-esp32-code.ino`

### 2. Konfigurasi Koneksi

Karena Wokwi tidak bisa langsung akses `localhost`, Anda perlu:

#### **Opsi A: Menggunakan ngrok**
```bash
# Install ngrok dari https://ngrok.com
ngrok http 8000
```

Kemudian ganti di code Wokwi:
```cpp
const char* apiEndpoint = "http://xxxx.ngrok.io/api/sensor-data";
```

#### **Opsi B: Deploy ke Server Online**
Deploy Laravel ke hosting atau VPS, lalu gunakan domain/IP public.

### 3. Diagram Koneksi Wokwi

```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   DHT22     │ ──────> │    ESP32     │ ──────> │  Laravel    │
│   Sensor    │  Data   │   (Wokwi)    │  HTTP   │   REST API  │
└─────────────┘         └──────────────┘         └─────────────┘
                                                         │
                                                         v
                                                   ┌─────────────┐
                                                   │  Dashboard  │
                                                   │     Web     │
                                                   └─────────────┘
```

## 🧪 Testing API

### Menggunakan Postman

1. **Kirim Data (POST)**
   - Method: POST
   - URL: `http://localhost:8000/api/sensor-data`
   - Headers: `Content-Type: application/json`
   - Body (raw JSON):
   ```json
   {
       "temperature": 28.5,
       "humidity": 65.3,
       "device_id": "test-01",
       "location": "Lab Testing"
   }
   ```

2. **Ambil Data (GET)**
   - Method: GET
   - URL: `http://localhost:8000/api/sensor-data`

### Menggunakan cURL (PowerShell)

```powershell
# POST data
Invoke-WebRequest -Uri "http://localhost:8000/api/sensor-data" `
  -Method POST `
  -ContentType "application/json" `
  -Body '{"temperature": 28.5, "humidity": 65.3, "device_id": "test-01"}'

# GET data
Invoke-WebRequest -Uri "http://localhost:8000/api/sensor-data" -Method GET
```

## 📸 Screenshot

### Dashboard View
- Card suhu dan kelembapan dengan animasi
- Grafik line chart interaktif
- Tabel data dengan auto-scroll
- Design modern dengan gradient background

### API Response
```json
{
    "success": true,
    "message": "Data berhasil disimpan",
    "data": {
        "id": 1,
        "temperature": 28.5,
        "humidity": 65.3,
        "device_id": "wokwi-01",
        "location": "Lab IoT",
        "created_at": "2024-11-12T10:30:00.000000Z",
        "updated_at": "2024-11-12T10:30:00.000000Z"
    }
}
```

## 🛠️ Troubleshooting

### Problem: API tidak bisa diakses dari Wokwi
**Solusi:** Gunakan ngrok atau deploy ke server online

### Problem: Data tidak muncul di dashboard
**Solusi:** 
- Refresh browser (F5)
- Check browser console untuk error
- Pastikan ada data di database dengan: `php artisan tinker` lalu `App\Models\SensorData::count()`

### Problem: Migration error
**Solusi:** 
- Pastikan database sudah dibuat
- Check konfigurasi `.env`
- Hapus tabel dan migrate ulang: `php artisan migrate:fresh`

### Problem: CORS error
**Solusi:** Install dan konfigurasi Laravel CORS:
```bash
composer require fruitcake/laravel-cors
```

## 📚 Referensi

- [Laravel Documentation](https://laravel.com/docs)
- [Wokwi Documentation](https://docs.wokwi.com)
- [DHT22 Sensor Datasheet](https://www.sparkfun.com/datasheets/Sensors/Temperature/DHT22.pdf)
- [Chart.js Documentation](https://www.chartjs.org/docs/)

## 👨‍💻 Developer

Dibuat untuk project IoT Lab dengan Laravel REST API

## 📝 License

MIT License - Silakan digunakan untuk keperluan edukasi dan pembelajaran.

---

**Happy Coding! 🚀**

Jika ada pertanyaan atau issue, silakan buat issue di repository ini.
