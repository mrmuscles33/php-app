<?php

class ServletManager
{

    public static function request(): void
    {
        header('Content-Type: application/json');
        $servletName = isset($_POST['servlet']) ? $_POST['servlet'] : $_GET['servlet'];
        $method = isset($_POST['method']) ? $_POST['method'] : $_GET['method'];

        if (isset($servletName) && isset($method)) {
            $servletUrl = self::getServlet($servletName);
            $fileName = realpath($_SERVER["DOCUMENT_ROOT"]) . '/' . $servletUrl . '.php';
            if (isset($servletUrl) && file_exists($fileName)) {
                self::includeServlet($fileName, $servletName, $method);
            } else {
                throw new ServletFail(isset($servletUrl) ? $fileName : $servletName);
            }
        }
    }

    private static function includeServlet(string $fileName, string $servletName, string $method): void
    {
        include_once($fileName);
        $servlet = new $servletName();
        if (
            Collection::toList(get_class_methods($servlet))->contains($method)
            && is_callable([$servlet, $method])
            && is_subclass_of($servlet, 'StdServlet')
        ) {
            try {
                $servlet->$method(self::getParams());
            } catch (Throwable $th) {
                $servlet->setError($th->__toString());
            }
            echo $servlet->send();
        } else {
            throw new ServletMethodFail($servletName, $method);
        }
    }

    private static function getServlet(string|null $servletName): string|null
    {
        if (Contexte::getServlets()->containsKey($servletName)) {
            return Contexte::getServlets()->get($servletName);
        } else {
            $url = DbManager::getMainConnection()->loadValue(
                'SELECT URL FROM SYS_SERVLET WHERE NAME = ?',
                Collection::toList($servletName)
            );
            if ($url != null) {
                Contexte::setServlet($servletName, $url);
            }
            return $url;
        }
    }

    private static function getParams(): Map
    {
        $params = new Map();
        if (isset($_POST['params'])) {
            $params->putAll($_POST['params']);
        } elseif (isset($_GET['params'])) {
            $params->putAll(json_decode($_GET['params'], true));
        }
        return $params;
    }
}

?>