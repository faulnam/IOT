# 🏗️ Arsitektur Sistem IoT

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         IoT MONITORING SYSTEM                            │
└─────────────────────────────────────────────────────────────────────────┘

┌───────────────┐          ┌──────────────┐          ┌─────────────────┐
│               │          │              │          │                 │
│    DHT22      │──────────│    ESP32     │──────────│  Laravel API    │
│    Sensor     │  I2C/    │   (Wokwi)    │   HTTP   │   (Backend)     │
│               │  Digital │              │   POST   │                 │
└───────────────┘          └──────────────┘          └─────────────────┘
                                                              │
                                                              │
                           ┌──────────────────────────────────┴───────┐
                           │                                          │
                           ▼                                          ▼
                  ┌─────────────────┐                      ┌─────────────────┐
                  │                 │                      │                 │
                  │    Database     │                      │   Dashboard     │
                  │  (MySQL/SQLite) │                      │      Web        │
                  │                 │                      │   (Frontend)    │
                  └─────────────────┘                      └─────────────────┘
```

---

## Component Details

### 1. Hardware Layer (Wokwi Simulation)

**DHT22 Sensor:**
- Temperature Range: -40°C to 80°C
- Humidity Range: 0% to 100%
- Accuracy: ±0.5°C, ±2%RH
- Interface: Digital (One-Wire)

**ESP32 Microcontroller:**
- WiFi: 802.11 b/g/n
- Processor: Dual-core
- Memory: 520KB SRAM
- WiFi Connection: Wokwi-GUEST

### 2. Communication Layer

**Protocol:** HTTP/HTTPS
**Method:** POST (untuk kirim data)
**Data Format:** JSON
**Interval:** Configurable (default 10 seconds)

**Sample Payload:**
```json
{
    "temperature": 28.5,
    "humidity": 65.3,
    "device_id": "wokwi-01",
    "location": "Lab IoT"
}
```

### 3. Backend Layer (Laravel)

**Framework:** Laravel 11
**Language:** PHP 8.2+
**Architecture:** MVC + RESTful API

**Components:**
```
SensorController
├── store()        → Receive & save data
├── getData()      → Get all sensor data
├── getLatest()    → Get latest reading
├── getStats()     → Get statistics
└── index()        → Display dashboard

SensorData Model
└── Eloquent ORM for database operations
```

### 4. Database Layer

**Schema: sensor_data**
```sql
┌─────────────┬──────────┬────────────┐
│ Field       │ Type     │ Note       │
├─────────────┼──────────┼────────────┤
│ id          │ INT      │ PK, AI     │
│ temperature │ FLOAT    │ Celsius    │
│ humidity    │ FLOAT    │ Percentage │
│ device_id   │ VARCHAR  │ Device ID  │
│ location    │ VARCHAR  │ Location   │
│ created_at  │ DATETIME │ Timestamp  │
│ updated_at  │ DATETIME │ Timestamp  │
└─────────────┴──────────┴────────────┘
```

### 5. Frontend Layer (Dashboard)

**Technologies:**
- Blade Templates
- Tailwind CSS
- Chart.js (for graphs)
- Vanilla JavaScript

**Features:**
- Real-time data display
- Interactive line charts
- Auto-refresh (10s interval)
- Responsive design
- Data table (20 latest records)

---

## Data Flow Diagram

### A. Data Sending Flow (Wokwi → Laravel)

```
┌──────────┐
│  Start   │
└────┬─────┘
     │
     ▼
┌─────────────────┐
│ Read DHT22      │
│ - Temperature   │
│ - Humidity      │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ Create JSON     │
│ Payload         │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ HTTP POST to    │
│ /api/sensor-data│
└────┬────────────┘
     │
     ▼
┌─────────────────┐      ┌─────────────────┐
│ Laravel API     │─────▶│ Validate Data   │
│ Receives        │      │ - temperature   │
└────┬────────────┘      │ - humidity      │
     │                   └─────────────────┘
     │                            │
     ▼                            ▼
┌─────────────────┐      ┌─────────────────┐
│ Save to DB via  │      │ Return JSON     │
│ Eloquent ORM    │      │ Response        │
└────┬────────────┘      └─────────────────┘
     │
     ▼
┌─────────────────┐
│ Success (201)   │
│ or Error (4xx)  │
└─────────────────┘
```

### B. Dashboard Display Flow (User → Dashboard)

```
┌──────────┐
│  User    │
│  Opens   │
│  Browser │
└────┬─────┘
     │
     ▼
