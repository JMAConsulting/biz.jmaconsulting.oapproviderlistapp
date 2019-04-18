<?php

class CRM_Oapproviderlistapp_Form_Report_ProviderList extends CRM_Report_Form_Contact_Summary {
  protected $_customGroupExtends = array(
      'Contact',
      'Individual',
      'Household',
      'Organization',
      'Relationship',
    );

  public function __construct() {
    parent::__construct();
    $this->_columns['civicrm_contact']['fields']['created_date'] = [
      'title' => ts('Created Date'),
      'default' => FALSE,
    ];
  }

  public function from() {
    $this->_from = "
        FROM civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom} ";
    $this->joinAddressFromContact();
    $this->joinPhoneFromContact();
    $this->joinEmailFromContact();
    $this->joinCountryFromAddress();
  }

  /**
   * Add custom data to the columns.
   *
   * @param bool $addFields
   * @param array $permCustomGroupIds
   */
  public function addCustomDataToColumns($addFields = TRUE, $permCustomGroupIds = []) {
    if (empty($this->_customGroupExtends)) {
      return;
    }
    if (!is_array($this->_customGroupExtends)) {
      $this->_customGroupExtends = [$this->_customGroupExtends];
    }
    $customGroupWhere = '';
    if (!empty($permCustomGroupIds)) {
      $customGroupWhere = "cg.id IN (" . implode(',', $permCustomGroupIds) .
        ") AND";
    }
    $sql = "
SELECT cg.table_name, cg.title, cg.extends, cf.id as cf_id, cf.label,
       cf.column_name, cf.data_type, cf.html_type, cf.option_group_id, cf.time_format
FROM   civicrm_custom_group cg
INNER  JOIN civicrm_custom_field cf ON cg.id = cf.custom_group_id
WHERE (cg.extends IN ('" . implode("','", $this->_customGroupExtends) . "') OR (cg.extends = 'Activity' AND cg.extends_entity_column_value = 56)) AND
      {$customGroupWhere}
      cg.is_active = 1 AND
      cf.is_active = 1 AND
      cf.is_searchable = 1
ORDER BY cg.weight, cf.weight";
    $customDAO = CRM_Core_DAO::executeQuery($sql);

    $curTable = NULL;
    while ($customDAO->fetch()) {
      if ($customDAO->table_name != $curTable) {
        $curTable = $customDAO->table_name;
        $curFields = $curFilters = [];

        // dummy dao object
        $this->_columns[$curTable]['dao'] = 'CRM_Contact_DAO_Contact';
        $this->_columns[$curTable]['extends'] = $customDAO->extends;
        $this->_columns[$curTable]['grouping'] = $customDAO->table_name;
        $this->_columns[$curTable]['group_title'] = $customDAO->title;

        foreach (['fields', 'filters', 'group_bys'] as $colKey) {
          if (!array_key_exists($colKey, $this->_columns[$curTable])) {
            $this->_columns[$curTable][$colKey] = [];
          }
        }
      }
      $fieldName = 'custom_' . $customDAO->cf_id;

      if ($addFields) {
        // this makes aliasing work in favor
        $curFields[$fieldName] = [
          'name' => $customDAO->column_name,
          'title' => $customDAO->label,
          'dataType' => $customDAO->data_type,
          'htmlType' => $customDAO->html_type,
        ];
      }
      if ($this->_customGroupFilters) {
        // this makes aliasing work in favor
        $curFilters[$fieldName] = [
          'name' => $customDAO->column_name,
          'title' => $customDAO->label,
          'dataType' => $customDAO->data_type,
          'htmlType' => $customDAO->html_type,
        ];
      }

      switch ($customDAO->data_type) {
        case 'Date':
          // filters
          $curFilters[$fieldName]['operatorType'] = CRM_Report_Form::OP_DATE;
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_DATE;
          // CRM-6946, show time part for datetime date fields
          if ($customDAO->time_format) {
            $curFields[$fieldName]['type'] = CRM_Utils_Type::T_TIMESTAMP;
          }
          break;

        case 'Boolean':
          $curFilters[$fieldName]['operatorType'] = CRM_Report_Form::OP_SELECT;
          $curFilters[$fieldName]['options'] = ['' => ts('- select -')] + CRM_Core_PseudoConstant::get('CRM_Core_BAO_CustomField', 'custom_' . $customDAO->cf_id, [], 'search');
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_INT;
          break;

        case 'Int':
          $curFilters[$fieldName]['operatorType'] = CRM_Report_Form::OP_INT;
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_INT;
          break;

        case 'Money':
          $curFilters[$fieldName]['operatorType'] = CRM_Report_Form::OP_FLOAT;
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_MONEY;
          $curFields[$fieldName]['type'] = CRM_Utils_Type::T_MONEY;
          break;

        case 'Float':
          $curFilters[$fieldName]['operatorType'] = CRM_Report_Form::OP_FLOAT;
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_FLOAT;
          break;

        case 'String':
        case 'StateProvince':
        case 'Country':
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_STRING;

          $options = CRM_Core_PseudoConstant::get('CRM_Core_BAO_CustomField', 'custom_' . $customDAO->cf_id, [], 'search');
          if ((is_array($options) && count($options) != 0) || (!is_array($options) && $options !== FALSE)) {
            $curFilters[$fieldName]['operatorType'] = CRM_Core_BAO_CustomField::isSerialized($customDAO) ? CRM_Report_Form::OP_MULTISELECT_SEPARATOR : CRM_Report_Form::OP_MULTISELECT;
            $curFilters[$fieldName]['options'] = $options;
          }
          break;

        case 'ContactReference':
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_STRING;
          $curFilters[$fieldName]['name'] = 'display_name';
          $curFilters[$fieldName]['alias'] = "contact_{$fieldName}_civireport";

          $curFields[$fieldName]['type'] = CRM_Utils_Type::T_STRING;
          $curFields[$fieldName]['name'] = 'display_name';
          $curFields[$fieldName]['alias'] = "contact_{$fieldName}_civireport";
          break;

        default:
          $curFields[$fieldName]['type'] = CRM_Utils_Type::T_STRING;
          $curFilters[$fieldName]['type'] = CRM_Utils_Type::T_STRING;
      }

      // CRM-19401 fix
      if ($customDAO->html_type == 'Select' && !array_key_exists('options', $curFilters[$fieldName])) {
        $options = CRM_Core_PseudoConstant::get('CRM_Core_BAO_CustomField', 'custom_' . $customDAO->cf_id, [], 'search');
        if ($options !== FALSE) {
          $curFilters[$fieldName]['operatorType'] = CRM_Core_BAO_CustomField::isSerialized($customDAO) ? CRM_Report_Form::OP_MULTISELECT_SEPARATOR : CRM_Report_Form::OP_MULTISELECT;
          $curFilters[$fieldName]['options'] = $options;
        }
      }

      if (!array_key_exists('type', $curFields[$fieldName])) {
        $curFields[$fieldName]['type'] = CRM_Utils_Array::value('type', $curFilters[$fieldName], []);
      }

      if ($addFields) {
        $this->_columns[$curTable]['fields'] = array_merge($this->_columns[$curTable]['fields'], $curFields);
      }
      if ($this->_customGroupFilters) {
        $this->_columns[$curTable]['filters'] = array_merge($this->_columns[$curTable]['filters'], $curFilters);
      }
      if ($this->_customGroupGroupBy) {
        $this->_columns[$curTable]['group_bys'] = array_merge($this->_columns[$curTable]['group_bys'], $curFields);
      }
    }
  }

}
