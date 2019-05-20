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
    $fileLink = (\Drupal::languageManager()->getCurrentLanguage()->getId() == 'fr') ? '/sites/default/files/2019-04/v2%20OAP%20Registry%20Application%20French%20package.pdf' : '/sites/default/files/2019-04/v2%20OAP%20Registry%20Application%20package_0.pdf';
    $this->assign('fileLink', $fileLink);
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

  public function sendConfirm($contactID) {
    if (empty($contactID)) {
      return;
    }
    $messageTemplates = new CRM_Core_DAO_MessageTemplate();
    $messageTemplates->id = 68;
    $messageTemplates->find(TRUE);

    $body_subject = CRM_Core_Smarty::singleton()->fetch("string:$messageTemplates->msg_subject");
    $body_text    = $messageTemplates->msg_text;
    $body_html    = "{crmScope extensionKey='biz.jmaconsulting.oapproviderlistapp'}" . $messageTemplates->msg_html . "{/crmScope}";
    $body_html = CRM_Core_Smarty::singleton()->fetch("string:{$body_html}");
    $body_text = CRM_Core_Smarty::singleton()->fetch("string:{$body_text}");

    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $contactID]);
    $mailParams = array(
      'groupName' => 'OAP Application Confirmation',
      'from' => "<info@oapproviderlist.ca>",
      'toName' =>  $contact['display_name'],
      'toEmail' => $contact['email'],
      'subject' => $body_subject,
      'messageTemplateID' => $messageTemplates->id,
      'html' => $body_html,
      'text' => $body_text,
    );
    CRM_Utils_Mail::send($mailParams);
  }

  public function sendDraft($contactID, $qfKey = NULL) {
    if (empty($contactID)) {
      return;
    }
    $qfKey = '';
    if (!empty($qfKey)) {
      $qfKey = "&qfKey{$qfKey}";
    }
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $url = CRM_Utils_System::url("civicrm/application", sprintf("cid=%d%s", $contactID, $qfKey), TRUE);
    $contact_params = array(array('contact_id', '=', $contactID, 0, 0));
    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $contactID]);
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
    civicrm_api3('Activity', 'create', [
      'source_contact_id' => $contactID,
      'activity_type_id' => "Draft Saved",
      'subject' => 'Draft Saved',
      'activity_status_id' => 'Completed',
      'target_id' => $contactID,
      'assignee_id' => 99184,
      'details' => $body_html,
    ]);
    $url = CRM_Utils_System::url("civicrm/draft-saved", "reset=1&cid=" . $contactID);
    CRM_Utils_System::redirect($url);
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
            //continue;
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
          if ($viewOnly) {
            $field['is_view'] = $viewOnly;
            if ($field['data_type'] == 'File' || $field['name'] == 'image_URL') {
              $this->add('text', $field['name'], $field['title'], []);
              $this->freeze($field['name']);
              continue;
            }
          }
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

  /**
   * Extract contact id from url for deleting contact image.
   */
  public static function processImage() {

    $action = CRM_Utils_Request::retrieve('action', 'String');
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive');
    // retrieve contact id in case of Profile context
    $id = CRM_Utils_Request::retrieve('id', 'Positive');
    $cid = $cid ? $cid : $id;
    if ($action & CRM_Core_Action::DELETE) {
      if (CRM_Utils_Request::retrieve('confirmed', 'Boolean')) {
        CRM_Contact_BAO_Contact::deleteContactImage($cid);
        CRM_Core_Session::setStatus(ts('Contact image deleted successfully'), ts('Image Deleted'), 'success');
        $toUrl = CRM_Utils_System::url('civicrm/editlisting');
        CRM_Utils_System::redirect($toUrl);
      }
    }
  }

  public function updateContactAddress($contactID, $params) {
    foreach (['email', 'phone', 'address'] as $param) {
      if (!empty($params[$param])) {
        $id = CRM_Utils_Array::value('id', civicrm_api3(ucwords($param), 'get', ['contact_id' => $contactID, 'is_primary' => TRUE, 'options' => ['limit' => 1]]));
        $apiParams = [
          'id' => $id,
          'contact_id' => $contactID,
          $param => $params[$param],
          'location_type_id' => 'Work',
          'is_primary' => TRUE,
        ];
        if ($param == 'address') {
          $apiParams['city'] = $params['city'];
          $apiParams['street_address'] = $params[$param];
        }
        civicrm_api3(ucwords($param), 'create', $apiParams);
      }
    }
  }

  public function getFileUpload($entityID, $tableName, $columnName, $fieldID) {
    if (!$entityID) {
      return NULL;
    }
    $fileID = CRM_Core_DAO::singleValueQuery("SELECT $columnName FROM $tableName WHERE entity_id = $entityID LIMIT 1");
    if (!empty($fileID)) {
      $fileDAO = new CRM_Core_DAO_File();
      $fileDAO->id = $fileID;
      if ($fileDAO->find(TRUE)) {
        $fileHash = CRM_Core_BAO_File::generateFileHash($entityID, $fileID);
        $displayURL = CRM_Utils_System::url('civicrm/file', "reset=1&id=$fileID&eid=$entityID&fcs=$fileHash");
        $deleteExtra = ts('Are you sure you want to delete attached file.');
        $deleteURL = [
          CRM_Core_Action::DELETE => [
            'name' => ts('Delete Attached File'),
            'url' => 'civicrm/file',
            'qs' => 'reset=1&id=%%id%%&eid=%%eid%%&fid=%%fid%%&action=delete&fcs=%%fcs%%',
            'extra' => 'onclick = "if (confirm( \'' . $deleteExtra
            . '\' ) ) this.href+=\'&amp;confirmed=1\'; else return false;"',
          ],
        ];
        $deleteURL = CRM_Core_Action::formLink($deleteURL,
          CRM_Core_Action::DELETE,
          [
            'id' => $fileID,
            'eid' => $entityID,
            'fid' => $fieldID,
            'fcs' => $fileHash,
          ],
          ts('more'),
          FALSE,
          'file.manage.delete',
          'File',
          $fileID
        );
        $fileName = CRM_Utils_File::cleanFileName(basename($fileDAO->uri));
        if ($fileDAO->mime_type == "image/jpeg" ||
          $fileDAO->mime_type == "image/pjpeg" ||
          $fileDAO->mime_type == "image/gif" ||
          $fileDAO->mime_type == "image/x-png" ||
          $fileDAO->mime_type == "image/png"
        ) {
          $entityId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_EntityFile',
            $fileDAO->id,
            'entity_id',
            'file_id'
          );
          $url = str_replace('persist/contribute', 'custom', $config->imageUploadURL) . $fileDAO->uri;
          list($path) = CRM_Core_BAO_File::path($fileDAO->id, $entityId);
          if ($path && file_exists($path)) {
            list($imageWidth, $imageHeight) = getimagesize($path);
            list($imageThumbWidth, $imageThumbHeight) = CRM_Contact_BAO_Contact::getThumbSize($imageWidth, $imageHeight);
            $displayURL = "<img src='$displayURL' width=$imageThumbWidth height=$imageThumbHeight />";
          }
        }
        else {
          $displayURL = "<a href=\"$displayURL\">$fileName</a>";
        }
        return [
          'deleteURL' => $deleteURL,
          'displayURL' => $displayURL,
        ];
      }
    }
  }

}
