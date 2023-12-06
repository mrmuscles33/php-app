<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Max-Age:3600');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include_once($root . '/php-app/autoLoad.php');

DbManager::register('base1', new Db("root", "root", "localhost", "dofus"));
DbManager::setMainConnection(DbManager::getConnection('base1'));

if (!isset($_POST) || empty($_POST)) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}

Contexte::init();
if (isset($_POST['servlet']) || isset($_GET['servlet'])) {
    ServletManager::request();
} elseif (isset($_GET['REQUEST_API'])) {
    ApiManager::request();
}

?>