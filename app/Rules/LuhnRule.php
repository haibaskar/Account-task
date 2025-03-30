<?php 
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LuhnRule implements Rule
{
    public function passes($attribute, $value)
    {
        return $this->isValidLuhn($value);
    }

    public function message()
    {
        return 'The :attribute is not a valid Luhn-compliant number.';
    }

    private function isValidLuhn($number)
    {
        $digits = array_reverse(str_split($number));
        $sum = 0;

        foreach ($digits as $index => $digit) {
            $digit = (int) $digit;
            if ($index % 2 == 1) { // Double every second digit from the right
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }
}
