<?php

class ClassGenerator
{
    private const AND = ' AND ';
    private const THIS = '$this->';
    private const DEFAUT_PARAMS = 'string $sql = null, Collection $params = new Collection()';

    private Table $table;

    public function __construct(string $tableName)
    {
        $this->table = new Table($tableName);
    }

    /**
     * Get generated code's class
     *
     * @return string
     */
    public function generate(): string
    {
        $class = '<?php' . PHP_EOL . PHP_EOL;

        $class .= PHP_EOL;

        $class .= 'class ' . ucfirst($this->table->getTable());
        $class .= ' implements JsonSerializable, DbMapper {' . PHP_EOL . PHP_EOL;
        $class .= '    // FIELDS' . PHP_EOL;
        $class .= $this->getFields() . PHP_EOL;

        $class .= '    // CONSTRUCTOR' . PHP_EOL;
        $class .= '    public function __construct(){}' . PHP_EOL . PHP_EOL;

        $class .= $this->getLoad() . PHP_EOL;
        $class .= $this->getInsert() . PHP_EOL;
        $class .= $this->getDelete() . PHP_EOL;
        $class .= $this->getUpdate() . PHP_EOL;
        $class .= $this->getExists() . PHP_EOL;
        $class .= $this->getFromMap() . PHP_EOL;

        $class .= '    // GETTERS' . PHP_EOL;
        $class .= $this->getGetters() . PHP_EOL;

        $class .= '    // SETTERS' . PHP_EOL;
        $class .= $this->getSetters() . PHP_EOL;
        $class .= $this->getToString();
        $class .= '}' . PHP_EOL . PHP_EOL;
        $class .= '?>';
        return $class;
    }

    /**
     * Get fromMap function code
     *
     * @return string
     */
    private function getFromMap(): string
    {
        $class = '    public function fromMap(Map $datas) : void {' . PHP_EOL;
        $me = $this;
        $this->table->getColumns()->forEach(function (Column $column) use (&$class, $me) {
            $setter = 'set' . ucfirst($me->toCamelCase($column->getName()));
            $class .= '        if($datas->containsKey(\'' . $column->getName() . '\')) { ';
            $class .= '$this->' . $setter . '($datas->get(\'' . $column->getName() . '\')); }' . PHP_EOL;
        });
        $class .= '    } ' . PHP_EOL;
        return $class;
    }

    /**
     * Get getLoad function code
     *
     * @return string
     */
    private function getLoad(): string
    {
        $where = '1 = 1';
        $params = '';
        if (!$this->table->getKeys()->isEmpty()) {
            $where = $this->table->getKeys()->map(function (Column $key) {
                return $key->getName() . ' = ?';
            })->join(ClassGenerator::AND );

            $params = $this->table->getKeys()->map(function (Column $key) {
                return ClassGenerator::THIS . $this->toCamelCase($key->getName());
            })->join();
        }

        $tableName = $this->table->getTable();
        $class = '    public function load(' . ClassGenerator::DEFAUT_PARAMS . ') : void  {' . PHP_EOL;
        $class .= '        if($sql == null){ ' . PHP_EOL;
        $class .= '            $sql = \'SELECT * FROM ' . $tableName . '  WHERE ' . $where . '\';' . PHP_EOL;
        $class .= '            $params =  Collection::toList(' . $params . '); ' . PHP_EOL;
        $class .= '        } ' . PHP_EOL;
        $class .= '        $map = DbManager::getMainConnection()->loadRow($sql, $params);' . PHP_EOL;
        $class .= '        $this->fromMap($map);' . PHP_EOL;
        $class .= '    } ' . PHP_EOL;
        return $class;
    }

