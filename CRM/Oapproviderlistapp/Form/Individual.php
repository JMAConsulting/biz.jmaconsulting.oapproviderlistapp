<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Individual extends CRM_Oapproviderlistapp_Form_ManageApplication {

  protected $_first = TRUE;
  public $_orgID;

  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INDIVIDUAL, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);

    if (!empty($this->_contactID)) {
      $contact = civicrm_api3('Contact', 'getsingle', ['id' => $this->_contactID]);
      $defaults['email[1]'] = $contact['email'];
      $address = civicrm_api3('Address', 'get', ['contact_id' => $this->_contactID, 'sequential' => 1, 'is_primary' => TRUE])['values'];
      $phone = civicrm_api3('Phone', 'get', ['contact_id' => $this->_contactID, 'sequential' => 1, 'is_primary' => TRUE])['values'];
      if (!empty($address[0])) {
        $defaults['work_address[1]'] = $address[0]['street_address'];
        $defaults['city[1]'] = $address[0]['city'];
      }
      if (!empty($phone[0])) {
        $defaults['phone[1]'] = $phone[0]['phone'];
      }
      $this->_orgID = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $this->_contactID, 'employer_id');
      if (!empty($this->_orgID)) {
        $orgnaization = civicrm_api3('Contact', 'getsingle', ['id' => $this->_orgID]);
        $defaults['organization_name[1]'] = $orgnaization['organization_name'];
      }
    }

    return $defaults;
  }

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(E::ts('Individual Information'));
    $this->buildCustom(OAP_INDIVIDUAL, 'individual', $this->_contactID);

    for ($rowNumber = 1; $rowNumber <= 5; $rowNumber++) {
      $this->add('text', "organization_name[$rowNumber]", E::ts('Primary Employer Organization Name'), ['class' => 'big']);
      $this->add('text', "work_address[$rowNumber]", E::ts('Work Address'), ['size' => 45, 'maxlength' => 96, 'class' => 'huge']);
      $this->add('text', "phone[$rowNumber]", E::ts('Phone Number'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "city[$rowNumber]", E::ts('City/Town'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "email[$rowNumber]", E::ts('Email Address'), ['size' => 20, 'maxlength' => 254, 'class' => 'medium'], ($rowNumber == 1));
      if ($rowNumber == 1) {
        CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_49", 49, TRUE);
      }
    }
    $this->addFormRule(array('CRM_Oapproviderlistapp_Form_Individual', 'formRule'), $this);
    parent::buildQuickForm();
  }

  public function formRule($fields, $files, $self) {
    $errors = [];
    if (empty($fields["organization_name"][1])) {
      $errors['organization_name[1]'] = E::ts("At least one Current Employer is required.");
    }
    return $errors;
  }

  public function postProcess() {
    parent::postProcess();
    $values = $this->controller->exportValues($this->_name);
    $email = $phone = NULL;
    $contactID = NULL;
    if (!empty($this->_contactID)) {
      $contactID = $this->_contactID;
    }

    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INDIVIDUAL, FALSE, CRM_Core_Action::VIEW);
    $contactID = CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $contactID, NULL, OAP_INDIVIDUAL);

    $params = [
      'email' => CRM_Utils_Array::value(1, $values['email']),
      'work_address' => CRM_Utils_Array::value(1, $values['work_address']),
      'phone' => CRM_Utils_Array::value(1, $values['phone']),
      'city' => CRM_Utils_Array::value(1, $values['city']),
    ];
    $this->updateContactAddress($contactID, $params);

    $customParams = [];
    $mapping = [
      'custom_53' => 'organization_name',
      'custom_54' => 'work_address',
      'custom_55' => 'phone',
      'custom_56' => 'city',
    ];
    foreach ($values['organization_name'] as $key => $name) {
      if (!$name) {
        continue;
      }

      if (strpos(strtolower($name), 'self') !== false || strpos(strtolower($name), 'employ') !== false) {
        $name = "Self employed by " . $values['last_name'] . "," . $values['first_name'];
      }

      if ($key == 1) {
        if (!empty($this->_orgID)) {
          $id = $this->_orgID;
        }
        else {
          $id = CRM_Utils_Array::value('id', civicrm_api3('Contact', 'get', [
            'organization_name' => $name,
            'options' => ['limit' => 1],
          ]));
        }
        $id = civicrm_api3('Contact', 'create', [
          'id' => $id,
          'organization_name' => $name,
          'contact_type' => 'Organization',
        ])['id'];
        $this->updateContactAddress($id, $params);

        if (empty($this->_orgID)) {
          $relationshipID = civicrm_api3('Relationship', 'create', [
            'relationship_type_id' => 5,
            'contact_id_a' => $contactID,
            'contact_id_b' => $id,
          ])['id'];
          CRM_Core_DAO::setFieldValue('CRM_Contact_DAO_Contact', $contactID, 'employer_id' , $id);
        }
        $fieldName = 'custom_49';
        $this->processEntityFile($fieldName, $values[$fieldName], $relationshipID);
      }
      else {
        foreach ($mapping as $cfName => $fieldName) {
          if (!empty($values[$fieldName][$key])) {
            $customParams["$cfName" . '_-' . $key] = $values[$fieldName][$key];
          }
        }
      }
    }

    if (!empty($customParams)) {
      $customValues = CRM_Core_BAO_CustomField::postProcess($customParams, $contactID, 'Individual');
      if (!empty($customValues) && is_array($customValues)) {
        CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $contactID);
      }
    }

    if (empty($this->_contactID)) {
      civicrm_api3('Contact', 'create', [
        'id' => $contactID,
        'is_deleted' => TRUE,
      ]);
    }

    if (!empty($values['_qf_Individual_submit_done'])) {
      $this->sendDraft($contactID, CRM_Utils_Array::value('qfKey', $this->exportValues()));
    }

    CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/professional", "&cid=" . $contactID));
  }

}
