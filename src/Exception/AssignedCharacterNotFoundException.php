<?php declare(strict_types=1);

namespace HoloRick\Exception;

class AssignedCharacterNotFoundException extends \Exception {
  public function __construct(string $message = 'Could not find an assigned character.', int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}