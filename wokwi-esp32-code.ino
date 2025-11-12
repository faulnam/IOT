// ========================================
// IoT Project - ESP32 + DHT22 Sensor
// Mengirim data suhu & kelembapan ke Laravel API
// ========================================

#include <WiFi.h>
#include <HTTPClient.h>

// ========================================
// KONFIGURASI WiFi
// ========================================
const char* ssid = "Wokwi-GUEST";        // WiFi SSID untuk Wokwi
const char* password = "";                // Password kosong untuk Wokwi

// ========================================
// KONFIGURASI API
// ========================================
// PENTING: Ganti dengan URL Laravel Anda
// Jika menggunakan ngrok: "http://xxxx.ngrok.io/api/sensor-data"
// Jika local dengan expose: "http://your-ip:8000/api/sensor-data"
const char* apiEndpoint = "http://YOUR_LARAVEL_URL/api/sensor-data";

// ========================================
// KONFIGURASI SENSOR DHT22
// ========================================
#define DHTPIN 4              // Pin data DHT22 terhubung ke GPIO 4
#define DHTTYPE DHT22         // Tipe sensor: DHT22 (AM2302)

// Simulasi pembacaan sensor DHT22
// Di Wokwi, Anda bisa gunakan library DHT atau simulasi manual
float temperature = 0.0;
float humidity = 0.0;

// ========================================
// KONFIGURASI DEVICE
// ========================================
const char* deviceId = "wokwi-esp32-01";
const char* location = "Lab IoT";

// ========================================
// INTERVAL PENGIRIMAN DATA
// ========================================
unsigned long previousMillis = 0;
const long interval = 10000;  // Kirim data setiap 10 detik

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  Serial.println("\n========================================");
  Serial.println("IoT Project - ESP32 + DHT22");
  Serial.println("========================================");
  
  // Koneksi ke WiFi
  connectToWiFi();
}

void loop() {
  unsigned long currentMillis = millis();
  
  // Kirim data setiap interval waktu
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    
    // Baca sensor (simulasi)
    readSensor();
    
    // Kirim data ke API
    if (WiFi.status() == WL_CONNECTED) {
      sendDataToAPI(temperature, humidity);
    } else {
      Serial.println("WiFi disconnected! Reconnecting...");
      connectToWiFi();
    }
  }
}

// ========================================
// FUNGSI: Koneksi ke WiFi
// ========================================
void connectToWiFi() {
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);
  
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    attempts++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✓ WiFi Connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("\n✗ WiFi Connection Failed!");
  }
}

// ========================================
// FUNGSI: Baca Sensor DHT22
// ========================================
void readSensor() {
  // SIMULASI PEMBACAAN SENSOR
  // Di implementasi nyata, gunakan library DHT:
  // temperature = dht.readTemperature();
  // humidity = dht.readHumidity();
  
  // Simulasi dengan nilai random
  temperature = 25.0 + random(-30, 70) / 10.0;  // 22°C - 32°C
  humidity = 60.0 + random(-100, 200) / 10.0;   // 50% - 80%
  
  Serial.println("\n--- Sensor Reading ---");
  Serial.print("Temperature: ");
  Serial.print(temperature, 1);
  Serial.println(" °C");
  Serial.print("Humidity: ");
  Serial.print(humidity, 1);
  Serial.println(" %");
}

// ========================================
// FUNGSI: Kirim Data ke API Laravel
// ========================================
void sendDataToAPI(float temp, float hum) {
  HTTPClient http;
  
  Serial.println("\n--- Sending to API ---");
  
  // Mulai koneksi HTTP
  http.begin(apiEndpoint);
  http.addHeader("Content-Type", "application/json");
  
  // Buat JSON payload
  String jsonPayload = "{";
  jsonPayload += "\"temperature\":" + String(temp, 1) + ",";
  jsonPayload += "\"humidity\":" + String(hum, 1) + ",";
  jsonPayload += "\"device_id\":\"" + String(deviceId) + "\",";
  jsonPayload += "\"location\":\"" + String(location) + "\"";
  jsonPayload += "}";
  
  Serial.println("Payload: " + jsonPayload);
  
  // Kirim POST request
  int httpResponseCode = http.POST(jsonPayload);
  
  // Cek response
  if (httpResponseCode > 0) {
    Serial.print("✓ HTTP Response Code: ");
    Serial.println(httpResponseCode);
    
    String response = http.getString();
    Serial.println("Response: " + response);
  } else {
    Serial.print("✗ Error Code: ");
    Serial.println(httpResponseCode);
    Serial.println("Error: " + http.errorToString(httpResponseCode));
  }
  
  http.end();
}

// ========================================
// CATATAN PENTING:
// ========================================
// 1. Ganti apiEndpoint dengan URL Laravel Anda
// 2. Untuk testing lokal, gunakan ngrok atau expose IP
// 3. Pastikan Laravel server sudah running
// 4. Untuk DHT22 real, install library: DHT sensor library by Adafruit
// 5. Sesuaikan deviceId untuk membedakan sensor
// ========================================
