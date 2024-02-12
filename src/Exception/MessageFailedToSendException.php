<?php declare(strict_types=1);

namespace HoloRick\Exception;

class MessageFailedToSendException extends \Exception {
  public function __construct(string $message = 'Failed to send message.', int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}