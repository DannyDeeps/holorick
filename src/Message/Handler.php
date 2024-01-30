<?php

namespace HoloRick\Message;

use \Discord\Discord;
use \Discord\Parts\Channel\Message;

use HoloRick\Commands;

final class Handler {
  public static function route(Message $message, Discord $discord) : void {
    switch ($message->channel_id) {
      default:
        self::process($message, $discord);
      break;
    }
  }

  public static function incomingMessage(Message $message, Discord $discord) : void {
    self::route($message, $discord);
  }

  public static function process(Message $message, Discord $discord) : void {
    if (Commands::detected($message)) {
      echo "Command Detected\n";
      Commands::execute($message, $discord);
    }
  }
}
