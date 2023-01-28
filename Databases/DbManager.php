<?php

class DbManager
{
    private static ?Db $mainDb = null;
    private static ?Map $allDb = null;

    /**
     * Return a connection with id or main connection
     *
     * @param string $id
     * @return Db
     */
    public static function getConnection(string $id): Db
    {
        $db = self::$allDb->containsKey($id) ? self::$allDb->get($id) : self::getMainConnection();
        $db->connect();
        return $db;
    }

    /**
     * Register new connection
     *
     * @param string $id
     * @param Db $db
     * @return void
     */
    public static function register(string $id, Db $db): void
    {
        if (self::$allDb == null) {
            self::$allDb = new Map('Db');
        }
        self::$allDb->put($id, $db);
    }

    /**
     * Set main connection
     *
     * @param Db $db
     * @return void
     */
    public static function setMainConnection(Db $db): void
    {
        self::$mainDb = $db;
    }

    /**
     * Get main connection
     *
     * @return Db
     */
    public static function getMainConnection(): Db
    {
        self::$mainDb->connect();
        return self::$mainDb;
    }

    /**
     * Undocumented function
     *
     * @param string $id
     * @return boolean
     */
    public static function connectionExists(string $id): bool
    {
        return self::$allDb->containsKey($id);
    }
}

?>