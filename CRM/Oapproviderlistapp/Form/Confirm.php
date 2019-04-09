<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;
require_once __DIR__ . '/../../../oapproviderlistapp.variables.php';

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Confirm extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public $_contactID;
  public function preProcess() {
    CRM_Utils_System::setTitle(E::ts('OAP PROVIDER LIST CONFIRMATION PAGE'));
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
  }

  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_PROFESSIONAL, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_EXPERIENCE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SECTORCHECK, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INSURANCE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SIGNATURE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    return $defaults;
  }


  public function buildQuickForm() {
    $displayName = CRM_Contact_BAO_Contact::displayName($this->_contactID);
    //$email = civicrm_api3('Email', 'getvalue', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'return' => ['email']]);
    $address = civicrm_api3('Address', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];
    $phone = civicrm_api3('Phone', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];
    $groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', NULL, $this->_contactID, 16, 'Provider');
    $details = CRM_Core_BAO_CustomGroup::buildCustomDataView($this, $groupTree, FALSE, NULL, NULL, NULL, $this->_contactID);
    $otherEmplyeeInformation = [];
    foreach ($details[16] as $fieldID => $info) {
      if (empty($info['fields'])) {
        continue;
      }
      foreach ($info['fields'] as $values) {
        $otherEmplyeeInformation[$fieldID][$info['field_title']] = $info['field_value'];
      }
    }

    $groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', NULL, $this->_contactID, 12, 'Provider');
    $details = CRM_Core_BAO_CustomGroup::buildCustomDataView($this, $groupTree, FALSE, NULL, NULL, NULL, $this->_contactID);
    $otherProfessional = [];
    foreach ($details[12] as $fieldID => $info) {
      if (empty($info['fields'])) {
        continue;
      }
      foreach ($info['fields'] as $values) {
        $otherProfessional[$fieldID][$info['field_title']] = $info['field_value'];
      }
    }

    $groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', NULL, $this->_contactID, 10, 'Provider');
    $details = CRM_Core_BAO_CustomGroup::buildCustomDataView($this, $groupTree, FALSE, NULL, NULL, NULL, $this->_contactID);
    $employers = [];
    foreach ($details[10] as $fieldID => $info) {
      if (empty($info['fields'])) {
        continue;
      }
      foreach ($info['fields'] as $values) {
        $employers[$fieldID][$info['field_title']] = $info['field_value'];
      }
    }

    $this->buildCustom(OAP_PROFESSIONAL, 'professional', TRUE);
    $this->buildCustom(OAP_EXPERIENCE, 'experience', TRUE);
    $this->buildCustom(OAP_SECTORCHECK, 'sectorcheck', TRUE);
    $this->buildCustom(OAP_INSURANCE, 'insurance', TRUE);
    $this->buildCustom(OAP_SIGNATURE, 'signature', TRUE);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
  }

}
