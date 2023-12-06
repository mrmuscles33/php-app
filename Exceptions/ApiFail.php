<?php

class ApiFail extends Exception
{
    public const CODE = 5;

    public function __construct()
    {
        $this->message = "Not a valid API call.";
        parent::__construct($this->message, ApiFail::CODE);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>