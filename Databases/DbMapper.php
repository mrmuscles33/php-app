<?php

interface DbMapper {
    public function load(string $sql = null, Collection $params = new Collection()) : void;

    public function insert(string $sql = null, Collection $params = new Collection()) : void;

    public function delete(string $sql = null, Collection $params = new Collection()) : void;

    public function update(string $sql = null, Collection $params = new Collection()) : void;

    public function exists(string $sql = null, Collection $params = new Collection()) : bool;

    public function fromMap(Map $datas) : void;
}

?>