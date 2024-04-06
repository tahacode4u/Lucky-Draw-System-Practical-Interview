<?php

namespace App\Models;

use App\Models\AwardedPrizes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prize extends Model
{

    protected $guarded = ['id'];


    // tried but not working logic
    // method: random number generator in arbitrary probability distributaion fashion
    /*
    public function findCeil ($arr, $r, $l, $h)
    {
        $mid = 0;
        while ($l < $h) {
            $mid = $l + (($h - $l) >> 1);
            $r = ($r > $arr[$mid]) ? ($l = $mid + 1) : ($h = $mid);
        }
        return ($arr[$l] >= $r) ? $l:-1;
    }

    public  static function nextPrize($arr, $freq, $n)
    {
        $prefix = [];
        $i = 0;
        $prefix[0] = $freq[0];
        for ($i = 1; $i < $n; ++$i) {
            $prefix[$i] = $prefix[$i - 1] + $freq[$i];
        }

        $r = floor(random() * $prefix[$n - 1]) + 1;
        $indexc = findCeil($prefix, $r, 0, $n - 1);
        return $arr[$indexc];
    }
    */

    public  static function nextPrize()
    {
        // TODO: Implement nextPrize() logic here.
    }

    public function getAwarded()
    {
        return $this->hasOne(AwardedPrizes::class, 'id', 'prizes_id');
    }
}
