<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
        // Log raw incoming request for debugging tunnel/forwarding issues
        try {
            Log::info('Incoming /api/sensor-data request', [
                'content' => $request->getContent(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);
        } catch (\Throwable $e) {
            // Prevent logging from breaking the endpoint
        }

        $validator = Validator::make($request->all(), [
            'adc' => 'required|numeric',
            'status' => 'nullable|string|max:255',
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
                'adc' => (int) $request->adc,
                'status' => $request->status ?? null,
                'device_id' => $request->device_id ?? 'wokwi-01',
                'location' => $request->location ?? 'Lab IoT'
            ]);

            // Jika status bahaya, kirim notifikasi via helper `sendTelegram`
            try {
                if (!empty($sensorData->status) && strcasecmp($sensorData->status, 'DARURAT') === 0) {
                    $when = $sensorData->created_at ? $sensorData->created_at->toDateTimeString() : now()->toDateTimeString();
                    $msg = "🚨 *Gas Alert* 🚨\n" .
                           "Status: *{$sensorData->status}*\n" .
                           "ADC: `{$sensorData->adc}`\n" .
                           "Device: {$sensorData->device_id}\n" .
                           "Location: {$sensorData->location}\n" .
                           "Waktu: {$when}";

                    // Panggil helper — pastikan helper sudah ter-load (app/Helpers/telegram.php)
                    $token = env('TELEGRAM_BOT_TOKEN');
                    $chatId = env('TELEGRAM_CHAT_ID');
                    if (empty($token) || empty($chatId)) {
                        Log::warning('Telegram env not configured, cannot send alert', ['has_token' => !empty($token), 'has_chat' => !empty($chatId)]);
                    } else {
                        if (function_exists('sendTelegram')) {
                            try {
                                $res = sendTelegram($msg);
                                // If response is a Laravel HTTP client response, we can inspect it
                                if (is_object($res) && method_exists($res, 'successful')) {
                                    if ($res->successful()) {
                                        Log::info('Telegram notification sent successfully', ['chat_id' => $chatId]);
                                    } else {
                                        $status = method_exists($res, 'status') ? $res->status() : null;
                                        $body = method_exists($res, 'body') ? $res->body() : null;
                                        Log::warning('Telegram notification failed', ['status' => $status, 'body' => $body]);
                                    }
                                } else {
                                    // unknown return type — log and continue
                                    Log::info('sendTelegram returned', ['result' => $res]);
                                }
                            } catch (\Throwable $e) {
                                Log::warning('Exception when calling sendTelegram', ['error' => $e->getMessage()]);
                            }
                        } else {
                            Log::warning('sendTelegram helper not found when trying to send alert', ['payload' => $msg]);
                        }
                    }
                }
            } catch (\Throwable $notifyEx) {
                Log::warning('Failed to send Telegram notification', ['error' => $notifyEx->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $sensorData
            ], 201);

        } catch (\Exception $e) {
            // Log exception details for easier debugging
            try {
                Log::error('SensorController@store exception', [
                    'message' => $e->getMessage(),
                    'payload' => $request->getContent()
                ]);
            } catch (\Throwable $ex) {
                // ignore
            }

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
                'adc' => [
                    'avg' => SensorData::avg('adc'),
                    'min' => SensorData::min('adc'),
                    'max' => SensorData::max('adc'),
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
