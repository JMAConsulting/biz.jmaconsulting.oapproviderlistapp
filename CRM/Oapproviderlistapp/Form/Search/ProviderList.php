<?php
use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * A custom contact search
 */
class CRM_Oapproviderlistapp_Form_Search_ProviderList extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {
  public $_languages;
  public $_searchByOrg;
  function __construct(&$formValues) {
    parent::__construct($formValues);
    $this->_searchByOrg = (bool) $formValues['search_by_org'];
    $this->_languages = CRM_Core_OptionGroup::values('languages');
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'css/style.css');
    CRM_Core_Resources::singleton()->addStyleFile('org.civicrm.shoreditch', 'css/custom-civicrm.css',1, 'html-header');
  }

  /**
   * Prepare a set of search fields
   *
   * @param CRM_Core_Form $form modifiable
   * @return void
   */
  function buildForm(&$form) {
    CRM_Utils_System::setTitle(E::ts('Search the OAP Provider List'));

    $form->addElement('checkbox', 'accepting_clients_filter', E::ts('Show only if accepting new clients') . '?', NULL);
    $form->addElement('checkbox', 'remote_travel_filter', E::ts('Travels to remote areas') . '?', NULL);
    $form->addElement('checkbox', 'supervision_filter', E::ts('Offers supervision') . '?', NULL);
    $form->addElement('checkbox', 'videoconferencing_filter', E::ts('Offers remote services') . '?', NULL);
    $check = [];
    foreach (['East', 'Central', 'Toronto', 'North', 'West'] as $key) {
      $check[] = $form->createElement('radio', NULL, '', E::ts($key), $key, ['allowClear' => TRUE]);
    }
    $group = $form->addGroup($check, 'region', E::ts('Region'));
    $group->setAttribute('allowClear', TRUE);

    $check = [];
    foreach ([
      1 => E::ts('Board Certified Behavior Analyst® (BCBA®)'),
      2 => E::ts('Board Certified Behavior Analyst-Doctoral (BCBA-D™)'),
      3 => E::ts('Registered Psychologist'),
      4 => E::ts('Registered Psychological Associate'),
    ] as $key => $label) {
      $check[] = &$form->addElement('checkbox', $key, NULL, $label, 'ts_sel', array('checked' => 'checked'));
    }
    $form->addGroup($check, 'credentials', E::ts('I am looking for'));
    $form->setDefaults([
      'East' => 1,
      'Central' => 1,
      'North' => 1,
      'South' => 1,
      1 => 1,
      2 => 1,
      3 => 1,
      4 => 1,
    ]);

    $form->addEntityRef('language', E::ts('Language'), [
      'entity' => 'OptionValue',
      'placeholder' => E::ts('- any -'),
      'multiple' => 1,
      'api' => [
        'params' => [
          'check_permissions' => FALSE,
          'option_group_id' => 'languages',
        ],
      ],
    ]);
    $form->addElement('checkbox', 'search_by_org',  '', NULL);
    $form->add('text',
      'provider_name',
      E::ts('Provider Name'),
      ['class' => 'huge']
    );
    $form->add('text',
      'organization_name',
      E::ts('Organization Name'),
      ['class' => 'huge']
    );
    $form->add('text',
      'city',
      E::ts('City'),
      ['class' => 'huge']
    );
    $form->assign('searchByOrg',  $this->_searchByOrg);

    $form->assign('elements', array(
      'credentials',
      'region',
      'language',
      'provider_name',
      'organization_name',
      'city',
      'accepting_clients_filter',
      'remote_travel_filter',
      'supervision_filter',
      'videoconferencing_filter',
      'search_by_org',
    ));

  }

  /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  function summary() {
    return NULL;
    // return array(
    //   'summary' => 'This is a summary',
    //   'total' => 50.0,
    // );
  }

  /**
   * Get a list of displayable columns
   *
   * @return array, keys are printable column headers and values are SQL column names
   */
  function &columns() {
    if ($this->_searchByOrg) {
      $columns = array(
        'contact_id',
        'organization_name',
        'postal_code'
      );
    }
    else {
      // return by reference
      $columns = array(
        'contact_id',
        'first_name',
        'last_name',
        'postal_code',
        'accepting_new_clients__65',
        'travels_to_remote_areas__67',
        'offers_supervision__68',
        'offer_video_conferencing_service_70',
        'region_63',
        'language_64',
        'bacb_r_disciplinary_action_71',
        'cpo_discipline_and_other_proceed_72',
      );
    }
    return $columns;
  }
  /**
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $returnSQL
   *
   * @return string
   */
  public function contactIDs($offset = 0, $rowcount = 0, $sort = NULL, $returnSQL = FALSE) {
    return $this->all($offset, $rowcount, $sort, FALSE, TRUE);
  }

  /**
   * @param $selectClause
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param null $groupBy
   *
   * @return string
   */
  public function sql(
    $selectClause,
    $offset = 0,
    $rowcount = 0,
    $sort = NULL,
    $includeContactIDs = FALSE,
    $groupBy = NULL
  ) {

    $sql = "SELECT $selectClause " . $this->from();
    $where = $this->where();
    if (!empty($where)) {
      $sql .= " WHERE " . $where;
    }

    if ($includeContactIDs) {
      $this->includeContactIDs($sql,
        $this->_formValues
      );
    }

    if ($groupBy) {
      $sql .= " $groupBy ";
    }

    $this->addSortOffset($sql, $offset, $rowcount, "contact_a.last_name, contact_a.first_name");
    return $sql;
  }

  /**
   * Construct a full SQL query which returns one page worth of results
   *
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param bool $justIDs
   * @return string, sql
   */
  function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    // delegate to $this->sql(), $this->select(), $this->from(), $this->where(), etc.

    return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, ' GROUP BY contact_a.id ');
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
  function select() {
    if ($this->_searchByOrg)  {
      return "
        contact_a.id as contact_id,
        contact_a.organization_name,
        GROUP_CONCAT(DISTINCT r.contact_id_a) as provider_ids,
        GROUP_CONCAT(DISTINCT contact_b.display_name) as provider_names,
        temp.*,
        address.postal_code,
        temp3.*
      ";
    }
    else {
      return "
        contact_a.id as contact_id,
        contact_a.first_name,
        contact_a.last_name,
        GROUP_CONCAT(DISTINCT r.contact_id_b) as org_ids,
        GROUP_CONCAT(DISTINCT contact_b.organization_name) as org_names,
        temp.*,
        address.postal_code,
        temp3.*
      ";
    }

  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
  function from() {
    if ($this->_searchByOrg) {
      return "
        FROM      civicrm_contact contact_a
        LEFT JOIN civicrm_relationship r ON r.contact_id_b = contact_a.id AND r.relationship_type_id = 5
        LEFT JOIN civicrm_contact contact_b ON r.contact_id_a = contact_b.id
        LEFT JOIN civicrm_address address ON ( address.contact_id       = contact_a.id AND
                                               address.is_primary       = 1 )
        LEFT JOIN civicrm_email           ON ( civicrm_email.contact_id = contact_a.id AND
                                               civicrm_email.is_primary = 1 )
        LEFT JOIN civicrm_state_province state_province ON state_province.id = address.state_province_id
        LEFT JOIN civicrm_value_contact_gener_19 temp ON temp.entity_id = r.contact_id_a
        LEFT JOIN civicrm_value_track_changes_17 temp1 ON temp1.entity_id = r.contact_id_a
        LEFT JOIN civicrm_value_applicant_det_4 temp2 ON temp2.entity_id = r.contact_id_a
        LEFT JOIN civicrm_value_disciplinary_20 temp3 ON temp3.entity_id = r.contact_id_a
      ";
    }
    else {
      return "
        FROM  civicrm_contact contact_a
        LEFT JOIN civicrm_relationship r ON r.contact_id_a = contact_a.id AND r.relationship_type_id = 5
        LEFT JOIN civicrm_contact contact_b ON r.contact_id_b = contact_b.id
        LEFT JOIN civicrm_address address ON ( address.contact_id       = contact_a.id AND
                                               address.is_primary       = 1 )
        LEFT JOIN civicrm_email           ON ( civicrm_email.contact_id = contact_a.id AND
                                               civicrm_email.is_primary = 1 )
        LEFT JOIN civicrm_state_province state_province ON state_province.id = address.state_province_id
        LEFT JOIN civicrm_value_contact_gener_19 temp ON temp.entity_id = contact_a.id
        LEFT JOIN civicrm_value_track_changes_17 temp1 ON temp1.entity_id = contact_a.id
        LEFT JOIN civicrm_value_applicant_det_4 temp2 ON temp2.entity_id = contact_a.id
        LEFT JOIN civicrm_value_disciplinary_20 temp3 ON temp3.entity_id = contact_a.id
      ";
    }

  }

  /**
   * Construct a SQL WHERE clause
   *
   * @param bool $includeContactIDs
   * @return string, sql fragment with conditional expressions
   */
  function where($includeContactIDs = FALSE) {
    $params = array();
    $where = "contact_a.contact_sub_type = 'Provider' AND temp1.status_60 = 'Approved'";
    if ($this->_searchByOrg) {
      $where = "contact_b.contact_sub_type = 'Provider' AND temp1.status_60 = 'Approved'";
    }
    $customElements = [
      'accepting_clients_filter' => 'accepting_new_clients__65',
      'remote_travel_filter' => 'travels_to_remote_areas__67',
      'supervision_filter' => 'offers_supervision__68',
      'videoconferencing_filter' => 'offer_video_conferencing_service_70',
      'credentials' => 'temp2.which_of_the_following_credentia_7',
      'region' => 'region_63',
      'language' => 'language_64',
      'provider_name' => 'contact_a.first_name',
      'organization_name' => 'contact_a.display_name',
      'city' => 'address.city',
    ];
    $submittedValues = $this->_formValues;
    $clauses = [];
    if (empty($submittedValues['credentials'])) {
     $clauses[] = " temp2.which_of_the_following_credentia_7 IS NULL ";
    }
    foreach ($submittedValues as $key => $value) {
      if (array_key_exists($key, $customElements) && !empty($value)) {
        if ($key == 'provider_name') {
          if ($this->_searchByOrg) {
            $clauses[] = "(provider_names LIKE '%$value%')";
          }
          else  {
            $clauses[] = "(contact_a.first_name LIKE '%$value%' OR contact_a.last_name LIKE '%$value%' OR contact_a.sort_name LIKE '%$value%' OR contact_a.display_name LIKE '%$value%' )";
          }
        }
        if ($key == 'organization_name') {
          if (!$this->_searchByOrg) {
            $clauses[] = "(org_names LIKE '%$value%')";
          }
          else {
            $clauses[] = "(contact_a.organization_name LIKE '%$value%')";
          }
        }
        elseif ($key == 'language') {
          $languages = explode(',', $value);
          $c = [];
          foreach ($languages as $lang) {
            $c[] = "$customElements[$key] LIKE '%$lang%'";
          }
          $clauses[] = '(' . implode(' OR ', $c) . ')';
        }
        elseif ($key == 'region') {
          $clauses[] =  ($value == 'West') ? " ( $customElements[$key] LIKE '%$value%' OR $customElements[$key] LIKE '%South%' ) " : "$customElements[$key] LIKE '%{$value}%'";
        }
        elseif ($key == 'credentials') {
          $c = [];
          foreach ($value as $k => $v) {
            if ($v == 1) {
              $c[] = "$customElements[$key] LIKE '%$k%'";
            }
          }
          if (!empty($c)) {
            $clauses[] = '(' . implode(' OR ', $c) . ')';
          }
          else {
            $clauses[] = " $customElements[$key] IS NULL ";
          }
        }
        elseif ($key == 'city') {
          $clauses[] = "$customElements[$key] LIKE '%$value%'";
        }
        else {
          $clauses[] = sprintf("%s = %d", $customElements[$key], $value);
        }
      }
    }

    if (!empty($clauses)) {
      $where .= ' AND ' . implode(' AND ', $clauses);
    }

    return $this->whereClause($where, $params);
  }

  /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
  function templateFile() {
    return 'CRM/Oapproviderlistapp/Form/Search/ProviderList.tpl';
  }

  /**
   * Modify the content of each row
   *
   * @param array $row modifiable SQL result row
   * @return void
   */
  function alterRow(&$row) {
   if (!empty($row['language_64'])) {
     $row['language_64'] = explode(CRM_Core_DAO::VALUE_SEPARATOR, $row['language_64']);
     foreach ($row['language_64'] as $k => $language) {
       if (!array_key_exists($language, $this->_languages)) {
         unset($row['language_64'][$k]);
         continue;
       }
       $row['language_64'][$k] = CRM_Utils_Array::value($language, $this->_languages);
     }
     $row['language_64'] = implode(', ', $row['language_64']);
   }
   if (!empty($row['region_63'])) {
     $regions = array_filter(explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($row['region_63'], 1, -1)));
     $row['region_63'] = str_replace('South', 'West', implode(', ', $regions));
   }
    //CRM_Core_Error::debug_var('row', $row);
  }
}
