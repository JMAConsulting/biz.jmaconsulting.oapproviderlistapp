<?php

use CRM_Oapproviderlistapp_ExtensionUtil as E;

require_once __DIR__ . '/../../../oapproviderlistapp.variables.php';
/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_ManageApplication extends CRM_Core_Form {
  protected $_first = FALSE;
  public $_contactID;
  public $_last = FALSE;

  public function preProcess() {
    CRM_Utils_System::setTitle(ts('OAP PROVIDER LIST APPLICATION FORM'));
    self::build($this);
  }

  public static function build(&$form) {
    $form->assign('selectedChild', CRM_Utils_Request::retrieve('selectedChild', 'Alphanumeric', $form));
    $selectChild = CRM_Utils_Request::retrieve('selectChild', 'String', $form, FALSE, NULL, 'GET') ?: 'individual';
    $form->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $form, FALSE);

    $tabs = $form->get('tabHeader');
    if (!$tabs || empty($_GET['reset'])) {
      $tabs = self::getTabs($form);
      $form->set('tabHeader', $tabs);
    }
    $form->assign_by_ref('tabHeader', $tabs);
    CRM_Core_Resources::singleton()
      ->addScriptFile('civicrm', 'templates/CRM/common/TabHeader.js', 1, 'html-header')
      ->addSetting(array(
        'tabSettings' => array(
          'active' => $selectChild,
        ),
      ));
    return $tabs;
  }

  public function getTabs(&$form) {
    $tabs = [];
    $profileNames = [
      'individual' => [
        'title' => ts('Individual Information'),
        'url' => CRM_Utils_System::url('civicrm/individual'),
      ],
      'professional' => [
        'title' => ts('Professional credential(s)'),
        'url' => CRM_Utils_System::url('civicrm/professional'),
      ],
      'experience' => [
        'title' => ts('Experience'),
        'url' => CRM_Utils_System::url('civicrm/experience'),
      ],
      'sectorcheck' => [
        'title' => ts('Vulnerable Sector Check'),
        'url' => CRM_Utils_System::url('civicrm/sectorcheck'),
      ],
      'insurance' => [
        'title' => ts('Professional Liability Insurance'),
        'url' => CRM_Utils_System::url('civicrm/insurance'),
      ],
      'documentation' => [
        'title' => ts('Documentation Checklist'),
        'url' => CRM_Utils_System::url('civicrm/documentation'),
      ],
      'signature' => [
        'title' => ts('Signature'),
        'url' => CRM_Utils_System::url('civicrm/signature'),
      ],
    ];
    foreach ($profileNames as $name => $info) {
      $tabs[$name] = [
        'title' => $info['title'],
        'class' => 'livePage',
        'link' =>  $info['url'],
        'valid' => NULL,
        'active' => TRUE,
      ];
    }
    return $tabs;
  }

  public function buildQuickForm() {

    $buttons = array();
    if (!$this->_first) {
      $buttons[] = array(
        'type' => 'submit',
        'name' => ts('Previous'),
        'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
      );
    }
    $buttons[] = array(
      'type' => 'upload',
      'name' => $this->_last ? ts('Submit') : ts('Next'),
      'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
      'isDefault' => TRUE,
    );

    $this->addButtons($buttons);

    // export form elements
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
  }

  public function buildCustom($id, $name, $viewOnly = FALSE) {
    if ($id) {
      $button = substr($this->controller->getButtonName(), -4);
      $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this);
      $session = CRM_Core_Session::singleton();
      $contactID = $session->get('userID');

      // we don't allow conflicting fields to be
      // configured via profile
      $fieldsToIgnore = array(
        'participant_fee_amount' => 1,
        'participant_fee_level' => 1,
      );
      if ($contactID) {
        //FIX CRM-9653
        if (is_array($id)) {
          $fields = array();
          foreach ($id as $profileID) {
            $field = CRM_Core_BAO_UFGroup::getFields($profileID, FALSE, CRM_Core_Action::ADD,
              NULL, NULL, FALSE, NULL,
              FALSE, NULL, CRM_Core_Permission::CREATE,
              'field_name', TRUE
            );
            $fields = array_merge($fields, $field);
          }
        }
        else {
          if (CRM_Core_BAO_UFGroup::filterUFGroups($id, $contactID)) {
            $fields = CRM_Core_BAO_UFGroup::getFields($id, FALSE, CRM_Core_Action::ADD,
              NULL, NULL, FALSE, NULL,
              FALSE, NULL, CRM_Core_Permission::CREATE,
              'field_name', TRUE
            );
          }
        }
      }
      else {
        $fields = CRM_Core_BAO_UFGroup::getFields($id, FALSE, CRM_Core_Action::ADD,
          NULL, NULL, FALSE, NULL,
          FALSE, NULL, CRM_Core_Permission::CREATE,
          'field_name', TRUE
        );
      }

      if (array_intersect_key($fields, $fieldsToIgnore)) {
        $fields = array_diff_key($fields, $fieldsToIgnore);
        CRM_Core_Session::setStatus(ts('Some of the profile fields cannot be configured for this page.'));
      }
      $addCaptcha = FALSE;

      if (!empty($this->_fields)) {
        $fields = @array_diff_assoc($fields, $this->_fields);
      }

      $this->assign($name, $fields);
      if (is_array($fields)) {
        foreach ($fields as $key => $field) {
          if ($viewOnly &&
            isset($field['data_type']) &&
            $field['data_type'] == 'File' || ($viewOnly && $field['name'] == 'image_URL')
          ) {
            // ignore file upload fields
            continue;
          }
          //make the field optional if primary participant
          //have been skip the additional participant.
          if ($button == 'skip') {
            $field['is_required'] = FALSE;
          }
          // CRM-11316 Is ReCAPTCHA enabled for this profile AND is this an anonymous visitor
          elseif ($field['add_captcha'] && !$contactID) {
            // only add captcha for first page
            $addCaptcha = TRUE;
          }
          list($prefixName, $index) = CRM_Utils_System::explode('-', $key, 2);
          CRM_Core_BAO_UFGroup::buildProfile($this, $field, CRM_Profile_Form::MODE_CREATE, $contactID, TRUE);

          $this->_fields[$key] = $field;
        }
      }

      if ($addCaptcha && !$viewOnly) {
        $captcha = CRM_Utils_ReCAPTCHA::singleton();
        $captcha->add($this);
        $this->assign('isCaptcha', TRUE);
      }
    }
  }

}
