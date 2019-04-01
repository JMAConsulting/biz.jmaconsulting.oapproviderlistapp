<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Experience extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_EXPERIENCE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    return $defaults;
  }

  public function buildQuickForm() {
    $this->buildCustom(OAP_EXPERIENCE, 'experience');
    $this->assign('customDataType', 'Individual');
    $this->assign('customDataSubType', 'Provider');
    $this->assign('entityID', $this->_contactID);

    $this->addFormRule(array('CRM_Oapproviderlistapp_Form_Experience', 'formRule'), $this);
    parent::buildQuickForm();
  }

  public function formRule($fields, $files, $self) {
    $errors = [];
    foreach ($fields['custom_12'] as $key => $value) {
      if (empty($value)) {
        $errors['custom_12'] = E::ts('Experience check list is a required field.');
      }
    }
    return $errors;
  }

  public function postProcess() {
    parent::postProcess();
    $values = $this->controller->exportValues($this->_name);
    if (!empty($this->_contactID)) {
      $params = array_merge($values, ['contact_id' => $this->_contactID]);
      $fields = [];
      CRM_Contact_BAO_Contact::createProfileContact($params, $fields);

      if (!empty($values['_qf_Experience_submit'])) {
        //$sql = sprintf("DELETE FROM %s WHERE entity_id = %d ", OAP_EMP_HIS, $this->_contactID);
        CRM_Core_DAO::executeQuery($sql);
      }
      $customValues = CRM_Core_BAO_CustomField::postProcess($params, $this->_contactID, 'Individual');
      if (!empty($customValues) && is_array($customValues)) {
        CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $this->_contactID);
      }
    }

    if (!empty($values['_qf_Experience_submit_done'])) {
      $values['contact_id'] = $this->_contactID;
      $values['url'] = CRM_Utils_System::url("civicrm/application",
        "selectChild=experience&cid=" . $this->_contactID, TRUE
      );
      $this->sendDraft($values);
    }
    if (!empty($values['_qf_Experience_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application",
        "selectChild=professional&cid=" . $this->_contactID
      ));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
        "selectChild=sectorcheck&cid=" . $this->_contactID
      ));
    }
  }

}
