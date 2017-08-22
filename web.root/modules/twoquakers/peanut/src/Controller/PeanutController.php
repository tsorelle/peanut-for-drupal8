<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\pnut_tops\Controller;
use Drupal;
use Peanut\sys\ViewModelPageBuilder;
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
  }

  public function getConfigSettings() {
      include(DRUPAL_ROOT.'/application/config/settings.php');
      exit;
  }

  public function renderViewModel() {
      $request = \Drupal::request();
      $vmname = $request->get('vmname');
      $content = ViewModelPageBuilder::Build($vmname);
      print $content;
      exit;
  }

}