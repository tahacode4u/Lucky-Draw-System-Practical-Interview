<?php

namespace App\Models;

use App\Models\Prize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardedPrizes extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'prizes_id',
        'simulation_value',
        'created_at',
        'updated_at'
    ];

    public function getPrizes()
    {
        return $this->belongsTo(Prize::class, 'id', 'prizes_id');
    }
}
