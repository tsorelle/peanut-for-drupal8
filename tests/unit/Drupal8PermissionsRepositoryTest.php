<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/21/2017
 * Time: 8:09 AM
 */



use PHPUnit\Framework\TestCase;
use Tops\drupal8\Drupal8PermissionsRepository;

class Drupal8PermissionsRepositoryTest extends TestCase
{
    public function testGetPermissions() {
        $repo = new Drupal8PermissionsRepository();
        $actual = $repo->getAll();
        $this->assertNotEmpty($actual);
    }
}
