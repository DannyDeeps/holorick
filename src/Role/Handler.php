<?php

declare(strict_types=1);

namespace HoloRick\Role;

use \Discord\Parts\Guild\Guild;
use \Discord\Parts\Guild\Role;
use \Discord\Parts\User\Member;
use \GuzzleHttp\Client;

final class Handler {
  public static function createCharacterRoleForMember(Member $member, Guild $guild): void {
    $character = self::getRandomCharacter();

    $guild->createRole([
      'name' => $character->name,
      'hoist' => true
    ])->done(function (Role $role) use ($character, $member) {
      echo "Role Created: $role->id | $character->name \n";

      $member->addRole($role)->done(function () use ($role, $member, $character) {
        self::assignCharacter($member->user->id, $role->id, $character);

        echo "$role->name assigned to {$member->user->username} \n";
      });
    });
  }

  public static function getCharacter(int $characterId): object {
    $http = new Client([
      'base_uri' => 'https://rickandmortyapi.com',
      'curl' => [CURLOPT_SSL_VERIFYPEER => false]
    ]);

    $response = $http->request('GET', '/api/character/' . $characterId);

    return json_decode((string) $response->getBody());
  }

  public static function getRandomCharacter(): object {
    $unassignedCharacterIds = self::getUnassignedCharacterIds();

    $characterId = $unassignedCharacterIds[random_int(0, count($unassignedCharacterIds) - 1)];

    return self::getCharacter($characterId);
  }

  public static function getUnassignedCharacterIds(): array {
    return array_diff(range(1, 826), array_map(fn ($character) => $character->id, self::getAssignedCharacters()));
  }

  public static function getAssignedCharacters(): array {
    return json_decode(file_get_contents(ROOT . 'assigned-characters.json'));
  }

  public static function getAssignedCharacter(string $userId): object {
    $assignedCharacters = self::getAssignedCharacters();

    foreach ($assignedCharacters as $character) {
      if ($userId === $character->user_id) {
        return $character;
      }
    }

    return new \stdClass();
  }

  public static function assignCharacter(string $userId, string $roleId, object $character): void {
    $assignedCharacters = self::getAssignedCharacters();

    $character->user_id = $userId;
    $character->role_id = $roleId;

    $assignedCharacters[] = $character;

    file_put_contents(ROOT . 'assigned-characters.json', json_encode($assignedCharacters, JSON_PRETTY_PRINT));
  }

  public static function unassignCharacter(string $userId): void {
    $assignedCharacters = self::getAssignedCharacters();

    $filteredAssignedCharacters = array_filter($assignedCharacters, fn ($character) => $character->user_id !== $userId);

    file_put_contents(ROOT . 'assigned-characters.json', json_encode(array_values($filteredAssignedCharacters), JSON_PRETTY_PRINT));
  }

  public static function unassignCharacters(): void {
    file_put_contents(ROOT . 'assigned-characters.json', '[]');
  }
}
