<?php

/**
 * Automatically load classes
 *
 * @param string $dir
 * @param string $class
 * @return void
 */
function autoLoad(string $dir, string $class): void
{
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    $fileName = $root . '/php-app/' . $dir . '/' . $class . '.php';
    if (file_exists($fileName)) {
        include_once($fileName);
    }
}

/**
 * Automatically load classes in Databases folder
 *
 * @param string $class
 * @return void
 */
function autoLoadDatabases(string $class): void
{
    autoLoad('Databases', $class);
}

/**
 * Automatically load classes in DataStructures folder
 *
 * @param string $class
 * @return void
 */
function autoLoadDataStructures(string $class): void
{
    autoLoad('DataStructures', $class);
}

/**
 * Automatically load classes in Exceptions folder
 *
 * @param string $class
 * @return void
 */
function autoLoadExceptions(string $class): void
{
    autoLoad('Exceptions', $class);
}

/**
 * Automatically load classes in Servlet folder
 *
 * @param string $class
 * @return void
 */
function autoLoadServlet(string $class): void
{
    autoLoad('Servlet', $class);
}

spl_autoload_register('autoLoadDatabases');
spl_autoload_register('autoLoadDataStructures');
spl_autoload_register('autoLoadExceptions');
spl_autoload_register('autoLoadServlet');

?>