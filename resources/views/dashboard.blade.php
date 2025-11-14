<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>IoT Dashboard - Monitoring Gas (MQ2)</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .stat-card {
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
</head>
<body class="p-6">
    <div class="container mx-auto max-w-7xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold text-white mb-2">
                <i class="fas fa-microchip"></i> IoT Dashboard
            </h1>
                <p class="text-white text-lg opacity-90">Monitoring Gas (MQ2) Real-time dari Wokwi</p>
        </div>

        <!-- Latest Data Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Temperature Card -->
            <div class="card p-6 stat-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase">Gas ADC Terkini</p>
                            <h2 class="text-5xl font-bold text-red-500" id="current-adc">
                                {{ $latestData ? $latestData->adc : '--' }}
                            </h2>
                    </div>
                    <div class="bg-red-100 p-4 rounded-full">
                            <i class="fas fa-biohazard text-4xl text-red-500"></i>
                    </div>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <span class="pulse-dot inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    <span id="temp-time">{{ $latestData ? $latestData->created_at->diffForHumans() : 'Belum ada data' }}</span>
                </div>
            </div>

                <!-- Status Card -->
            <div class="card p-6 stat-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase">Status Sensor</p>
                            <h2 class="text-5xl font-bold text-blue-500" id="current-status">
                                {{ $latestData ? $latestData->status : '--' }}
                            </h2>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full">
                            <i class="fas fa-info-circle text-4xl text-blue-500"></i>
                    </div>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <span class="pulse-dot inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    <span id="humidity-time">{{ $latestData ? $latestData->created_at->diffForHumans() : 'Belum ada data' }}</span>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card p-6 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-chart-line"></i> Grafik ADC Sensor Gas
            </h3>
            <div class="h-96">
                <canvas id="sensorChart"></canvas>
            </div>
        </div>

        <!-- Recent Data Table -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-table"></i> Data Terbaru
                </h3>
                <button onclick="refreshData()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ADC</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Device ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="data-table">
                        @forelse($recentData as $index => $data)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-semibold text-red-600">{{ $data->adc }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-semibold text-blue-600">{{ $data->status ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $data->device_id ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $data->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Belum ada data tersedia</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white">
            <p class="opacity-75">
                <i class="fas fa-code"></i> IoT Project with Laravel & Wokwi |
                <a href="/api/sensor-data" class="underline hover:text-yellow-300">API Endpoint</a>
            </p>
        </div>
    </div>

    <script>
        // Chart Configuration
        const ctx = document.getElementById('sensorChart') && document.getElementById('sensorChart').getContext('2d');
        let sensorChart = null;

        // Data untuk chart
        const chartData = {
            labels: [],
                adc: []
        };

        // Inisialisasi chart
        function initChart() {
            if (!ctx) {
                console.error('Canvas element #sensorChart not found');
                return;
            }

            // Destroy existing chart instance if exists
            if (sensorChart) {
                try { sensorChart.destroy(); } catch (e) { /* ignore */ }
                sensorChart = null;
            }

            // Create chart with empty data; we'll update it after fetching
            sensorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'ADC',
                            data: [],
                            borderColor: 'rgba(220, 38, 38, 1)',
                            backgroundColor: 'rgba(254, 202, 202, 0.4)',
                            tension: 0.25,
                            fill: true,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // Load data dari API
        async function loadChartData() {
            try {
                const response = await fetch('/api/sensor-data?limit=20');
                const result = await response.json();

                console.debug('loadChartData result', result);

                if (!result || !result.success) {
                    console.warn('No chart data available or API returned error');
                    // Initialize chart if missing
                    if (!sensorChart) initChart();
                    return;
                }

                const data = (result.data || []).slice().reverse(); // from old -> new

                const labels = data.map(item => {
                    const date = new Date(item.created_at);
                    return isNaN(date) ? item.created_at : date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                });

                const adcValues = data.map(item => {
                    // ensure numeric; keep nulls so labels/data lengths match
                    const v = Number(item.adc);
                    return Number.isFinite(v) ? v : null;
                });

                console.debug('chart labels length:', labels.length, 'adcValues length:', adcValues.length);

                chartData.labels = labels;
                chartData.adc = adcValues;

                if (!sensorChart) initChart();

                // update chart dataset
                sensorChart.data.labels = chartData.labels;
                sensorChart.data.datasets[0].data = chartData.adc;

                // adjust y axis max/min if we have values
                if (chartData.adc.length) {
                    const max = Math.max(...chartData.adc);
                    const min = Math.min(...chartData.adc);
                    sensorChart.options.scales.y.max = Math.ceil(max * 1.05);
                    sensorChart.options.scales.y.min = Math.floor(Math.min(0, min * 0.95));
                }

                sensorChart.update();
            } catch (error) {
                console.error('Error loading chart data:', error);
            }
        }

        // Refresh data
        async function refreshData() {
            try {
                const response = await fetch('/api/sensor-data/latest');
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                        document.getElementById('current-adc').textContent = data.adc;
                        document.getElementById('current-status').textContent = data.status ?? '--';

                    // Reload chart
                    await loadChartData();

                    // Reload page untuk update tabel
                    location.reload();
                }
            } catch (error) {
                console.error('Error refreshing data:', error);
            }
        }

        // Auto refresh setiap 10 detik
        setInterval(async () => {
            try {
                const response = await fetch('/api/sensor-data/latest');
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                        document.getElementById('current-adc').textContent = data.adc;
                        document.getElementById('current-status').textContent = data.status ?? '--';

                    await loadChartData();
                }
            } catch (error) {
                console.error('Error auto-refresh:', error);
            }
        }, 10000);

        // Initialize
        window.addEventListener('load', () => {
            initChart();
            loadChartData();
        });
    </script>
</body>
</html>
