<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/30/2017
 * Time: 7:15 AM
 */

namespace Tops\drupal8;


use Tops\sys\IUser;
use Tops\sys\IUserFactory;

class TDrupal8UserFactory implements IUserFactory
{

    /**
     * @return IUser
     */
    public function createUser()
    {
        return new TDrupal8User();
    }
}