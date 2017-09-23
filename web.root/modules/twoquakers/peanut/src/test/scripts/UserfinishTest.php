<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/23/2017
 * Time: 9:41 AM
 */

namespace PeanutTest\scripts;


use Tops\sys\IPermissionsManager;
use Tops\sys\TObjectContainer;
use Tops\sys\TUser;

class UserfinishTest extends TestScript
{

    public function execute()
    {
        /**
         * @var $manager IPermissionsManager
         */
        $manager = TObjectContainer::Get('tops.permissions');
        $this->assert(!empty($manager), 'Manager not instantiated.');

        $roles = $manager->getRoles();

        $count = sizeof($roles);
        $this->assert($count > 0, 'No roles returned');

        $testUserRole = 'Test User';
        $testPermission = 'perform test';
        $user = TUser::getByUserName('mrtester');
        $actual = $user->isMemberOf($testUserRole);
        $this->assert($actual,'not member of Test User');

        $actual = $user->isAuthorized('perform test');
        $this->assert($actual,'not authorized perform test');

        $manager->revokePermission($testUserRole,$testPermission);
        $actual = $user->isAuthorized('perform test');
        $this->assert($actual == false,'authorization not revoked');

        $manager->removeRole($testUserRole);
        $actual = $user->isMemberOf($testUserRole);
        $this->assert($actual,'role not removed');

    }
}