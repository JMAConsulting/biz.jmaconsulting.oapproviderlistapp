<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Insurance extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INSURANCE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    return $defaults;
  }
  public function buildQuickForm() {
    $this->buildCustom(OAP_INSURANCE, 'insurance');

    parent::buildQuickForm();
  }

  public function postProcess() {
    parent::postProcess();
    $values = array_merge($this->_submitValues, $this->_submitFiles);
    if (!empty($this->_contactID)) {
      $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INSURANCE, FALSE, CRM_Core_Action::VIEW);
      CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $this->_contactID, NULL, OAP_INSURANCE);
    }
    if (!empty($values['_qf_Insurance_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application", "selectChild=experience&cid=" . $this->_contactID));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=signature&cid=" . $this->_contactID
      ));
    }
  }

}
