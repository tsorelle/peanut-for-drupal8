<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 5/16/2017
 * Time: 5:58 AM
 */

namespace Tops\drupal8;

// Uncomment for Symfony or Drupal 8
use Symfony\Component\HttpFoundation\Request;
use Tops\sys\TStrings;

class ServiceRequestInputHandler extends \Tops\services\ServiceRequestInputHandler
{
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function getRequest() {
        global $request;
        return $request;
    }

    /**
     * @return 'POST' | 'GET'
     */
    public function getMethod()
    {
        global $request;
        return $this->getRequest()->getMethod();
    }

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->getRequest()->get($key);
    }

    public function getSecurityToken()
    {
        return $this->getRequest()->get(\Tops\services\ServiceRequestInputHandler::securityTokenKey);
    }

    public function getServiceNamespace($key)
    {
        $namespace = TStrings::formatNamespace($key);
        return "\\$namespace\\Services";
    }

}