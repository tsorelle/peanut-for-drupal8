<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/12/2017
 * Time: 10:13 AM
 */

namespace PeanutTest\scripts;


use Tops\sys\IPermissionsManager;
use Tops\sys\TObjectContainer;

class PermissionsTest extends TestScript
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

        $manager->addRole('Test User');
        $roles = $manager->getRoles();
        $actual = sizeof($roles);
        $expected = $count + 1;
        $this->assertEquals($expected,$actual,'Test not added');


        $roles = $manager->getRoles();
        var_dump($roles);
        print "\n\n";

        $manager->addPermission('perform test','Perform TOPS Tests');

        $manager->assignPermission('Test User','perform test');


/*

        $manager->removeRole('Test Role');
        $roles = $manager->getRoles();
        $actual = sizeof($roles);
        $expected = $count;
        $this->assertEquals($expected,$actual,'Test not removed');
  */

    }
}