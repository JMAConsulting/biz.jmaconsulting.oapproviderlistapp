<?php
define('OAPPROVIDERLIST', 14);

// Education Custom Group
define('EDUCATION', 'custom_28');
define('DEGREE', 'custom_29');
define('YEAR', 'custom_30');

// Employment History
define('ORG', 'custom_32');
define('TITLE', 'custom_33');
define('DATES', 'custom_34');
define('TASKS', 'custom_35');
define('TOTAL_HOURS', 'custom_36');
define('SUPER_HOURS', 'custom_37');
define('SUPER_CONTACT', 'custom_38');

require_once 'oapproviderlistapp.civix.php';

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
          $form->add(strtolower($value['html_type']), $name, ts($value['label']), NULL);
          if (strpos($value['label'], 'Year Completed') !== false) {
            $form->addRule($name, ts($value['label'] . ' must be a number.'), 'numeric');
          }
        }
      }
    }
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
    CRM_Core_BAO_CustomValueTable::postProcess($customValues,
      'civicrm_contact',
      $contactID,
      'Individual'
    );
  }
}
