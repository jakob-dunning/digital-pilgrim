<?php

declare(strict_types = 1);
namespace App\ValueObject\StorageItem;

interface StorageItem
{

    public function getKey(): string;

    public function __toString(): string;
}
