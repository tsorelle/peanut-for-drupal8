<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/2/2017
 * Time: 6:13 AM
 */

namespace PeanutTest\scripts;

use Tops\sys\TUser;

class UserattrTest extends TestScript
{

    private function createAttribute($args,$type='text',$pkg=false) {

    }

    public function execute()
    {
        $user = TUser::getCurrent();
        $this->assertNotNull($user,'User');
    }
}