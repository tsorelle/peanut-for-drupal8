<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/23/2017
 * Time: 8:51 AM
 */

namespace Tops\drupal8;


use Drupal\user\RoleInterface;
use Tops\sys\TStrings;

class Drupal8Roles
{
    public static $roleNameFormat = TStrings::keyFormat;
    public static $roleDescriptionFormat = TStrings::initialCapFormat;

    public static function formatRoleName($roleName) {
        return TStrings::convertNameFormat($roleName,self::$roleNameFormat);
    }

    /**
     * @param $roleName
     * @return bool|RoleInterface
     */
    public static function getDrupalRole($roleName)
    {
        $roleName = TStrings::convertNameFormat($roleName,self::$roleNameFormat);
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
        $exists = self::getDrupalRole($roleName);
        if ($exists !== false) {
            return false;
        }
        $roleId = TStrings::convertNameFormat($roleName,self::$roleNameFormat);
        $roleDescription = TStrings::convertNameFormat($roleName,TStrings::initialCapFormat);
        /**
         * @var $roleObject RoleInterface
         */
        $roleObject = \Drupal\user\Entity\Role::create(array('id' => $roleId, 'label' => $roleDescription));
        $roleObject->save();
        return true;
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public static function removeRole($roleName)
    {
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