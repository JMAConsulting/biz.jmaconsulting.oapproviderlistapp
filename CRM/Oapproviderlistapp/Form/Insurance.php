<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Insurance extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function buildQuickForm() {
    $this->buildCustom(OAP_INSURANCE, 'insurance');

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    if (!empty($values['_qf_Insurance_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/sectorcheck"));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=documentation"
      ));
    }
  }

}
