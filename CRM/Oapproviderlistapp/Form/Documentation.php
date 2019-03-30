<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Documentation extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function buildQuickForm() {
    $this->buildCustom(OAP_DOCUMENTATION, 'documentation');

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    if (!empty($values['_qf_Documentation_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/insurance"));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=signature"
      ));
    }
  }

}
