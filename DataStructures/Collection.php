<?php

class Collection implements JsonSerializable
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
     * @return mixed
     */
    public function jsonSerialize(): array
    {
        return $this->datas;
    }

    /**
     * Put value
     * @param object value
     * @return void
     */
    public function put($value): void
    {
        $givenType = gettype($value) == 'object' ? get_class($value) : gettype($value);
        if (!is_null($value) && $this->type != '' && $givenType != $this->type) {
            throw new ArgumentFail($this->type, $givenType);
        }
        $this->datas[] = $value;
    }

    /**
     * Put value in the collection if it is not existing
     * @param object value
     * @return void
     */
    public function putIfAbsent($value): void
    {
        $givenType = gettype($value) == 'object' ? get_class($value) : gettype($value);
        if (!is_null($value) && $this->type != '' && $givenType != $this->type) {
            throw new ArgumentFail($this->type, $givenType);
        }
        if (!$this->contains($value)) {
            $this->put($value);
        }
    }

    /**
     * Put all values
     * @param Collection|array value
     * @return void
     */
    public function putAll(Collection|array $collection): void
    {
        if ($collection instanceof Collection) {
            if ($this->type != $collection->type && $this->type != '') {
                throw new ArgumentFail($this->type, $collection->type);
            }
            $this->datas = array_merge($this->datas, $collection->getDatas());
        } elseif (is_array($collection)) {
            foreach ($collection as $value) {
                $this->put($value);
            }
        } else {
            $givenType = gettype($collection) == 'object' ? get_class((object) $collection) : gettype($collection);
            throw new ArgumentFail('Collection|array', $givenType);
        }
    }

    /**
     * Return if collection contains value
     * @param mixed value
     * @return boolean
     */
    public function contains(mixed $value): bool
    {
        $givenType = gettype($value) == 'object' ? get_class($value) : gettype($value);
        if ($givenType != $this->type && $this->type != '') {
            throw new ArgumentFail($this->type, $givenType);
        }
        return in_array($value, $this->datas);
    }

    /**
     * Get a value with index
     * @param int $idx
     * @return object
     */
    public function get(int $idx)
    {
        if (!is_integer($idx)) {
            $givenType = gettype($idx) == 'object' ? get_class((object) $idx) : gettype($idx);
            throw new ArgumentFail('int', $givenType);
        }
        if ($idx < $this->size()) {
            return $this->datas[$idx];
        } else {
            throw new RangeFail($this->size(), $idx);
        }
    }

    /**
     * Get all values
     * @return array
     */
    public function getDatas(): array
    {
        return $this->datas;
    }

    /**
     * Clear all values
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
     * Remove a value with index
     * @param int $idx
     * @return void
     */
    public function remove(int $idx): void
    {
        if (!is_integer($idx)) {
            $givenType = gettype($idx) == 'object' ? get_class((object) $idx) : gettype($idx);
            throw new ArgumentFail('int', $givenType);
        }
        if ($idx < $this->size()) {
            unset($this->datas[$idx]);
            $this->datas = array_values($this->datas);
        } else {
            throw new RangeFail($this->size(), $idx);
        }
    }

    /**
     * Convert variables list in Collection
     *
     * @param mixed ...$values
     * @return Collection $list
     */
    public static function toList(mixed...$values): Collection
    {
        $list = new Collection('');
        $arr = [];
        foreach ($values as $value) {
            $arr[] = (array) $value;
        }
        $list->putAll(array_merge(...$arr));
        return $list;
    }

    /**
     * Transform each value in another and return the result
     *
     * @param callable $fct
     * @param string $type
     * @return Collection
     */
    public function map(callable $fct, string $type = ''): Collection
    {
        $retour = clone $this;
        $retour->type = $type;
        for ($i = 0; $i < $retour->size(); $i++) {
            $retour->datas[$i] = $fct($retour->get($i));
        }
        return $retour;
    }

    /**
     * Apply filter and return the result
     *
     * @param callable $fct
     * @return Collection
     */
    public function filter(callable $fct): Collection
    {
        $retour = clone $this;
        $retour->clear();
        for ($i = 0; $i < $this->size(); $i++) {
            if ($fct($this->get($i))) {
                $retour->put($this->get($i));
            }
        }
        return $retour;
    }

    /**
     * Execute a callable on each values
     *
     * @param callable $fct
     * @return void
     */
    public function forEach (callable $fct): void
    {
        for ($i = 0; $i < $this->size(); $i++) {
            $fct($this->datas[$i]);
        }
    }

    /**
     * Aggregate all values separated by a separator (default : ',')
     * 
     * @param string $separator
     * @return string
     */
    public function join(string $separator = ','): string
    {
        $retour = '';
        for ($i = 0; $i < $this->size(); $i++) {
            if ($i > 0) {
                $retour .= $separator;
            }
            $retour .= $this->datas[$i];
        }
        return $retour;
    }
}

?>