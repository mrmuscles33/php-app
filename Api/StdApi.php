<?php

abstract class StdApi {

    private Map $params = null;

    public function request(Map $pParams) : void {
        $result = '';
        $this->params = $pParams;
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $result = $this->get();
                break;
            case 'POST':
                $result = $this->post();
                break;
            case 'PUT':
                $result = $this->put();
                break;
            case 'DELETE':
                $result = $this->delete();
                break;
            default:
                break;
        }
        echo json_encode($result);
    }

    abstract protected function get() : mixed;

    abstract protected function post() : mixed;

    abstract protected function put() : mixed;

    abstract protected function delete() : mixed;
}

?>