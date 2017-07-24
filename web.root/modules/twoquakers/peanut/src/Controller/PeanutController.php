<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\pnut_tops\Controller;
use Drupal;
use Tops\Drupal8\ServiceRequestInputHandler;
use Tops\services\ServiceFactory;

class PeanutController {
  public function helpcontent() {
    return array(
      '#type' => 'markup',
      '#markup' => t('Watch this space for Peanut Information'),
    );
  }
  public function executeService() {
      $response = ServiceFactory::Execute();
      print json_encode($response);
      exit;
      /*
      $request = \Drupal::request();
      $result = new \stdClass();
      $result->message = 'Hello world';
      print \json_encode($result);
      exit;
      */
  }

  public function getConfigSettings() {
      include(DRUPAL_ROOT.'/application/config/settings.php');
      exit;
  }

}