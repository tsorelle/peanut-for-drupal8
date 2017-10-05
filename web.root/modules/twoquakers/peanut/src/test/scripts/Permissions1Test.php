<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/12/2017
 * Time: 10:13 AM
 */

namespace PeanutTest\scripts;


use Tops\concrete5\Concrete5PermissionsManager;
use Tops\sys\TUser;

class Permissions1Test extends TestScript
{
    // log in as admin

    const TestPermissionName = 'User can test peanut 4';
    const TestRoleName = 'Permissions test role 4';
    public function execute()
    {
        $user = TUser::getCurrent();
        $name = $user->getUserName();
        $admin = $user->isAdmin() ? 'Administrator' : 'Non-administrator';

        print "Succeed if user is administrator\n";
        print "Running test as: $name ($admin) \n";


        $manager = new Concrete5PermissionsManager();
                $roles = $manager->getRoles();

                $count = sizeof($roles);
                $this->assert($count > 0, 'No roles returned');

                $testRoleName = self::TestRoleName;
                $testPermissionName = self::TestPermissionName;
                $manager->addPermission($testPermissionName,$testPermissionName);

                $manager->addRole($testRoleName);
                $roles = $manager->getRoles();
                $actual = sizeof($roles);
                $expected = $count + 1;
                $this->assertEquals($expected,$actual,'Test not added');


                $roles = $manager->getRoles();
                //    var_dump($roles);
                //   print "\n\n";

        $manager->assignPermission(self::TestRoleName,self::TestPermissionName);

        //*************************************************************************
        // Next: check results, add tester user to 'Test Role' group then run permissions2
        print "\nSet up for Permisions2 by assigning a user to 'Test Role' and log him in\n";



    }
}