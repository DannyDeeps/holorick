<?php declare(strict_types=1);

namespace HoloRick\Exception;

class RoleAssignFailedException extends \Exception {
  public function __construct(string $message = 'Failed to assign role.', $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}