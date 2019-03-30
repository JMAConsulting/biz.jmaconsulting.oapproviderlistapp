<?php

require_once 'oapproviderlistapp.civix.php';
require_once 'oapproviderlistapp.variables.php';

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
          $form->add(strtolower($value['html_type']), $name, ts($value['label']), NULL);
          if (in_array($value['label'], ["Year Completed", "Total number of hours", "Number of hours that involved supervisory duties"])) {
            $form->addRule($name, ts($value['label'] . ' must be a number.'), 'numeric');
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
  foreach ($fields[SIGNATURE] as $key => $val) {
    if (empty($val)) {
      $errors[SIGNATURE] = ts('Your consent is required for all statements');
    }
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
}
