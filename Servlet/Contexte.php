<?php

class Contexte
{

    private static Map $servlets;
    private static Map $datas;

    public static function init(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        self::$servlets = self::loadServlets();
        self::$datas = self::loadDatas();
    }

    /**
     * Load servlets in context from sessions variables
     *
     * @return Map
     */
    private static function loadServlets(): Map
    {
        $map = new Map('string');
        if (isset($_SESSION['servlets'])) {
            $arrServlets = $_SESSION['servlets'];
            $map->putAll(unserialize($arrServlets));
        }
        return $map;
    }

    /**
     * Get servlets in context
     * @return Map
     */
    public static function getServlets(): Map
    {
        return self::$servlets;
    }

    /**
     * Save a servlet in context
     * @param string $name
     * @param string $url
     * @return void
     */
    public static function setServlet(string $name, string $url): void
    {
        self::$servlets->put($name, $url);
        $_SESSION['servlets'] = serialize(self::$servlets->getDatas());
    }

    /**
     * Load datas in context from sessions variables
     *
     * @return Map
     */
    private static function loadDatas(): Map
    {
        $map = new Map();
        if (isset($_SESSION['datas'])) {
            $arrServlets = $_SESSION['datas'];
            $map->putAll(unserialize($arrServlets));
        }
        return $map;
    }

    /**
     * Get saved datas in context
     * @return mixed|object|null
     */
    public static function getData($key)
    {
        return self::$datas->get($key);
    }

    /**
     * Save a data in context
     * @param string $name
     * @param string $url
     * @return void
     */
    public static function setData(string $key, mixed $value): void
    {
        self::$datas->put($key, $value);
        $_SESSION['datas'] = serialize(self::$datas->getDatas());
    }
}

?>