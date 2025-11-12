<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester - IoT Sensor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                🧪 IoT API Tester
            </h1>
            
            <div class="mb-6">
                <p class="text-gray-600">
                    Tool ini untuk testing API endpoint sebelum integrasi dengan Wokwi.
                </p>
            </div>

            <!-- Form untuk kirim data -->
            <div class="border-2 border-blue-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-700 mb-4">📤 Kirim Data Sensor</h2>
                
                <form id="sensorForm" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Suhu (°C)
                            </label>
                            <input type="number" id="temperature" step="0.1" value="28.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kelembapan (%)
                            </label>
                            <input type="number" id="humidity" step="0.1" value="65.5" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Device ID
                            </label>
                            <input type="text" id="device_id" value="test-device-01" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <input type="text" id="location" value="Lab Testing" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Kirim Data ke API
                    </button>
                </form>

                <div id="postResponse" class="mt-4 hidden">
                    <h3 class="font-semibold text-gray-700 mb-2">Response:</h3>
                    <pre class="bg-gray-100 p-4 rounded-lg overflow-x-auto text-sm" id="postResponseData"></pre>
                </div>
            </div>

            <!-- Tombol untuk ambil data -->
            <div class="border-2 border-green-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-700 mb-4">📥 Ambil Data</h2>
                
                <div class="grid grid-cols-3 gap-4">
                    <button onclick="getAllData()" 
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Semua Data
                    </button>
                    
                    <button onclick="getLatestData()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Data Terbaru
                    </button>
                    
                    <button onclick="getStats()" 
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Statistik
                    </button>
                </div>

                <div id="getResponse" class="mt-4 hidden">
                    <h3 class="font-semibold text-gray-700 mb-2">Response:</h3>
                    <pre class="bg-gray-100 p-4 rounded-lg overflow-x-auto text-sm max-h-96" id="getResponseData"></pre>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="border-2 border-yellow-200 rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-700 mb-4">⚡ Quick Actions</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="sendRandomData()" 
                            class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Kirim Data Random
                    </button>
                    
                    <a href="/" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition text-center">
                        Lihat Dashboard
                    </a>
                </div>
            </div>

            <!-- API Endpoints Info -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="font-bold text-gray-800 mb-3">📡 API Endpoints:</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li>• POST <code class="bg-gray-200 px-2 py-1 rounded">/api/sensor-data</code> - Kirim data sensor</li>
                    <li>• GET <code class="bg-gray-200 px-2 py-1 rounded">/api/sensor-data</code> - Ambil semua data</li>
                    <li>• GET <code class="bg-gray-200 px-2 py-1 rounded">/api/sensor-data/latest</code> - Data terbaru</li>
                    <li>• GET <code class="bg-gray-200 px-2 py-1 rounded">/api/sensor-data/stats</code> - Statistik</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/api';

        // Form submit handler
        document.getElementById('sensorForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const data = {
                temperature: parseFloat(document.getElementById('temperature').value),
                humidity: parseFloat(document.getElementById('humidity').value),
                device_id: document.getElementById('device_id').value,
                location: document.getElementById('location').value
            };

            await postData(data);
        });

        // POST data
        async function postData(data) {
            try {
                const response = await fetch(`${API_BASE}/sensor-data`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                showResponse('post', result);
            } catch (error) {
                showResponse('post', { error: error.message });
            }
        }

        // GET all data
        async function getAllData() {
            try {
                const response = await fetch(`${API_BASE}/sensor-data?limit=10`);
                const result = await response.json();
                showResponse('get', result);
            } catch (error) {
                showResponse('get', { error: error.message });
            }
        }

        // GET latest data
        async function getLatestData() {
            try {
                const response = await fetch(`${API_BASE}/sensor-data/latest`);
                const result = await response.json();
                showResponse('get', result);
            } catch (error) {
                showResponse('get', { error: error.message });
            }
        }

        // GET stats
        async function getStats() {
            try {
                const response = await fetch(`${API_BASE}/sensor-data/stats`);
                const result = await response.json();
                showResponse('get', result);
            } catch (error) {
                showResponse('get', { error: error.message });
            }
        }

        // Send random data
        async function sendRandomData() {
            const data = {
                temperature: (Math.random() * 10 + 22).toFixed(1),
                humidity: (Math.random() * 20 + 50).toFixed(1),
                device_id: 'test-random',
                location: 'Random Test'
            };

            // Update form
            document.getElementById('temperature').value = data.temperature;
            document.getElementById('humidity').value = data.humidity;
            
            await postData(data);
        }

        // Show response
        function showResponse(type, data) {
            const responseDiv = document.getElementById(`${type}Response`);
            const responseData = document.getElementById(`${type}ResponseData`);
            
            responseDiv.classList.remove('hidden');
            responseData.textContent = JSON.stringify(data, null, 2);
            
            // Scroll to response
            responseDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    </script>
</body>
</html>
