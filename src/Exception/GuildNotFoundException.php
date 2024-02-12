<?php declare(strict_types=1);

namespace HoloRick\Exception;

class GuildNotFoundException extends \Exception {
  public function __construct(string $message = 'Guild not found.', int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}