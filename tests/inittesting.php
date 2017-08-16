<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 5/14/2017'
 * Time: 6:01 AM
 */
$projectFileRoot =   str_replace('\\','/', realpath(__DIR__.'/../web.root')).'/';
\Tops\sys\TPath::Initialize($projectFileRoot,'application/config');

