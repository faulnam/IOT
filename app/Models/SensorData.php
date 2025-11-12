<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor_data';

    protected $fillable = [
        'temperature',
        'humidity',
        'device_id',
        'location'
    ];

    protected $casts = [
        'temperature' => 'float',
        'humidity' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
