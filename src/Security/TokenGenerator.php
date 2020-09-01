<?php

namespace App\Security;

use Exception;

class TokenGenerator
{

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    public function getRandomToken(int $length = 30): string
    {
        $token='';
        $max = strlen(self::ALPHABET);

        for ($i = 0; $i < $length; $i++){
            try {
                $token .= self::ALPHABET[random_int(0, $max - 1)];
            } catch (Exception $e) {
                $e->getMessage();
            }
        }

        return $token;
    }
}