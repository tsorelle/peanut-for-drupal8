<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/25/2017
 * Time: 6:27 AM
 */

namespace Tops\sys;


/**
 * Class AftmConfiguration
 * @package Tops
 *
 * Handles access to configuration file config.ini, located in same directory as this class file.
 * For ini file documentation see http://php.net/manual/en/function.parse-ini-file.php
 *
 */
class TConfiguration
{
    /**
     * @var array
     */
    private static $ini = null;
    private static $configs = array();

    public static function clearCache() {
        self::$ini = null;
    }

    private static function getIni(array $packages=null)
    {
        if (self::$ini === null) {
            self::$ini = self::loadIni();
        }
        self::$configs = array();
        return self::$ini;
    }

    private static function loadIni($fileName = 'settings.ini', $iniPath = null)
    {
        if ($iniPath === null) {
            $iniPath = TPath::getConfigPath() . $fileName;
        } else {
            $iniPath = TPath::combine($iniPath, $fileName);
        }

        return parse_ini_file($iniPath, true);
    }

    public static function loadAppSettings($files='') {
        if (self::$ini == null) {
            self::getIni();
        }
        $files = explode(',',$files);
        foreach ($files as  $fileName) {
            if ($fileName != 'settings.ini') {
                self::addSettings($fileName);
            }
        }
    }

    public static function addSettings($fileName, $iniPath = null, $replaceExisting = true)
    {
        $settings = self::loadIni($fileName,$iniPath);
        if ($settings !== false) {
            $keys = array_keys($settings);
            foreach ($keys as $key) {
                if (array_key_exists($key,$settings)) {
                    if ($replaceExisting) {
                        self::$ini[$key] = $settings[$key];
                    }
                }
                else {
                    self::$ini[] = $settings[$key];
                }
            }
        }
    }


    /**
     * Retrieve value by key and section
     *
     * @param $key
     * @param $sectionKey
     * @param bool $defaultValue - returned if value does not exist
     * @return bool|mixed
     */
    public static function getValue($key, $sectionKey, $defaultValue = false)
    {
        $section = self::getIniSection($sectionKey);
        if (is_array($section) && array_key_exists($key, $section)) {
            return $section[$key];
        }
        return $defaultValue;
    }

    public static function getBoolean($key, $sectionKey, $defaultValue = false)
    {
        $section = self::getIniSection($sectionKey);
        if (is_array($section) && array_key_exists($key, $section)) {
            return  !empty($section[$key]);
        }
        return $defaultValue;
    }

    /**
     * Get array of values by section key
     *
     * @param $sectionKey
     * @return bool|mixed
     */
    public static function getIniSection($sectionKey)
    {
        $ini = self::getIni();
        if (array_key_exists($sectionKey, $ini)) {
            return $ini[$sectionKey];
        }
        return false;
    }


    public function getIniEmailValues($key, $sectionKey)
    {
        $result = array();
        $keys = self::getIniValue($key, $sectionKey);
        if ($keys !== false) {
            $keys = explode(',', $keys);
            foreach ($keys as $key) {
                $key = trim($key);
                if (strstr($key, '@')) {
                    $email = $key;
                } else {
                    $email = self::getIniValue($key, 'email');
                }
                if (!empty($email)) {
                    $result[] = $email;
                }
            }
        }
        return $result;
    }

}