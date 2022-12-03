<?php

header('Content-Type: application/json');
header('Content-Type: text/html; charset=utf-8');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include_once($root . '/Framocaer/autoLoad.php');
include_once($root . '/Framocaer/test_table.php');

DbManager::register('base1', new Db("root", "root", "localhost", "dofus"));
DbManager::setMainConnection(DbManager::getConnection('base1'));

// $generator = new ClassGenerator('test');
// highlight_string($generator->generate());

$list = DbManager::getMainConnection()->load('SELECT * FROM test', new Collection(), 'Test');
echo $list;
?>