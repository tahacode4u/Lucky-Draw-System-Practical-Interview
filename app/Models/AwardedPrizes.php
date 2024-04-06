<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardedPrizes extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'prizes_id',
        'simulation_value',
        'awarded',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
