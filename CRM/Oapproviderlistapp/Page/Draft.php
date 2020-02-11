<?php
use CRM_Oapproviderlistapp_ExtensionUtil as E;

class CRM_Oapproviderlistapp_Page_Draft extends CRM_Core_Page {

  protected $contactID;

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Core_Resources::singleton()->addStyleFile('org.civicrm.shoreditch', 'css/custom-civicrm.css',1, 'html-header');
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.oapproviderlistapp', 'templates/css/oapp.css');
    
    $form = NULL;
    $this->contactID = CRM_Utils_Request::retrieve('cid', 'Positive', $form, TRUE, 'GET');
    $checksum = CRM_Utils_Request::retrieve('cs', 'String', $form, FALSE, 'GET');
    $includeURL = TRUE;
    if ($this->contactID && !CRM_Contact_BAO_Contact_Utils::validChecksum($this->contactID, $checksum)) {
      $includeURL = FALSE;
    }

    $text = E::ts('Your draft registration for the OAP provider list has been saved successfully!');
    if ($includeURL) {
      $url = '<a href="' . CRM_Utils_System::url("civicrm/application", "reset=1&cid=" . $this->contactID . "&cs=" . CRM_Contact_BAO_Contact_Utils::generateChecksum($this->contactID, NULL, 'inf')) . '">' . E::ts('here') . '</a>';
      $text = E::ts('Your draft registration for the OAP provider list has been saved successfully! Click %1 to resume your application', [1 => $url]);
    }
    CRM_Utils_System::setTitle(E::ts('Draft saved successfully!'));
    // Example: Assign a variable for use in a template
    $this->assign('text', $text);

    parent::run();
  }

}
