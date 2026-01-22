<?php
/*-------------------------------------------------------+
| CiviProxy                                              |
| Copyright (C) 2015-2021 SYSTOPIA                       |
| Author: Jaap Jansma (jaap.jansma@civicoop.org)         |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/

namespace Civi\Civiproxy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Firebase\JWT\JWT;

class Api {

  /**
   * Call the CiviProxy api
   * 
   * @param string $action
   *   The API Action
   * @param array $params
   *   Additional parameters for the api
   * @return null|string
   */
  public static function call(string $action, array $params = []):? string {
    $proxiApiKey = \Civi::settings()->get('proxy_api_key') ?? CIVICRM_SITE_KEY;
    $url = \Civi::settings()->get('proxy_url');
    $query = $params;
    $query['action'] = $action;
    if ($url) {
      $url .=  '/api.php';
      $client = new Client();
      $expire = time() + 900; // Expire after 15 minutes.
      $jwtPayload = $params;
      $jwtPayload['sub'] = $action;
      $jwtPayload['exp'] = $expire;
      $token = JWT::encode($jwtPayload, $proxiApiKey, 'HS256');
      $options['headers']['X-Civi-Auth'] = 'Bearer ' . $token;
      $options['query'] = $query;
      try {
        $response = $client->get($url, $options);
        if ($response->getStatusCode() === 200) {
          return $response->getBody();
        }
      } catch (GuzzleException $e) {
        // @ignoreException
      }
    }
    return NULL;
  }


}