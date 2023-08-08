<?php

namespace App\Exceptions;

class FixChapterException extends \Exception
{
    public $context;

    public function __construct($code = 0, $message = '', $context = [])
    {
        parent::__construct($message, $code);

        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
