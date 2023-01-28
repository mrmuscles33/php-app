<?php

class ServletFail extends Exception
{
    public const CODE = 3;

    public function __construct(string $servlet)
    {
        $this->message = "Unknown Servlet : " . $servlet . ".";
        parent::__construct($this->message, ServletFail::CODE);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>