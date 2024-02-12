<?php declare(strict_types=1);

namespace HoloRick\Exception;

class UserIdNotFoundException extends \Exception {
  public function __construct(string $message = 'Could not find user id.', int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}