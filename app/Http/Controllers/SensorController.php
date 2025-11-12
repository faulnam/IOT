<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorController extends Controller
{
    /**
     * Menampilkan dashboard web dengan data sensor
     */
    public function index()
    {
        $latestData = SensorData::latest()->first();
        $recentData = SensorData::latest()->take(20)->get();
        
        return view('dashboard', compact('latestData', 'recentData'));
    }

    /**
     * API Endpoint untuk menerima data dari Wokwi (POST)
     * Endpoint: POST /api/sensor-data
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric|min:0|max:100',
            'device_id' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sensorData = SensorData::create([
                'temperature' => $request->temperature,
                'humidity' => $request->humidity,
                'device_id' => $request->device_id ?? 'wokwi-01',
                'location' => $request->location ?? 'Lab IoT'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $sensorData
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Endpoint untuk mendapatkan semua data sensor (GET)
     * Endpoint: GET /api/sensor-data
     */
    public function getData(Request $request)
    {
        try {
            $limit = $request->query('limit', 50);
            $sensorData = SensorData::latest()->take($limit)->get();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'count' => $sensorData->count(),
                'data' => $sensorData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Endpoint untuk mendapatkan data sensor terbaru (GET)
     * Endpoint: GET /api/sensor-data/latest
     */
    public function getLatest()
    {
        try {
            $latestData = SensorData::latest()->first();

            if (!$latestData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data terbaru berhasil diambil',
                'data' => $latestData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Endpoint untuk statistik data sensor (GET)
     * Endpoint: GET /api/sensor-data/stats
     */
    public function getStats()
    {
        try {
            $stats = [
                'temperature' => [
                    'avg' => SensorData::avg('temperature'),
                    'min' => SensorData::min('temperature'),
                    'max' => SensorData::max('temperature'),
                ],
                'humidity' => [
                    'avg' => SensorData::avg('humidity'),
                    'min' => SensorData::min('humidity'),
                    'max' => SensorData::max('humidity'),
                ],
                'total_records' => SensorData::count(),
                'latest_record' => SensorData::latest()->first()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistik berhasil diambil',
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
