<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/21/2017
 * Time: 7:23 AM
 */

namespace Tops\drupal8;


use Drupal\user\RoleInterface;
use Tops\sys\TPermission;

class TDrupal8Permission extends TPermission
{

    public function check($roleName)
    {
        $roleName =  Drupal8Roles::roleNameToMachineName($roleName);
        try {
            /**
             * @var $roleObject RoleInterface
             */
            $roleObject = \Drupal\user\Entity\Role::load($roleName);
            if (empty($roleObject)) {
                return false;
            }
            return $roleObject->hasPermission($this->permissionName);
        }
        catch (\Exception $ex) {
            return false;
        }
    }
}