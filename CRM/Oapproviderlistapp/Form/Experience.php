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
    if (!empty($fields['_qf_Experience_submit_done'])) {
      return TRUE;
    }
    $errors = [];
    foreach ($fields['custom_12'] as $key => $value) {
      if (empty($value)) {
        $errors['custom_12'] = E::ts('Each option of Experience check list is a required field.');
      }
    }
    $keys = [];
    foreach ($fields as $key => $value) {
      if (strstr($key, 'custom_32_')) {
        $keys[] = str_replace('custom_32_','', $key);
      }
    }

    // Validation for custom groups.
    $cg = [
      "custom_32_",
      "custom_33_",
      "custom_47_",
      "custom_35_",
      "custom_36_",
      "custom_37_",
      "custom_38_",
    ];
    $count = $fields['hidden_custom_group_count'][10];
    for ($i = 1; $i <= $count; $i++) {
      foreach ($cg as $field) {
        $part = $keys[$i-1];
        $fieldName = $field.$part;
        if (!array_key_exists($fieldName, $fields)) {
          continue;
        }
        if (empty($fields[$fieldName])) {
          $errors['_qf_default'] = E::ts('All fields in Employment History are required.');
          CRM_Core_Session::setStatus("", E::ts('All fields in Employment History are required.'), "alert");
        }
        elseif (strstr($fieldName, 'custom_47') && !empty($fields[$fieldName])) {
          $contact = civicrm_api3('Contact', 'get', [
            'sequential' => 1,
            'id' => $self->_contactID,
            'return' => ['custom_8', 'custom_9', 'custom_10', 'custom_11'],
          ])['values'][0];
          $lowestData = NULL;
          foreach (['custom_8', 'custom_9', 'custom_10', 'custom_11'] as $key) {
            if (!$lowestData || $lowestData > strtotime($contact[$key])) {
              $lowestData = strtotime($contact[$key]);
            }
          }
          if (strtotime($fields[$fieldName]) < $lowestData) {
            $errors['_qf_default'] = E::ts('For the purposes of OAP Provider List, only your employment after certification is counted. Please change Employment History Start Date to your certification date');
          }
        }
        elseif ((strstr($fieldName, 'custom_36') || strstr($fieldName, 'custom_37')) && !CRM_Utils_Rule::positiveInteger($fields[$fieldName])) {
          $name = strstr($fieldName, 'custom_37') ? E::ts('Approximate number of hours that involved supervisory duties') : E::ts('Total number of hours');
          $errors['_qf_default'] = E::ts('Please enter a numeric value for "%1"', [1 => $name]);
        }
      }
    }
    if (empty($errors) && !empty($_SESSION[$self->get('qfKey')])) {
      $errors = $_SESSION[$self->get('qfKey')];
      unset($_SESSION[$self->get('qfKey')]);
    }
    elseif (!empty($errors)) {
      $_SESSION[$self->get('qfKey')] = $errors;
      CRM_Core_BAO_Cache::setItem($fields, 'custom params', $self->get('qfKey'));
    }

    if (count($errors) > 0) {
      $self->assign('disableTab', 1);
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

      if (CRM_Utils_Array::value('_qf_Experience_submit', $this->exportValues())) {
        //$sql = sprintf("DELETE FROM %s WHERE entity_id = %d ", OAP_EMP_HIS, $this->_contactID);
        CRM_Core_DAO::executeQuery($sql);
      }
      $customValues = CRM_Core_BAO_CustomField::postProcess($this->_submitValues, $this->_contactID, 'Individual');
      if (!empty($customValues) && is_array($customValues)) {
        CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $this->_contactID);
      }
    }

    if (CRM_Utils_Array::value('_qf_Experience_submit_done', $this->exportValues())) {
      $this->sendDraft($this->_contactID);
    }
    if (CRM_Utils_Array::value('_qf_Experience_submit', $this->exportValues())) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/professional", "reset=1&cid=" . $this->_contactID . '&cs=' .  CRM_Contact_BAO_Contact_Utils::generateChecksum($this->_contactID, NULL, 'inf')));
    }
    else {
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/sectorcheck",
        "reset=1&cid=" . $this->_contactID . '&cs=' .  CRM_Contact_BAO_Contact_Utils::generateChecksum($this->_contactID, NULL, 'inf')
      ));
    }
  }

}
