<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/28/2017
 * Time: 12:09 PM
 */

namespace PeanutTest\scripts;


use Tops\sys\TStrings;
use Tops\sys\TUser;

class Permissions2Test extends TestScript
{

    public function execute()
    {
        // run logged in as tester then try others for expected fail


        $user = TUser::getCurrent();
        $name = $user->getUserName();
        print "Succeed if user is 'tester'\n";
        print "Running test as: $name\n";

        print "Email: ".$user->getEmail()."\n";
        print "Last: ".$user->getLastName()."\n";
        print "First: ".$user->getFirstName()."\n";
        print "Full:".$user->getFullName()."\n";
        print "Short:".$user->getUserShortName()."\n";
        print "Admin? ".($user->isAdmin() ? 'Yes' : 'No')."\n";
        print "Authenticated? ".($user->isAuthenticated() ? 'Yes' : 'No')."\n";
        print "Member test users? ".($user->isMemberOf(Permissions1Test::TestRoleName) ? 'Yes' : 'No')."\n";
        print "Editor? ".($user->isMemberOf('editor') ? 'Yes' : 'No')."\n";
        print "Author? ".($user->isMemberOf('author') ? 'Yes' : 'No')."\n";



        // $manager = new WordpressPermissionsManager();

        // $actual = $manager->verifyPermission(Permissions1Test::TestPermissionName);
        // $this->assert($actual,'Not authorized, or tester not loggedin');

        // $roleName = TStrings::convertNameFormat(Permissions1Test::TestRoleName,TStrings::keyFormat);
        $actual = $user->isMemberOf(Permissions1Test::TestRoleName);
        $this->assert($actual,'Not member of role.');

        $permissionName = TStrings::convertNameFormat(Permissions1Test::TestPermissionName,TStrings::wordCapsFormat);
        $actual = $user->isAuthorized(Permissions1Test::TestPermissionName);
        $this->assert($actual,'Not authorized, or not loggedin');
        print "Authorized ".Permissions1Test::TestPermissionName."? ".($actual ? 'Yes' : 'No')."\n";

        // run permisions3 to clean up
        print "Try other logins and finally run permission3 to cleanup";
    }
}