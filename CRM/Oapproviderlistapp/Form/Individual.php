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
        $defaults['postal_code[1]'] = $address[0]['postal_code'];
      }
      if (!empty($phone[0])) {
        $defaults['phone[1]'] = $phone[0]['phone'];
      }
      $this->_orgID = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $this->_contactID, 'employer_id');
      if (!empty($this->_orgID)) {
        $orgnaization = civicrm_api3('Contact', 'getsingle', ['id' => $this->_orgID]);
        $defaults['organization_name[1]'] = $orgnaization['organization_name'];
      }

      $count = 1;
      $relationships = civicrm_api3('Relationship', 'get', [
        'relationship_type_id' => 5,
        'contact_id_a' => $this->_contactID,
        'sequential' => 1,
      ])['values'];
      foreach ($relationships as $relationship) {
        $contact = civicrm_api3('Contact', 'getsingle', ['id' => $relationship['contact_id_b']]);
        $defaults["organization_name[$count]"] = $contact['organization_name'];
   //     $defaults["email[$count]"] = $contact['email'];
   //     $address = civicrm_api3('Address', 'get', ['contact_id' => $relationship['contact_id_b'], 'sequential' => 1])['values'];
   //     $phone = civicrm_api3('Phone', 'get', ['contact_id' => $relationship['contact_id_b'], 'sequential' => 1])['values'];
        if (!empty($address[0])) {
          $defaults["work_address[$count]"] = $address[0]['street_address'];
          $defaults["city[$count]"] = $address[0]['city'];
          $defaults["postal_code[$count]"] = $address[0]['postal_code'];
        }
        if (!empty($phone[0])) {
          $defaults["phone[$count]"] = $phone[0]['phone'];
        }
        $count++;
      }
    }

    return $defaults;
  }

  public function buildQuickForm() {
    $this->buildCustom(OAP_INDIVIDUAL, 'individual', $this->_contactID);

    for ($rowNumber = 1; $rowNumber <= 5; $rowNumber++) {
      $this->add('text', "organization_name[$rowNumber]", E::ts('Primary Employer Organization Name'), ['class' => 'big']);
      $this->add('text', "phone[$rowNumber]", E::ts('Phone Number'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "work_address[$rowNumber]", E::ts('Work Address'), ['size' => 45, 'maxlength' => 96, 'class' => 'huge']);
      $this->add('text', "postal_code[$rowNumber]", E::ts('Postal code'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "city[$rowNumber]", E::ts('City/Town'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "email[$rowNumber]", E::ts('Email Address'), ['size' => 20, 'maxlength' => 254, 'class' => 'medium'], ($rowNumber == 1));
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_49[$rowNumber]", 49, FALSE);
    }
    $totalCount = 1;
    if (!empty($this->_contactID)) {
      $files = [];
      $relationships = civicrm_api3('Relationship', 'get', [
        'relationship_type_id' => 5,
        'contact_id_a' => $this->_contactID,
        'sequential' => 1,
      ])['values'];
      foreach ($relationships as $key => $relationship) {
        $count = $key + 1;
        $files[$count] = $this->getFileUpload($relationship['id'], 'civicrm_value_proof_of_empl_13', 'proof_of_employment_letter_49', 49);
      }
      $this->assign('custom_49_file', $files);
      $totalCount = count($relationships) ?: 1;
    }
    $this->assign('employer_count', $totalCount);

    $this->addFormRule(array('CRM_Oapproviderlistapp_Form_Individual', 'formRule'), $this);
    parent::buildQuickForm();
  }

  public function formRule($fields, $files, $self) {
    $errors = [];
    if (!empty($fields['_qf_Individual_submit_done'])) {
      if (empty($fields['email[1]'])) {
        $errors['email[1]'] = E::ts("Email is a required field to send draft link.");
      }
      return $errors;
    }

    if (empty($fields["organization_name"][1])) {
      $errors['organization_name[1]'] = E::ts("At least one Current Employer is required.");
    }
    if (empty($fields['first_name'])) {
      $errors['first_name'] = E::ts("First Name is required");
    }
    if (empty($fields['last_name'])) {
      $errors['last_name'] = E::ts("Last Name is required");
    }
    if (!empty($fields['email'][1]) && !empty($self->_contactID)) {
      // Check to see if email exists as provider.
      $isProvider = CRM_Core_DAO::singleValueQuery('SELECT IF(is_deleted=0, "Yes", "No") FROM civicrm_email e INNER JOIN civicrm_contact c ON c.id = e.contact_id WHERE email LIKE %1 AND is_deleted <> 1', [1 => [$fields['email'][1], 'String']]);
      if (!empty($isProvider) && $isProvider == "Yes") {
        $errors['email[1]'] = E::ts("A person with this email address has already created an OAP Provider Listing application. Please contact info@oapproviderlist.ca for more information.");
      }
    }
    if (empty($fields["work_address"][1])) {
      $errors['work_address[1]'] = E::ts("Work Address is required.");
    }
    if (empty($fields["phone"][1])) {
      $errors['phone[1]'] = E::ts("Phone Number is required.");
    }
    if (empty($fields["postal_code"][1])) {
      $errors['postal_code[1]'] = E::ts("Postal Code is required.");
    }
    if (empty($fields["city"][1])) {
      $errors['city[1]'] = E::ts("City/Town is required.");
    }
   /* for ($rowNumber = 1; $rowNumber <= 5; $rowNumber++) {
      if (!empty($fields["organization_name"][$rowNumber]) && empty($files['custom_49']['name'][$rowNumber])) {
        $errors["custom_49[{$rowNumber}]"] = E::ts("Proof of Employment Letter is required.");
      }
    } */

    return $errors;
  }

  public static function submit($form, $values, $contactID, $orgID) {
    $params = [
      'email' => CRM_Utils_Array::value(1, $values['email']),
    ];
    if (empty($contactID) && !empty($params['email'])) {
      // fetch contact from existing approved Contact
      $contact = civicrm_api3('contact', 'get', ['email' => $params['email'], 'contact_sub_type' => 'Provider', 'sequential' => 1])['values'];
      $contactID = NULL;
      if (!empty($contact)) {
        $contactID = $contact[0]['contact_id'];
      }
      else {
        // if not found then fetch contact from trash based on first and last name
        $contact = civicrm_api3('Contact', 'get', [
          'sequential' => 1,
          'contact_sub_type' => "Provider",
          'first_name' => $values['first_name'],
          'last_name' => $values['last_name'],
          'is_deleted' => 1,
        ])['values'];
        if (!empty($contact)) {
          $contactID = $contact[0]['contact_id'];
        }
      }
    }

    E::checkProviderExist($contactID);

    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INDIVIDUAL, FALSE, CRM_Core_Action::VIEW);
    $values['skip_greeting_processing'] = TRUE;
    $contactID = CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $contactID, NULL, OAP_INDIVIDUAL);

    foreach ($values['organization_name'] as $key => $name) {
      if (!$name) {
        continue;
      }

      if (strpos(strtolower($name), 'self') !== false || strpos(strtolower($name), 'employ') !== false) {
        $name = "Self employed by " . $values['last_name'] . ", " . $values['first_name'];
      }

      $id = CRM_Utils_Array::value('id', civicrm_api3('Contact', 'get', [
        'organization_name' => $name,
        'contact_type' => 'Organization',
        'options' => ['limit' => 1],
      ]));
      if (empty($id)) {
        $id = civicrm_api3('Contact', 'create', [
          'id' => $id,
          'organization_name' => $name,
          'contact_type' => 'Organization',
        ])['id'];
        $relationshipID = civicrm_api3('Relationship', 'create', [
          'relationship_type_id' => 5,
          'contact_id_a' => $contactID,
          'contact_id_b' => $id,
        ])['id'];
        $fieldName = 'custom_49';
        if (!empty($values[$fieldName])) {
          $form->processEntityFile($fieldName, $values[$fieldName][$key], $relationshipID);
        }
      }
      else {
        $relationshipParams = [
          'relationship_type_id' => 5,
          'contact_id_a' => $contactID,
          'contact_id_b' => $id,
        ];
        $relationship = civicrm_api3('Relationship', 'get', $relationshipParams);
        if (empty($relationship['count'])) {
          $relationshipID = civicrm_api3('Relationship', 'create', [
            'relationship_type_id' => 5,
            'contact_id_a' => $contactID,
            'contact_id_b' => $id,
          ])['id'];
        }
        else {
          $relationshipID = $relationship['id'];
        }
        $fieldName = 'custom_49';
        if (!empty($values[$fieldName])) {
          $form->processEntityFile($fieldName, $values[$fieldName][$key], $relationshipID);
        }
      }
      $params = [
        'email' => CRM_Utils_Array::value($key, $values['email']),
        'address' => CRM_Utils_Array::value($key, $values['work_address']),
        'phone' => CRM_Utils_Array::value($key, $values['phone']),
        'city' => CRM_Utils_Array::value($key, $values['city']),
        'postal_code' => CRM_Utils_Array::value($key, $values['postal_code']),
      ];
      $form->updateContactAddress($id, $params);
      if ($key == 1) {
        CRM_Core_DAO::setFieldValue('CRM_Contact_DAO_Contact', $contactID, 'employer_id', $id);
        $form->updateContactAddress($contactID, $params);
      }
    }

    return $contactID;
  }

  public function postProcess() {
    parent::postProcess();
    $values = $this->controller->exportValues($this->_name);
    $contactID = self::submit($this, $values, $this->_contactID, $this->_orgID);

    if (empty($this->_contactID)) {
      civicrm_api3('Contact', 'create', [
        'id' => $contactID,
        'is_deleted' => TRUE,
      ]);
    }

    if (!empty($this->exportValues()['_qf_Individual_submit_done'])) {
      $this->sendDraft($contactID);
    }

    CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/professional", "&cid=" . $contactID));
  }

}
