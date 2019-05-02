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
  }

  function preProcess() {
    if (!CRM_Core_Permission::check('edit my listing')) {
      return CRM_Utils_System::permissionDenied();
    }
    $this->_contactId = CRM_Core_Session::getLoggedInContactID();
    if (empty($this->_contactId)) {
      return CRM_Utils_System::permissionDenied();
    }
    $this->_action = CRM_Utils_Request::retrieve('action', 'Positive', $this);
    if (empty($this->_action)) {
      $this->_action = CRM_Core_Action::UPDATE;
    }
  }

  function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(OAP_LISTING, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->_contactID, $fields, $defaults, TRUE);
    return $defaults;
  }

  function buildQuickForm() {
    $this->buildCustom(OAP_LISTING, 'listing');
    $this->assign('employers', $this->getEmployers($this->_contactId));
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
  }

  function postProcess() {
    $values = $this->controller->exportValues($this->_name);
    $fields = [];
    CRM_Contact_BAO_Contact::createProfileContact($params, $fields);
    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url("civicrm/editlisting",
      "reset=1&action=4&cid=" . $this->_contactId
    ));
    CRM_Core_Session::setStatus(E::ts('Your provider listing has been updated.'), ts('Listing Updated'), 'success');
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
