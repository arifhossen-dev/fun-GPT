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
        $assistant = new Assistant();

        $assistant->systemMessage('You are a forum moderator who always response JSON.');

        $message = <<<EOT
                        Pleas inspect the following text determine if it is spam.

                        {$value}

                        Expected Response Example:

                        {"is_spam":true|false}
                    EOT;

        $response = $assistant->send($message);

        $response = json_decode($response);

        if ($response->is_spam) {
            $fail('Spam was detected.');
        }
    }
}