    /**
     * Get getInsert function code
     *
     * @return string
     */
    private function getInsert(): string
    {
        $columns = $this->table->getColumns()->map(function (Column $col) {
            return $col->getName();
        }, 'string')->join();
        $values = $this->table->getColumns()->map(function (Column $col) {
            return $col != null ? '?' : '';
        }, 'string')->join();
        $me = $this;
        $params = $this->table->getColumns()->map(function (Column $col) use ($me) {
            return ClassGenerator::THIS . lcfirst($me->toCamelCase($col->getName()));
        }, 'string')->join();

        $tableName = $this->table->getTable();
        $class = '    public function insert(' . ClassGenerator::DEFAUT_PARAMS . ') : void  {' . PHP_EOL;
        $class .= '        if($sql == null){ ' . PHP_EOL;
        $class .= '            $sql = \'INSERT INTO ' . $tableName . ' (' . $columns . ')';
        $class .= '                     VALUES (' . $values . ')\';' . PHP_EOL;
        $class .= '            $params = Collection::toList( ' . $params . ');' . PHP_EOL;
        $class .= '        }  ' . PHP_EOL;
        $class .= '        DbManager::getMainConnection()->execute($sql, $params); ' . PHP_EOL;
        $class .= '    }  ' . PHP_EOL;
        return $class;
    }

    /**
     * Get getDelete function code
     *
     * @return string
     */
    private function getDelete(): string
    {
        $where = $this->table->getKeys()->map(function (Column $col) {
            return $col->getName() . ' = ?';
        }, 'string')->join(ClassGenerator::AND );
        $me = $this;
        $params = $this->table->getKeys()->map(function (Column $col) use ($me) {
            return ClassGenerator::THIS . lcfirst($me->toCamelCase($col->getName()));
        }, 'string')->join();

        $tableName = $this->table->getTable();
        $class = '    public function delete(' . ClassGenerator::DEFAUT_PARAMS . ') : void { ' . PHP_EOL;
        $class .= '        if($sql == null){  ' . PHP_EOL;
        $class .= '            $sql = \'DELETE FROM ' . $tableName . ' WHERE  ' . $where . '\';' . PHP_EOL;
        $class .= '            $params  = Collection::toList(' . $params . ');' . PHP_EOL;
        $class .= '        }  ' . PHP_EOL;
        $class .= '        DbManager::getMainConnection()->execute($sql, $params);' . PHP_EOL;
        $class .= '    }  ' . PHP_EOL;
        return $class;
    }

    /**
     * Get getUpdate function code
     *
     * @return string
     */
    private function getUpdate(): string
    {
        $columns = $this->table->getColumns()->map(function (Column $col) {
            return $col->getName() . ' = ?';
        }, 'string')->join();
        $me = $this;
        $params = $this->table->getColumns()->map(function (Column $col) use ($me) {
            return ClassGenerator::THIS . lcfirst($me->toCamelCase($col->getName()));
        }, 'string');
        $where = $this->table->getKeys()->map(function (Column $col) {
            return $col->getName() . ' = ?';
        }, 'string')->join(ClassGenerator::AND );
        $params->putAll($this->table->getKeys()->map(function (Column $col) use ($me) {
            return ClassGenerator::THIS . lcfirst($me->toCamelCase($col->getName()));
        }, 'string'));

        $tableName = $this->table->getTable();
        $class = '    public function update(' . ClassGenerator::DEFAUT_PARAMS . ') : void {' . PHP_EOL;
        $class .= '        if($sql == null){' . PHP_EOL;
        $class .= '            $sql = \'UPDATE ' . $tableName;
        $class .= '                     SET ' . $columns . ' WHERE ' . $where . '\';' . PHP_EOL;
        $class .= '            $params = Collection::toList(' . $params->join() . ');' . PHP_EOL;
        $class .= '        }' . PHP_EOL;
        $class .= '        DbManager::getMainConnection()->execute($sql, $params);' . PHP_EOL;
        $class .= '    }   ' . PHP_EOL;
        return $class;
    }

