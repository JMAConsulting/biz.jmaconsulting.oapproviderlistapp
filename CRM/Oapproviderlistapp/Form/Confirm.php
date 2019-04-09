<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Confirm extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function buildQuickForm() {
    $displayName = CRM_Contact_BAO_Contact::displayName($this->_contactID);
    $email = civicrm_api3('Email', 'getvalue', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'return' => ['email']]);
    $address = civicrm_api3('Address', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];
    $phone = civicrm_api3('Phone', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];


    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues()
    parent::postProcess();
  }

}
