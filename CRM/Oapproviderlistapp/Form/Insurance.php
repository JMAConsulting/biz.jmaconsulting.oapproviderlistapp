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
    if (!empty($this->_contactID)) {
      $file = $this->getFileUpload($this->_contactID, 'civicrm_value_professional__7', 'professional_liability_insurance_57', 57);
      $this->assign('custom_57_file', $file);
    }

    parent::buildQuickForm();
  }

  public function postProcess() {
    parent::postProcess();
    $values = $this->controller->exportValues($this->_name);

    if (!empty($this->_contactID)) {
      $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INSURANCE, FALSE, CRM_Core_Action::VIEW);
      CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $this->_contactID, NULL, OAP_INSURANCE);
    }
    if (CRM_Utils_Array::value('_qf_Insurance_submit_done', $this->exportValues())) {
      $this->sendDraft($this->_contactID);
    }
    elseif (CRM_Utils_Array::value('_qf_Insurance_submit', $this->exportValues())) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/experience", "selectChild=experience&cid=" . $this->_contactID));
    }
    else {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/signature", "cid=" . $this->_contactID));
    }
  }

}
