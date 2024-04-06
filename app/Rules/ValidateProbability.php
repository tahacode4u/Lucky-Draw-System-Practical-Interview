<?php

namespace App\Rules;

use Closure;
use App\Models\Prize;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;

class ValidateProbability implements DataAwareRule, InvokableRule
{
    protected $data = [];
    private $id;

    public function __construct($id = '')
    {
        if ($id != '') {
            $this->id = $id;
        }
    }

    public function __invoke(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->id != '') {
            $probability_value = Prize::where('id', $this->id)->get()->value('probability');
            $updated_value = Prize::where('id', $this->id)->update(['probability' => $value]);
            $current_probability = floatval(Prize::sum('probability'));
            if ($current_probability > 100) {
                $updated_value = Prize::where('id', $this->id)->update(['probability' => $probability_value]);
                $fail('The probability field must not be greater than '.$probability_value);
            }
        } else {
            $current_probability = floatval(Prize::sum('probability'));
            $remain_probability = (100 - $current_probability);
            if ($value > $remain_probability) {
                $fail('The probability field must not be greater than '.$remain_probability);
            }            
        }
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
