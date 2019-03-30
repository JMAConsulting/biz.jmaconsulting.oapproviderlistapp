<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Documentation extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public $_contactID;
  public function buildQuickForm() {
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    $this->buildCustom(OAP_DOCUMENTATION, 'documentation');

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_DOCUMENTATION, FALSE, CRM_Core_Action::VIEW);
    CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $this->_contactID, NULL, OAP_DOCUMENTATION);
    if (!empty($values['_qf_Documentation_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application", "selectChild=insurance&cid=" . $this->_contactID));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=signature&cid=" . $this->_contactID
      ));
    }
  }

}
