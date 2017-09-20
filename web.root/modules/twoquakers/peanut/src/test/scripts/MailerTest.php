<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/8/2017
 * Time: 7:21 AM
 */

namespace PeanutTest\scripts;




use Tops\mail\TPhpMailer;
use Tops\sys\TObjectContainer;

class MailerTest extends TestScript
{
    public function execute()
    {
        $msg = new \Tops\mail\TEMailMessage();
        $this->assertNotNull($msg, 'no message class');
        $msg->addRecipient('tls@2quakers.net', 'Terry SoRelle');
        $msg->setSubject('Test message');
        $msg->setMessageBody('Hello world');
        $msg->setFromAddress('admin@foo.com', 'Administrator');
        $msg->setReturnAddress('bounce@foo.com');
        $msg->addAttachment('testfile');

        /**
         * @var $mailer TPhpMailer
         */
        $mailer = TObjectContainer::Get('tops.mailer');

        $result = $mailer->send($msg);
        $this->assert($result,'Mail send failed.');

    }
}