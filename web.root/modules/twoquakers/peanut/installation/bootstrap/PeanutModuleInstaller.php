<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/18/2017
 * Time: 9:42 AM
 */

namespace Tops\drupal8;
use Peanut\sys\DefaultPeanutInstaller;
use Tops\sys\TConfiguration;
use Tops\sys\TIniFileMerge;
use Tops\sys\TPath;
use Tops\sys\TSession;
use ZipArchive;

class PeanutModuleInstaller
{
    private $fileRoot;
    private $pluginRoot;
    private $configPath;
    private $templatePath;

    public function __construct()
    {
        $this->fileRoot =  DRUPAL_ROOT;
        $this->pluginRoot = realpath(__DIR__.'/../..');
        $this->configPath = DRUPAL_ROOT.'/application/config';
        $this->templatePath = realpath(__DIR__.'/..');
        $this->initialize();
    }

    private function initialize() {
        // Assume that if TPath already autoloaded, bootstrap tasks were already run
        if (!class_exists('\Tops\sys\TPath')) {
            $srcRoot = "$this->pluginRoot/src";
            // $loader = BootstrapAutoloader::getInstance();
            $autoloaderPath = realpath($this->pluginRoot.'/src/tops/sys/Autoloader.php');
            if ($autoloaderPath === false) {
                throw new \Exception('Autoloader path not found.');
            }
            $loader = require($autoloaderPath);
            // $loader->addPsr4('Tops', "$srcRoot/tops");
            $loader->addPsr4('Peanut', "$srcRoot/peanut");
            $loader->addPsr4('Tops\drupal8', "$srcRoot/drupal8");
            $loader->addPsr4('PeanutTest', "$srcRoot/test");

            if (!class_exists('\Tops\sys\TPath')) {
                throw new \Exception('Autoload failed in installer.');
            }

            TPath::Initialize($this->fileRoot);
            TSession::Initialize();
        }
    }

    public function installFiles() {
        $zipfile =  realpath(__DIR__.'/../application.zip');
        $target = $this->fileRoot.'/';
        $zip = new ZipArchive;
        if ($zip->open($zipfile) === TRUE) {
            $zip->extractTo($target);
            $zip->close();
        } else {
            throw new \Exception('Extraction error. Problem with application.zip.');
        }

        $iniPath = realpath("$this->templatePath/ini");
        if ($iniPath === false) {
            throw new \Exception('Cannot locate ini files.');
        }
        $configs = scandir($iniPath);
        foreach ($configs as $file) {
            if (strpos($file,'.ini') !== false) {
                TIniFileMerge::merge(
                    "$iniPath/$file",
                    "$this->configPath/$file");
            }
        }
        TConfiguration::clearCache();
    }

    public static function install() {
        $instance = new PeanutModuleInstaller();
        $instance->installFiles();
        $installer = new DefaultPeanutInstaller();
        $installer->installPackage('peanut');
        $installer->installPackage('mailboxes');
    }

}