<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Professional extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function preProcess() {
    parent::preProcess();
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
  }
  public function buildQuickForm() {
    $this->buildCustom(OAP_PROFESSIONAL, 'professional');
    $this->add('hidden', 'contact_id', $this->_contactID);

    $this->assign('customDataType', 'Individual');
    $this->assign('customDataSubType', 'Provider');

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $contactID = $values['contact_id'];
    if (!empty($values['_qf_Professional_submit_done'])) {
      $this->sendDraft($values);
    }
    $params = array_merge($values, ['contact_id' => $contactID]);
    $fields = [];
    CRM_Contact_BAO_Contact::createProfileContact($params, $fields);

    $customValues = CRM_Core_BAO_CustomField::postProcess($params, $contactID, 'Individual');
    if (!empty($customValues) && is_array($customValues)) {
      CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $contactID);
    }

    if (!empty($values['_qf_Professional_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application"));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=experience&cid=" . $contactID
      ));
    }
  }

}
