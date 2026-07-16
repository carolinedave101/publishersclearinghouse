<?php

namespace App\Services;

use Illuminate\Support\Str;

class CodeGenerator
{
    public function generate(int $length = 10): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    }

    public function generateUniqueCode(int $length = 10): string
    {
        do {
            $code = $this->generate($length);
        } while (\App\Models\Winner::where('unique_code', $code)->exists());

        return $code;
    }
}
