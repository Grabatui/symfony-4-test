<?php

namespace App\Security;

class TokenGenerator
{
    private const LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';

    public function getRandomSecureToken(int $length): string
    {
        $maxLength = strlen(self::LETTERS);

        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= self::LETTERS[mt_rand(0, $maxLength - 1)];
        }

        return $token;
    }
}
