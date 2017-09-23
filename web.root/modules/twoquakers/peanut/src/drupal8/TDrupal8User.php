<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/30/2017
 * Time: 7:17 AM
 */

namespace Tops\drupal8;

use Drupal;
use Drupal\Core\Session\AccountInterface;
use Masterminds\HTML5\Exception;
use Tops\cache\ITopsCache;
use Tops\cache\TSessionCache;
use Tops\sys\IUser;
use Tops\sys\TAbstractUser;

/**
 * Class TConcrete5User
 * @package Tops\sys
 *
 */
class TDrupal8User extends TAbstractUser
{
    protected function test() {
        return 'drupal';
    }


    function __construct(AccountInterface $user = null) {
        if (isset($user)) {
            $this->loadDrupalUser($user);
        }
        $result = $this->test();
    }

    /**
     * @var AccountInterface
     */
    private   $drupalUser;



    protected function loadDrupalUser(AccountInterface $account = null)
    {
        $test = $this->test();
        $this->drupalUser = $account;
        if (empty($account)) {
            return;
        }

        // todo: this may not work in Drupal 8
        if ($_SERVER['SCRIPT_NAME'] === '/cron.php') {
            $this->authenticated = true;
            $this->userName = 'cron';
            $this->isCurrentUser = true;
            return;
        }

        $currentUser = Drupal::currentUser();

        if ($account == null) {
            $this->drupalUser = $account;
            $this->isCurrentUser = true;
        } else {
            $this->isCurrentUser = $account->id() == $currentUser->id();
        }


        if ($account) {
            $this->email = $account->getEmail();
            $this->userName = $account->getUsername();
            $this->id = $account->id();
        }

    }

    /**
     * @var ITopsCache
     */
    private static $profileCache;

    /**
     * @return ITopsCache
     */
    public static function getProfileCache()
    {
        if (!isset(self::$profileCache)) {
            self::$profileCache = new TSessionCache();
        }
        // self::$profileCache->FlushCachedItems();
        return self::$profileCache;
    }

    private function getCachedProfile() {
        $cache = self::getProfileCache();
        $result = $cache->Get('users.'.$this->userName);
        return $result;
    }

    private function cacheProfile() {
        $cache = self::getProfileCache();
        $result = $cache->Set('users.'.$this->userName,$this->profile,20);
        return $result;
    }

    public function getContentTypes() {
        // todo: review and test before use in Drupal8
        $result = $this->getProfileValue('user-content-types');
        if (!$result) {
            $types = node_type_get_types();
            $result = array();
            foreach($types as $type) {
                if (!$type->disabled) {
                    $permission =  $type->type == 'book' ? 'create new books' : 'create '.$type->type." content";
                    if ($this->isAuthorized($permission)) {
                        $result[$type->type] = $type;
                    }
                }
            }
            $this->setProfileValue('user-content-type',$result);
        }
        return $result;
    }



    /**
     * @param AccountInterface $user
     *
     * Assign firstName, lastName, middleName and pictureFile
     *
     * This version for Drupal 7 - might work with 8, we'll see...
     */
    protected function loadProfile() {
        // todo: Revise for Drupal 8
        $this->profile = $this->getCachedProfile();
        if (empty($this->profile)) {
            $this->profile = array();
            $drupalUser = user_load($this->getId());
            if ($drupalUser != null) {
                $vars = get_object_vars($drupalUser);
                $keys = array_keys($vars);
                foreach ($keys as $key) {
                    if (substr($key, 0, 6) === "field_") {
                        $value = $this->getSingleFieldValue($drupalUser,$key);
                        if ($value !== false) {
                            $fieldName = substr($key, 6);
                            if ($fieldName == 'full_name' && !empty($value)) {
                                // as expected by TAbstract user
                                $fieldName = 'fullName';
                                $this->profile['shortName'] = $value;
                            }
                            $this->profile[$fieldName] = $value;
                        }
                    }
                }
            }
            $this->cacheProfile();
        }
    }

    private function getSingleFieldValue($account,$fieldName,$safeValue=true) {
        // todo: Subroutine for profile management. Revise or remove for Drupal 8
        $items = field_get_items('user',$account,$fieldName);
        if (!empty($items)) {
            $valueIndex = $safeValue ? 'safe_value' : 'value';
            $hasValue = isset($items[0][$valueIndex]);
            if ($safeValue && !$hasValue) {
                $valueIndex = 'value';
                $hasValue = isset($items[0][$valueIndex]);
            }
            if ($hasValue) {
                return $items[0][$valueIndex];
            }
        }
        return false;
    }


    /**
     * @param $id
     * @return void
     */
    public function loadById($id)
    {
        // for Drupal 8
        $drupalAccount = \Drupal\user\Entity\User::load($id);
        // for Drupal 7
        // $drupalAccount = TDrupalAccount::GetById($id);

        $this->loadDrupalUser($drupalAccount);
    }

    /**
     * @param $userName
     * @return void
     */
    public function loadByUserName($userName)
    {
        $account = user_load_by_name($userName);
        $this->loadDrupalUser($account);
    }

    /**
     * @return void
     */
    public function loadCurrentUser()
    {
        $account = Drupal::currentUser();
        $this->loadDrupalUser($account);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->drupalUser) {
            return ($this->isMemberOf('administrator'));
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        if ($this->drupalUser) {
            return $this->drupalUser->getRoles();
        }
        return array();
    }

    /**
     * @param $roleName
     * @return bool
     */
    public function isMemberOf($roleName)
    {
        if ($this->drupalUser) {
            $roleName = Drupal8Roles::roleNameToMachineName($roleName);
            $roles = $this->drupalUser->getRoles();
            return in_array($roleName,$roles);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        if ($this->drupalUser) {
            return $this->drupalUser->isAuthenticated();
        }
        return false;
    }


    /**
     * @param $email
     * @return mixed
     */
    public function loadByEmail($email)
    {
        // TODO: Implement loadByEmail() method.
        throw new Exception("Method 'loadbyEmail not implemented.");
    }
}