<?php
/*-------------------------------------------------------+
| CiviProxy                                              |
| Copyright (C) 2015 SYSTOPIA                            |
| Author: B. Endres (endres -at- systopia.de)            |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/

declare(strict_types = 1);

/*
 * Settings metadata file
 */
use CRM_Civiproxy_ExtensionUtil as E;

return [
  'proxy_enabled' => [
    'group_name' => 'CiviProxy Settings',
    'group' => 'de.systopia',
    'name' => 'proxy_enabled',
    'type' => 'Integer',
    'html_type' => 'Select',
    'default' => 0,
    'add' => '4.3',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Enables or disables the proxy',
    'help_text' => '',
  ],
  'proxy_url' => [
    'group_name' => 'CiviProxy Settings',
    'group' => 'de.systopia',
    'name' => 'proxy_url',
    'type' => 'String',
    'default' => '',
    'add' => '4.3',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The URL from which the proxy will be available for requests',
    'help_text' => '',
  ],
  'proxy_version' => [
    'group_name' => 'CiviProxy Settings',
    'group' => 'de.systopia',
    'name' => 'proxy_version',
    'type' => 'String',
    'default' => '',
    'add' => '4.3',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The version of the currently selected proxy',
    'help_text' => '',
  ],
  'custom_mailing_base' => [
    'group_name' => 'CiviProxy Settings',
    'group' => 'de.systopia',
    'name' => 'custom_mailing_base',
    'type' => 'String',
    'default' => '',
    'add' => '4.3',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The URL can override the proxy for custom designed mailing subscribe/unsubscribe pages',
    'help_text' => '',
  ],
  'proxy_api_key' => [
    'group_name' => 'CiviProxy Settings',
    'group' => 'de.systopia',
    'name' => 'proxy_api_key',
    'type' => 'String',
    'title' => E::ts('CiviProxy API Key'),
    'default' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts(
      // phpcs:disable Generic.Files.LineLength.TooLong
      'The CiviProxy API key. This key is used to connect to the API of civi proxy. Leave empty to use the site key.'
      // phpcs:enable
    ),
    'help_text' => E::ts(
      // phpcs:disable Generic.Files.LineLength.TooLong
      'This is the CiviProxy API Key. Used for connecting to the CiviProxy api. Leave empty to use the SITE KEY.'
      // phpcs:enable
    ),
  ],
];
