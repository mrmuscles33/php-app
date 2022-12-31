<?php

class Map implements JsonSerializable
{
    private array $datas = [];
    private string $type = '';

    /**
     * Constructor
     * @param string $type
     * @return void
     */
    public function __construct(string $type = '')
    {
        $this->datas = [];
        $this->type = $type;
    }

    /**
     * Destructor
     * @return void
     */
    public function __destruct()
    {
        $this->datas = [];
        $this->type = '';
    }

    /**
     * Return string for echo or printf method
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->datas);
    }

    /**
     * Convert to JSON
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->datas;
    }

    /**
     * Put or update value with key
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function put(string $key, mixed $value): void
    {
        if (!is_string($key)) {
            throw new ArgumentFail("string", gettype($key));
        }
        $givenType = gettype($value) == 'object' ? get_class($value) : gettype($value);
        if (!is_null($value) && $this->type != '' && $givenType != $this->type) {
            throw new ArgumentFail($this->type, $givenType);
        }
        $this->datas[$key] = $value;
    }

    /**
     * Put value with key in the map if it is not existing
     * @param string $key
     * @param object value
     * @return void
     */
    public function putIfAbsent(string $key, $value): void
    {
        $givenType = gettype($value) == 'object' ? get_class($value) : gettype($value);
        if (!is_null($value) && $this->type != '' && $givenType != $this->type) {
            throw new ArgumentFail($this->type, $givenType);
        }
        if (!$this->containsKey($key)) {
            $this->put($key, $value);
        }
    }

    /**
     * Put or update all values with keys
     * @param Map/associative_array $map
     * @return void
     */
    public function putAll(Map|array $map): void
    {
        if ($map instanceof Map) {
            if ($this->type != $map->type && $this->type != '') {
                throw new ArgumentFail($this->type, $map->type);
            }
            $this->datas = array_merge($this->datas, $map->getDatas());
        } elseif (is_array($map)) {
            foreach ($map as $key => $value) {
                $this->put($key, $value);
            }
        } else {
            $givenType = gettype($map) == 'object' ? get_class((object) $map) : gettype($map);
            throw new ArgumentFail('Map|array', $givenType);
        }
    }

    /**
     * Return if map contains key
     * @param string $key
     * @return boolean
     */
    public function containsKey(string $key): bool
    {
        if (!is_string($key)) {
            $givenType = gettype($key) == 'object' ? get_class((object) $key) : gettype($key);
            throw new ArgumentFail('string', $givenType);
        }
        return array_key_exists($key, $this->datas);
    }

    /**
     * Get a value with key
     * @param string $key
     * @return mixed|object|null
     */
    public function get(string $key)
    {
        if (!is_string($key)) {
            $givenType = gettype($key) == 'object' ? get_class((object) $key) : gettype($key);
            throw new ArgumentFail('string', $givenType);
        }
        if ($this->containsKey($key)) {
            return $this->datas[$key];
        }
        return null;
    }

    /**
     * Get all keys & values
     * @return array
     */
    public function getDatas(): array
    {
        return $this->datas;
    }

    /**
     * Clear all keys & values
     * @return void
     */
    public function clear(): void
    {
        $this->datas = [];
    }

    /**
     * Return number of values
     * @return int
     */
    public function size(): int
    {
        return count($this->datas);
    }

    /**
     * Return if the collection is empty
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return $this->size() == 0;
    }

    /**
     * Return all keys
     * @return Collection
     */
    public function keys(): Collection
    {
        $list = new Collection('string');
        $list->putAll(array_keys($this->datas));
        return $list;
    }

    /**
     * Return all values
     * @return Collection
     */
    public function values(): Collection
    {
        $list = new Collection($this->type);
        $list->putAll(array_values($this->datas));
        return $list;
    }

    /**
     * Remove a value with key
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        if (!is_string($key)) {
            $givenType = gettype($key) == 'object' ? get_class((object) $key) : gettype($key);
            throw new ArgumentFail('string', $givenType);
        }
        unset($this->datas[$key]);
    }
}

?>