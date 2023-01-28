<?php

header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include_once($root . '/php-app/autoLoad.php');

DbManager::register('base1', new Db("root", "root", "localhost", "dofus"));
DbManager::setMainConnection(DbManager::getConnection('base1'));

if (!isset($_POST) || empty($_POST)) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}

Contexte::init();
ServletManager::request();
?>