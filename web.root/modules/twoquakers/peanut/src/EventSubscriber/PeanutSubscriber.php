<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/10/2017
 * Time: 6:59 AM
 */
namespace Drupal\pnut_tops\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tops\drupal8\ViewModel;

class PeanutSubscriber implements EventSubscriberInterface {

    private static $peanutInitialized = false;

    public function onRequest(GetResponseEvent $event) {
        if (!self::$peanutInitialized) {
            include_once(DRUPAL_ROOT . "/application/config/peanut-bootstrap.php");
            \Peanut\Bootstrap::initialize(DRUPAL_ROOT);
            session_start();
            \Tops\sys\TSession::Initialize();

            /**
             * @var
             */
            global $request;
            ViewModel::Initialize($request);

            self::$peanutInitialized = TRUE;

        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        $events[KernelEvents::REQUEST][] = array('onRequest');
        return $events;
    }

}