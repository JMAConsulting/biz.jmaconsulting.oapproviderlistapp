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
    if (!empty($this->_contactID)) {
      $activityID = civicrm_api3('Activity', 'get', [
        'source_contact_id' => $this->_contactID,
        'activity_type_id' => "Provider List Application Submission",
        'target_id' => $this->_contactID,
        'assignee_id' => 99184,
        'options' => ['limit' => 1],
      ])['id'];
      $file = $this->getFileUpload($activityID, 'civicrm_value_signature_14', 'signature_58', 58);
      $this->assign('custom_58_file', $file);
    }

    parent::buildQuickForm();
  }

  public function postProcess() {
    parent::postProcess();
    $values = $this->controller->exportValues($this->_name);
    if (!empty($this->_contactID)) {
      $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SIGNATURE, FALSE, CRM_Core_Action::VIEW);
      $activity = civicrm_api3('Activity', 'get', [
        'source_contact_id' => $this->_contactID,
        'activity_type_id' => "Provider List Application Submission",
        'sequential' => 0,
      ])['values'][0];
      $actParams = [
        'source_contact_id' => $this->_contactID,
        'activity_type_id' => "Provider List Application Submission",
        'subject' => 'Provider List Application Submission',
        'activity_status_id' => 'Scheduled',
        'target_id' => $this->_contactID,
        'assignee_id' => 99184,
      ];
      if (!empty($activity['id'])) {
        $actParams['id'] = $activity['id'];
      }
      $activityID = civicrm_api3('Activity', 'create', $actParams)['id'];

      $fieldName = 'custom_58';
      $this->processEntityFile($fieldName, $values[$fieldName], $activityID);
    }
    if (CRM_Utils_Array::value('_qf_Signature_submit_done', $this->exportValues())) {
      $this->sendDraft($this->_contactID);
    }
    elseif (CRM_Utils_Array::value('_qf_Signature_submit', $this->exportValues())) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/insurance", "cid=" . $this->_contactID));
    }
    else {
      CRM_Core_Session::setStatus("", E::ts('Thank you for submitting your application to the OAP Provider List'), "success");
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/application/confirm', "cid=" . $this->_contactID));
    }
  }

}
