<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/1/2017
 * Time: 4:01 PM
 */

namespace PeanutTest\scripts;



use Tops\drupal8\TDrupal8User;
use Tops\sys\TUser;

class User2Test extends TestScript
{

    const adminUser = 'admin';
    const testUser = 'mrpeanut';
    const anonymous = 'Guest';

    public function execute()
    {
        /**
         * @var $user TDrupal8User
         */
        $user = TUser::getCurrent();


        print "Current user " . $user->getUserName() . "\n";
        $actual = $user->isCurrent();
        print "Is current user? " . ($actual ? 'Yes' : 'No') . "\n";
        $this->assert($actual, 'not current');
        $currentUserName = $user->getUserName();
        $authenticated = $user->isAuthenticated();
        print "Authenticated? " . ($authenticated ? 'Yes' : 'No') . "\n";
        if (!$authenticated) {
            $expected = ($currentUserName === self::anonymous) ? false : true;
            $this->assertEquals($expected, $actual, 'isAuthenticated failed');
        } else {
            $this->assert($currentUserName !== self::anonymous, 'isAuthenticated failed');
        }

        $isAdmin = $user->isAdmin();
        print "Current is admin? " . ($isAdmin ? 'Yes' : 'No') . "\n";
        if ($isAdmin) {
            $this->assertEquals(self::adminUser, $currentUserName, 'isAdmin failed');
        } else {
            $this->assert(self::adminUser != $currentUserName, 'isAdmin failed');
        }
        print "Current user short name: ".$user->getShortName()."\n";

        $user = TUser::getByUserName(self::testUser);
        $actual = $user->getUserName();
        $this->assertEquals(self::testUser, $actual, 'user name');
        print "Loaded user " . self::testUser . "\n";

        $testUserCurrent = $user->isCurrent();
        print "Is '" . self::testUser . "' current user? " . ($testUserCurrent ? 'Yes' : 'No') . "\n";

        $actual = $user->getEmail();
        $this->assertNotNull($actual, 'Email');
        print "Email: $actual\n";



                $actual = $user->getFullName();
                $this->assertNotNull($actual, 'full name');
                print "Full name: $actual\n";


                $actual = $user->getShortName();
                $this->assertNotNull($actual, 'short name');
                print "Short name: $actual\n";

                $actual = $user->getDisplayName();
                $this->assertNotNull($actual, 'display name');
                print "Display name: $actual\n";

                $actusl = $user->isMemberOf('mail_administrator');
                print "Mail admin? " . ($actual ? 'Yes' : 'No') . "\n";
                $this->assert($actual, 'Not member of mail admin');

                $actual = $user->isAuthorized(TUser::mailAdminPermissionName);
                $this->assert($actual, 'cannot administer mail');
                print "Can administer mail? " . ($actual ? 'Yes' : 'No') . "\n";

                $actual = $user->isAuthorized(TUser::appAdminPermissionName);
                $this->assert($actual, 'Not expected to administer peanut');
                print "Can administer peanut? " . ($actual ? 'Yes' : 'No') . "\n";
    }
}