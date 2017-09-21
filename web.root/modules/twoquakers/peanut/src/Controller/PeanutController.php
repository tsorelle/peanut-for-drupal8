<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\pnut_tops\Controller;
use Drupal;
use Peanut\sys\ViewModelPageBuilder;
use PeanutTest\scripts\TestScript;
use Tops\drupal8\Drupal8PermissionsManager;
use Tops\Drupal8\ServiceRequestInputHandler;
use Tops\services\ServiceFactory;
use Tops\sys\TObjectContainer;

class PeanutController
{
    public function helpcontent()
    {
        return array(
            '#type' => 'markup',
            '#markup' => t('Watch this space for Peanut Information'),
        );
    }

    public function executeService()
    {
        $response = ServiceFactory::Execute();
        print json_encode($response);
        exit;
    }

    public function getConfigSettings()
    {
        include(DRUPAL_ROOT . '/application/config/settings.php');
        exit;
    }

    public function renderViewModel()
    {
        $request = \Drupal::request();
        $vmname = $request->get('vmname');
        $content = ViewModelPageBuilder::Build($vmname);
        print $content;
        exit;
    }

    public function runtest()
    {
        $request = \Drupal::request();
        $testname = $request->get('testname');
        print "<pre>";
        print "Running $testname\n";
        if (empty($testname)) {
            exit("No test name!");
        }
        $testname = strtoupper(substr($testname, 0, 1)) . substr($testname, 1);
        $className = "\\PeanutTest\\scripts\\$testname" . 'Test';
        set_error_handler(null);
        set_exception_handler(null);
        try {
            /**
             * @var $test TestScript
             */
            $test = new $className();
            $test->run();
        } catch (\Exception $ex) {
            print "Exception occured: " . $ex->getMessage() . ' ' . $ex->getFile() . ': ' . $ex->getLine();
        }
        print "</pre>";
        exit;
    }

    public function getPermissionsList()
    {
        if (!TObjectContainer::HasDefinition('tops.permissions')) {
            return [];
        }

        /**
         * @var $manager Drupal8PermissionsManager
         */
        $manager = TObjectContainer::Get('tops.permissions');
        return $manager->getPermissionsList();

        /*
        $permissions = [];
        $permissions['manage-mailboxes'] =
            ['title' => 'Manage mailboxes', 'description' => 'Manage Peanut Mailbox list.'];

        return $permissions;
        */
    }


}