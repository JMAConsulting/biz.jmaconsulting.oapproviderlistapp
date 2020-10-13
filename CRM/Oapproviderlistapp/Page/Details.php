<?php

require_once 'CRM/Core/Page.php';

class CRM_Oapproviderlistapp_Page_Details extends CRM_Core_Page {
  function run() {
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive');
    $isOrg = CRM_Utils_Request::retrieve('is_org', 'Positive');

    if ($isOrg) {
      $details = self::getOrgs($cid);
      $this->assign('isOrg', $isOrg);
      if (!empty($details['providers'])) {
        $this->assign('providers', $details['providers']);
      }
    }
    else  {
      $details = self::getAdditionalDetails($cid);
    }

    $this->assign('employers', $details['employers']);
    $this->assign('credentials', $details['credentials']);
    $this->assign('image', $details['image']);
    parent::run();
  }

  public static function getOrgs($cid) {
    $details = [];
    $options = CRM_Core_OptionGroup::values('which_of_the_following_credentia_20190321014056');
    $rType = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_RelationshipType', 'Employee of', 'id', 'name_a_b');
    $sql = "SELECT o.id, o.organization_name, a.street_address, a.supplemental_address_1, o.image_URL,
      a.city, a.postal_code, e.email, p.phone, p.phone_ext, sp.abbreviation, w.url,
      GROUP_CONCAT(DISTINCT r.contact_id_a) as provider_ids
      FROM civicrm_contact o
      INNER JOIN civicrm_relationship r ON r.contact_id_b = o.id
      LEFT JOIN civicrm_contact contact_a ON r.contact_id_a = contact_a.id AND contact_a.contact_sub_type LIKE '%Provider%'
      LEFT JOIN civicrm_address a ON a.contact_id = o.id AND a.location_type_id = 2
      LEFT JOIN civicrm_state_province sp ON sp.id = a.state_province_id
      LEFT JOIN civicrm_email e ON e.contact_id = o.id AND e.location_type_id = 2
      LEFT JOIN civicrm_phone p ON p.contact_id = o.id AND p.location_type_id = 2
      LEFT JOIN civicrm_website w ON w.contact_id = o.id AND w.website_type_id = 1
      LEFT JOIN civicrm_value_track_changes_17 temp1 ON temp1.entity_id = r.contact_id_a
      WHERE r.contact_id_b = %1 AND r.relationship_type_id = %2 AND o.is_deleted <> 1 AND temp1.status_60 = 'Approved'
      AND r.is_active = 1
      GROUP BY o.id
      ";

      $employers = CRM_Core_DAO::executeQuery($sql, [
        1 => [$cid, 'Integer'],
        2 => [$rType, 'Integer'],
      ])->fetchAll();

      foreach ($employers as $key => $employer) {
        $providerIDs = $employer['provider_ids'];
        if (!empty($employer['image_URL'])) {
          $url = $employer['image_URL'];
          list($width, $height) = getimagesize(CRM_Utils_String::unstupifyUrl($url));
          list($thumbWidth, $thumbHeight) = CRM_Contact_BAO_Contact::getThumbSize($width, $height);
          $details['image'] = '<img src="' . $url . '" height= ' . $thumbHeight . ' width= ' . $thumbWidth . '  />';
        }
      }

      $providers = explode(',', $employer['provider_ids']);
      $details['providers'] = [];
      if (!empty($providers)) {
        $sql = "
        SELECT display_name, cc.id, GROUP_CONCAT(DISTINCT c.which_of_the_following_credentia_7) as credentials
         FROM civicrm_contact cc
          LEFT JOIN civicrm_value_applicant_det_4 c ON cc.id = c.entity_id
         WHERE cc.id IN (" . $employer['provider_ids'] . ") AND display_name NOT LIKE '%@%'
         GROUP BY cc.id
         ORDER BY cc.last_name, cc.first_name ";
        $result = CRM_Core_DAO::executeQuery($sql)->fetchAll();
        foreach ($result as $k => $value) {
          $allCreds = [];
          foreach (explode(',', $value['credentials']) as $cred) {
            $creds = array_filter(explode(CRM_Core_DAO::VALUE_SEPARATOR, $cred));
            foreach ($creds as $cred) {
              $allCreds[] = $options[$cred];
            }
          }

          $details['providers'][$k] = sprintf(
            "<a href='%s' target='_blank'>%s</a>, %s",
            CRM_Utils_System::url('civicrm/contact/search/custom', "reset=1&csid=16&force=1&cid=" . $value['id']),
            $value['display_name'],
            implode(', ', $allCreds)
          );
        }
      }
      $details['employers'] = $employers;

      return $details;
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

    if (!empty($credentials)) {
      $details['credentials'] = $credentials;
    }

    // Get contact image
    $details['image'] = CRM_Core_DAO::singleValueQuery("SELECT image_URL FROM civicrm_contact WHERE id = %1", [1 => [$cid, 'Integer']]);
    if (!empty($details['image'])) {
      $url = $details['image'];
      list($width, $height) = getimagesize(CRM_Utils_String::unstupifyUrl($url));
      list($thumbWidth, $thumbHeight) = CRM_Contact_BAO_Contact::getThumbSize($width, $height);
      $details['image'] = '<img src="' . $url . '" height=200 width=200  />';
    }

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
      AND r.is_active = 1
      GROUP BY o.id
      ORDER BY o.organization_name
      ";
    $rtype = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_RelationshipType', 'Employee of', 'id', 'name_a_b');
    $employers = CRM_Core_DAO::executeQuery($sql, [
      1 => [$cid, 'Integer'],
      2 => [$rtype, 'Integer'],
    ])->fetchAll();
    if (!empty($employers)) {
      foreach ($employers as $k => $employer) {
        $employers[$k]['organization_name'] = sprintf(
          "<a href='%s' target='_blank' class='employer' >%s</a>",
          CRM_Utils_System::url('civicrm/contact/search/custom', "reset=1&csid=16&force=1&is_org=1&cid=" . $employer['id']),
          $employer['organization_name']
        );
      }
      $details['employers'] = $employers;
    }
    return $details;
  }
}
