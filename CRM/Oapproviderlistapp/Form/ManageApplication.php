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
  public $_contactID = NULL;
  public $_last = FALSE;

  public function preProcess() {
    CRM_Utils_System::setTitle(E::ts('OAP PROVIDER LIST APPLICATION FORM'));
    $this->_contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    self::build($this);
  }

  public static function build(&$form) {
    $form->assign('selectedChild', CRM_Utils_Request::retrieve('selectedChild', 'Alphanumeric', $form));
    $selectChild = CRM_Utils_Request::retrieve('selectChild', 'String', $form, FALSE, NULL, 'GET') ?: 'individual';

    $tabs = $form->get('tabHeader');
    if (!$tabs || empty($_GET['reset'])) {
      $tabs = self::getTabs($form);
      $form->set('tabHeader', $tabs);
    }
    $form->assign_by_ref('tabHeader', $tabs);
    CRM_Core_Resources::singleton()->addStyleFile('org.civicrm.shoreditch', 'css/custom-civicrm.css',1, 'html-header');
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'templates/css/oapp.css');
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
    $qfKey = $form->get('qfKey');
    $query = 'reset=1';
    if (!empty($form->_contactID)) {
      $query .= '&cid=' . $form->_contactID;
    }
    $profileNames = [
      'individual' => [
        'title' => E::ts('Individual Information'),
        'url' => CRM_Utils_System::url('civicrm/individual', $query),
      ],
      'professional' => [
        'title' => E::ts('Professional Credential(s)'),
        'url' => CRM_Utils_System::url('civicrm/professional', $query),
      ],
      'experience' => [
        'title' => E::ts('Experience'),
        'url' => CRM_Utils_System::url('civicrm/experience', $query),
      ],
      'sectorcheck' => [
        'title' => E::ts('Vulnerable Sector Check'),
        'url' => CRM_Utils_System::url('civicrm/sectorcheck', $query),
      ],
      'insurance' => [
        'title' => E::ts('Professional Liability Insurance'),
        'url' => CRM_Utils_System::url('civicrm/insurance', $query),
      ],
      'signature' => [
        'title' => E::ts('Signature'),
        'url' => CRM_Utils_System::url('civicrm/signature', $query),
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
      $tabs[$name]['qfKey'] = $qfKey ? "&qfKey={$qfKey}" : NULL;

    }
    return $tabs;
  }

  public function buildQuickForm() {

    $buttons = array();
    if (!$this->_first) {
      $buttons[] = array(
        'type' => 'submit',
        'name' => E::ts('Previous'),
        'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
      );
    }
    $buttons[] = array(
      'type' => 'upload',
      'name' => $this->_last ? E::ts('Submit') : E::ts('Next'),
      'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
      'isDefault' => TRUE,
    );
    $buttons[] = array(
      'type' => 'submit',
      'name' => E::ts('Save Draft'),
      'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
      'subName' => 'done',
    );

    $this->addButtons($buttons);

    // export form elements
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
  }

  public function sendDraft($params) {
    if (empty($params['contact_id'])) {
      return;
    }
    $contact_params = array(array('contact_id', '=', $params['contact_id'], 0, 0));
    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $params['contact_id']]);
    $messageTemplates = new CRM_Core_DAO_MessageTemplate();
    $messageTemplates->id = 68;
    $messageTemplates->find(TRUE);
    $body_subject = CRM_Core_Smarty::singleton()->fetch("string:$messageTemplates->msg_subject");
    $body_text    = str_replace('{date}', date('D, M j, Y \a\t g:ia'), str_replace('{url}', $params['url'], $messageTemplates->msg_text));
    $body_html    = "{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}" . str_replace('{date}', date('D, M j, Y \a\t g:ia'), str_replace('{url}', $params['url'], $messageTemplates->msg_html)) . "{/crmScope}";
    $body_html = CRM_Core_Smarty::singleton()->fetch("string:{$body_html}");
    $body_text = CRM_Core_Smarty::singleton()->fetch("string:{$body_text}");

    $mailParams = array(
      'groupName' => 'Send Draft',
      'from' => "<info@oapproviderlist.ca>",
      'toName' =>  $contact['display_name'],
      'toEmail' => $contact['email'],
      'subject' => $body_subject,
      'messageTemplateID' => $messageTemplates->id,
      'html' => ts($body_html),
      'text' => ts($body_text),
    );
    CRM_Utils_Mail::send($mailParams);
    CRM_Utils_System::redirect('https://oapproviderlist.ca');
  }

  public function buildCustom($id, $name, $viewOnly = FALSE, $ignoreContact = FALSE) {
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
      if ($contactID && !$ignoreContact) {
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
