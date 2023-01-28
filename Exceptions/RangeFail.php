<?php

class RangeFail extends Exception
{
    public const CODE = 2;

    public function __construct($size, $reach)
    {
        $this->message = "Out of range. Collection size : " . $size . ", attempting to reach " . $reach . ".";
        parent::__construct($this->message, RangeFail::CODE);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>