<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Confirm extends CRM_Core_Form {
  public $_contactID;
  public function preProcess() {
    CRM_Utils_System::setTitle(E::ts('OAP PROVIDER LIST CONFIRMATION PAGE'));
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    parent::preProcess();
  }

  public function buildQuickForm() {
    $displayName = CRM_Contact_BAO_Contact::displayName($this->_contactID);
    //$email = civicrm_api3('Email', 'getvalue', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'return' => ['email']]);
    $address = civicrm_api3('Address', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];
    $phone = civicrm_api3('Phone', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];
    $groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', NULL, $this->_contactID, 16);
    $details = CRM_Core_BAO_CustomGroup::buildCustomDataView($this, $groupTree, FALSE, NULL, NULL, NULL, $this->_contactID);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
  }

}
