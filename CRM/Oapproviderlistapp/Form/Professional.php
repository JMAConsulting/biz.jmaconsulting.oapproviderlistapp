<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Professional extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_PROFESSIONAL, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    return $defaults;
  }

  public function buildQuickForm() {
    $this->buildCustom(OAP_PROFESSIONAL, 'professional');

    $this->assign('customDataType', 'Individual');
    $this->assign('customDataSubType', 'Provider');
    $this->assign('entityID', $this->_contactID);

    $this->addFormRule(array('CRM_Oapproviderlistapp_Form_Professional', 'formRule'), $this);
    parent::buildQuickForm();
  }

  public function formRule($fields, $files, $self) {
    if (!empty($fields['_qf_Professional_submit_done'])) {
      return TRUE;
    }
    $errors = [];
    if (count(array_filter($fields["custom_7"])) == 0) {
      $errors['custom_7'] = E::ts("At least one credential must be selected.");
    }
    foreach ($fields['custom_7'] as $key => $val) {
      if (!empty($val)) {
        switch ($key) {
          case 1:
            if (empty($fields['custom_8'])) {
              $errors['custom_8'] = E::ts("Please provide Certification Date for this credential.");
            }
            if (empty($fields['custom_40'])) {
              $errors['custom_40'] = E::ts("Please provide Certification Number for this credential.");
            }
          break;
          case 2:
            if (empty($fields['custom_9'])) {
              $errors['custom_9'] = E::ts("Please provide Certification Date for this credential.");
            }
            if (empty($fields['custom_41'])) {
              $errors['custom_41'] = E::ts("Please provide Certification Number for this credential.");
            }
          break;
          case 3:
            if (empty($fields['custom_10'])) {
              $errors['custom_10'] = E::ts("Please provide Registration Date for this credential.");
            }
            if (empty($fields['custom_42'])) {
              $errors['custom_42'] = E::ts("Please provide Registration Number for this credential.");
            }
          break;
          case 4:
            if (empty($fields['custom_11'])) {
              $errors['custom_11'] = E::ts("Please provide Registration Date for this credential.");
            }
            if (empty($fields['custom_43'])) {
              $errors['custom_43'] = E::ts("Please provide Registration Number for this credential.");
            }
          break;
        }
      }
    }
    return $errors;
  }

  public function postProcess() {
    parent::postProcess();
    $values = $this->controller->exportValues($this->_name);
    $this->processCustomValue($values);
    $this->processCustomValue($this->_submitValues);
    if (!empty($this->_contactID)) {
      $params = array_merge($values, ['contact_id' => $this->_contactID]);
      $fields = [];
      CRM_Contact_BAO_Contact::createProfileContact($params, $fields);

      if (CRM_Utils_Array::value('_qf_Professional_submit', $this->exportValues())) {
        $sql = sprintf("DELETE FROM %s WHERE entity_id = %d ", OAP_OTHER_PRO, $this->_contactID);
        //CRM_Core_DAO::executeQuery($sql);
      }
      $customValues = CRM_Core_BAO_CustomField::postProcess($this->_submitValues, $this->_contactID, 'Individual');
      if (!empty($customValues) && is_array($customValues)) {
        CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $this->_contactID);
      }
    }
    if (CRM_Utils_Array::value('_qf_Professional_submit_done', $this->exportValues())) {
      $this->sendDraft($this->_contactID);
    }
    elseif (CRM_Utils_Array::value('_qf_Professional_submit', $this->exportValues())) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/individual", "reset=1&cid=" . $this->_contactID));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/experience",
        "reset=1&cid=" . $this->_contactID
      ));
    }
  }

}
