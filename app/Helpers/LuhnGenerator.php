<?php 
namespace App\Helpers;

class LuhnGenerator
{
    /**
     * Generate a Luhn-compliant account number.
     */
    public static function generate(int $length = 10): string
    {
        $number = '';

        // Generate random digits excluding the check digit
        for ($i = 0; $i < $length - 1; $i++) {
            $number .= rand(0, 9);
        }

        // Compute the check digit and append it
        $checkDigit = self::calculateLuhnCheckDigit($number);
        return $number . $checkDigit;
    }

    /**
     * Calculate the Luhn check digit.
     */
    private static function calculateLuhnCheckDigit(string $number): int
    {
        $digits = array_reverse(str_split($number)); // Reverse the array for Luhn processing
        $sum = 0;

        foreach ($digits as $index => $digit) {
            $digit = (int) $digit;
            if ($index % 2 == 0) { // Double every second digit
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit;
    }
}
