<?php
namespace App\Controllers;


abstract class BaseController
{
    private $_params;

    public function __construct()
    {
        $json = file_get_contents('php://input');
        $this->_params = json_decode($json, true);
    }

    /**
     * JSON output
     *
     * @param array $data
     */
    public function json(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function jsonError(string $message)
    {
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }

    /**
     * Return param by name
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParam(string $name, $default = null)
    {
        if (!array_key_exists($name, $this->_params)) {
            return $default;
        }

        return $this->_params[$name];
    }

    /**
     * Return params
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->_params;
    }
}