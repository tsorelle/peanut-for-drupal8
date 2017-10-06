<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/4/2017
 * Time: 6:35 AM
 */

namespace PeanutTest\scripts;


use Tops\drupal8\Drupal8PermissionsManager;
use Tops\drupal8\TDrupal8User;
use Tops\sys\TStrings;
use Tops\sys\TUser;
use Tops\wordpress\TWordpressUser;
use Tops\wordpress\WordpressPermissionsManager;

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
        $removed = $this->manager->removeRole($roleName);
        $expected = $removed ? $roleCount - 1 : $roleCount;
        $actual = $this->getRoleCount();
        $this->assertEquals($expected,$actual,'Remove failed.');
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
        $roleCount = $this->addRole(TUser::appAdminRoleName,$roleCount);
        $roleCount = $this->addRole(TUser::mailAdminRoleName,$roleCount);

        $this->manager->assignPermission(TUser::mailAdminRoleName,TUser::mailAdminPermissionName);
        $this->manager->assignPermission(TUser::appAdminRoleName,TUser::mailAdminPermissionName);
        $this->manager->assignPermission(TUser::appAdminRoleName,TUser::appAdminPermissionName);


        // print "\n".($this->continueTest ? 'Ready for "user" test. Add your test user to the mail admin role' : 'Setup failed')."\n";

    }
}