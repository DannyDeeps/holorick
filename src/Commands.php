<?php

declare(strict_types=1);

namespace HoloRick;

use HoloRick\Role\Handler as RoleHandler;

use \Discord\Discord;
use \Discord\Parts\Guild\Guild;
use \Discord\Parts\Channel\Message;
use \Discord\Parts\Embed\Embed;
use \Discord\Builders\MessageBuilder;

final class Commands {
  public static function detected(Message $message): bool {
    return str_starts_with($message->content, '!');
  }

  public static function execute(Message $message, Discord $discord): void {
    $args = explode(' ', $message->content);
    $command = array_shift($args);
    echo $command . "\n";

    $guild = $discord->guilds->first();

    if ($message->user_id === '254357887445499908') {
      switch ($command) {
        case '!resetroles':
          self::unassignRoles($guild);
          self::assignRoles($guild);
          break;

        case '!assignroles':
          self::assignRoles($guild);
          break;

        case '!unassignroles':
          self::unassignRoles($guild);
          break;
      }
    }

    switch ($command) {
      case '!mychar':
        $character = RoleHandler::getAssignedCharacter($message->user_id);

        $embed = new Embed($discord, [
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
              'value' => $character->origin->name,
              'inline' => true
            ],
            [
              'name' => 'Location',
              'value' => $character->location->name,
              'inline' => true
            ]
          ]
        ]);

        $reply = MessageBuilder::new()
          ->setContent("<@{$message->user_id}>")
          ->addEmbed($embed);

        $message->reply($reply)->done();
      break;

      case '!newchar':
        $character = RoleHandler::getAssignedCharacter($message->user_id);

        $guild->roles
          ->delete($character->role_id)
          ->done(function () use ($message, $guild, $discord) {
            RoleHandler::unassignCharacter($message->user_id);
            RoleHandler::createCharacterRoleForMember($message->member, $guild);

            $character = RoleHandler::getAssignedCharacter($message->user_id);

            $embed = new Embed($discord, [
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
                  'value' => $character->origin->name,
                  'inline' => true
                ],
                [
                  'name' => 'Location',
                  'value' => $character->location->name,
                  'inline' => true
                ]
              ]
            ]);

            $reply = MessageBuilder::new()
              ->setContent("<@{$message->user_id}>")
              ->addEmbed($embed);

            $message->reply($reply)->done();
          });

      break;
    }
  }

  public static function unassignRoles(Guild $guild): void {
    $ignoreRoles = ['@everyone', 'Holo-Rick', 'Server Booster'];

    foreach ($guild->roles as $role) {
      if (in_array($role->name, $ignoreRoles)) continue;

      $guild->roles->delete($role, '!resetroles was called');
    }

    RoleHandler::unassignCharacters();
  }

  public static function assignRoles(Guild $guild): void {
    foreach ($guild->members as $member) {
      if ($member->user->bot) continue;

      RoleHandler::createCharacterRoleForMember($member, $guild);
    }
  }
}
