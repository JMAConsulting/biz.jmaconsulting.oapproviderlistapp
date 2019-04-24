<?php
use CRM_Oapproviderlistapp_ExtensionUtil as E;

/**
 * A custom contact search
 */
class CRM_Oapproviderlistapp_Form_Search_ProviderList extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {
  function __construct(&$formValues) {
    parent::__construct($formValues);
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'css/style.css');
  }

  /**
   * Prepare a set of search fields
   *
   * @param CRM_Core_Form $form modifiable
   * @return void
   */
  function buildForm(&$form) {
    CRM_Utils_System::setTitle(E::ts('Provider Search List'));

    $form->addElement('checkbox', 'accepting_clients_filter', ts('Show only if Accepting new clients?'), NULL);
    $form->addElement('checkbox', 'remote_travel_filter', ts('Travels to remote areas?'), NULL);
    $form->addElement('checkbox', 'supervision_filter', ts('Currently offers supervision?'), NULL);
    $form->addElement('checkbox', 'videoconferencing_filter', ts('Offers video conferencing services?'), NULL);
    $form->addElement('checkbox', 'bc', ts('Behaviour Consultants'), NULL);
    $form->addElement('checkbox', 'ot', ts('Occupational Therapists'), NULL);
    $form->addElement('checkbox', 'pt', ts('Physical Therapists'), NULL);
    $form->addElement('checkbox', 'slp', ts('Speech Language Pathologists'), NULL);
    $check = [];
    foreach (['East', 'Central', 'North', 'South'] as $key) {
      $check[] = &$form->addElement('advcheckbox', strtolower($key), NULL, ts($key));
    }
    $form->addGroup($check, 'region', ts('Region'));
    $form->addEntityRef('language', ts('Language'), [
      'entity' => 'OptionValue',
      'placeholder' => ts('- any -'),
      'multiple' => 1,
      'api' => [
        'params' => ['option_group_id' => 'languages'],
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
      'accepting_new_clients__65',
      'offer_remote_services__66',
      'travels_to_remote_areas__67',
      'offers_supervision__68',
      'region_63',
      'language_64',
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
    $where = "contact_a.contact_sub_type  = 'Provider'";
    $customElements = [
      'custom_65_-1' => 'accepting_new_clients__65',
      'custom_66_-1' => 'offer_remote_services__66',
      'custom_67_-1' => 'travels_to_remote_areas__67',
      'custom_68_-1' => 'offers_supervision__68',
      'custom_63_-1' => 'region_63',
      'custom_64_-1' => 'language_64',
      'first_name' => 'contact_a.first_name',
      'last_name' => 'contact_a.last_name',
      'city' => 'civicrm_address.city',
    ];
    $submittedValues = $_POST;
    $clauses = [];
    foreach ($_POST as $key => $value) {
      if (array_key_exists($key, $customElements) && !empty($value)) {
        if (in_array($key, ['custom_63_-1', 'custom_64_-1', 'first_name', 'last_name', 'city'])) {
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
    //CRM_Core_Error::debug_var('row', $row);
  }
}
