<?php
use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * A custom contact search
 */
class CRM_Oapproviderlistapp_Form_Search_ProviderList extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {
  public $_languages;
  function __construct(&$formValues) {
    parent::__construct($formValues);
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

    $form->addElement('checkbox', 'accepting_clients_filter', E::ts('Show only if accepting new clients?'), NULL);
    $form->addElement('checkbox', 'remote_travel_filter', E::ts('Travels to remote areas?'), NULL);
    $form->addElement('checkbox', 'supervision_filter', E::ts('Offers supervision?'), NULL);
    $form->addElement('checkbox', 'videoconferencing_filter', E::ts('Offers remote services?'), NULL);
    $check = [];
    foreach (['East', 'Central', 'North', 'South'] as $key) {
      $check[] = &$form->addElement('checkbox', $key, NULL, E::ts($key), 'ts_sel', array('checked' => 'checked'));
    }
    $form->addGroup($check, 'region', E::ts('Region'));

    $check = [];
    foreach ([
      1 => E::ts('Board Certified Behavior Analyst® (BCBA®)'),
      2 => E::ts('Board Certified Behavior Analyst-Doctoral (BCBA-D™)'),
      3 => E::ts('Registered Psychologist'),
      4 => E::ts('Registered Psychological Associate'),
    ] as $key => $label) {
      $check[] = &$form->addElement('checkbox', $key, NULL, $label, 'ts_sel', array('checked' => 'checked'));
    }
    $form->addGroup($check, 'credentials', E::ts('Credentials'));
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
'option_group_id' => 'languages'],
      ],
    ]);
    $form->add('text',
      'name',
      E::ts('Name'),
      ['class' => 'huge']
    );
    $form->add('text',
      'city',
      E::ts('City'),
      ['class' => 'big']
    );

    $form->assign('elements', array(
      'credentials',
      'region',
      'language',
      'name',
      'city',
      'accepting_clients_filter',
      'remote_travel_filter',
      'supervision_filter',
      'videoconferencing_filter',
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
    // return by reference
    $columns = array(
      'contact_id',
      'first_name',
      'last_name',
      'postal_code',
      'accepting_new_clients__63',
      'travels_to_remote_areas__65',
      'offers_supervision__66',
      'offers_video_conferencing_servic_69',
      'region_67',
      'language_68',
    );
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
    return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, NULL);
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
  function select() {
    return "
      contact_a.id as contact_id,
      contact_a.first_name,
      contact_a.last_name,
      temp.*,
      address.postal_code
    ";
  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
  function from() {
    return "
      FROM      civicrm_contact contact_a
      LEFT JOIN civicrm_address address ON ( address.contact_id       = contact_a.id AND
                                             address.is_primary       = 1 )
      LEFT JOIN civicrm_email           ON ( civicrm_email.contact_id = contact_a.id AND
                                             civicrm_email.is_primary = 1 )
      LEFT JOIN civicrm_state_province state_province ON state_province.id = address.state_province_id
      LEFT JOIN civicrm_value_contact_gener_19 temp ON temp.entity_id = contact_a.id
      LEFT JOIN civicrm_value_track_changes_17 temp1 ON temp1.entity_id = contact_a.id
      LEFT JOIN civicrm_value_applicant_det_4 temp2 ON temp2.entity_id = contact_a.id
    ";
  }

  /**
   * Construct a SQL WHERE clause
   *
   * @param bool $includeContactIDs
   * @return string, sql fragment with conditional expressions
   */
  function where($includeContactIDs = FALSE) {
    $params = array();
    $where = "contact_a.contact_sub_type  = 'Provider' AND temp1.status_60 = 'Approved'";
    $customElements = [
      'accepting_clients_filter' => 'accepting_new_clients__63',
      'remote_travel_filter' => 'travels_to_remote_areas__65',
      'supervision_filter' => 'offers_supervision__66',
      'videoconferencing_filter' => 'offers_video_conferencing_servic_69',
      'credentials' => 'temp2.which_of_the_following_credentia_7',
      'region' => 'region_67',
      'language' => 'language_68',
      'name' => 'contact_a.first_name',
      'city' => 'address.city',
    ];
    $submittedValues = $_POST;
    $clauses = [];
if (empty($_POST['credentials'])) {
$clauses[] = " temp2.which_of_the_following_credentia_7 IS NULL ";
}
    foreach ($_POST as $key => $value) {
      if (array_key_exists($key, $customElements) && !empty($value)) {
        if ($key == 'name') {
          $clauses[] = "(contact_a.first_name LIKE '%$value%' OR contact_a.last_name LIKE '%$value%')";
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
          $c = [];
          foreach ($value as $k => $v) {
            if ($v == 1) {
              $c[] = "$customElements[$key] = '$k'";
            }
          }
          if (!empty($c)) {
            $clauses[] = '(' . implode(' OR ', $c) . ')';
          }
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
   if (!empty($row['language_68'])) {
     $value = [];
     $languages = explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($row['language_68'], 1, -1));
     foreach ($languages as $lang) {
       $value[] = $this->_languages[$lang];
     }
     $row['language_68'] = implode(', ', $value);
   }
   if (!empty($row['region_67'])) {
     $regions = array_filter(explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($row['region_67'], 1, -1)));
     $row['region_67'] = implode(', ', $regions);
   }
    //CRM_Core_Error::debug_var('row', $row);
  }
}
