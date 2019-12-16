<?php

require_once 'oapproviderlistapp.civix.php';
require_once 'oapproviderlistapp.variables.php';
use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function oapproviderlistapp_civicrm_config(&$config) {
  _oapproviderlistapp_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function oapproviderlistapp_civicrm_xmlMenu(&$files) {
  _oapproviderlistapp_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function oapproviderlistapp_civicrm_install() {
  _oapproviderlistapp_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function oapproviderlistapp_civicrm_uninstall() {
  _oapproviderlistapp_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function oapproviderlistapp_civicrm_enable() {
  _oapproviderlistapp_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function oapproviderlistapp_civicrm_disable() {
  _oapproviderlistapp_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function oapproviderlistapp_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _oapproviderlistapp_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function oapproviderlistapp_civicrm_managed(&$entities) {
  _oapproviderlistapp_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function oapproviderlistapp_civicrm_caseTypes(&$caseTypes) {
  _oapproviderlistapp_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function oapproviderlistapp_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _oapproviderlistapp_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_permission
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_permission
 */
function oapproviderlistapp_civicrm_permission(&$permissions) {
  $prefix = ts('CiviCRM') . ': ';
  $permissions['edit my listing'] = array(
    $prefix . ts('edit my listing'),
    ts('Edit own Provider Listing'),
  );
}

function oapproviderlistapp_civicrm_pageRun(&$page) {
  if (get_class($page) == "CRM_Profile_Page_Dynamic" && ($page->getVar('_gid') == OAPPROVIDERLIST)) {
    CRM_Core_Resources::singleton()->addScript(
      "CRM.$(function($) {
        $('div.action-link a').hide();
      });"
    );
  }
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function oapproviderlistapp_civicrm_buildForm($formName, &$form) {
  if ($formName == "CRM_Profile_Form_Edit" && $form->getVar('_gid') == OAPPROVIDERLIST) {
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/ProviderList.tpl',
    ));
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'templates/css/style.css');
    $submittedValues = [];

    // Get fields for custom groups.
    $customGroups = [
      "Post_Secondary_Education",
      "Employment_History",
    ];
    foreach ($customGroups as $group) {
      $result = civicrm_api3('CustomField', 'get', [
        'sequential' => 1,
        'custom_group_id' => $group,
      ])['values'];
      for ($rowNumber = 0; $rowNumber <= 5; $rowNumber++) {
        foreach ($result as $field => $value) {
          $name = sprintf("%s[%d]", "field_custom_" . $value['id'], $rowNumber);
          if (!empty($_POST["field_custom_" . $value['id']]) && !empty($_POST["field_custom_" . $value['id']][$rowNumber])) {
            $submittedValues[$group][] = $rowNumber;
          }
          $form->add(strtolower($value['html_type']), $name, E::ts("%1", [1 => $value['label']]), NULL);
          if (in_array($value['label'], ["Year Completed", "Total number of hours", "Number of hours that involved supervisory duties"])) {
            $form->addRule($name, E::ts('%1 must be a number.', [1 => $value['label']]), 'numeric');
          }
        }
      }
    }
    $form->assign('customSubmitted', json_encode($submittedValues));
    $form->assign('educationField', 'field_' . EDUCATION);
    $form->assign('degreeField', 'field_' . DEGREE);
    $form->assign('yearField', 'field_' . YEAR);

    $form->assign('orgField', 'field_' . ORG);
    $form->assign('titleField', 'field_' . TITLE);
    $form->assign('datesField', 'field_' . DATES);
    $form->assign('tasksField', 'field_' . TASKS);
    $form->assign('hoursField', 'field_' . TOTAL_HOURS);
    $form->assign('superHoursField', 'field_' . SUPER_HOURS);
    $form->assign('superContactField', 'field_' . SUPER_CONTACT);
  }
  if ($formName == "CRM_Contact_Form_Search_Custom" && $form->getVar('_customSearchClass') == 'CRM_Oapproviderlistapp_Form_Search_ProviderList') {
    $form->addButtons([
      [
        'type' => 'refresh',
        'name' => ts('Search by Provider'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'refresh',
        'subName' => 'savenext',
        'name' => ts('Search By Organization'),
      ],
    ]);
  }
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @param string $formName
 * @param array $fields
 * @param array $files
 * @param CRM_Core_Form $form
 * @param array $errors
 */
function oapproviderlistapp_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  if ($form->getVar('_gid') != OAPPROVIDERLIST) {
    return;
  }
}

/**
 * Implementation of hook_civicrm_postProcess
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function oapproviderlistapp_civicrm_postProcess($formName, &$form) {
  if ($formName == "CRM_Profile_Form_Edit" && $form->getVar('_gid') == OAPPROVIDERLIST) {
    $params = $form->_submitValues;
    $contactID = $form->getVar('_id');
    foreach ($params as $field => $value) {
      if (strpos($field, 'field_custom_') !== false) {
        foreach ($value as $key => $val) {
          if (empty($val)) {
            continue;
          }
          $customField = substr($field, 6);
          $customValues[$customField . '_-' . $key] = $val;
        }
      }
    }
    if (empty($customValues)) {
      return;
    }
    CRM_Core_BAO_CustomValueTable::postProcess($customValues,
      'civicrm_contact',
      $contactID,
      'Individual'
    );
  }
  if ($formName == "CRM_Contact_Form_Contact") {
    if (!empty($form->_contactId) && count(preg_grep('/^custom_60_[\d]*/', array_keys($form->_submitValues))) > 0) {
      setMembership($form->_contactId, $form->_submitValues);
    }
  }
  if ($formName == "CRM_Contact_Form_Inline_CustomData") {
    if (!empty($form->_submitValues['cid']) && count(preg_grep('/^custom_60_[\d]*/', array_keys($form->_submitValues))) > 0) {
      setMembership($form->_submitValues['cid'], $form->_submitValues);
    }
  }
}

function setMembership($cid, $submitValues) {
  if (!empty($cid)) {
    $oldStatus = NULL;
    $oldStatus = civicrm_api3('Contact', 'getvalue', ['return' => "custom_60", 'id' => $cid]);
    $submitKeys = array_keys($submitValues);
    $key = preg_grep('/^custom_60_[\d]*/', $submitKeys);
    $newStatus = reset($key);
    if (CRM_Utils_Array::value($newStatus, $submitValues) == 'Approved') {
      civicrm_api3('Membership', 'create', [
        'membership_type_id' => "OAP Clinical Supervisor Provider",
        'contact_id' => $cid,
        'start_date' => date('Ymd'),
      ]);
      // Create drupal account if not exists.
      createCMSAccount($cid);

      $activityID = civicrm_api3('Activity', 'get', [
        'source_contact_id' => $cid,
        'activity_type_id' => "Provider List Application Submission",
        'status_id' => 'Scheduled',
        'sequential' => 1,
      ])['values'][0]['id'];
      if (!empty($activityID)) {
        civicrm_api3('Activity', 'create', [
          'id' => $activityID,
          'status_id' => 'Completed',
        ]);
      }
      civicrm_api3('Contact', 'create', [
        'id' => $cid,
        'is_deleted' => FALSE,
        'custom_65' => 1,
      ]);
    }
    elseif (CRM_Utils_Array::value($newStatus, $submitValues) == 'Cancelled') {
      $membershipID = civicrm_api3('Membership', 'get', [
        'membership_type_id' => "OAP Clinical Supervisor Provider",
        'contact_id' => $cid,
      ])['id'];
      if ($membershipID) {
        civicrm_api3('Membership', 'create', [
          'id' => $membershipID,
          'status_id' => 'Cancelled',
        ]);
      }
    }
    if ($oldStatus) {
      $activityID = civicrm_api3('Activity', 'create', [
        'source_contact_id' => $cid,
        'activity_type_id' => "Provider Status Changed",
        'subject' => sprintf("Application status changed to %s", $oldStatus),
        'activity_status_id' => 'Completed',
        'details' => '<a class="action-item crm-hover-button" href="https://oapproviderlist.ca/civicrm/application/confirm?cid=' . $cid . '&mode=embedded">View Applicant</a>',
        'target_id' => $cid,
        'assignee_id' => 99184,
      ])['id'];
    }
  }
}

function createCMSAccount($cid) {
  require_once 'CRM/CU/Form/Task/CreateUserLogin.php';
  if (!CRM_CU_Form_Task_CreateUserLogin::usernameRule($cid)) {
    $name = CRM_Core_DAO::executeQuery("SELECT LOWER(CONCAT(first_name, '.', COALESCE(last_name, $cid))) as name, display_name FROM civicrm_contact WHERE id = %1", [1 => [$cid, "Integer"]])->fetchAll()[0];
    $name1 = CRM_Core_DAO::executeQuery("SELECT LOWER(CONCAT(first_name, '.', last_name)) as name, display_name FROM civicrm_contact WHERE id != %1", [1 => [$cid, "Integer"]])->fetchAll()[0];
    if (!empty($name1['name']) && $name['name'] == $name1['name']) {
      $name['name'] .= $cid;
    }
    $params = [
      'cms_name' => $name['name'],
      'cms_pass' => 'changeme',
      'cms_confirm_pass' => 'changeme',
      'email' => CRM_Core_DAO::singleValueQuery("SELECT email FROM civicrm_email WHERE contact_id = %1 AND is_primary = 1", [1 => [$cid, "Integer"]]),
      'contactID' => $cid,
      'name' => $name['display_name'],
      'notify' => TRUE,
    ];
    $ufId = CRM_Core_BAO_CMSUser::create($params, 'email');
    $user = user_load($ufId);
    if ($user) {
      $user->addRole('provider');
      $user->save();
    }
  }
}
