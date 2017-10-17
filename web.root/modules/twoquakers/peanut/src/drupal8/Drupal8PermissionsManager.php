<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/20/2017
 * Time: 8:42 AM
 */

namespace Tops\drupal8;


use Tops\db\model\repository\BasicPermissionsRepository;
use Tops\sys\TPermission;
use Tops\sys\TPermissionsManager;
use Tops\sys\TStrings;
use Tops\sys\TUser;

class Drupal8PermissionsManager extends TPermissionsManager
{
    const permisionHandleFormat = TStrings::wordFormat; // used by Drupal
    const permisionKeyFormat = TStrings::dashedFormat; // used in database
    const permisionDescriptionFormat = TStrings::initialCapFormat;
    const roleHandleFormat = TStrings::keyFormat;
    const roleDescriptionFormat = TStrings::initialCapFormat;

    public function getPermissionHandleFormat()
    {
        return self::permisionHandleFormat;
    }

    public function getRoleHandleFormat()
    {
        return Drupal8Roles::roleHandleFormat;
    }

    public function getGuestRole()
    {
        return Drupal8Roles::guestRole;
    }

    public function getAuthenticatedRole()
    {
        return Drupal8Roles::authenticatedRole;
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public function addRole($roleName, $roleDescription = null)
    {
        // drupal 8 doesn't use verbose descriptions

        return Drupal8Roles::addRole($roleName);
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public function removeRole($roleName)
    {
        return Drupal8Roles::removeRole($roleName);
    }

    /**
     * @return \stdClass[]
     */
    public function getRoles()
    {
        return Drupal8Roles::getRoles();
    }

    /**
     * @return TPermission
     */
    public function getPermission($permissionName)
    {
        $permissionName = TStrings::convertNameFormat($permissionName,self::permisionKeyFormat);
        return $permission = $this->getRepository()->getEntity($permissionName);
    }

    /**
     * @param string $roleName
     * @param string $permissionName
     * @return bool
     */
    public function assignPermission($roleName, $permissionName)
    {
        $permissionName = TStrings::convertNameFormat($permissionName,self::permisionHandleFormat);
        $roleObject = Drupal8Roles::getDrupalRole($roleName);
        if ($roleObject === false) {
            return false;
        }
        $roleObject->grantPermission($permissionName);
        $roleObject->trustData()->save();
        return true;
    }

    /**
     * @param string $roleName
     * @param string $permissionName
     * @return bool
     */
    public function revokePermission($roleName, $permissionName)
    {
        $permissionName = TStrings::convertNameFormat($permissionName,self::permisionHandleFormat);
        $roleObject = Drupal8Roles::getDrupalRole($roleName);
        if ($roleObject === false) {
            return false;
        }
        $roleObject->revokePermission($permissionName);
        $roleObject->save();
        return true;
    }

    /******** Tops functions **********************/

    /**
     * @var BasicPermissionsRepository
     */
    private $permissionsRepository;

    /**
     * @return BasicPermissionsRepository
     */
    private function getRepository()
    {
        if (!isset($this->permissionsRepository)) {
            $this->permissionsRepository = new BasicPermissionsRepository();
        }
        return $this->permissionsRepository;
    }


    /**
     * @return TPermission[]
     */
    public function getPermissions()
    {
        return $this->getRepository()->getAll();
    }


    public function addPermission($name, $description=null)
    {
        $description = TStrings::convertNameFormat($name,self::permisionDescriptionFormat);
        $permissionName = TStrings::convertNameFormat($name,self::permisionKeyFormat);
        $username = TUser::getCurrent()->getUserName();
        $existing = $this->getRepository()->getPermission($name);
        if (empty($existing)) {
            $this->getRepository()->addPermission($permissionName, $description, $username);
        }
        return true;
    }

    public function getPermissionsList() {
        $permissions = [];
        /** @var $list TDrupal8Permission[] */
        $list = $this->getRepository()->getAll();
        foreach ($list as $permission) {
            $name = TStrings::ConvertNameFormat($permission->getPermissionName(),self::permisionHandleFormat);
            $permissions[$name] =
                ['title' => $permission->getDescription(),'description' => ''];
        }
        return $permissions;
    }

    public function removePermission($name)
    {
        $name = TStrings::convertNameFormat($name,self::permisionKeyFormat);
        return $this->getRepository()->removePermission($name);
    }

}