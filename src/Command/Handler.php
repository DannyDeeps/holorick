<?php

declare(strict_types=1);

namespace HoloRick\Command;

use HoloRick\Logger;
use HoloRick\Character\Handler as CharacterHandler;

use HoloRick\Exception\RoleCreationFailedException;
use HoloRick\Exception\RoleDeletionFailedException;
use HoloRick\Exception\RoleAssignFailedException;
use HoloRick\Exception\MessageFailedToSendException;
use HoloRick\Exception\GuildNotFoundException;
use HoloRick\Exception\UserIdNotFoundException;
use HoloRick\Exception\CharactersExhaustedException;

use \Discord\Discord;
use \Discord\Parts\Guild\Guild;
use \Discord\Parts\Channel\Message;
use \Discord\Builders\MessageBuilder;

final class Handler {
  public static function detectCommand(Message $message): bool {
    return str_starts_with($message->content, '!');
  }

  public static function executeCommand(Message $message, Discord $discord): void {
    try {
      $guild = $discord->guilds->first();
      if (!$guild) {
        throw new GuildNotFoundException;
      }

      $args = explode(' ', $message->content);
      $command = array_shift($args);

      Logger::log("{$message->author?->username} -> $command", 'command');

      if ($message->user_id === MASTER_USER_ID) {
        switch ($command) {
          case '!assignchars': self::assignCharacters($guild); break;
          case '!unassignchars': self::unassignCharacters($guild); break;
        }
      }

      switch ($command) {
        case '!mychar': self::myChar($message); break;
        case '!newchar': self::newChar($message, $guild); break;
      }
    } catch (GuildNotFoundException $e) {
      Logger::error($e);
    }
  }

  public static function unassignCharacters(Guild $guild): void {
    $assignedCharacters = CharacterHandler::getAssignedCharacters();

    foreach ($assignedCharacters as $character) {
      try {
        $guild->roles->delete($character->role_id)
          ->then(function() use ($character) {
            CharacterHandler::unassignCharacter($character->user_id);
          }, fn() => throw new RoleDeletionFailedException);
      } catch (RoleDeletionFailedException $e) {
        Logger::error($e);
      }
    }
  }

  public static function assignCharacters(Guild $guild): void {
    foreach ($guild->members as $member) {
      if ($member->user->bot) continue;

      try {
        $character = CharacterHandler::getAssignedCharacter($member->user->id);
        if (!$character) {
          $newCharacter = CharacterHandler::getRandomCharacter();

          $guild->createRole(['name' => $newCharacter->name, 'hoist' => true])
            ->then(function ($newRole) use ($member, $newCharacter) {
              CharacterHandler::assignCharacter($member->user->id, $newRole->role_id, $newCharacter);
            }, fn() => throw new RoleCreationFailedException);
        }
      } catch (CharactersExhaustedException|RoleCreationFailedException $e) {
        Logger::error($e);
      }
    }
  }

  public static function myChar(Message $message): void {
    if (null === $message->user_id) throw new UserIdNotFoundException;

    $character = CharacterHandler::getAssignedCharacter($message->user_id);

    if (null !== $character) {
      $embed = CharacterHandler::getCharacterEmbed($character);

      $reply = MessageBuilder::new()
        ->setContent("<@{$message->user_id}>")
        ->addEmbed($embed);

      $message->reply($reply)->then(null, fn() => throw new MessageFailedToSendException);
    }
  }

  public static function newChar(Message $message, Guild $guild): void {
    if (null === $message->user_id) throw new UserIdNotFoundException;

    $character = CharacterHandler::getAssignedCharacter($message->user_id);

    if (null !== $character) {
      $guild->roles->delete($character->role_id)
        ->then(function () use ($message) {
          CharacterHandler::unassignCharacter($message->user_id);
        }, fn() => throw new RoleDeletionFailedException);
    }

    $newCharacter = CharacterHandler::getRandomCharacter();

    $guild->createRole(['name' => $newCharacter->name, 'hoist' => true])
      ->then(function ($newRole) use ($message, $newCharacter) {
        $message->member?->addRole($newRole)
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
  }
}
