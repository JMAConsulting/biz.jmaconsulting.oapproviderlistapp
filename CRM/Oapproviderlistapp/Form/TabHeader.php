<?php
use CRM_Oapproviderlistapp_ExtensionUtil as E;

class CRM_Oapproviderlistapp_Form_TabHeader {
  /**
   * @param CRM_Core_Form $form
   *
   * @return array
   */
  public static function build(&$form, $cid) {
    $tabs = $form->get('tabHeader');
    if (!$tabs || empty($_GET['reset'])) {
      $tabs = self::process($form, $cid);
      $form->set('tabHeader', $tabs);
    }
    $form->assign_by_ref('tabHeader', $tabs);
    CRM_Core_Resources::singleton()
      ->addScriptFile('civicrm', 'templates/CRM/common/TabHeader.js', 1, 'html-header')
      ->addSetting(array(
        'tabSettings' => array(
          'active' => self::getCurrentTab($tabs),
        ),
      ));
    return $tabs;
  }

  /**
   * @param CRM_Core_Form $form
   *
   * @return array
   */
  public static function process(&$form, $cid) {
    $tabs = [
      'individual' => [
        'title' => E::ts('Individual Information'),
        'url' => NULL,
        'valid' => FALSE,
        'active' => FALSE,
        'current' => FALSE,
      ],
      'professional' => [
        'title' => E::ts('Professional Credential(s)'),
        'url' => NULL,
        'valid' => FALSE,
        'active' => FALSE,
        'current' => FALSE,
      ],
      'experience' => [
        'title' => E::ts('Experience'),
        'url' => NULL,
        'valid' => FALSE,
        'active' => FALSE,
        'current' => FALSE,
      ],
      'sectorcheck' => [
        'title' => E::ts('Vulnerable Sector Check'),
        'url' => NULL,
        'valid' => FALSE,
        'active' => FALSE,
        'current' => FALSE,
      ],
      'insurance' => [
        'title' => E::ts('Professional Liability Insurance'),
        'url' => NULL,
        'valid' => FALSE,
        'active' => FALSE,
        'current' => FALSE,
      ],
      'signature' => [
        'title' => E::ts('Signature'),
        'url' => NULL,
        'valid' => FALSE,
        'active' => FALSE,
        'current' => FALSE,
      ],
    ];

    $fullName = $form->getVar('_name');
    $class = strtolower(CRM_Utils_String::getClassName($fullName));

    $tabs[$class]['current'] = TRUE;
    $qfKey = $form->get('qfKey');
    if ($qfKey) {
      $tabs[$class]['qfKey'] = "&qfKey={$qfKey}";
    }
    if ($cid) {
      $cid = "cid={$cid}";
    }

    foreach ($tabs as $key => $value) {
      if (!isset($tabs[$key]['qfKey'])) {
        $tabs[$key]['qfKey'] = NULL;
      }

      $tabs[$key]['link'] = CRM_Utils_System::url(
          "civicrm/{$key}",
          "{$cid}{$tabs[$key]['qfKey']}"
        );
      $tabs[$key]['active'] = $tabs[$key]['valid'] = TRUE;
    }
    return $tabs;
  }

  /**
   * @param $form
   */
  public static function reset(&$form) {
    $tabs = self::process($form);
    $form->set('tabHeader', $tabs);
  }

  /**
   * @param $tabs
   *
   * @return int|string
   */
  public static function getCurrentTab($tabs) {
    static $current = FALSE;

    if ($current && array_key_exists($current, $tabs)) {
      return $current;
    }

    if (is_array($tabs)) {
      foreach ($tabs as $subPage => $pageVal) {
        if ($pageVal['current'] === TRUE) {
          $current = $subPage;
          break;
        }
      }
    }

    $current = $current ? $current : 'individual';
    return $current;
  }

}
