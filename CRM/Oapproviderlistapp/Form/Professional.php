<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Professional extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function buildQuickForm() {
    $this->buildCustom(OAP_PROFESSIONAL, 'professional');

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $params = array_merge($values, ['contact_id' => $contactID]);
    $fields = [];
    CRM_Contact_BAO_Contact::createProfileContact($params, $fields);

    if (!empty($values['_qf_Professional_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application"));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=experience"
      ));
    }
  }

}
