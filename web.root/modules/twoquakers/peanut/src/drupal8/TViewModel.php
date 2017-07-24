<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/12/2017
 * Time: 3:44 PM
 */

namespace Tops\drupal8;

use Drupal;
use Symfony\Component\HttpFoundation\Request;
use Tops\sys\TConfiguration;
use Tops\sys\TPath;
use Tops\ui\TViewModelInfo;
use Tops\ui\TViewModelManager;

class TViewModel
{
    /**
     *
     * Extracts an alias from the request returns it if it is valid for a view model
     * Rules for ViewModel names exclude the names of first level Drupal and Tops directories
     * and they cannot have a file extension.
     *
     * @param Request $request Symfony\Component\HttpFoundation\Request
     * @return bool|null|string
     */
    public static function getVmPathFromRequest(Request $request)
    {
        $path = $request->getPathInfo();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (
            // exclude invalid or unknown path
            (!empty($path)) &&
            // exclude file or document requests
            empty($ext) &&
            // exclude posts and AJAX requests
            ($request->getMethod() == 'GET' && $request->getRequestFormat() == 'html')
        ) {
            $pathParts = explode('/', $path);
            $count = count($pathParts);
            if ($count < 2) {
                return null;
            }

            $name =  $pathParts[1];
            $lowerName = strtolower($name); // for case insensitive compare
            // exclude Drupal and Tops root directories and standard Drupal root functions
            if ($lowerName == 'config' || $lowerName == 'admin' || $lowerName == 'user' ||
                $lowerName == 'sites' || $lowerName == 'misc' || $lowerName == 'assets' || $lowerName == 'lib' || $lowerName == 'core' ||
                $lowerName == 'modules' || $lowerName == 'themes' || $lowerName == 'tops'
            ) {
                return null;
            }

            $arg = '';

            // if 'node' get arguments
            if ($name == 'node') {
                if ($count < 3 || !is_numeric($pathParts[2])) {
                    return null;
                }
                if ($count > 3) {
                    $arg = $pathParts[3];
                }
            } else {
                if ($count > 2) {
                    $arg = $pathParts[2];
                }
            }

            // eliminate standard form functions
            if ($arg == 'add' || $arg == 'edit') {
                return null;
            }

            // get node alias
            if ($name == 'node') {
                $aliasManager = self::getAliasManager();
                if (!$aliasManager) {
                    return null;
                }
                $name = $aliasManager->getAliasByPath($path);
                if (strstr('/node/', $name)) {
                    // must have alias
                    return null;
                }
                else {
                    return $name;
                }
            }

            if ($count <= 2) {
                return $name;
            }
            $path = $pathParts[1];
            for ($i = 2; $i < $count; $i++) {
                $path .= '/'.$pathParts[$i];
            }
            return $path;

        }

        return null;
    }

    /**
     * @var Drupal\Core\Path\AliasManagerInterface
     */
    private static $aliasManager;

    /**
     * @return Drupal\Core\Path\AliasManagerInterface
     */
    private static function getAliasManager() {
        if (!self::$aliasManager) {
            self::$aliasManager = Drupal::service('path.alias_manager');
        }
        return self::$aliasManager;
    }

    public static function getViewModelInfo()
    {
        return isset(self::$info) ? self::$info : false;
    }

    /**
     * @param $request \Symfony\Component\HttpFoundation\Request
     */
    private static function getCurrentNodeId($request)
    {
        $path = $request->getPathInfo();
        if (strstr($path,'node/') === false) {
            $path = self::getAliasManager()->getPathByAlias($path);
            if (empty($path)) {
                return false;
            }
        }
        $parts = explode('/',$path);
        if (sizeof($parts) < 2) {
            return false;
        }
        return array_pop($parts);
    }

    public static function Initialize(Request $request) {
        $name = self::getVmPathFromRequest($request);
        if ($name)
        {
            $result = TViewModelManager::getViewModelSettings($name);
            $result->id = self::getCurrentNodeId($request);
            return $result;
        }
        return false;
    }
}