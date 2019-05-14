<?php
use CRM_Oapproviderlistapp_ExtensionUtil as E;

class CRM_Oapproviderlistapp_Page_Draft extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $title = ($lang == 'fr') ? 'Sauvegarde de l’ébauche de la Demande d’inscription sur la liste des fournisseurs du POSA!' : 'Draft saved successfully!';
    $text = ($lang == 'fr') ? sprintf(
      "Sauvegarde de l’ébauche de la demande d’inscription sur la liste des fournisseurs du POSA! Cliquez  <a href='%s'>ici pour représenter votre demande",
        CRM_Utils_System::url("fr/civicrm/application", "reset=1&cid=" . $_GET['cid'])) :
      sprintf(
        "Your draft registration for the OAP provider list has been saved successfully! Click <a href='%s'>here</a> to resume your application.",
          CRM_Utils_System::url("civicrm/application", "reset=1&cid=" . $_GET['cid'])
      );
    CRM_Utils_System::setTitle($title);
    // Example: Assign a variable for use in a template
    $this->assign('text', $text);

    parent::run();
  }

}
