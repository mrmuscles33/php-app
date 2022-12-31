<?php

class Test implements JsonSerializable, DbMapper
{

    // FIELDS
    private int $id;
    private ?string $libelle;
    private ?string $dateCrea;

    // CONSTRUCTOR
    public function __construct()
    {
    }

    public function load(string $sql = null, Collection $params = new Collection()): void
    {
        if ($sql == null) {
            $sql = 'SELECT * FROM test  WHERE ID = ?';
            $params = Collection::toList($this->id);
        }
        $map = DbManager::getMainConnection()->loadRow($sql, $params);
        $this->fromMap($map);
    }

    public function insert(string $sql = null, Collection $params = new Collection()): void
    {
        if ($sql == null) {
            $sql = 'INSERT INTO test (ID,LIBELLE,DATE_CREA) VALUES (?,?,?)';
            $params = Collection::toList($this->id, $this->libelle, $this->dateCrea);
        }
        DbManager::getMainConnection()->execute($sql, $params);
    }

    public function delete(string $sql = null, Collection $params = new Collection()): void
    {
        if ($sql == null) {
            $sql = 'DELETE FROM test WHERE  ID = ?';
            $params = Collection::toList($this->id);
        }
        DbManager::getMainConnection()->execute($sql, $params);
    }

    public function update(string $sql = null, Collection $params = new Collection()): void
    {
        if ($sql == null) {
            $sql = 'UPDATE test SET ID = ?,LIBELLE = ?,DATE_CREA = ? WHERE ID = ?';
            $params = Collection::toList($this->id, $this->libelle, $this->dateCrea, $this->id);
        }
        DbManager::getMainConnection()->execute($sql, $params);
    }

    public function exists(string $sql = null, Collection $params = new Collection()): bool
    {
        if ($sql == null) {
            $sql = 'SELECT * FROM test WHERE ID = ?';
            $params = Collection::toList($this->id);
        }
        return DbManager::getMainConnection()->existRow($sql, $params);
    }

    public function fromMap(Map $datas): void
    {
        if ($datas->containsKey('ID')) {
            $this->setId($datas->get('ID'));
        }
        if ($datas->containsKey('LIBELLE')) {
            $this->setLibelle($datas->get('LIBELLE'));
        }
        if ($datas->containsKey('DATE_CREA')) {
            $this->setDateCrea($datas->get('DATE_CREA'));
        }
    }

    // GETTERS
    public function getId(): int
    {
        return $this->id;
    }
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }
    public function getDateCrea(): ?string
    {
        return $this->dateCrea;
    }

    // SETTERS
    public function setId(int $pId): void
    {
        $this->id = $pId;
    }
    public function setLibelle(?string $pLibelle): void
    {
        $this->libelle = $pLibelle;
    }
    public function setDateCrea(?string $pDateCrea): void
    {
        $this->dateCrea = $pDateCrea;
    }

    public function __toString()
    {
        return json_encode($this);
    }

    public function jsonSerialize(): array
    {
        return (array)get_object_vars($this);
    }
}

?>