<?php

require_once 'CRM/Core/Form.php';
use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_EditListing extends CRM_Core_Form {

  public $_contactId;

  function __construct(&$formValues) {
    parent::__construct($formValues);
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'css/style.css');
    CRM_Core_Resources::singleton()->addStyleFile('org.civicrm.shoreditch', 'css/custom-civicrm.css',1, 'html-header');
  }

  function preProcess() {
    $this->_contactId = CRM_Core_Session::getLoggedInContactID();
    if (empty($this->_contactId)) {
      CRM_Core_Error::fatal(E::ts('You must be logged in to view this form.'));
    }
    $this->_action = CRM_Utils_Request::retrieve('action', 'Positive', $this);
    if (empty($this->_action)) {
      $this->_action = CRM_Core_Action::UPDATE;
    }
  }

  function setDefaultValues() {
    $result = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => "Contact_general",
      'return' => 'id',
    ])['values'];
    foreach ($result as $fid) {
      $fieldIds[] = $fid['id'];
    }
    $customValues = CRM_Core_BAO_CustomValueTable::getEntityValues($this->_contactId, 'Contact', $fieldIds);
    foreach ($customValues as $key => $value) {
      $customValues['custom_' . $key] = $value;
      if ($key == 67) {
        $value = array_filter(explode(CRM_Core_DAO::VALUE_SEPARATOR, $value));
        foreach ($value as $val) {
          $vals[$val] = 1;
        }
        $customValues['custom_' . $key] = $vals;
      }
      unset($customValues[$key]);
    }
    return $customValues;
  }

  function buildQuickForm() {
    $customGroups = [
      "Contact_general",
    ];
    foreach ($customGroups as $group) {
      $result = civicrm_api3('CustomField', 'get', [
        'sequential' => 1,
        'custom_group_id' => $group,
        'options' => ['sort' => "weight"],
      ])['values'];
      foreach ($result as $field => $value) {
        $name = sprintf("%s_%d", "custom", $value['id']);
        if (strtolower($value['html_type']) == 'multi-select') {
          $this->addEntityRef($name, E::ts($value['label']), [
            'entity' => 'OptionValue',
            'placeholder' => E::ts('- any -'),
            'multiple' => 1,
            'api' => [
              'params' => [
                'check_permissions' => FALSE,
                'option_group_id' => $value['option_group_id'],
              ],
            ],
          ]);
        }
        elseif (strtolower($value['html_type']) == 'checkbox') {
          $options = CRM_Core_OptionGroup::values(CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', $value['option_group_id'], 'name', 'id'));
          $this->addCheckBox($name, E::ts("%1", [1 => $value['label']]), $options, NULL, NULL, NULL, NULL, ' &nbsp; ');
        }
        elseif (strtolower($value['html_type']) == 'radio') {
          $this->addYesno($name, E::ts("%1", [1 => $value['label']]), NULL);
        }
        else {
          $this->add(strtolower($value['html_type']), $name, E::ts("%1", [1 => $value['label']]), NULL);
        }
      }
    }
    $this->add('file', 'image_URL', E::ts('Image'));
    $this->addUploadElement('image_URL');
    $this->assign('elementNames', $this->getRenderableElementNames());
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
    if ($this->_action & CRM_Core_Action::VIEW) {
      foreach ($this->getRenderableElementNames() as $element) {
        $this->freeze($element);
      }
    }
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->controller->exportValues($this->_name);
    foreach ($values as $field => $value) {
      if (!is_array($value) && strpos($value, ',') !== false) {
        $value = explode(',', $value);
      }
      if (strpos($field, 'custom_') !== false) {
        $customValues[$field] = $value;
      }
    }
    CRM_Core_BAO_CustomValueTable::postProcess($customValues,
      'civicrm_contact',
      $this->_contactId,
      'Individual'
    );
    if (!empty($values['image_URL'])) {
      CRM_Contact_BAO_Contact::processImageParams($values);
      civicrm_api3('Contact', 'create', [
        'id' => $this->_contactId,
        'image_URL' => $values['image_URL'],
      ]);
    }
    parent::postProcess();
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

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
