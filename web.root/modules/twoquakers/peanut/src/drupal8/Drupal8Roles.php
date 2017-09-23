<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/23/2017
 * Time: 8:51 AM
 */

namespace Tops\drupal8;


use Drupal\user\RoleInterface;

class Drupal8Roles
{
    public static function roleNameToMachineName($roleName) {
        if (strpos($roleName,' ') !== false) {
            return strtolower(str_replace(' ','_',$roleName));
        }
        return $roleName;
    }

    /**
     * @param $roleName
     * @return bool|RoleInterface
     */
    public static function getDrupalRole($roleName)
    {
        $roleName = self::roleNameToMachineName($roleName);
        try {
            /**
             * @var $roleObject RoleInterface
             */
            $roleObject = \Drupal\user\Entity\Role::load($roleName);
            if (empty($roleObject)) {
                return false;
            }
            return $roleObject;
        }
        catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * @param $roleName
     * @param null $roleDescription
     * @return bool
     */
    public static function addRole($roleName)
    {
        $roleDescription = $roleName;
        $roleName = self::roleNameToMachineName($roleName);
        /**
         * @var $roleObject RoleInterface
         */
        $roleObject = \Drupal\user\Entity\Role::create(array('id' => $roleName, 'label' => $roleDescription));
        $roleObject->save();
        return true;
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public static function removeRole($roleName)
    {
        $roleName = self::roleNameToMachineName($roleName);
        $roleObject = self::getDrupalRole($roleName);
        if ($roleObject === false) {
            return false;
        }
        $roleObject->delete();
        return true;
    }

    /**
     * @return \stdClass[]
     */
    public static function getRoles()
    {
        $result = array();

        /**
         * @var $roleObjects RoleInterface[]
         */
        $roleObjects = user_roles();

        // unset($roleObjects['administrator']);

        foreach ($roleObjects as $roleName => $roleObject) {
            $item = new \stdClass();
            $item->Name = $roleName;
            $item->Value = $roleObject->label();
            $result[] = $item;
        }
        return $result;
    }

}