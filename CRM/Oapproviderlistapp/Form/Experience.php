<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Experience extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function buildQuickForm() {
    $this->buildCustom(OAP_EXPERIENCE, 'experience');
    $this->assign('customDataType', 'Individual');
    $this->assign('groupID', OAP_EXPERIENCE_CGID);
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $params = array_merge($values, ['contact_id' => $this->_contactID]);
    $fields = [];
    CRM_Contact_BAO_Contact::createProfileContact($params, $fields);

    $customValues = CRM_Core_BAO_CustomField::postProcess($params, $this->_contactID, 'Individual');
    if (!empty($customValues) && is_array($customValues)) {
      CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $this->_contactID);
    }

    if (!empty($values['_qf_Experience_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/professional"));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=sectorcheck"
      ));
    }
  }

}
