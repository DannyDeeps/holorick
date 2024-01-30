<?php

declare(strict_types=1);

namespace HoloRick\Role;

use HoloRick\Member\Handler as MemberHandler;

use \Discord\Discord;
use \Discord\Parts\Guild\Guild;
use \Discord\Parts\Guild\Role;
use \Discord\Parts\User\Member;
use \GuzzleHttp\Client;

final class Handler {
  public static function createCharacterRoleForMember(Member $member, Guild $guild): void {
    $character = self::getRandomCharacter();

    $guild->createRole([
      'name' => $character['name'],
      'hoist' => true
    ])->done(function (Role $role) use ($character, $member) {
      echo 'Role Created: ' . $role->id . "\n";

      self::addCharacterIdInUse($member->user->id, $character['id']);

      $member->addRole($role)->done(function() use($role, $member) {
        echo $role->name . ' assigned to ' . $member->user->username . "\n";
      });
    });
  }

  public  static function getMemberCharacterRole(int $memberId) : array {
    $character = [];
    return $character;
  }

  public static function getRandomCharacter(): array {
    $availableCharacterIds = self::getAvailableCharacterIds();

    $characterId = $availableCharacterIds[random_int(0, count($availableCharacterIds) - 1)];

    $http = new Client([
      'base_uri' => 'https://rickandmortyapi.com',
      'curl' => [CURLOPT_SSL_VERIFYPEER => false]
    ]);

    $response = $http->request('GET', '/api/character/' . $characterId);

    return json_decode($response->getBody(), true);
  }

  public static function getAvailableCharacterIds(): array {
    return array_diff(range(1, 826), self::getCharacterIdsInUse());
  }

  public static function getCharacterIdsInUse(): array {
    return array_map('intval', array_filter(explode("\n", file_get_contents(ROOT . 'characters-in-use.txt'))));
  }

  public static function addCharacterIdInUse(string $memberId, int $characterId): void {
    $characterIdsInUse = array_merge(self::getCharacterIdsInUse(), [$memberId => $characterId]);
    file_put_contents('characters-in-use.txt', implode("\n", $characterIdsInUse));
  }

  public static function removeCharacterIdInUse(int $characterId): void {
    $characterIdsInUse = array_diff(self::getCharacterIdsInUse(), [$characterId]);
    file_put_contents('characters-in-use.txt', implode("\n", $characterIdsInUse));
  }

  public static function resetCharacterIdsInUse(): void {
    file_put_contents('characters-in-use.txt', '');
  }
}
