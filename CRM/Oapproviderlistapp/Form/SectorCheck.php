<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_SectorCheck extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SECTORCHECK, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    return $defaults;
  }

  public function buildQuickForm() {
    $this->buildCustom(OAP_SECTORCHECK, 'sectorcheck');

    parent::buildQuickForm();
  }

  public function postProcess() {
    parent::postProcess();
    $values = $this->controller->exportValues($this->_name);
    if (!empty($this->_contactID)) {
      $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SECTORCHECK, FALSE, CRM_Core_Action::VIEW);
      CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $this->_contactID, NULL, OAP_SECTORCHECK);
    }
    if (CRM_Utils_Array::value('_qf_SectorCheck_submit_done', $this->exportValues())) {
      $values['contact_id'] = $this->_contactID;
      $values['url'] = CRM_Utils_System::url("civicrm/application",
        "selectChild=sectorcheck&cid=" . $this->_contactID, TRUE
      );
      $this->sendDraft($values);
    }
    elseif (CRM_Utils_Array::value('_qf_SectorCheck_submit', $this->exportValues())) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application", "selectChild=experience&cid=" . $this->_contactID));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=insurance&cid=" . $this->_contactID
      ));
    }
  }

}
