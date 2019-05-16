<?php

require_once 'CRM/Core/Form.php';
use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_EditListing extends CRM_Oapproviderlistapp_Form_ManageApplication {

  public $_contactId;

  function __construct(&$formValues) {
    parent::__construct($formValues);
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'css/style.css');
    CRM_Core_Resources::singleton()->addStyleFile('org.civicrm.shoreditch', 'css/custom-civicrm.css',1, 'html-header');
    $this->_contactId = CRM_Core_Session::getLoggedInContactID();
  }

  function preProcess() {
    if (!CRM_Core_Permission::check('edit my listing')) {
      return CRM_Utils_System::permissionDenied();
    }
    if (empty($this->_contactId)) {
      return CRM_Utils_System::permissionDenied();
    }
    $this->_action = CRM_Utils_Request::retrieve('action', 'Positive', $this);
    if (empty($this->_action)) {
      $this->_action = CRM_Core_Action::UPDATE;
    }
    $this->assign('action', $this->_action);
  }

  function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_LISTING, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactId, $fields, $defaults, TRUE);
    if (!empty($defaults['image_URL'])) {
      $url = $defaults['image_URL'];
      list($width, $height) = getimagesize(CRM_Utils_String::unstupifyUrl($url));
      list($thumbWidth, $thumbHeight) = CRM_Contact_BAO_Contact::getThumbSize($width, $height);
      $image_URL = '<img src="' . $url . '" height= ' . $thumbHeight . ' width= ' . $thumbWidth . '  />';
      $this->assign('imageURL', "<a href='#' onclick='contactImagePopUp(\"{$url}\", 180, 200);'>{$image_URL}</a>");
      if ($this->_action == CRM_Core_Action::VIEW) {
        unset($defaults['image_URL']);
      }
      else {
        $deleteExtra = json_encode(ts('Are you sure you want to delete contact image.'));
        $deleteURL = array(
          CRM_Core_Action::DELETE => array(
            'name' => ts('Delete Contact Image'),
            'url' => 'civicrm/contact/image',
            'qs' => 'reset=1&id=%%id%%&gid=%%gid%%&action=delete',
            'extra' => 'onclick = "' . htmlspecialchars("if (confirm($deleteExtra)) this.href+='&confirmed=1'; else return false;") . '"',
          ),
        );
        $deleteURL = CRM_Core_Action::formLink($deleteURL,
          CRM_Core_Action::DELETE,
          array(
            'id' => $this->_contactId,
            'gid' => OAP_LISTING,
          ),
          ts('more'),
          FALSE,
          'contact.profileimage.delete',
          'Contact',
          $this->_contactId
        );
        $this->assign('deleteURL', $deleteURL);
      }
    }
    return $defaults;
  }

  function buildQuickForm() {
    $title = ($this->_action == CRM_Core_Action::VIEW) ? E::ts('View My Listing') : E::ts('Edit My Listing');
    CRM_Utils_System::setTitle($title);
    $this->buildCustom(OAP_LISTING, 'listing', ($this->_action == CRM_Core_Action::VIEW));
    $this->assign('name', CRM_Contact_BAO_Contact::displayName($this->_contactId));
    $this->assign('employers', $this->getEmployers($this->_contactId));
    $this->assign('credentials', $this->getCredentials($this->_contactId));
    $this->assign('disciplinary', $this->getDisciplinaryActions($this->_contactId));
    $this->addButtons(array(
      array(
        'type' => 'upload',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ),
    ));
    $this->addFormRule(array('CRM_Oapproviderlistapp_Form_EditListing', 'imageRule'), $this);
  }

  function imageRule($fields, $files = array(), $self = NULL) {
    if (empty($files['image_URL']['name'])) {
      return TRUE;
    }
    $errors = [];
    $mimeType = array(
      'image/jpeg',
      'image/jpg',
      'image/png',
      'image/p-jpeg',
      'image/x-png',
    );
    if (!in_array($files['image_URL']['type'], $mimeType)) {
      $errors['image_URL'] = E::ts('Image could not be uploaded due to invalid type extension.');
    }
    $maxSize = CRM_Core_Config::singleton()->maxFileSize * 1024*1024;
    if (empty($maxSize)) {
      $maxSize = $fields['MAX_FILE_SIZE'];
    }
    if ($files['image_URL']['size'] > $maxSize) {
      $errors['image_URL'] = E::ts('Maximum file size cannot exceed upload max size');
    }
    return empty($errors) ? TRUE : $errors;
  }

  function postProcess() {
    $values = $this->controller->exportValues($this->_name);
    CRM_Contact_BAO_Contact::processImageParams($values);
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_LISTING, FALSE, CRM_Core_Action::VIEW);
    CRM_Contact_BAO_Contact::createProfileContact($values, $fields, $this->_contactId, NULL, OAP_LISTING);
    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/editlisting",
      "reset=1&action=4"
    ));
    CRM_Core_Session::setStatus(E::ts('Your provider listing has been updated.'), ts('Listing Updated'), 'success');
  }

  function getDisciplinaryActions($cid) {
    $sql = "SELECT * FROM civicrm_value_disciplinary_20 c
      WHERE c.entity_id = %1";
    $disc = CRM_Core_DAO::executeQuery($sql, [1 => [$cid, 'Integer']])->fetchAll();
    $field = '';
    if (!empty($disc)) {
      if (!empty($disc[0]['bacb_r_disciplinary_action_71'])) {
        $field = "<a href='" . $disc[0]['bacb_r_disciplinary_action_71'] . "'>" . E::ts('BACB(r) Disciplinary Action') . "</a>";
      }
      if (!empty($disc[0]['bacb_r_disciplinary_action_71']) && !empty($disc[0]['cpo_discipline_and_other_proceed_72'])) {
        $field .= ", ";
      }
      if (!empty($disc[0]['cpo_discipline_and_other_proceed_72'])) {
        $field .= "<a href='" . $disc[0]['cpo_discipline_and_other_proceed_72'] . "'>" . E::ts('CPO Discipline and Other Proceedings') . "</a>";
      }
      return $field;
    }
  }

  function getCredentials($cid) {
    $sql = "SELECT c.which_of_the_following_credentia_7 FROM civicrm_value_applicant_det_4 c
      WHERE c.entity_id = %1";
    $credentials = CRM_Core_DAO::executeQuery($sql, [1 => [$cid, 'Integer']])->fetchAll();
    if (!empty($credentials[0]['which_of_the_following_credentia_7'])) {
      $options = CRM_Core_OptionGroup::values('which_of_the_following_credentia_20190321014056');
      $creds = array_filter(explode(CRM_Core_DAO::VALUE_SEPARATOR, $credentials[0]['which_of_the_following_credentia_7']));
      foreach ($creds as $cred) {
        $allCreds[] = $options[$cred];
      }
      $credentials[0]['which_of_the_following_credentia_7'] = implode(', ', $allCreds);
    }
    if (!empty($credentials)) {
      return $credentials;
    }
  }

  function getEmployers($cid) {
    $sql = "SELECT o.id, o.organization_name, a.street_address, a.city, a.postal_code, e.email, p.phone, sp.abbreviation FROM civicrm_contact o
      INNER JOIN civicrm_relationship r ON r.contact_id_b = o.id
      LEFT JOIN civicrm_address a ON a.contact_id = o.id AND a.location_type_id = 2
      LEFT JOIN civicrm_state_province sp ON sp.id = a.state_province_id
      LEFT JOIN civicrm_email e ON e.contact_id = %1 AND e.location_type_id = 2
      LEFT JOIN civicrm_phone p ON p.contact_id = %1 AND p.location_type_id = 2
      WHERE r.contact_id_a = %1 AND r.relationship_type_id = %2
      GROUP BY o.id";
    $rtype = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_RelationshipType', 'Employee of', 'id', 'name_a_b');
    $employers = CRM_Core_DAO::executeQuery($sql, [1 => [$cid, 'Integer'], 2 => [$rtype, 'Integer']])->fetchAll();
    if (!empty($employers)) {
      return $employers;
    }
  }

  public function getTemplateFileName() {
    return 'CRM/Oapproviderlistapp/Form/EditListing.tpl';
  }
}
