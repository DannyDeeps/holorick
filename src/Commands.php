<?php

declare(strict_types=1);

namespace HoloRick;

use HoloRick\Role\Handler as RoleHandler;

use \Discord\Discord;
use \Discord\Parts\Guild\Guild;
use \Discord\Parts\Channel\Message;
use \Discord\Parts\Embed\Embed;

final class Commands {
  public static function detected(Message $message): bool {
    return str_starts_with($message->content, '!');
  }

  public static function execute(Message $message, Discord $discord) : void {
    $args = explode(' ', $message->content);
    $command = array_shift($args);
    echo $command . "\n";

    $guild = $discord->guilds->first();

    if ($message->user_id === '254357887445499908') {
      switch ($command) {
        case '!assignroles':
          self::resetRoles($guild);
          self::assignRoles($guild);
        break;

        case '!resetroles':
          self::resetRoles($guild);
        break;

        case '!t':
          // Test 
        break;

      }
    }

    switch ($command) {
      case '!mycharacter':
        $character = RoleHandler::getMemberCharacterRole($message->user_id);

        $embed = new Embed($discord, [
          'title' => ''
        ]);
      break;

      case '!newcharacter':
        $message->member->roles;
      break;
    }
  }

  public static function resetRoles(Guild $guild) : void {
    $ignoreRoles = ['@everyone', 'Holo-Rick', 'Server Booster'];

    foreach ($guild->roles as $role) {
      if (in_array($role->name, $ignoreRoles)) continue;

      $guild->roles->delete($role, '!resetroles was called');
    }

    RoleHandler::resetCharacterIdsInUse();
  }

  public static function assignRoles(Guild $guild) : void {
    foreach ($guild->members as $member) {
      if ($member->user->bot) continue;

      RoleHandler::createCharacterRoleForMember($member, $guild);
    }
  }
}
