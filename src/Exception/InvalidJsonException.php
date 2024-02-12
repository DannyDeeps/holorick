<?php declare(strict_types=1);

namespace HoloRick\Exception;

class InvalidJsonException extends \Exception {
  public function __construct(string $message = 'API did not response with 200.', int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}