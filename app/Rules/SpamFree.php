<?php

namespace App\Rules;

use App\AI\Assistant;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SpamFree implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = (new Assistant())
            ->systemMessage('You are a forum moderator who always response JSON.')
            ->send(<<<EOT
                        Pleas inspect the following text determine if it is spam.

                        {$value}

                        Expected Response Example:

                        {"is_spam":true|false}
                    EOT
            );

        if (json_decode($response)?->is_spam) {
            $fail('Spam was detected.');
        }
    }
}
