<?php

namespace HoloRick;

use \Discord\Discord;
use \Discord\WebSockets\Event;
use \Discord\Parts\Channel\Message;
use \Discord\WebSockets\Intents;
use \React\EventLoop\Loop;

use HoloRick\Message\Handler as MessageHandler;

class Bot {
  private Discord $discord;

  public function start() : void {
    $this->discord = new Discord([
      'token' => $_ENV['BOT_TOKEN'],
      'loop' => Loop::get(),
      'disabledEvents' => [],
      'loadAllMembers' => true,
      'intents' => [
        Intents::GUILD_MEMBERS,
        Intents::GUILD_MESSAGES,
        Intents::GUILDS
      ]
    ]);

    $this->discord->on('ready', function (Discord $discord) {
      $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
        if ($message->author?->bot) return;
        if (!$message->channel_id) return;

        try {
          MessageHandler::incomingMessage($message, $discord);
        } catch (\Exception $e) {
          Logger::error($e);
        }
      });
    });

    $this->discord->run();
  }
}