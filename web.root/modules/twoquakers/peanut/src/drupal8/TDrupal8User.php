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
use Tops\db\TDBPermissionsManager;
use Tops\sys\IUser;
use Tops\sys\TAbstractUser;
use Tops\sys\TStrings;
use Tops\sys\TUser;

/**
 * Class TConcrete5User
 * @package Tops\sys
 *
 */
class TDrupal8User extends TAbstractUser
{
    /**
     * @var AccountInterface
     */
    private $drupalUser;
    private $userEntity;
    private $fieldDefinitions;

    protected function loadDrupalUser(AccountInterface $account = null)
    {
        unset ($this->isCurrentUser);
        if ($account == null) {
            unset($this->drupalUser);
            unset($this->drupalEntity);
            return;
        }

        $this->drupalUser = $account;

        // todo: this may not work in Drupal 8
        if ($_SERVER['SCRIPT_NAME'] === '/cron.php') {
            $this->authenticated = true;
            $this->userName = 'cron';
            $this->isCurrentUser = true;
            return;
        }

        $this->userName = $account->getAccountName();//  getUsername();
        if (empty($this->userName)) {
            $this->userName = TUser::DefaultUserName;
        }
        $this->id = $account->id();

    }




    /**
     * @param $email
     * @return void
     */
    public function loadByEmail($email)
    {
        $account = user_load_by_mail($email);
        $this->loadDrupalUser($account);
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
        $this->isCurrentUser = true;
    }

    /**
     * @param AccountInterface $user
     *
     * Assign firstName, lastName, middleName and pictureFile
     *
     * This version for Drupal 7 - might work with 8, we'll see...
     */
    protected function loadProfile()
    {
        // $this->profile['email'] = $this->drupalUser->get('mail')->value;
        $this->userEntity =\Drupal\user\Entity\User::load($this->getId());
        $this->fieldDefinitions =  $this->userEntity->getFieldDefinitions();
        $email = $this->getEntityValue('mail',false);
        $this->profile[TUser::profileKeyEmail] = $email;
    }

    public function getProfileValue($key)
    {
        $result = parent::getProfileValue($key);
        if ($result == false) {
            $result = $this->getEntityValue($key);
        }
        return empty($result) ? '' : $result;
    }

    private function getEntityValue($key,$custom=true) {
        $result = '';
        $key = TUser::getProfileFieldKey($key); // TStrings::convertNameFormat($key,TStrings::keyFormat);
        if ($custom) {
            $key = "field_$key";
        }
        if (array_key_exists($key,$this->fieldDefinitions)) {
            $field = $this->userEntity->get($key);
            if (is_object($field)) {
                $value = $field->getValue();
                if (is_array($value) && !empty($value[0]['value'])) {
                    $result = $value[0]['value'];
                }
            }
        }
        return $result;
    }

    private $isAdminUser;
    /**
     * @return bool
     */
    public function isAdmin()
    {
        if (!isset($this->isAdminUser)) {
            if ($this->drupalUser) {
                $roles = $this->drupalUser->getRoles();
                $this->isAdminUser = in_array(Drupal8Roles::administratorRole, $roles);
            } else {
                $this->isAdminUser = false;
            }
        }
        return $this->isAdminUser;
    }

    public function isCurrent()
    {
        if (!isset($this->isCurrentUser)) {
            $current = Drupal::currentUser();
            $this->isCurrentUser = ($this->getId() === $current->id());
        }
        return $this->isCurrentUser;
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
        $result = parent::isMemberOf($roleName);
        if (!$result) {
            if ($this->drupalUser) {
                $roleName = Drupal8Roles::formatRoleName($roleName);
                $roles = $this->drupalUser->getRoles();
                return in_array($roleName,$roles);
            }
        }
        return $result;
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


   public function isAuthorized($permissionName = '')
    {
        $authorized = parent::isAuthorized($permissionName);
        if (!$authorized) {
            $permissionHandle = TStrings::convertNameFormat($permissionName,Drupal8PermissionsManager::permisionHandleFormat);
            if (!$authorized) {
                $authorized = $this->drupalUser->hasPermission($permissionHandle);
            }
        }
        return $authorized;
    }


    /**
     * @param $account
     * @param $fieldName
     * @param bool $safeValue
     * @return bool
     * @deprecated Drupal 7
     */
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
     * @return array|bool|mixed|string
     * @deprecated Drupal 7
     */
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


}