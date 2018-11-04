<?php
namespace App\ValueObject\StorageItem;

interface StorageItem
{
    public function getKey() : string;
    
    public function __toString() : string;
}
