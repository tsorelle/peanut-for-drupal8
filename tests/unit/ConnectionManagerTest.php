<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/15/2017
 * Time: 7:47 PM
 */

use PHPUnit\Framework\TestCase;
use Tops\drupal8\Drupal8ConnectionManager;

class ConnectionManagerTest extends TestCase
{
    public function testConnectionManager() {
        // $manager = new Drupal8ConnectionManager();
        // $actual = $manager->getNativeConfiguration();
        $actual = \Tops\db\TDatabase::getDbConfigurationForTest();
        $this->assertEquals('drupal',$actual->default);
        $this->assertNotEmpty($actual->connections);
        $keyExists=array_key_exists('drupal',$actual->connections);
        $this->assertTrue($keyExists);
        $config = $actual->connections['drupal'];
        $this->assertNotEmpty($config->user);
        $this->assertNotEmpty($config->pwd);
        $this->assertNotEmpty($config->database);

        self::assertNotNull($actual);
    }
}
