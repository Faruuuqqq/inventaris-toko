<?php

namespace App\Exceptions;

use Exception;

class CreditLimitExceededException extends Exception
{
    protected $code = 400;
    
    public function __construct($message = "Batas kredit customer telah terlampaui", Exception $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }
}
