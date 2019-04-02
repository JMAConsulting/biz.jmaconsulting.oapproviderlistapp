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
    CRM_Oapproviderlistapp_Form_TabHeader::build($this, $this->_contactID);
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

  public function sendDraft($contactID, $qfKey = NULL) {
    if (empty($params['contact_id'])) {
      return;
    }
    $qfKey = '';
    if (!empty($params['qfKey'])) {
      $qfKey = "&qfKey{$params['qfKey']}";
    }
    $url = CRM_Utils_System::url("civicrm/application", sprintf("cid=%d%s", $params['contact_id'], $qfKey), TRUE);
    $contact_params = array(array('contact_id', '=', $params['contact_id'], 0, 0));
    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $params['contact_id']]);
    $messageTemplates = new CRM_Core_DAO_MessageTemplate();
    $messageTemplates->id = 68;
    $messageTemplates->find(TRUE);
    $body_subject = CRM_Core_Smarty::singleton()->fetch("string:$messageTemplates->msg_subject");
    $body_text    = str_replace('{date}', date('D, M j, Y \a\t g:ia'), str_replace('{url}', $url, $messageTemplates->msg_text));
    $body_html    = "{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}" . str_replace('{date}', date('D, M j, Y \a\t g:ia'), str_replace('{url}', $url, $messageTemplates->msg_html)) . "{/crmScope}";
    $body_html = CRM_Core_Smarty::singleton()->fetch("string:{$body_html}");
    $body_text = CRM_Core_Smarty::singleton()->fetch("string:{$body_text}");

    $mailParams = array(
      'groupName' => 'Send Draft',
      'from' => "<info@oapproviderlist.ca>",
      'toName' =>  $contact['display_name'],
      'toEmail' => $contact['email'],
      'subject' => $body_subject,
      'messageTemplateID' => $messageTemplates->id,
      'html' => $body_html,
      'text' => $body_text,
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

  public function getTemplateFileName() {
    if (empty($_GET['snippet'])) {
      // hack lets suppress the form rendering for now
      self::$_template->assign('isForm', FALSE);
      return 'CRM/Oapproviderlistapp/Form/ManageApplication.tpl';
    }
    else {
      return parent::getTemplateFileName();
    }
  }

  public function processEntityFile($fieldName, $fileInfo, $entityID) {
    if (empty($fileInfo['name'])) {
      return;
    }
    $customFieldId = str_replace('custom_', '', $fieldName);
    list($tableName, $columnName, $groupID) = CRM_Core_BAO_CustomField::getTableColumnGroup($customFieldId);

    $fileDAO = new CRM_Core_DAO_File();
    $filename = pathinfo($fileInfo['name'], PATHINFO_BASENAME);
    $fileDAO->uri = $filename;
    $fileDAO->mime_type = $fileInfo['type'];
    $fileDAO->upload_date = date('YmdHis');
    $fileDAO->save();
    $fileID = $fileDAO->id;

    $ef = new CRM_Core_DAO_EntityFile();
    $ef->entity_table = $tableName;
    $ef->entity_id = $entityID;
    $ef->file_id = $fileID;
    $ef->save();

    $sql = sprintf("INSERT IGNORE INTO %s(entity_id, %s) VALUES (%d, %d)", $tableName, $columnName, $entityID, $fileID);
    CRM_Core_DAO::executeQuery($sql);
  }


}
