<?php

namespace App\Models;

use App\Models\AwardedPrizes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prize extends Model
{

    protected $guarded = ['id'];

    public  static function nextPrize($prizesArr = array(), $total_prizes = "")
    {
        shuffle($prizesArr);
        $prize_id = $prizesArr[0];
        AwardedPrizes::create([
            'prizes_id' => $prize_id,
            'simulation_value' => $total_prizes,
        ]);
    }

    public function getAwarded()
    {
        return $this->hasOne(AwardedPrizes::class, 'prizes_id', 'id');
    }
}
