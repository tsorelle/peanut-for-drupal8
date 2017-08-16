<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/15/2017
 * Time: 5:07 PM
 */

namespace Tops\drupal8;


use Masterminds\HTML5\Exception;
use Tops\db\TConnectionManager;
use Tops\sys\TPath;

class Drupal8ConnectionManager extends TConnectionManager
{

    private function endStatement($line) {
        $line = trim(explode('//', $line)[0]); // remove comments
        return substr($line,-1) === ';';
    }

    public function getNativeConfiguration()
    {
        $configfile = TPath::getFileRoot().'sites/default/settings.php';
        $lines = file($configfile);
        $readingDbInfo = false;
        $dbConfigCode = array();

        foreach ($lines as $line) {
            $line = trim($line);
            if ($readingDbInfo) {
                $dbConfigCode[] = $line;
                if ($this->endStatement($line)) {
                    $readingDbInfo = false;
                }
            }
            else if (substr($line, 0, 10) == '$databases') {
                $dbConfigCode[] = $line;
                if (!$this->endStatement($line)) {
                    $readingDbInfo = true;
                }
            }
        }
        $result = new \stdClass();
        $result->default = 'drupal';
        $result->connections = array();

        $databases = array();
        eval (join("\n",$dbConfigCode));
        if (empty($databases)) {
            return false;
        }
        foreach ($databases as $key => $definitions) {
            $definition = $definitions['default'];
            if ($definition['driver'] === 'mysql') {
                $topsConfig = $this->makeParameterObject(
                    $definition['database'],
                    $definition['username'],
                    $definition['password'],
                    $definition['host']
                );
                $name = $key == 'default' ? 'drupal' : $key;
                $result->connections[$name] = $topsConfig;
            }
        }

        return $result;

    }

}