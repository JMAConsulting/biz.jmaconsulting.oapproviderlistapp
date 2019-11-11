<?php

require_once 'CRM/Core/Page.php';

class CRM_Oapproviderlistapp_Page_Details extends CRM_Core_Page {
  function run() {
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive');

    $details = self::getAdditionalDetails($cid);

    $this->assign('employers', $details['employers']);
    $this->assign('credentials', $details['credentials']);
    $this->assign('image', $details['image']);
    parent::run();
  }


  public static function getAdditionalDetails($cid, $fetchOrg = FALSE) {
    $details = [];

    // Get Credentials
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
    /* $dates = [
      'bcba_certification_date_8',
      'bcba_d_9',
      'registered_psychologist_registra_10',
      'registered_psychological_associa_11',
    ];
    foreach ($dates as $dateField) {
      if (!empty($credentials[0][$dateField])) {
        $credentials[0][$dateField] = date('Y-m-d', strtotime($credentials[0][$dateField]));
      }
    } */
    if (!empty($credentials)) {
      $details['credentials'] = $credentials;
    }

    // Get contact image
    $details['image'] = CRM_Core_DAO::singleValueQuery("SELECT image_URL FROM civicrm_contact WHERE id = %1", [1 => [$cid, 'Integer']]);

    // Get employers
    $sql = "SELECT o.id, o.organization_name, a.street_address, a.supplemental_address_1,
      a.city, a.postal_code, e.email, p.phone, p.phone_ext, sp.abbreviation, w.url
      FROM civicrm_contact o
      INNER JOIN civicrm_relationship r ON r.contact_id_b = o.id
      LEFT JOIN civicrm_address a ON a.contact_id = o.id AND a.location_type_id = 2
      LEFT JOIN civicrm_state_province sp ON sp.id = a.state_province_id
      LEFT JOIN civicrm_email e ON e.contact_id = o.id AND e.location_type_id = 2
      LEFT JOIN civicrm_phone p ON p.contact_id = o.id AND p.location_type_id = 2
      LEFT JOIN civicrm_website w ON w.contact_id = o.id AND w.website_type_id = 1
      WHERE r.contact_id_a = %1 AND r.relationship_type_id = %2 AND o.is_deleted <> 1
      GROUP BY o.id";
    $rtype = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_RelationshipType', 'Employee of', 'id', 'name_a_b');
    $employers = CRM_Core_DAO::executeQuery($sql, [
      1 => [$cid, 'Integer'],
      2 => [$rtype, 'Integer'],
    ])->fetchAll();
    if (!empty($employers)) {
      $details['employers'] = $employers;
    }
    return $details;
  }
}
