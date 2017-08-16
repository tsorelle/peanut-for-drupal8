<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/12/2017
 * Time: 3:18 PM
 */

class quicktest {

    private static $settings = false;

    function getVmConfig($pathAlias) {
        if (self::$settings === false) {
            $path = __DIR__.'/../application/config';
            self::$settings = parse_ini_file($path.'/viewmodels.ini',true);
        }
        if (array_key_exists($pathAlias,self::$settings)) {
            $item = self::$settings[$pathAlias];
            $result = new stdClass();
            $result->vmName = empty($item['vm']) ? array_pop(explode('/',$pathAlias)) : $item['vm'];
            $result->view = empty($item['view']) ? $result : $item['view'];
        }
        return false;
    }
}


echo "done";