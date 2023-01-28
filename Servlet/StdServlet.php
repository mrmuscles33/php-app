<?php

class StdServlet implements JsonSerializable
{
    private Map $datas;
    private string $error;
    private string $succes;
    private string $warning;

    public function __construct()
    {
        $this->datas = new Map();
        $this->error = "";
        $this->succes = "";
        $this->warning = "";
    }

    public function send(): Map
    {
        $output = new Map();
        $output->put('datas', $this->datas);
        $output->put('error', $this->error);
        $output->put('succes', $this->succes);
        $output->put('warning', $this->warning);
        return $output;
    }

    /**
     * Set a data to return to the screen
     * @param string $key
     * @param mixed|object $value
     * @return void
     */
    final public function setData(string $key, $value): void
    {
        $this->datas->put($key, $value);
    }

    /**
     * Set an error message
     * @param string $msg, message
     * @return void
     */
    final public function setError(string $msg): void
    {
        $this->error = $msg;
    }

    /**
     * Set a warning message
     * @param string $msg, message
     * @return void
     */
    final public function setWarning(string $msg): void
    {
        $this->warning = $msg;
    }

    /**
     * Set a succes message
     * @param string $msg, message
     * @return void
     */
    final public function setSucces(string $msg): void
    {
        $this->succes = $msg;
    }

    /**
     * Return datas to the screen
     * @return Map
     */
    public function getDatas(): Map
    {
        return $this->datas;
    }

    /**
     * Return error message
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Return succes message
     * @return string
     */
    public function getSucces(): string
    {
        return $this->succes;
    }

    /**
     * Return warning message
     * @return string
     */
    public function getWarning(): string
    {
        return $this->warning;
    }

    public function __toString()
    {
        return json_encode($this);
    }

    public function jsonSerialize(): array
    {
        return (array) get_object_vars($this);
    }

}
?>