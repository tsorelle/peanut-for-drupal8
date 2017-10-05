<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/28/2017
 * Time: 12:09 PM
 */

namespace PeanutTest\scripts;


use Tops\concrete5\Concrete5PermissionsManager;
use Tops\sys\TUser;

class Permissions3Test extends TestScript
{

    public function execute()
    {
        // run logged in as admin
        $user = TUser::getCurrent();
        $name = $user->getUserName();
        $admin = $user->isAdmin() ? 'Administrator' : 'Non-administrator';

        print "Succeed if user is administrator\n";
        print "Running test as: $name ($admin) \n";

        $manager = new Concrete5PermissionsManager();
        $roles = $manager->getRoles();
        $count = sizeof($roles);
        $this->assert($count > 0, 'No roles returned');

        $manager->removeRole(Permissions1Test::TestRoleName);

        $roles = $manager->getRoles();
        $actual = sizeof($roles);
        $expected = $count - 1;
        $this->assertEquals($expected,$actual,'Test not removed');
    }
}