<?php

require_once 'CRM/Core/Form.php';
use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Oapproviderlistapp_Form_EditListing extends CRM_Core_Form {
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
        $name = sprintf("%s_%d", "custom_", $value['id']);
        if (strtolower($value['html_type']) == 'multi-select') {
          $this->addEntityRef('language', E::ts($value['label']), [
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
        if (strtolower($value['html_type']) == 'checkbox') {
          $options = CRM_Core_OptionGroup::values(CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', $value['option_group_id'], 'name', 'id'));
          $this->addCheckBox($name, E::ts("%1", [1 => $value['label']]), $options, NULL, NULL, NULL, NULL, ' &nbsp; ');
        }
        elseif (strtolower($value['html_type']) == 'radio') {
          $this->addYesno($name, E::ts("%1", [1 => $value['label']]), NULL);
        }
	elseif (strtolower($value['html_type']) != 'multi-select') {
          $this->add(strtolower($value['html_type']), $name, E::ts("%1", [1 => $value['label']]), NULL);
        }
      }
    }
    $this->assign('elementNames', $this->getRenderableElementNames());
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ),
    ));
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    CRM_Core_Error::debug('ga', $values);exit;
    parent::postProcess();
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