    /**
     * Get getExists function code
     *
     * @return string
     */
    private function getExists(): string
    {
        $where = $this->table->getKeys()->map(function (Column $col) {
            return $col->getName() . ' = ?';
        }, 'string')->join(ClassGenerator::AND );
        $me = $this;
        $params = $this->table->getKeys()->map(function (Column $col) use ($me) {
            return ClassGenerator::THIS . lcfirst($me->toCamelCase($col->getName()));
        }, 'string')->join();

        $tableName = $this->table->getTable();
        $class = '    public function exists(' . ClassGenerator::DEFAUT_PARAMS . ') : bool {' . PHP_EOL;
        $class .= '        if($sql == null){' . PHP_EOL;
        $class .= '            $sql = \'SELECT * FROM ' . $tableName . ' WHERE ' . $where . '\';' . PHP_EOL;
        $class .= '            $params = Collection::toList(' . $params . ');' . PHP_EOL;
        $class .= '        }' . PHP_EOL;
        $class .= '        return DbManager::getMainConnection()->existRow($sql, $params);' . PHP_EOL;
        $class .= '    }   ' . PHP_EOL;
        return $class;
    }

    /**
     * Get all privates fields code
     *
     * @return string
     */
    private function getFields(): string
    {
        $class = '';
        $me = $this;
        $this->table->getColumns()->forEach(function (Column $column) use (&$class, $me) {
            $phpType = $column->isKey() ? '' : '?';
            $phpType .= $me->sqlTypeToPhpType($column->getType());
            $class .= '    private ' . $phpType . ' $' . lcfirst($me->toCamelCase($column->getName())) . ';' . PHP_EOL;
        });
        return $class;
    }

    /**
     * Get all getters code
     *
     * @return string
     */
    private function getGetters(): string
    {
        $class = '';
        $me = $this;
        $this->table->getColumns()->forEach(function (Column $column) use (&$class, $me) {
            $phpType = $column->isKey() ? '' : '?';
            $phpType .= $me->sqlTypeToPhpType($column->getType());
            $getter = 'get' . ucfirst($me->toCamelCase($column->getName()));
            $class .= '     public function ' . $getter . '() : ' . $phpType . ' {';
            $class .= '         return $this->' . $me->toCamelCase($column->getName()) . '; ';
            $class .= '     }' . PHP_EOL;
        });
        return $class;
    }

    /**
     * Get all setters code
     *
     * @return string
     */
    private function getSetters(): string
    {
        $class = '';
        $me = $this;
        $this->table->getColumns()->forEach(function (Column $column) use (&$class, $me) {
            $phpType = $column->isKey() ? '' : '?';
            $phpType .= $me->sqlTypeToPhpType($column->getType());
            $field = $me->toCamelCase($column->getName());
            $setter = 'set' . ucfirst($field);
            $param = 'p' . ucfirst($field);
            $class .= '     public function ' . $setter . '(' . $phpType . ' $' . $param . ') : void {';
            $class .= '         $this->' . $field . ' = $' . $param . '; }' . PHP_EOL;
        });
        return $class;
    }

    /**
     * Get getToString function code
     *
     * @return string
     */
    private function getToString(): string
    {
        $class = '    public function __toString() {' . PHP_EOL;
        $class .= '        return json_encode($this);' . PHP_EOL;
        $class .= '    }' . PHP_EOL . PHP_EOL;
        $class .= '    public function jsonSerialize() : array {' . PHP_EOL;
        $class .= '        return (array) get_object_vars($this);' . PHP_EOL;
        $class .= '    }' . PHP_EOL;
        return $class;
    }

    /**
     * Transform sql type to php type
     *
     * @param string $sqlType
     * @return string
     */
    private function sqlTypeToPhpType(string $sqlType): string
    {
        $phpType = '';
        switch ($sqlType) {
            case 'char':
            case 'varchar':
            case 'text':
            case 'blob':
            case 'date':
            case 'datetime':
                $phpType = 'string';
                break;
            case 'int':
            case 'integer':
            case 'smallint':
            case 'tinyint':
            case 'mediumint':
            case 'bigint':
                $phpType = 'int';
                break;
            default:
                $phpType = '';
                break;
        }
        return $phpType;
    }

    /**
     * Transform column name to field name
     *
     * @param string $property
     * @return string
     */
    private function toCamelCase(string $property): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($property)))));
    }
}

?>