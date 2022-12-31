<?php

class ServletMethodFail extends Exception
{
    public const CODE = 4;

    public function __construct(string $servlet, string $method)
    {
        $this->message = "Unknown method " . $servlet . "::" . $method . ".";
        parent::__construct($this->message, ServletMethodFail::CODE);
    }

    public function __toString() : string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>