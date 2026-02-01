<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $code = 400;
    
    public function __construct($message = "Stok tidak cukup untuk produk ini", Exception $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }
}
