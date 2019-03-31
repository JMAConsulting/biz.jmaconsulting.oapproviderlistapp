<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Individual extends CRM_Oapproviderlistapp_Form_ManageApplication {

  protected $_first = TRUE;

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Individual Information'));
    $this->buildCustom(OAP_INDIVIDUAL, 'individual');

    for ($rowNumber = 1; $rowNumber <= 5; $rowNumber++) {
      $this->add('text', "organization_name[$rowNumber]", ts('Primary Employer Organization Name'), ['class' => 'big']);
      $this->add('text', "work_address[$rowNumber]", ts('Work Address'), ['size' => 45, 'maxlength' => 96, 'class' => 'huge']);
      $this->add('text', "phone[$rowNumber]", ts('Phone Number'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "city[$rowNumber]", ts('City/Town'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "email[$rowNumber]", ts('Email Address'), ['size' => 20, 'maxlength' => 254, 'class' => 'medium']);
    }
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INDIVIDUAL, FALSE, CRM_Core_Action::VIEW);
    $contactID = CRM_Contact_BAO_Contact::createProfileContact($values, $fields, NULL, NULL, OAP_INDIVIDUAL);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_PHONEADDRESS, FALSE, CRM_Core_Action::VIEW);
    $contactID = CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $contactID, NULL, OAP_PHONEADDRESS);

    $organizationNames = [];
    foreach ($values['organization_name'] as $key => $name) {
      if (!$name) {
        continue;
      }
      $id = CRM_Utils_Array::value('id', civicrm_api3('Contact', 'get', [
        'organization_name' => $name,
        'options' => ['limit' => 1],
      ]));
      if (!$id) {
        $id = civicrm_api3('Contact', 'create', [
          'organization_name' => $name,
          'contact_type' => 'Organization',
          'email' => CRM_Utils_Array::value($key, $values['email']),
          'is_deleted' => TRUE,
        ])['id'];
      }
      if (!empty($values['work_address'][$key])) {
        civicm_api3('Address', 'create', [
          'contact_id' => $id,
          'location_type_id' => 2,
          'is_primary' => TRUE,
          'street_address' => $values['work_address'][$key],
          'city' => CRM_Utils_Array::value($key, $values['city']),
          'phone' => CRM_Utils_Array::value($key, $values['phone']),
        ]);
      }
      if ($key == 1) {
        civicrm_api3('Contact', 'create', ['id' => $contactID, 'employer_id' => $id]);
      }
      else {
        civicrm_api3('Relationship', 'create', [
          'relationship_type_id' => 5,
          'contact_id_a' => $contactID,
          'contact_id_b' => $id,
        ]);
      }
    }

    civicrm_api3('Contact', 'create', [
      'id' => $contactID,
      'is_deleted' => TRUE,
    ]);

    if (!empty($values['_qf_Individual_submit_done'])) {
      $values['contact_id'] = $contactID;
      $values['url'] = CRM_Utils_System::url("civicrm/application",
        "cid=" . $contactID
      );
      $this->sendDraft($values);
    }

    parent::postProcess();
    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/application",
      "selectChild=professional&cid=" . $contactID
    ));
  }

}
