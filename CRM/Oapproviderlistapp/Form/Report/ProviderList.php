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
    unset($this->_columns['civicrm_contact']['fields']['employer_id']);
    $this->_columns['civicrm_contact']['fields']['employer'] = [
      'title' => ts('Employer(s)'),
      'dbAlias' => "'1'",
      'default' => FALSE,
    ];
    $this->_columns['civicrm_value_proof_of_empl_13']['fields']['custom_49']['dbAlias'] = '1';
    $this->_columns['civicrm_value_other_profess_12']['fields']['custom_44']['dbAlias'] = 'GROUP_CONCAT(other_relevant_credential_44)';
    $this->_columns['civicrm_value_other_profess_12']['fields']['custom_45']['dbAlias'] = 'GROUP_CONCAT(date_obtained_45)';
    $this->_columns['civicrm_value_employment_hi_10']['fields']['custom_32']['dbAlias'] = 'GROUP_CONCAT(employer_organization_32)';
    $this->_columns['civicrm_value_employment_hi_10']['fields']['custom_33']['dbAlias'] = 'GROUP_CONCAT(position_title_33)';
    $this->_columns['civicrm_value_employment_hi_10']['fields']['custom_47']['dbAlias'] = 'GROUP_CONCAT(start_date_47)';
    $this->_columns['civicrm_value_employment_hi_10']['fields']['custom_47']['type'] = CRM_Utils_Type::T_STRING;
    $this->_columns['civicrm_value_employment_hi_10']['fields']['custom_48']['dbAlias'] = 'GROUP_CONCAT(end_date_48)';
    $this->_columns['civicrm_value_employment_hi_10']['fields']['custom_48']['type'] = CRM_Utils_Type::T_STRING;
    $this->_columns['civicrm_value_employment_hi_10']['fields']['custom_35']['dbAlias'] = 'GROUP_CONCAT(main_tasks_that_involved_deliver_35)';
  }

  public function groupBy() {
    $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_contact']}.id ";
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
            if (!CRM_Utils_Rule::integer($val)) {
              continue;
            }
            $currentAttachmentInfo = CRM_Core_BAO_File::getEntityFile('*', $val);
            foreach ($currentAttachmentInfo as $fileKey => $fileValue) {
              $rows[$rowNum][$tableCol] = ($this->_outputMode == 'csv') ? CRM_Utils_System::url($fileValue['url'], NULL, TRUE) : sprintf("<a href='%s'>%s</a>", CRM_Utils_System::url($fileValue['url'], NULL, TRUE), $fileValue['cleanName']);
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

  /**
   * Alter display of rows.
   *
   * Iterate through the rows retrieved via SQL and make changes for display purposes,
   * such as rendering contacts as links.
   *
   * @param array $rows
   *   Rows generated by SQL, with an array for each row.
   */
  public function alterDisplay(&$rows) {
    $dateFormat = CRM_Core_Config::singleton()->dateformatTime;
    $entryFound = FALSE;

    foreach ($rows as $rowNum => $row) {
      $employerInfo = CRM_Oapproviderlistapp_Page_Details::getAdditionalDetails($row['civicrm_contact_id'], TRUE)['employers'];

      foreach ([
      'civicrm_contact_employer' => 'organization_name',
      'civicrm_address_address_city' => 'city',
      'civicrm_address_address_street_address' => 'street_address',
      'civicrm_address_postal_code' => 'postal_code',
      'civicrm_email_email' => 'email',
      'civicrm_phone_phone' => 'phone',
      ] as $column => $name) {
        if (!empty($row[$column])) {
          $rows[$rowNum][$column] = implode(', ', array_filter(CRM_Utils_Array::collect($name, $employerInfo)));
        }
      }

      // make count columns point to detail report
      // convert sort name to links
      if (array_key_exists('civicrm_contact_sort_name', $row) &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Report_Utils_Report::getNextUrl('contact/detail',
          'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
          $this->_absoluteUrl, $this->_id, $this->_drilldownReport
        );
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts('View Contact Detail Report for this contact');
        $entryFound = TRUE;
      }

      foreach (['civicrm_value_other_profess_12_custom_45', 'civicrm_value_employment_hi_10_custom_48', 'civicrm_value_employment_hi_10_custom_47'] as $k) {
        if (!empty($rows[$rowNum][$k])) {
          $value = (array) explode(',', $rows[$rowNum][$k]);
          $v = [];
          foreach($value as $date) {
            $v[] = date("F j Y g:i a", strtotime($date));
          }
          $rows[$rowNum][$k] = implode(', ', $v);
          $entryFound = TRUE;
        }
      }


      if (!empty($rows[$rowNum]['civicrm_value_proof_of_empl_13_custom_49'])) {
         $values = (array) explode(',', CRM_Core_DAO::singleValueQuery("
          SELECT GROUP_CONCAT(proof_of_employment_letter_49)
           FROM civicrm_value_proof_of_empl_13 temp
            LEFT JOIN civicrm_relationship r ON temp.entity_id = r.id AND r.relationship_type_id = 5
          WHERE r.contact_id_a = " . $rows[$rowNum]['civicrm_contact_id']));
          if (!empty($values)) {
            foreach ($values as $k => $val) {
              $currentAttachmentInfo = CRM_Core_BAO_File::getEntityFile('*', $val);
              foreach ($currentAttachmentInfo as $fileKey => $fileValue) {
                $values[$k] = ($this->_outputMode == 'csv') ? CRM_Utils_System::url($fileValue['url'], NULL, TRUE) : sprintf("<a href='%s'>%s</a>", CRM_Utils_System::url($fileValue['url'], NULL, TRUE), $fileValue['cleanName']);
              }
            }
          }

          $rows[$rowNum]['civicrm_value_proof_of_empl_13_custom_49'] = implode(', ', $values);
          $entryFound = TRUE;
      }

      // Handle ID to label conversion for contact fields
      $entryFound = $this->alterDisplayContactFields($row, $rows, $rowNum, 'contact/summary', 'View Contact Summary') ? TRUE : $entryFound;

      // skip looking further in rows, if first row itself doesn't
      // have the column we need
      if (!$entryFound) {
        break;
      }
    }
  }

  public function getEmployers($contactID) {
    return CRM_Core_DAO::singleValueQuery("SELECT GROUP_CONCAT(organization_name) FROM civicrm_contact c LEFT JOIN civicrm_relationship r ON r.contact_id_b = c.id AND r.relationship_type_id = 5 WHERE r.contact_id_a = $contactID ");
  }

}
