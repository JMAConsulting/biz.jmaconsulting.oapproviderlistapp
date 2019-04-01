<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Individual extends CRM_Oapproviderlistapp_Form_ManageApplication {

  protected $_first = TRUE;

  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INDIVIDUAL, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);

    if (!empty($this->_contactID)) {
      $contact = civicrm_api3('Contact', 'getsingle', ['id' => $this->_contactID]);
      $defaults['email[1]'] = $contact['email'];
      //$defaults = array_merge($defaults, $contact);
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

    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INDIVIDUAL, FALSE, CRM_Core_Action::VIEW);
    $contactID = CRM_Contact_BAO_Contact::createProfileContact($values, $fields, NULL, NULL, OAP_INDIVIDUAL);

    if (!empty($values['email'][1])) {
      civicrm_api3('Email', 'create', [
        'contact_id' => $contactID,
        'email' => $values['email'][1],
        'location_type_id' => "Work",
        'is_primary' => TRUE,
      ]);
    }

    if (!empty($values['phone'][1])) {
      civicrm_api3('Phone', 'create', [
        'contact_id' => $contactID,
        'phone' => $values['phone'][1],
        'location_type_id' => "Work",
        'is_primary' => TRUE,
      ]);
    }

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
        $id = CRM_Utils_Array::value('id', civicrm_api3('Contact', 'get', [
          'organization_name' => $name,
          'options' => ['limit' => 1],
        ]));
        if (!$id) {
          $id = civicrm_api3('Contact', 'create', [
            'organization_name' => $name,
            'contact_type' => 'Organization',
            'email' => $values['email'][$key],
          ])['id'];
        }
        if (!empty($values['work_address'][$key])) {
          $addressParams = [
            'contact_id' => $id,
            'location_type_id' => 'Work',
            'is_primary' => TRUE,
            'street_address' => $values['work_address'][$key],
            'city' => CRM_Utils_Array::value($key, $values['city']),
          ];
          if (!empty($values['phone'][$key])) {
            civicrm_api3('Phone', 'create', [
              'contact_id' => $id,
              'phone' => $values['phone'][$key],
              'location_type_id' => "Work",
              'is_primary' => TRUE,
            ]);
          }

          $addressID = civicrm_api3('Address', 'create', $addressParams)['id'];
          $address = civicrm_api3('Address', 'create', array_merge($addressParams, ['contact_id' => $contactID]));
        }
        $relationship = civicrm_api3('Relationship', 'create', [
          'relationship_type_id' => 5,
          'contact_id_a' => $contactID,
          'contact_id_b' => $id,
        ]);
        CRM_Core_DAO::setFieldValue('CRM_Contact_DAO_Contact', $contactID, 'employer_id' , $id);
        /**
        $customValues = CRM_Core_BAO_CustomField::postProcess($values, $relationshipID, 'Relationships');
        if (!empty($customValues) && is_array($customValues)) {
          CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_relationship', $relationshipID);
        }
        */
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

    civicrm_api3('Contact', 'create', [
      'id' => $contactID,
      'is_deleted' => TRUE,
    ]);

    if (!empty($values['_qf_Individual_submit_done'])) {
      $values['contact_id'] = $contactID;
      $values['url'] = CRM_Utils_System::url("civicrm/application",
        "cid=" . $contactID, TRUE
      );
      $this->sendDraft($values);
    }

    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
      "selectChild=professional&cid=" . $contactID
    ));
  }

}
