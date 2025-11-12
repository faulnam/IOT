# IoT REST API - Laravel
## Monitoring Suhu & Kelembapan dari Wokwi

---

## 📋 Daftar Isi
1. [Setup & Instalasi](#setup--instalasi)
2. [API Endpoints](#api-endpoints)
3. [Cara Kirim Data dari Wokwi](#cara-kirim-data-dari-wokwi)
4. [Testing API](#testing-api)

---

## 🚀 Setup & Instalasi

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Jalankan Server Laravel
```bash
php artisan serve
```

Server akan berjalan di: `http://localhost:8000`

---

## 📡 API Endpoints

### Base URL
```
http://localhost:8000/api
```

### 1. **POST** - Kirim Data Sensor (untuk Wokwi)
**Endpoint:** `POST /api/sensor-data`

**Request Body (JSON):**
```json
{
    "temperature": 28.5,
    "humidity": 65.3,
    "device_id": "wokwi-01",
    "location": "Lab IoT"
}
```

**Response Success (201):**
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

---

### 2. **GET** - Ambil Semua Data Sensor
**Endpoint:** `GET /api/sensor-data?limit=50`

**Query Parameters:**
- `limit` (optional): Jumlah data yang diambil (default: 50)

**Response Success (200):**
```json
{
    "success": true,
    "message": "Data berhasil diambil",
    "count": 50,
    "data": [
        {
            "id": 50,
            "temperature": 27.8,
            "humidity": 68.2,
            "device_id": "wokwi-01",
            "location": "Lab IoT",
            "created_at": "2024-11-12T10:35:00.000000Z",
            "updated_at": "2024-11-12T10:35:00.000000Z"
        },
        // ... data lainnya
    ]
}
```

---

### 3. **GET** - Ambil Data Sensor Terbaru
**Endpoint:** `GET /api/sensor-data/latest`

**Response Success (200):**
```json
{
    "success": true,
    "message": "Data terbaru berhasil diambil",
    "data": {
        "id": 50,
        "temperature": 27.8,
        "humidity": 68.2,
        "device_id": "wokwi-01",
        "location": "Lab IoT",
        "created_at": "2024-11-12T10:35:00.000000Z",
        "updated_at": "2024-11-12T10:35:00.000000Z"
    }
}
```

---

### 4. **GET** - Ambil Statistik Data
**Endpoint:** `GET /api/sensor-data/stats`

**Response Success (200):**
```json
{
    "success": true,
    "message": "Statistik berhasil diambil",
    "data": {
        "temperature": {
            "avg": 28.3,
            "min": 25.0,
            "max": 32.5
        },
        "humidity": {
            "avg": 67.5,
            "min": 55.0,
            "max": 80.0
        },
        "total_records": 150,
        "latest_record": {
            "id": 150,
            "temperature": 27.8,
            "humidity": 68.2,
            "device_id": "wokwi-01",
            "location": "Lab IoT",
            "created_at": "2024-11-12T10:35:00.000000Z",
            "updated_at": "2024-11-12T10:35:00.000000Z"
        }
    }
}
```

---

## 🔌 Cara Kirim Data dari Wokwi

### Menggunakan ESP32/Arduino di Wokwi

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

// WiFi credentials
const char* ssid = "Wokwi-GUEST";
const char* password = "";

// API endpoint
const char* serverName = "http://localhost:8000/api/sensor-data";

// DHT Sensor
#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

void setup() {
  Serial.begin(115200);
  
  // Koneksi WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected!");
  
  dht.begin();
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // Baca sensor
    float temperature = dht.readTemperature();
    float humidity = dht.readHumidity();
    
    if (!isnan(temperature) && !isnan(humidity)) {
      // Kirim data ke API
      http.begin(serverName);
      http.addHeader("Content-Type", "application/json");
      
      String jsonData = "{";
      jsonData += "\"temperature\":" + String(temperature, 1) + ",";
      jsonData += "\"humidity\":" + String(humidity, 1) + ",";
      jsonData += "\"device_id\":\"wokwi-01\",";
      jsonData += "\"location\":\"Lab IoT\"";
      jsonData += "}";
      
      int httpResponseCode = http.POST(jsonData);
      
      if (httpResponseCode > 0) {
        String response = http.getString();
        Serial.println("Response: " + response);
      } else {
        Serial.println("Error: " + String(httpResponseCode));
      }
      
      http.end();
    }
  }
  
  delay(10000); // Kirim data setiap 10 detik
}
```

---

## 🧪 Testing API

### 1. Menggunakan cURL

**Kirim Data:**
```bash
curl -X POST http://localhost:8000/api/sensor-data \
  -H "Content-Type: application/json" \
  -d "{\"temperature\": 28.5, \"humidity\": 65.3, \"device_id\": \"test-01\"}"
```

**Ambil Data:**
```bash
curl http://localhost:8000/api/sensor-data
```

### 2. Menggunakan Postman

1. Buat request baru dengan method **POST**
2. URL: `http://localhost:8000/api/sensor-data`
3. Headers: `Content-Type: application/json`
4. Body (raw JSON):
```json
{
    "temperature": 28.5,
    "humidity": 65.3,
    "device_id": "wokwi-01",
    "location": "Lab IoT"
}
```

---

## 🌐 Akses Dashboard Web

Buka browser dan akses:
```
http://localhost:8000
```

Dashboard akan menampilkan:
- 📊 Data suhu dan kelembapan terkini
- 📈 Grafik real-time
- 📋 Tabel data terbaru
- 🔄 Auto-refresh setiap 10 detik

---

## 📝 Catatan Penting

1. **CORS**: Jika Wokwi tidak bisa kirim data, tambahkan CORS middleware
2. **Database**: Pastikan database sudah di-setup di `.env`
3. **Testing**: Gunakan Postman untuk test API sebelum integrate dengan Wokwi
4. **Device ID**: Sesuaikan device_id untuk membedakan beberapa sensor

---

## 🛠️ Troubleshooting

### Error "Connection Refused"
- Pastikan Laravel server sudah berjalan
- Cek firewall yang mungkin memblokir port 8000

### Error "CSRF Token Mismatch"
- API routes tidak memerlukan CSRF token
- Pastikan menggunakan endpoint `/api/...`

### Data tidak muncul di dashboard
- Refresh browser (F5)
- Cek console browser untuk error JavaScript
- Pastikan ada data di database

---

## 📧 Support

Jika ada pertanyaan atau masalah, silakan hubungi tim IoT Lab.

---

**Happy Coding! 🚀**
