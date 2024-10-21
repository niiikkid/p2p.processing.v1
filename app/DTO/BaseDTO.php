<?php

namespace App\DTO;

abstract class BaseDTO
{
    public static function make(array $data): static
    {
        return make(static::class, $data);
    }
}
