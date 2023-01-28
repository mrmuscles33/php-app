<?php

class Table implements JsonSerializable
{
    private string $table;
    private Collection $keys;
    private Collection $columns;

    public function __construct(string $table)
    {
        $this->table = $table;
        $this->columns = new Collection('Column');
        $this->keys = new Collection('Column');
        $this->init();
    }

    /**
     * Load columns and keys
     *
     * @return void
     */
    private function init(): void
    {
        $this->loadColumns();
        $this->loadKeys();
    }

    /**
     * Load columns
     *
     * @return void
     */
    private function loadColumns(): void
    {
        $colsResult = DbManager::getMainConnection()->load("SHOW COLUMNS FROM " . $this->table, Collection::toList());
        $me = $this;
        $me->columns->putAll($colsResult->map(function ($colResult) use (&$me) {
            $splittedType = Collection::toList(preg_split('/[\(,\)]/', $colResult->get('Type')));
            $col = new Column(
                    $me->table,
                $colResult->get('Field')
            );
            $col->setType((string) $splittedType->get(0));
            $col->setSize($splittedType->size() > 1 ? intval($splittedType->get(1)) : 0);
            $col->setPrecision(
                'float' == $colResult->get('Type') && $splittedType->size() > 2 ? 
                intval($splittedType->get(2)) :
                null
            );
            $col->setDefault($colResult->get('Default'));
            $col->setNullable($colResult->get('Null') == 'YES');
            $col->setKey($colResult->get('Key') == 'PRI');
            return $col;
        }, 'Column'));
    }

    /**
     * Load keys
     *
     * @return void
     */
    private function loadKeys(): void
    {
        $this->keys->putAll($this->columns->filter(function ($col) {
            return $col->isKey();
        }));
    }

    /**
     * Return string for echo or printf method
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this);
    }

    /**
     * Convert to JSON
     * @return array
     */
    public function jsonSerialize(): array
    {
        return (array) get_object_vars($this);
    }
    /**
     * Get table name
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }
    /**
     * Get all columns
     * 
     * @return Collection
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * Get columns keys
     * 
     * @return Collection
     */
    public function getKeys(): Collection
    {
        return $this->keys;
    }
}

?>