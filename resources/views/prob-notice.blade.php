<?php

use App\Models\Prize;

$current_probability = floatval(Prize::sum('probability'));
?>
@if ($current_probability < 100)
<div class="row">
    <div class="alert alert-danger">
        {{ 'Some of all prizes probability is 100%. Currently  its ' . $current_probability . '% You have to add ' . (100 - $current_probability) . '% to the prize' }}
    </div>
</div>
@endif
