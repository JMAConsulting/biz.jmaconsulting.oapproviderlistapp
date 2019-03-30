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
    $this->buildCustom(OAP_PHONEADDRESS, 'phoneaddress');

    for ($rowNumber = 1; $rowNumber <= 5; $rowNumber++) {
      $this->add('text', "organization_name[$rowNumber]", ts('Primary Employer Organization Name'), ['size' => 2, 'class' => 'big']);
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
        ])['id'];
      }
      if ($key == 1) {
        civicrm_api3('Contact', 'create', ['id' => $contactID, 'current_employer' => $id]);
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
