<?php
class ArgumentFail extends Exception
{
    public const CODE = 1;

    public function __construct($expected, $given)
    {
        $this->message = 'This is not a valid argument. ' . $expected . ' expected, ' . $given . ' given.';
        parent::__construct($this->message, ArgumentFail::CODE);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>