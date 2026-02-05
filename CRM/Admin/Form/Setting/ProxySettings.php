<?php
/*-------------------------------------------------------+
| CiviProxy                                              |
| Copyright (C) 2015-2021 SYSTOPIA                       |
| Author: B. Endres (endres -at- systopia.de)            |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/

declare(strict_types = 1);

use CRM_Civiproxy_ExtensionUtil as E;

/**
 *
 * CiviProxy Settings Form
 *
 */
class CRM_Admin_Form_Setting_ProxySettings extends CRM_Core_Form {

  public static function validateURL($value) {
    return preg_match(
      // phpcs:disable Generic.Files.LineLength.TooLong
      "/^(http(s?):\/\/)?(((www\.)?+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,6})+)|(localhost)|(\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b))(:[0-9]{1,5})?(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/",
      // phpcs:enable
      $value
    );
  }

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(E::ts('CiviProxy - Settings'));

    // add all required elements
    $this->addElement('checkbox', 'proxy_enabled');
    $this->addElement('text', 'proxy_url', E::ts('Proxy URL'), ['disabled' => 'disabled']);
    $this->addElement('static', 'proxy_version', E::ts('Proxy version'));

    $this->addElement(
      'text',
      'custom_mailing_base',
      E::ts('Custom Subscribe/Unsubscribe Pages'),
      ['disabled' => 'disabled']
    );
    $this->addElement('text', 'proxy_api_key', E::ts('CiviProxy API Key'), ['disabled' => 'disabled']);

    $this->addButtons(
      [
        ['type' => 'next', 'name' => E::ts('Save'), 'isDefault' => TRUE],
        ['type' => 'cancel', 'name' => E::ts('Cancel')],
      ]
    );

    $this->registerRule('onlyValidURL', 'callback', 'validateURL', 'CRM_Admin_Form_Setting_ProxySettings');
  }

  public function addRules() {
    $this->addRule('proxy_url', E::ts('This may only contain a valid URL'), 'onlyValidURL');
    $this->addRule('custom_mailing_base', E::ts('This may only contain a valid URL'), 'onlyValidURL');
  }

  public function preProcess() {
    $this->assign('proxy_enabled', CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_enabled'));
    $proxyUrl = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_url');
    $proxyVersion = '-';

    if (NULL !== $proxyUrl && '' !== $proxyUrl) {
      // try to get the current proxy version
      $response = $this->requestProxyVersion($proxyUrl);
      if ($response['is_error']) {
        $proxyVersion = $response['message'];
        Civi::settings()->revert('proxy_version');
      }
      else {
        $proxyVersion = $response['version'];
        Civi::settings()->set('proxy_version', $proxyVersion);
      }
    }

    $this->setDefaults(
      [
        'proxy_url' => $proxyUrl,
        // watch out, this might contain an error message
        'proxy_version' => $proxyVersion,
        'custom_mailing_base' => Civi::settings()->get('custom_mailing_base'),
        'proxy_api_key' => Civi::settings()->get('proxy_api_key'),
      ]
    );
  }

  /**
   * Performs an http request to the specified url and tries
   * to parse the response in order to get the current proxy
   * version
   *
   * @param $url url of the proxy to use
   *
   * @return array(int is_error, [string message || string version])
   *
   */
  public function requestProxyVersion($url) {
    $response = @file_get_contents($url);
    if ($response === FALSE) {
      return ['is_error' => 1, 'message' => E::ts('Error: cannot access "%1"', [1 => $url])];
    }
    else {
      $result = preg_match(
        '/<p id="version">CiviProxy Version (([0-9]+\.[0-9]+|[0-9]+\.[0-9]+\.[0-9]+)(?:-[0-9A-Za-z-]+)?)<\/p>/',
        $response,
        $output_array
      );
      if ($result === FALSE || $result === 0) {
        return ['is_error' => 1, 'message' => E::ts('Error: failed to parse version information: (%1)', [1 => $url])];
      }
      else {
        return ['is_error' => 0, 'version' => $output_array[1]];
      }
    }
  }

  public function postProcess() {
    // process all form values and save valid settings
    $values = $this->exportValues();

    // checkboxes
    Civi::settings()->set('proxy_enabled', (bool) ($values['proxy_enabled'] ?? NULL));

    // text
    if (isset($values['proxy_url'])) {
      Civi::settings()->set('proxy_url', $values['proxy_url']);
    }
    if (isset($values['custom_mailing_base'])) {
      // check if it is simply default ({proxy_url}/mailing)
      if ($values['custom_mailing_base'] === $values['proxy_url'] . '/mailing') {
        // ...in which case we'll simply set it to ''
        $values['custom_mailing_base'] = '';
      }
      Civi::settings()->set('custom_mailing_base', $values['custom_mailing_base']);
    }
    if (isset($values['proxy_api_key'])) {
      Civi::settings()->set('proxy_api_key', $values['proxy_api_key']);
    }

    // give feedback to user
    $session = CRM_Core_Session::singleton();
    CRM_Core_Session::setStatus(E::ts('Settings successfully saved'), E::ts('Settings'), 'success');
    $session->replaceUserContext(CRM_Utils_System::url('civicrm/admin/setting/civiproxy'));
  }

}
