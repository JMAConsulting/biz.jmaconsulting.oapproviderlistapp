<?php

class CRM_Oapproviderlistapp_Form_Report_ProviderList extends CRM_Report_Form_Contact_Summary {
  protected $_customGroupExtends = array(
      'Contact',
      'Individual',
      'Household',
      'Organization',
      'Relationship',
      'Activity',
    );

  public function __construct() {
    parent::__construct();
    $this->_columns['civicrm_contact']['fields']['created_date'] = [
      'title' => ts('Created Date'),
      'default' => FALSE,
    ];
  }

  public function postProcess() {

    $this->beginPostProcess();

    // get the acl clauses built before we assemble the query
    $this->buildACLClause($this->_aliases['civicrm_contact']);

    $sql = $this->buildQuery(TRUE);
    $match = "LEFT JOIN civicrm_value_proof_of_empl_13 value_proof_of_empl_13_civireport ON value_proof_of_empl_13_civireport.entity_id = .id";
    $replace = " LEFT JOIN civicrm_relationship re ON re.contact_id_a = contact_civireport.id AND re.contact_id_b = contact_civireport.employer_id
       LEFT JOIN civicrm_value_proof_of_empl_13 value_proof_of_empl_13_civireport ON value_proof_of_empl_13_civireport.entity_id = re.id
     ";
    $sql = str_replace($match, $replace, $sql);
    $this->_from = str_replace($match, $replace, $this->_from);

    $match = "LEFT JOIN civicrm_value_signature_14 value_signature_14_civireport ON value_signature_14_civireport.entity_id = .id";
    $replace = "LEFT JOIN civicrm_activity_contact cac ON cac.contact_id = contact_civireport.id AND cac.record_type_id = 2
    LEFT JOIN civicrm_activity ca ON ca.id = cac.activity_id AND ca.activity_type_id = 56
    LEFT JOIN civicrm_value_signature_14 value_signature_14_civireport ON value_signature_14_civireport.entity_id = ca.id AND value_signature_14_civireport.entity_id IS NOT NULL
    ";
    $sql = str_replace($match, $replace, $sql);
    $this->_from = str_replace($match, $replace, $this->_from);

    $rows = $graphRows = array();
    $this->buildRows($sql, $rows);

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }

  /**
   * Alter the way in which custom data fields are displayed.
   *
   * @param array $rows
   */
  public function alterCustomDataDisplay(&$rows) {
    // custom code to alter rows having custom values
    if (empty($this->_customGroupExtends)) {
      return;
    }

    $customFields = [];
    $customFieldIds = $fileFields = [];
    foreach ($this->_params['fields'] as $fieldAlias => $value) {
      if ($fieldId = CRM_Core_BAO_CustomField::getKeyID($fieldAlias)) {
        $customFieldIds[$fieldAlias] = $fieldId;
      }
    }
    if (empty($customFieldIds)) {
      return;
    }

    // skip for type date and ContactReference since date format is already handled
    $query = "
SELECT cg.table_name, cf.id, cf.data_type
FROM  civicrm_custom_field cf
INNER JOIN civicrm_custom_group cg ON cg.id = cf.custom_group_id
WHERE cg.extends IN ('" . implode("','", $this->_customGroupExtends) . "') AND
      cg.is_active = 1 AND
      cf.is_active = 1 AND
      cf.is_searchable = 1 AND
      cf.data_type   NOT IN ('ContactReference', 'Date') AND
      cf.id IN (" . implode(",", $customFieldIds) . ")";

    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      $customFields[$dao->table_name . '_custom_' . $dao->id] = $dao->id;
      if ($dao->data_type == 'File') {
        $fileFields[$dao->table_name . '_custom_' . $dao->id] = $dao->id;
      }
    }
    $dao->free();

    $entryFound = FALSE;
    foreach ($rows as $rowNum => $row) {
      foreach ($row as $tableCol => $val) {
        if (array_key_exists($tableCol, $customFields)) {
          if (array_key_exists($tableCol, $fileFields)) {
            $currentAttachmentInfo = CRM_Core_BAO_File::getEntityFile('*', $val);
            foreach ($currentAttachmentInfo as $fileKey => $fileValue) {
              $rows[$rowNum][$tableCol] = ($this->_outputMode == 'csv') ? CRM_Utils_System::url($fileValue['url'], NULL, TRUE) : sprintf('<a href='%s'>%s</a>', CRM_Utils_System::url($fileValue['url'], NULL, TRUE), $fileValue['cleanName']); 
            }
          }
          else {
            $rows[$rowNum][$tableCol] = CRM_Core_BAO_CustomField::displayValue($val, $customFields[$tableCol]);
          }
          $entryFound = TRUE;
        }
      }

      // skip looking further in rows, if first row itself doesn't
      // have the column we need
      if (!$entryFound) {
        break;
      }
    }
  }

}
