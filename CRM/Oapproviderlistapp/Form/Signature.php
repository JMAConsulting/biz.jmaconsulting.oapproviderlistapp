<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Signature extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public $_last = TRUE;
  public $_contactID;
  public function buildQuickForm() {
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    $this->buildCustom(OAP_SIGNATURE, 'signature', FALSE, TRUE);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->_submitValues;
    if (!empty($this->_contactID)) {
      $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SIGNATURE, FALSE, CRM_Core_Action::VIEW);
      $activityID = civicrm_api3('Activity', 'create', [
        'source_contact_id' => $this->_contactID,
        'activity_type_id' => "Provider List Application Submission",
        'subject' => 'Provider List Application Submission',
        'activity_status_id' => 'Completed',
        'target_id' => $this->_contactID,
      ])['id'];
      // TODO return error
      //CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $activityID, NULL, OAP_SIGNATURE);
      civicrm_api3('Contact', 'create', [
        'id' => $this->_contactID,
        'is_deleted' => FALSE,
      ]);
    }
    if (!empty($values['_qf_Signature_submit_done'])) {
      $values['contact_id'] = $this->_contactID;
      $values['url'] = CRM_Utils_System::url("civicrm/application",
        "selectChild=signature&cid=" . $this->_contactID, TRUE
      );
      $this->sendDraft($values);
    }
    if (!empty($values['_qf_Signature_submit'])) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/application", "selectChild=insurance&cid=" . $this->_contactID));
    }
    else {
      CRM_Core_Session::setStatus("", E::ts('Thank you for submitting your application to the OAP Provider List'), "success");
      CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url(''));
    }
  }

}
