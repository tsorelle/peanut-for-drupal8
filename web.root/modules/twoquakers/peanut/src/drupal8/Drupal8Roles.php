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
    const roleHandleFormat = TStrings::keyFormat;
    const roleDescriptionFormat = TStrings::initialCapFormat;
    const administratorRole = 'administrator';
    const authenticatedRole = 'authenticated_user';
    const guestRole = 'anonymous';


    public static function formatRoleName($roleName) {
        return TStrings::convertNameFormat($roleName,self::roleHandleFormat);
    }

    public static function roleExists($roleName) {
        $roleName = TStrings::convertNameFormat($roleName,self::roleHandleFormat);
        $exists = \Drupal\user\Entity\Role::load($roleName);
        return ($exists !== null);
    }

    /**
     * @param $roleName
     * @return bool|RoleInterface
     */
    public static function getDrupalRole($roleName)
    {
        $roleName = TStrings::convertNameFormat($roleName,self::roleHandleFormat);
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
        $roleId = TStrings::convertNameFormat($roleName,TStrings::keyFormat);
        $roleDescription = TStrings::convertNameFormat($roleName,self::roleDescriptionFormat);
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

        foreach ($roleObjects as $roleName => $roleObject) {
            $item = new \stdClass();
            $item->name = $roleName;
            $item->description = $roleObject->label();
            $result[] = $item;
        }
        return $result;
    }

}