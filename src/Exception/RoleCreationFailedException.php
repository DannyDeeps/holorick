<?php declare(strict_types=1);

namespace HoloRick\Exception;

class RoleCreationFailedException extends \Exception {
  public function __construct(string $message = 'Role creation failed.', int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}