┌─────────────────┐
│ GET /           │
│ (Dashboard)     │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ Controller      │
│ - Get latest    │
│ - Get recent 20 │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ Render Blade    │
│ Template with   │
│ Data            │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ Browser JS      │
│ - Init Chart    │
│ - Auto-refresh  │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ Periodic AJAX   │
│ GET /api/       │
│ sensor-data/    │
│ latest          │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ Update UI       │
│ - Cards         │
│ - Chart         │
│ - Table         │
└─────────────────┘
```

---

## API Endpoint Architecture

```
/api/sensor-data
│
├── POST    /                → Store new sensor data
│   ├── Middleware: None (public endpoint)
│   ├── Validation: Required
│   └── Response: 201 Created
│
├── GET     /                → Get all data (with limit)
│   ├── Query: ?limit=50
│   └── Response: 200 OK
│
├── GET     /latest          → Get latest reading
│   └── Response: 200 OK
│
└── GET     /stats           → Get statistics
    ├── Calculation: AVG, MIN, MAX
    └── Response: 200 OK
```

---

## Security Considerations

### Current Implementation (MVP)
- ✅ Input validation
- ✅ SQL injection protection (Eloquent ORM)
- ✅ JSON response format
- ❌ No authentication (public endpoint)
- ❌ No rate limiting

### Recommended for Production
- Add API Key authentication
- Implement rate limiting
- Add CORS configuration
- SSL/TLS encryption (HTTPS)
- Input sanitization
- API versioning

**Example Enhancement:**
```php
// Add API Key middleware
Route::middleware('api.key')->group(function () {
    Route::post('/sensor-data', [SensorController::class, 'store']);
});
```

---

## Deployment Architecture

### Development (Current)
```
Local Machine
├── Laravel Server (localhost:8000)
├── Database (SQLite/MySQL)
└── Wokwi Simulator (via ngrok tunnel)
```

### Production (Recommended)
```
Cloud Server (VPS/AWS/DigitalOcean)
├── Nginx/Apache → Laravel
├── MySQL Database
├── Redis (for caching)
├── SSL Certificate (Let's Encrypt)
└── Domain/IP Public ← ESP32 Devices
```

---

## Performance Considerations

### Database Optimization
- Index on `created_at` for faster latest queries
- Consider partitioning for large datasets
- Implement data retention policy

**Example Migration Enhancement:**
```php
$table->index('created_at');
$table->index('device_id');
```

### Caching Strategy
- Cache latest reading (TTL: 10 seconds)
- Cache statistics (TTL: 1 minute)
- Use Laravel Cache facade

**Example:**
```php
$latest = Cache::remember('sensor.latest', 10, function () {
    return SensorData::latest()->first();
});
```

---

## Scalability Plan

### Horizontal Scaling
1. Load balancer untuk multiple Laravel instances
2. Database replication (Master-Slave)
3. Queue system untuk async processing

### Vertical Scaling
1. Increase server resources (CPU, RAM)
2. Optimize database queries
3. Implement full-text search (Elasticsearch)

---

## Monitoring & Logging

### Recommended Tools
- Laravel Telescope (development)
- Sentry (error tracking)
- New Relic (performance monitoring)
- Grafana (metrics visualization)

### Log Points
```php
// In SensorController
Log::info('Sensor data received', ['device_id' => $request->device_id]);
Log::error('Failed to save sensor data', ['error' => $e->getMessage()]);
```

---

## Future Enhancements

### Phase 2 Features
- [ ] User authentication & authorization
- [ ] Multi-device management
- [ ] Alert system (email/SMS when threshold exceeded)
- [ ] Data export (CSV, Excel)
- [ ] Historical data analysis
- [ ] WebSocket for real-time updates
- [ ] Mobile app (React Native/Flutter)

### Phase 3 Features
- [ ] Machine Learning predictions
- [ ] Anomaly detection
- [ ] Custom dashboard builder
- [ ] API rate limiting per device
- [ ] Webhook support
- [ ] GraphQL API

---

## Technology Stack Summary

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **IoT Device** | ESP32 + DHT22 | Data acquisition |
| **Simulator** | Wokwi.com | Testing & development |
| **Backend** | Laravel 11 | API & business logic |
| **Database** | MySQL/SQLite | Data persistence |
| **Frontend** | Blade + Tailwind | User interface |
| **Charts** | Chart.js | Data visualization |
| **Hosting** | Local/VPS | Deployment |
| **Tunnel** | ngrok | Local exposure |

---

**Document Version:** 1.0  
**Last Updated:** November 2024  
**Author:** IoT Lab Team
