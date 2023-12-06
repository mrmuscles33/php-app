<?php

class ApiManager
{

    public static function request(): void
    {
        $route = explode('?', $_GET['REQUEST_API'])[0];
        $query = parse_url($_GET['REQUEST_API'], PHP_URL_QUERY);
        $params = array();
        parse_str($query, $params);
        $mapParamas = new Map();
        $mapParamas->putAll($params);
        if (isset($route)) {
            $api = self::getAPI($route);
            $fileName = realpath($_SERVER["DOCUMENT_ROOT"]) . '/' . 
                        ($api != null &&  !$api->isEmpty() ? $api->get('url') : '404') . '.php';
            if ($api != null &&  !$api->isEmpty() && file_exists($fileName)) {
                $class = self::includeAPI($fileName, $api);
                $class->request($mapParamas);
            } else {
                throw new APIFail();
            }
        }
    }

    private static function getAPI(string|null $route): Map|null
    {
        if (Contexte::getAPIs()->containsKey($route)) {
            return Contexte::getAPIs()->get($route);
        } else {
            $api = DbManager::getMainConnection()->loadRow(
                'SELECT * FROM SYS_API WHERE NAME = ?',
                Collection::toList($route)
            );
            if ($api != null && !$api->isEmpty()) {
                Contexte::setAPI($api->get('name'), $api->get('url'));
            }
            return $api;
        }
    }

    private static function includeAPI(string $fileName, Map $api): StdAPI
    {
        include_once($fileName);
        $class = $api->get('name');
        $apiClass = new $class();
        if (is_subclass_of($apiClass, 'StdAPI')) {
            return $apiClass;
        } else {
            throw new APIFail();
        }
    }
}

?>