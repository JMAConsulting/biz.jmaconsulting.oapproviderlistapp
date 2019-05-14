<?php
use CRM_Oapproviderlistapp_ExtensionUtil as E;

class CRM_Oapproviderlistapp_Page_Draft extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Draft saved successfully!'));
    $entext = sprintf("Your draft registration for the OAP provider list has been saved successfully! Click <a href='%s'>here</a> to resume your application.",
    CRM_Utils_System::url("civicrm/application", "reset=1&cid=" . $_GET['cid']));
    $frtext = sprintf("Sauvegarde de l’ébauche de la demande d’inscription sur la liste des fournisseurs du POSA! Cliquez  <a href='%s'>ici pour représenter votre demande",
    CRM_Utils_System::url("fr/civicrm/application", "reset=1&cid=" . $_GET['cid']));

    $text = \Drupal::languageManager()->getCurrentLanguage()->getId() == 'fr' ? $frtext : $entext;

    // Example: Assign a variable for use in a template
    $this->assign('text', $text);

    parent::run();
  }

}
