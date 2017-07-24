<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/5/2017
 * Time: 6:36 AM
 */

namespace Tops\sys;


class TStrings
{


    public static function toCamelCase($s) {
        $parts = explode('-',$s);
        $len = sizeof($parts);
        for ($i = 0; $i<$len;$i++) {
            $part = $parts[$i];
            $initial = substr($part,0,1);
            if ($initial) {
                $initial = strtoupper($initial);
                $remainder = substr($part,1);
                $parts[$i] = strtoupper($initial).($remainder ? $remainder : '');
            }
        }
        return join('',$parts);
    }

    public static function toTitle($s,$seperator=false) {
        if ($seperator) {
            $s = str_replace($seperator,' ',$s);
        }
        $parts = explode(' ',$s);
        $len = sizeof($parts);
        for ($i = 0; $i<$len;$i++) {
            $part = $parts[$i];
            if ($i > 0 && ($part == 'the' || $part == 'a' || $part == 'of' || $part == 'an' || $part == 'in')) {
                continue;
            }
            $initial = substr($part,0,1);
            if ($initial) {
                $initial = strtoupper($initial);
                $remainder = substr($part,1);
                $parts[$i] = strtoupper($initial).($remainder ? $remainder : '');
            }
        }
        return join(' ',$parts);
    }

    /**
     * Convert namespace code, usually for a service call to literal namespace, per TOPS convention
     * Periods replaed by backslashes
     * First part converts to Pascal case
     * Subsequent parts convert to Pascal case if it contains a hyphen, otherwise literal text used.
     *
     * example:
     * two-quakers.testing.services.sub-services > TwoQuakers\testing\services\SubServices
     * two-quakers.Testing.services.sub-services > TwoQuakers\Testing\services\SubServices
     *
     * @param $nscode string
     * @return bool|string
     */
    public static function formatNamespace($nscode)
    {
        if (empty($nscode)) {
            return false;
        }
        $parts = explode('.',$nscode);

        $count = sizeof($parts);
        for ($i=0;$i<$count;$i++) {
            $part = $parts[$i];
            if ($i==0 || strpos($part,'-') !== false) {
                $parts[$i] = self::toCamelCase($part);
            }
        }
        return join('\\',$parts);
    }

}