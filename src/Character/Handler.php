<?php declare(strict_types=1);

namespace HoloRick\Character;

use HoloRick\Api\RickAndMorty;
use HoloRick\Character\Character;
use HoloRick\Exception\CharactersExhaustedException;
use HoloRick\Exception\GetFileContentsException;
use HoloRick\Exception\JsonDecodeFailedException;


final class Handler {
  public static function getCharacter(int $characterId): Character {
    $api = new RickAndMorty;
    $characterJson = $api->get('character', (string) $characterId);
    $character = Character::fromJson($characterJson);

    return $character;
  }

  public static function getRandomCharacter(): Character {
    $unassignedCharacterIds = self::getUnassignedCharacterIds();

    $idCount = count($unassignedCharacterIds);
    if ($idCount < 1) {
      throw new CharactersExhaustedException;
    }

    $characterId = $unassignedCharacterIds[random_int(0, --$idCount)];

    return self::getCharacter($characterId);
  }

  /** @return array<string,mixed> */
  public static function getCharacterEmbed(Character $character): array {
    return [
      'title' => $character->name,
      'image' => [
        'url' => $character->image
      ],
      'fields' => [
        [
          'name' => 'Species',
          'value' => $character->species,
          'inline' => true
        ],
        [
          'name' => 'Type',
          'value' => $character->type,
          'inline' => true
        ],
        [
          'name' => 'Gender',
          'value' => $character->gender,
          'inline' => true
        ],
        [
          'name' => 'Origin',
          'value' => $character->origin,
          'inline' => true
        ],
        [
          'name' => 'Location',
          'value' => $character->location,
          'inline' => true
        ],
        [
          'name' => 'Status',
          'value' => $character->status,
          'inline' => true
        ]
      ]
    ];
  }

  /** @return array<int> */
  public static function getUnassignedCharacterIds(): array {
    return array_diff(range(1, 826), array_map(fn($character) => (int) $character->id, self::getAssignedCharacters()));
  }

  public static function assignCharacter(string $userId, string $roleId, Character $character): void {
    $character->user_id = $userId;
    $character->role_id = $roleId;

    $assignedCharacters = self::getAssignedCharacters();
    $assignedCharacters[] = $character;

    file_put_contents(ROOT . 'assigned-characters.json', json_encode($assignedCharacters, JSON_PRETTY_PRINT));
  }

  public static function getAssignedCharacter(string $userId): ?Character {
    $assignedCharacters = self::getAssignedCharacters();

    $foundCharacter = null;

    foreach ($assignedCharacters as $character) {
      if ($userId === $character->user_id) {
        $foundCharacter = $character;
        break;
      }
    }

    return $foundCharacter;
  }

  /** @return array<Character> */
  public static function getAssignedCharacters(): array {
    $json = file_get_contents(ROOT . 'assigned-characters.json');
    if (!$json) {
      throw new GetFileContentsException;
    }

    $characterObjects = json_decode($json);
    if (!is_array($characterObjects)) {
      throw new JsonDecodeFailedException;
    }

    $characters = [];

    foreach ($characterObjects as $characterObj) {
      $characters[] = Character::fromObject($characterObj);
    }

    return $characters;
  }

  public static function unassignCharacter(string $userId): void {
    $assignedCharacters = self::getAssignedCharacters();

    $filteredAssignedCharacters = array_filter($assignedCharacters, fn($character) => $character->user_id !== $userId);

    file_put_contents(ROOT . 'assigned-characters.json', json_encode(array_values($filteredAssignedCharacters), JSON_PRETTY_PRINT));
  }
}
