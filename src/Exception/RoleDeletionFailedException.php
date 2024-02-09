<?php declare(strict_types=1);

namespace HoloRick\Exception;

class RoleDeletionFailedException extends \Exception {
  public function __construct(string $message = 'Role deletion failed.', $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}