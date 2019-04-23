<?php

require_once 'CRM/Core/Page.php';

class CRM_Oapproviderlistapp_Page_Details extends CRM_Core_Page {
  function run() {
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive');

    $details = self::getAdditionalDetails($cid);

    $this->assign('employers', $details['employers']);
    parent::run();
  }


  public static function getAdditionalDetails($cid) {
    $details = [];
    // Get employers
    $sql = "SELECT o.id, o.organization_name, a.street_address, a.city, a.postal_code, e.email, p.phone, sp.abbreviation FROM civicrm_contact o
      INNER JOIN civicrm_relationship r ON r.contact_id_b = o.id
      LEFT JOIN civicrm_address a ON a.contact_id = o.id AND a.location_type_id = 2
      LEFT JOIN civicrm_state_province sp ON sp.id = a.state_province_id
      LEFT JOIN civicrm_email e ON e.contact_id = o.id AND e.location_type_id = 2
      LEFT JOIN civicrm_phone p ON p.contact_id = o.id AND p.location_type_id = 2
      WHERE r.contact_id_a = %1 AND r.relationship_type_id = %2";
    $rtype = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_RelationshipType', 'Employee of', 'id', 'name_a_b');
    $employers = CRM_Core_DAO::executeQuery($sql, [1 => [$cid, 'Integer'], 2 => [$rtype, 'Integer']])->fetchAll();
    if (!empty($employers)) {
      $details['employers'] = $employers;
    }
    return $details;
  }
}
