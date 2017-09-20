<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/5/2017
 * Time: 10:38 AM
 */

use PHPUnit\Framework\TestCase;
use Tops\sys\TObjectContainer;

class MailerTest extends TestCase
{
    private $enabled;


    private function checkTestsDisabled() {
        if (!isset($this->enabled)) {
            $this->enabled = @mail('test@foo.com', 'Testing server availability', 'Ok');
            if (!$this->enabled) {
                print 'Warning: Test mail server is not running. Tests disabled. Need to run a dummy smtp server (such as smtp4dev) for these tests. ' . "\n";
                $this->assertTrue(true);
            }
        }
        return (!$this->enabled);
    }

    public function testMailer() {
        if ($this->checkTestsDisabled()) {
            return;
        }
        $msg = new \Tops\mail\TEMailMessage();
        $msg->addRecipient('tls@2quakers.net','Terry SoRelle');
        $msg->setSubject('Test message');
        $msg->setMessageBody('Hello world');
        $msg->setFromAddress('admin@foo.com','Administrator');

        /**
         * @var $mailer \Tops\mail\IMailer
         */
        $mailer = TObjectContainer::Get('tops.mailer');
        $this->assertNotNull($mailer);
        $result = $mailer->send($msg);
        $this->assertTrue($result);
    }
}
