<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 5/16/2017
 * Time: 5:52 AM
 */

namespace Tops\services;


use Tops\sys\TStrings;

class DefaultInputHandler extends ServiceRequestInputHandler
{

    /**
     * @return 'POST' | 'GET'
     */
    protected function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return mixed
     */
    public function get($key)
    {
        if ($this->getMethod() == 'POST') {
            return $_POST[$key];
        }
        return $_GET[$key];
    }

    public function getSecurityToken()
    {
        $result = empty($_GET[ServiceRequestInputHandler::securityTokenKey]) ? null
            : $_GET[ServiceRequestInputHandler::securityTokenKey];
        if (empty($result)) {
            $result = empty($_POST[ServiceRequestInputHandler::securityTokenKey]) ? null
                : $_POST[ServiceRequestInputHandler::securityTokenKey];

        }
        return $result;
    }

}