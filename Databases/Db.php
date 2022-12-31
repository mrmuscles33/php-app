<?php

class Db
{

    private ?PDO $cnx = null;
    private ?string $user = null;
    private ?string $password = null;
    private ?string $url = null;
    private ?string $base = null;

    /**
     * Init db credencials
     * @param string $user
     * @param string $password 
     * @param string $url 
     * @param string $base 
     * @return void
     */
    public function __construct(string $user, string $password, string $url, string $base)
    {
        $this->user = $user;
        $this->password = $password;
        $this->url = $url;
        $this->base = $base;
    }

    /**
     * Destruct the connection
     * @return void
     */
    public function __destruct()
    {
        $this->user = null;
        $this->password = null;
        $this->url = null;
        $this->base = null;
        $this->cnx = null;
    }

    /**
     * Init db connection
     * @return void
     */
    public function connect()
    {
        if ($this->cnx == null) {
            try {
                $this->cnx = new PDO(
                    'mysql:host=' . $this->url . ';dbname=' . $this->base . ';charset=utf8',
                    $this->user,
                    $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                    );
            }
            catch (Exception $e) {
                die('Db.connect : ' . $e->getMessage());
            }
        }
    }

    /**
     * Close the connection
     * @return void
     */
    public function close()
    {
        $this->cnx = null;
        unset($this->cnx);
    }

    /**
     * Execute query without return datas
     * @param string $query
     * @param Collection $params
     * @return void
     */
    public function execute(string $query, Collection $params)
    {
        $this->cnx->prepare($query)->execute($params->getDatas());
    }

    /**
     * Load multiple row & columns
     * @param string $query
     * @param Collection $params
     * @return Collection
     */
    public function load(string $query, Collection $params, string $type = null): Collection
    {
        $stmt = $this->cnx->prepare($query);
        $stmt->execute($params->getDatas());
        $list = new Collection($type != null && is_subclass_of($type, 'DbMapper') ? $type : 'Map');
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map = new Map();
            $map->putAll($row);
            if ($type != null && is_subclass_of($type, 'DbMapper')) {
                $obj = new $type();
                $obj->fromMap($map);
                $list->put($obj);
            }
            else {
                $list->put($map);
            }
        }
        return $list;
    }

    /**
     * Load unique row & multiple columns
     * @param string $query
     * @param Collection $params
     * @return Map|object
     */
    public function loadRow(string $query, Collection $params, string $type = null): object
    {
        $list = $this->load($query, $params, $type);
        if (!$list->isEmpty()) {
            return $list->get(0);
        }
        return new Map();
    }

    /**
     * Load multiple row & unique columns
     * @param string $query
     * @param Collection $params
     * @return Collection
     */
    public function loadColumn(string $query, Collection $params): Collection
    {
        $list = $this->load($query, $params);
        $return = new Collection();
        if (!$list->isEmpty()) {
            $column = $list->get(0)->keys()->get(0);
            // TODO use foreach on list
            for ($i = 0; $i < $list->size(); $i++) {
                $return->put($list->get($i)->get($column));
            }
        }
        return $return;
    }

    /**
     * Load unique value
     * @param string $query
     * @param Collection $params
     * @return mixed
     */
    public function loadValue(string $query, Collection $params): mixed
    {
        $list = $this->load($query, $params);
        if (!$list->isEmpty()) {
            $column = $list->get(0)->keys()->get(0);
            return $list->get(0)->get($column);
        }
        return null;
    }

    /**
     * Return row number in dataset
     * @param string $query
     * @param Collection $params
     * @return int
     */
    public function countRow(string $query, Collection $params): int
    {
        return $this->load($query, $params)->size();
    }

    /**
     * Return if a row exist
     * @param string $query
     * @param Collection $params
     * @return boolean
     */
    public function existRow(string $query, Collection $params): bool
    {
        return !$this->load($query, $params)->isEmpty();
    }

    /**
     * Return database name / schema
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }
}

?>