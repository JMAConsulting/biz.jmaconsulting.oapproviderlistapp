<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Signature extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public $_last = TRUE;
  public function buildQuickForm() {
    $this->buildCustom(OAP_SIGNATURE, 'signature');

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    if (!empty($values['_qf_Signature_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/documentation"));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm"));
    }
  }

}
