<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/4/2017
 * Time: 6:35 AM
 */

namespace PeanutTest\scripts;


use Tops\drupal8\Drupal8PermissionsManager;
use Tops\sys\TPermissionsManager;
use Tops\sys\TStrings;

class UsersetupTest extends TestScript
{

    private $roles;
    private $continueTest = false;
    /**
     * @var Drupal8PermissionsManager
     */
    private $manager;

    private function roleExists($value) {
        if (!isset($this->roles)) {
            if ($this->getRoleCount() === 0) {
                return false;
            }
        }
        $value = TStrings::convertNameFormat($value,TStrings::keyFormat);
        foreach ($this->roles as $role) {
            if ($role->Value === $value) {
                return true;
            }
        }
        return false;
    }

    private function getRoleCount() {
        $this->roles = $this->manager->getRoles();
        $count = sizeof($this->roles);
        $this->assert($count > 0, 'No roles returned');
        return $count;
    }

    private function addRole($roleName,$roleCount,$fail=false) {
        $added = $this->manager->addRole($roleName);
        $expected = $added ? $roleCount + 1 : $roleCount;
        $actual = $this->getRoleCount();
        $msg = ($expected == $actual) ?
             'duplicate role created' : 'role not added';
        $result = $this->assertEquals($expected,$actual,$msg);
        if ($fail && !$result) {
            exit;
        }
        $this->continueTest = $result;
        if ($result) {
            print  ($actual > $roleCount ?  "Role '$roleName' added.\n" : "Role '$roleName' exists.\n");
        }
        return $actual;
    }

    private function removeRole($roleName,$roleCount) {
        $this->manager->removeRole($roleName);
        $actual = $this->getRoleCount();
        print ( $actual < $roleCount ? "Role '$roleName' removed.\n" : "Role '$roleName' not removed.\n" );
        return $actual;
    }

    public function execute()
    {
        $this->manager = new Drupal8PermissionsManager();

        $roleCount = $this->getRoleCount();

        $testRole = 'test role';
        $roleCount = $this->addRole($testRole,$roleCount,true);
        $roleCount = $this->removeRole($testRole,$roleCount);
        $roleCount = $this->addRole(TPermissionsManager::appAdminRoleName,$roleCount);
        $roleCount = $this->addRole(TPermissionsManager::mailAdminRoleName,$roleCount);
        $roleCount = $this->addRole(TPermissionsManager::directoryAdminRoleName,$roleCount);

        $this->manager->addPermission(TPermissionsManager::mailAdminPermissionName);
        $this->manager->addPermission(TPermissionsManager::appAdminPermissionName);
        $this->manager->addPermission(TPermissionsManager::directoryAdminPermissionName);
        $this->manager->addPermission(TPermissionsManager::viewDirectoryPermissionName);
        $this->manager->addPermission(TPermissionsManager::updateDirectoryPermissionName);

        $this->manager->assignPermission(TPermissionsManager::appAdminRoleName,TPermissionsManager::mailAdminPermissionName);
        $this->manager->assignPermission(TPermissionsManager::appAdminRoleName,TPermissionsManager::appAdminPermissionName);
        $this->manager->assignPermission(TPermissionsManager::appAdminRoleName,TPermissionsManager::directoryAdminPermissionName);
        $this->manager->assignPermission(TPermissionsManager::appAdminRoleName,TPermissionsManager::viewDirectoryPermissionName);
        $this->manager->assignPermission(TPermissionsManager::appAdminRoleName,TPermissionsManager::updateDirectoryPermissionName);

        $this->manager->assignPermission(TPermissionsManager::directoryAdminRoleName,TPermissionsManager::directoryAdminPermissionName);
        $this->manager->assignPermission(TPermissionsManager::directoryAdminRoleName,TPermissionsManager::updateDirectoryPermissionName);
        $this->manager->assignPermission(TPermissionsManager::directoryAdminRoleName,TPermissionsManager::viewDirectoryPermissionName);

        $this->manager->assignPermission(TPermissionsManager::mailAdminRoleName,TPermissionsManager::mailAdminPermissionName);
        $this->manager->assignPermission(TPermissionsManager::mailAdminRoleName,TPermissionsManager::updateDirectoryPermissionName);
        $this->manager->assignPermission(TPermissionsManager::directoryAdminRoleName,TPermissionsManager::viewDirectoryPermissionName);

        $this->manager->assignPermission(TPermissionsManager::authenticatedRole,TPermissionsManager::viewDirectoryPermissionName);

        print "\n".($this->continueTest ? 'Ready for "user" test. Add your test user to the mail admin role' : 'Setup failed')."\n";

    }
}