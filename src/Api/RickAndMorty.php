<?php declare(strict_types=1);

namespace HoloRick\Api;

use HoloRick\Exception\BadResponseException;
use HoloRick\Exception\InvalidJsonException;

use \GuzzleHttp\Client;

class RickAndMorty {
  const API_BASE = 'https://rickandmortyapi.com/api/';

  public function get(string $context, string $identifier): string {
    $http = new Client([
      'base_uri' => self::API_BASE,
      'curl' => [
        CURLOPT_SSL_VERIFYPEER => false
      ]
    ]);

    $response = $http->get("$context/$identifier");
    if (200 !== $response->getStatusCode()) {
      throw new BadResponseException;
    }

    $json = (string) $response->getBody();

    if (!json_validate($json)) {
      throw new InvalidJsonException;
    }

    return $json;
  }
}