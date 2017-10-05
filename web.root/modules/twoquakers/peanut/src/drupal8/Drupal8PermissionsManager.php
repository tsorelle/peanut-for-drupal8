<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/20/2017
 * Time: 8:42 AM
 */

namespace Tops\drupal8;


use Drupal\user\RoleInterface;
use Tops\db\model\repository\BasicPermissionsRepository;
use Tops\sys\IPermissionsManager;
use Tops\drupal8\TDrupal8Permission;
use Tops\sys\TPermission;
use Tops\sys\TStrings;
use Tops\sys\TUser;

class Drupal8PermissionsManager implements IPermissionsManager
{
    public static $permisionNameFormat = TStrings::initialCapFormat;

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
        $permissionName = TStrings::convertNameFormat($permissionName,self::$permisionNameFormat);
        return $permission = $this->getRepository()->getEntity($permissionName);
    }

    /**
     * @param string $roleName
     * @param string $permissionName
     * @return bool
     */
    public function assignPermission($roleName, $permissionName)
    {
        $permissionName = TStrings::convertNameFormat($permissionName,self::$permisionNameFormat);
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
        $permissionName = TStrings::convertNameFormat($permissionName,self::$permisionNameFormat);
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


    public function addPermission($name, $description)
    {
        $name = TStrings::convertNameFormat($name,self::$permisionNameFormat);
        $username = TUser::getCurrent()->getUserName();
        $existing = $this->getRepository()->getPermission($name);
        if (empty($existing)) {
            $this->getRepository()->addPermission($name, $description, $username);
        }
        return true;
    }

    public function getPermissionsList() {
        $permissions = [];
        /** @var $list TDrupal8Permission[] */
        $list = $this->getRepository()->getAll();
        foreach ($list as $permission) {
            $permissions[$permission->getPermissionName()] =
                ['title' => '','description' => $permission->getDescription()];

        }
        return $permissions;
    }

    public function removePermission($name)
    {
        $name = TStrings::convertNameFormat($name,self::$permisionNameFormat);

        return $this->getRepository()->removePermission($name);
    }

    public function verifyPermission($permissionName)
    {
        // TODO: Implement verifyPermission() method.
    }
}