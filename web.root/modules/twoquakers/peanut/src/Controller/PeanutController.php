<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\pnut_tops\Controller;
use Drupal;
use Peanut\sys\ViewModelPageBuilder;
use PeanutTest\scripts\TestScript;
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

    public function runtest() {
        $request = \Drupal::request();
        $testname = $request->get('testname');
        print "<pre>";
        print "Running $testname\n";
        if (empty($testname)) {
            exit("No test name!");
        }
        $testname = strtoupper(substr($testname,0,1)).substr($testname,1);
        $className = "\\PeanutTest\\scripts\\$testname".'Test';
        /**
         * @var $test TestScript
         */
        $test = new $className();
        $test->run();
        print "</pre>";
        exit;
    }

}