<?php

namespace HoloRick\Message;

use \Discord\Discord;
use \Discord\Parts\Channel\Message;

use HoloRick\Command\Handler as CommandHandler;
use HoloRick\Logger;

class Handler {
  public static function incomingMessage(Message $message, Discord $discord) : void {
    if (in_array($message->channel_id, COMMAND_CHANNELS)) {
      if (CommandHandler::detectCommand($message)) {
        CommandHandler::executeCommand($message, $discord);
      }
    }
  }
}
