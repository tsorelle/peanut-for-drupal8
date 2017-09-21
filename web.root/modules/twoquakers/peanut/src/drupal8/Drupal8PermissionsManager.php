<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/20/2017
 * Time: 8:42 AM
 */

namespace Tops\drupal8;


use Drupal\user\RoleInterface;
use Tops\sys\IPermissionsManager;
use Tops\drupal8\TDrupal8Permission;
use Tops\sys\TUser;

class Drupal8PermissionsManager implements IPermissionsManager
{

    /**
     * @param $roleName
     * @return bool|RoleInterface
     */
    private function getDrupalRole($roleName)
    {
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
     * @param string $roleName
     * @return bool
     */
    public function addRole($roleName, $roleDescription = null)
    {
        /**
         * @var $roleObject RoleInterface
         */
        $roleObject = \Drupal\user\Entity\Role::create(array('id' => $roleName, 'label' => $roleDescription));
        $roleObject->save();
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public function removeRole($roleName)
    {
        $roleObject = $this->getDrupalRole($roleName);
        if ($roleObject === false) {
            return false;
        }
        $roleObject->delete();
        return true;
    }

    /**
     * @return \stdClass[]
     */
    public function getRoles()
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

    /**
     * @return TPermission
     */
    public function getPermission($permissionName)
    {
        // not used in drupal
        return null;
    }

    /**
     * @param string $roleName
     * @param string $permissionName
     * @return bool
     */
    public function assignPermission($roleName, $permissionName)
    {
        $roleObject = $this->getDrupalRole($roleName);

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
        $roleObject = $this->getDrupalRole($roleName);
        if ($roleObject === false) {
            return false;
        }
        $roleObject->revokePermission($permissionName);
        $roleObject->save();
        return true;
    }

    /******** Tops functions **********************/

    /**
     * @var Drupal8PermissionsRepository
     */
    private $permissionsRepository;

    /**
     * @return Drupal8PermissionsRepository
     */
    private function getRepository()
    {
        if (!isset($this->permissionsRepository)) {
            $this->permissionsRepository = new Drupal8PermissionsRepository();
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
        $username = TUser::getCurrent()->getUserName();
        $this->getRepository()->addPermission($name,$description,$username);
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
}