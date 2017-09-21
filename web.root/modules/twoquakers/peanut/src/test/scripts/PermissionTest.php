<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/21/2017
 * Time: 7:53 AM
 */

namespace PeanutTest\scripts;


use Tops\drupal8\Drupal8ConnectionManager;
use Tops\drupal8\TDrupal8Permission;

class PermissionTest extends TestScript
{

    public function execute()
    {
        $permission = new TDrupal8Permission();
        $permission->setPermissionName('manage-mailboxes');
        $actual = $permission->check("peanut_test");
    }
}