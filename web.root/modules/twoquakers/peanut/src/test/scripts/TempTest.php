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

class TempTest extends TestScript
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
        $manager->addRole($testUserRole);
        $roles = $manager->getRoles();
        $actual = sizeof($roles);
        $expected = $count + 1;
        $this->assertEquals($expected,$actual,'Test not added');


//        $roles = $manager->getRoles();
//        var_dump($roles);
//        print "\n\n";

        $testPermission = 'perform test';
        $manager->addPermission('perform test','Perform TOPS Tests');
        $manager->assignPermission($testUserRole,$testPermission);

        return;


    }
}