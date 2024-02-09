<?php

declare(strict_types=1);

namespace HoloRick\Command;

use HoloRick\Logger;
use HoloRick\Role\Handler as RoleHandler;
use HoloRick\Character\Handler as CharacterHandler;

use HoloRick\Exception\RoleCreationFailedException;
use HoloRick\Exception\RoleDeletionFailedException;
use HoloRick\Exception\RoleAssignFailedException;
use HoloRick\Exception\MessageFailedToSendException;

use \Discord\Discord;
use \Discord\Parts\Guild\Guild;
use \Discord\Parts\Channel\Message;
use \Discord\Builders\MessageBuilder;

final class Handler {
  public static function detectCommand(Message $message): bool {
    return str_starts_with($message->content, '!');
  }

  public static function executeCommand(Message $message, Discord $discord): void {
    $guild = $discord->guilds->first();
    $args = explode(' ', $message->content);
    $command = array_shift($args);

    Logger::log("Command: {$message->author->username} -> $command", LOG_DIR);

    if ($message->user_id === MASTER_USER_ID) {
      switch ($command) {
        case '!assignroles': self::assignRoles($guild); break;
        case '!unassignroles': self::unassignRoles($guild); break;
        case '!resetroles':
          self::unassignRoles($guild);
          self::assignRoles($guild);
        break;
      }
    }

    switch ($command) {
      case '!mychar': self::myChar($message); break;
      case '!newchar': self::newChar($message, $guild); break;
    }
  }

  public static function unassignRoles(Guild $guild): void {
    $ignoreRoles = ['@everyone', 'Holo-Rick', 'Server Booster'];

    foreach ($guild->roles as $role) {
      if (in_array($role->name, $ignoreRoles)) continue;

      try {    
        $guild->roles->delete($role)->then(null, fn() => throw new RoleDeletionFailedException);
      } catch (\Throwable $e) {
        Logger::log($e->getMessage(), LOG_DIR);
      }
    }
      
    CharacterHandler::unassignCharacters();
  }

  public static function assignRoles(Guild $guild): void {
    try {
      foreach ($guild->members as $member) {
        if ($member->user->bot)
          continue;

        $character = CharacterHandler::getAssignedCharacter($member->user_id);

        $guild->roles->delete($character->role_id)
          ->then(function () use ($member, $guild) {
            CharacterHandler::unassignCharacter($member->user_id);

            $newCharacter = CharacterHandler::getRandomCharacter();

            $guild->createRole(['name' => $newCharacter->name, 'hoist' => true])
              ->then(function ($newRole) use ($member, $newCharacter) {
                CharacterHandler::assignCharacter($member->user_id, $newRole->role_id, $newCharacter);
              }, fn() => throw new RoleCreationFailedException);
          }, fn() => throw new RoleDeletionFailedException);
      }
    } catch (\Throwable $e) {
      Logger::log($e->getMessage(), LOG_DIR);
    }
  }

  public static function myChar(Message $message): void {
    try {
      $character = CharacterHandler::getAssignedCharacter($message->user_id);

      $embed = CharacterHandler::getCharacterEmbed($character);

      $reply = MessageBuilder::new()
        ->setContent("<@{$message->user_id}>")
        ->addEmbed($embed);

      $message->reply($reply)->then(null, fn() => throw new MessageFailedToSendException);
    } catch (\Throwable $e) {
      Logger::log($e->getMessage(), LOG_DIR);
    }
  }

  public static function newChar(Message $message, Guild $guild): void {
    try {
      $character = CharacterHandler::getAssignedCharacter($message->user_id);

      $guild->roles->delete($character->role_id)
        ->then(function () use ($guild, $message) {
          CharacterHandler::unassignCharacter($message->user_id);

          $newCharacter = CharacterHandler::getRandomCharacter();

          $guild->createRole(['name' => $newCharacter->name, 'hoist' => true])
            ->then(function ($newRole) use ($message, $newCharacter) {
              $message->member->addRole($newRole)
                ->then(function () use ($message, $newCharacter, $newRole) {
                  CharacterHandler::assignCharacter($message->user_id, $newRole->role_id, $newCharacter);

                  $embed = CharacterHandler::getCharacterEmbed($newCharacter);

                  $reply = MessageBuilder::new()
                    ->setContent("<@{$message->user_id}>")
                    ->addEmbed($embed);

                  $message->reply($reply)
                    ->then(null, fn() => throw new MessageFailedToSendException);
              }, fn() => throw new RoleAssignFailedException);
          }, fn() => throw new RoleCreationFailedException);
        }, fn() => throw new RoleDeletionFailedException);
    } catch (\Throwable $e) {
      Logger::log($e->getMessage(), LOG_DIR);
    }
  }
}
