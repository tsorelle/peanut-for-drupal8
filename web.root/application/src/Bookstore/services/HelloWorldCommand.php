<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 5/15/2017
 * Time: 5:02 PM
 */

namespace Bookstore\services;


class HelloWorldCommand extends \Tops\services\TServiceCommand
{
    protected function run()
    {
        $request = $this->getRequest();
        if (empty($request)) {
            $this->addErrorMessage('No request received.');
            return;
        }
        if (empty($request->tester)) {
            $this->addErrorMessage('Tester name not received.');
            return;
        }
        $this->addInfoMessage('Hello World from: '.$request->tester);
        $responseValue = new \stdClass();
        $responseValue->message = "Greatings earthlings.";
        $this->setReturnValue($responseValue);
    }
}