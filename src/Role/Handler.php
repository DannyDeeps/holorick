<?php

declare(strict_types=1);

namespace HoloRick\Role;

use HoloRick\Character\Handler as CharacterHandler;
use HoloRick\Exception\RoleCreationFailedException;
use HoloRick\Exception\RoleDeletionFailedException;
use HoloRick\Logger;

use \Discord\Parts\Guild\Guild;
use \Discord\Parts\Guild\Role;
use \Discord\Parts\User\Member;

final class Handler {
  public static function createCharacterRoleForMember(Member $member, Guild $guild): void {
    $character = CharacterHandler::getRandomCharacter();

    $guild->createRole(['name' => $character->name, 'hoist' => true])
      ->done(function (Role $role) use ($character, $member) {
        Logger::log("Role Created: $role->id | $character->name", LOG_DIR);

        $member->addRole($role)->done(function () use ($role, $member, $character) {
          CharacterHandler::assignCharacter($member->user->id, $role->id, $character);

          Logger::log("$role->name assigned to {$member->user->username}", LOG_DIR);
        });
      });
  }

  public static function createRole(string $name, Guild $guild): ?Role {
    $newRole = null;

    $guild
      ->createRole(['name' => $name, 'hoist' => true])
      ->then(function ($role) {
        $newRole = $role;
      }, function ($x) {
        throw new RoleCreationFailedException($x->getMessage());
      });

    return $newRole;
  }

  public static function deleteRole(string|Role $role, Guild $guild): void {
    $guild->roles
      ->delete($role, 'Holo-Rick')
      ->then(null, fn($e) => throw new RoleDeletionFailedException($e));
  }
}
