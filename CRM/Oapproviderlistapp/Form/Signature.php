<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Signature extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public $_last = TRUE;
  public $_contactID;
  public function buildQuickForm() {
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    $this->buildCustom(OAP_SIGNATURE, 'signature');

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SIGNATURE, FALSE, CRM_Core_Action::VIEW);
    CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $this->_contactID, NULL, OAP_SIGNATURE);
    if (!empty($values['_qf_Signature_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application", "selectChild=documentation&cid=" . $this->_contactID));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm"));
    }
  }

}
