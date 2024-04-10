<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class EndsWithRule implements ValidationRule
{
    private string $suffix;

    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Str::endsWith($value, $this->suffix)) {
            $fail('The :attribute must be ends with ' . $this->suffix . '.');
        }
    }
}
