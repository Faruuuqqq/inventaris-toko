<?php

namespace App\Exceptions;

use Exception;

class InvalidTransactionException extends Exception
{
    protected $code = 400;
    
    public function __construct($message = "Transaksi tidak valid", Exception $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }
}
