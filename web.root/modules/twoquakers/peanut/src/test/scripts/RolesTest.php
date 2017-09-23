<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/17/2017
 * Time: 9:06 PM
 */

namespace PeanutTest\scripts;


class RolesTest extends TestScript
{

    public function execute()
    {
        $roleObjects =  user_roles();
        var_dump($roleObjects);

    }
}