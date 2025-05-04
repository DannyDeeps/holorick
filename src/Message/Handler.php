<?php

namespace HoloRick\Message;

use \Discord\Discord;
use \Discord\Parts\Channel\Message;

use HoloRick\Command\Handler as CommandHandler;
use HoloRick\Logger;

class Handler {
  public static function incomingMessage(Message $message, Discord $discord) : void {
    if ($message->channel_id === $_ENV['COMMAND_CHANNEL_ID']) {
      if (CommandHandler::detectCommand($message)) {
        CommandHandler::executeCommand($message, $discord);
      }
    }
  }
}
