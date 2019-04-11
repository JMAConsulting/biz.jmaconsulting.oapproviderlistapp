<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;
require_once __DIR__ . '/../../../oapproviderlistapp.variables.php';

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_Confirm extends CRM_Oapproviderlistapp_Form_ManageApplication {
  public $_contactID;
  public $_mode;
  public function preProcess() {
    CRM_Utils_System::setTitle(E::ts('OAP PROVIDER LIST CONFIRMATION PAGE'));
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    $this->_mode = CRM_Utils_Request::retrieve('mode', 'Alphanumeric', $this, FALSE);
    CRM_Core_Resources::singleton()->addStyleFile('org.civicrm.shoreditch', 'css/custom-civicrm.css',1, 'html-header');
    // CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'templates/css/oapp.css');
  }

  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_PROFESSIONAL, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_EXPERIENCE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SECTORCHECK, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_INSURANCE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_SIGNATURE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    foreach (['custom_46', 'custom_57', 'custom_58'] as $name) {
      if (!empty($defaults[$name])) {
        $defaults[$name] = CRM_Core_BAO_CustomField::displayValue($defaults[$name], $name, $this->_contactID);
      }
    }
    return $defaults;
  }


  public function buildQuickForm() {
    $displayName = CRM_Contact_BAO_Contact::displayName($this->_contactID);
    $this->assign('displayName', $displayName);
    $employerID = CRM_Core_DAO::getFieldValue('CRM_Contact_BAO_Contact', $this->_contactID, 'employer_id');
    if (!empty($employerID)) {
      $this->assign('employerName', CRM_Contact_BAO_Contact::displayName($employerID));
    }
    $relationship = civicrm_api3('Relationship', 'get', [
      'relationship_type_id' => 5,
      'contact_id_a' => $this->_contactID,
      'contact_id_b' => $employerID,
      'sequential' => 1,
    ])['values'][0];
    $this->assign('custom_49', CRM_Core_BAO_CustomField::displayValue($relationship['custom_49']['fid'], 'custom_49', $relationship['id']));

    $email = civicrm_api3('Email', 'getvalue', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'return' => 'email']);
    $this->assign('email', $email);

    $address = civicrm_api3('Address', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];
    $addressValue = sprintf("%s,<br/>%s", $address['street_address'], $address['city']);
    $this->assign('address', $addressValue);
    $phone = civicrm_api3('Phone', 'get', ['contact_id' => $this->_contactID, 'is_primary' => TRUE, 'sequential' => 1])['values'][0];
    $this->assign('phone', $phone['phone']);

    $groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', NULL, $this->_contactID, 16, 'Provider');
    $details = CRM_Core_BAO_CustomGroup::buildCustomDataView($this, $groupTree, FALSE, NULL, NULL, NULL, $this->_contactID);
    $otherEmplyeeInformation = [];
    foreach ($details[16] as $fieldID => $info) {
      if (empty($info['fields'])) {
        continue;
      }
      foreach ($info['fields'] as $values) {
        if (empty($values['field_value'])) {
          continue;
        }
        $otherEmplyeeInformation[$fieldID][$values['field_title']] = $values['field_value'];
      }
    }
    $this->assign('otherEmplyeeInformation', $otherEmplyeeInformation);

    $groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', NULL, $this->_contactID, 12, 'Provider');
    $details = CRM_Core_BAO_CustomGroup::buildCustomDataView($this, $groupTree, FALSE, NULL, NULL, NULL, $this->_contactID);
    $otherProfessional = [];
    foreach ($details[12] as $fieldID => $info) {
      if (empty($info['fields'])) {
        continue;
      }
      foreach ($info['fields'] as $values) {
        if (empty($values['field_value'])) {
          continue;
        }
        $otherProfessional[$fieldID][$values['field_title']] = $values['field_value'];
      }
    }
    $this->assign('otherProfessional', $otherProfessional);

    $groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', NULL, $this->_contactID, 10, 'Provider');
    $details = CRM_Core_BAO_CustomGroup::buildCustomDataView($this, $groupTree, FALSE, NULL, NULL, NULL, $this->_contactID);
    $employers = [];
    foreach ($details[10] as $fieldID => $info) {
      if (empty($info['fields'])) {
        continue;
      }
      foreach ($info['fields'] as $values) {
        if (empty($values['field_value'])) {
          continue;
        }
        $employers[$fieldID][$values['field_title']] = $values['field_value'];
      }
    }
    $this->assign('employers', $employers);

    $this->buildCustom(OAP_PROFESSIONAL, 'professional', TRUE);
    $this->buildCustom(OAP_EXPERIENCE, 'experience', TRUE);
    $this->buildCustom(OAP_SECTORCHECK, 'sectorcheck', TRUE);
    $this->buildCustom(OAP_INSURANCE, 'insurance', TRUE);
    $this->buildCustom(OAP_SIGNATURE, 'signature', TRUE, TRUE);

    $buttons[] = array(
      'type' => 'upload',
      'name' =>  E::ts('Previous'),
      'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
      'isDefault' => TRUE,
    );
    $buttons[] = array(
      'type' => 'submit',
      'name' => E::ts('Confirm'),
      'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
      'subName' => 'done',
    );

    if ($this->_mode != 'embedded') {
      $this->addButtons($buttons);
    }
  }

  public function postProcess() {
    $values = $this->exportValues();
    if (CRM_Utils_Array::value('_qf_Confirm_upload', $values)) {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/signature", "reset=1&cid=" . $this->_contactID));
    }
    else {
      $activity = civicrm_api3('Activity', 'get', [
        'source_contact_id' => $this->_contactID,
        'activity_type_id' => "Provider List Application Submission",
        'sequential' => 0,
      ])['values'][0];
      if (!empty($activity['id'])) {
        civicrm_api3('Activity', 'create', [
          'id' => $activity['id'],
          'status_id' => 'Completed',
        ]);
      }
      civicrm_api3('Contact', 'create', [
        'id' => $this->_contactID,
        'is_deleted' => FALSE,
        'custom_60' => "Submitted",
      ]);
      CRM_Core_Session::setStatus("", E::ts('Thank you for submitting your application to the OAP Provider List'), "success");
      CRM_Utils_System::redirect('https://oapproviderlist.ca');
    }
    parent::postProcess();
  }

  public function getTemplateFileName() {
    return 'CRM/Oapproviderlistapp/Form/Confirm.tpl';
  }

}
