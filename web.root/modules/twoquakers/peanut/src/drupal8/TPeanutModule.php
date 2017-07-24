<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/12/2017
 * Time: 3:45 PM
 */

namespace Tops\drupal8;

use Drupal;
use Drupal\Core\Path\AliasManager;
use Symfony\Component\HttpFoundation\Request;
use Tops\sys\TPath;

class TPeanutModule
{

    public static function GetLibraries() {

        $libraries['knockoutjs'] = array(
            'title' => 'Knockout JS',
            'website' => 'http://cdnjs.cloudflare.com',
            'version' => '3.3.0',
            'js' => array(
                'http://cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-min.js' => array('type'=>'external', 'group' => JS_LIBRARY,  'weight' => '1')
            ),
        );

        $libraries['headjs'] = array(
            'title' => 'Head JS Load',
            'website' => 'http://cdnjs.cloudflare.com',
            'version' => '1.0.3',
            'js' => array(
                'http://cdnjs.cloudflare.com/ajax/libs/headjs/1.0.3/head.load.min.js' => array('type'=>'external', 'group' => JS_LIBRARY,  'weight' => '1')
            )

        );

        $libraries['peanut'] = array(
            'title' => 'Peanut Service Library',
            'version' => '1.0',
            'js' => array(
                'assets/js/Tops.Peanut/Peanut.js' => array('group' => JS_LIBRARY, 'weight' => '2'),
            ),
            'dependencies' => array(
                array('system', 'jquery'),
                array('tops','knockoutjs')
                // we also need bootstrap but will rely on bootstrap module for that
            ),
        );

        $libraries['underscorejs'] = array(
            'title' => 'Underscore JS',
            'website' => 'http://cdnjs.cloudflare.com',
            'version' => '1.8.3',
            'js' => array(
                'http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js' => array('type'=>'external', 'group' => JS_LIBRARY,  'weight' => '3')
            ),
        );

        $libraries['peanut.app'] = array(
            'title' => 'Peanut Application',
            'version' => '1.0',
            'js' => array(
                'assets/js/Tops.App/App.js' => array('group' => JS_LIBRARY, 'weight' => '4'),
            ),
            'dependencies' => array(
                array('system', 'jquery'),
                array('tops','knockoutjs'),
                array('tops','underscorejs'),
                array('tops','headjs'),
                array('tops','peanut')
                // we also need bootstrap but will rely on bootstrap module for that
            )
        );

        return $libraries;

    }


    public static function GetPermissions() {
        return array(
            'execute service command' => array(
                'title' => 'Execute TOPS service',
                'description' => 'Post AJAX request to execute a service command.'
            ),

            'execute tops script' => array(
                'title' => 'Execute TOPS script',
                'description' => 'Execute a maintenance script.'
            ),
        );
    }


}