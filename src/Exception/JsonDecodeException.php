<?php declare(strict_types=1);

namespace HoloRick\Exception;

class JsonDecodeFailedException extends \Exception {
  public function __construct(string $message = '', int $code = 0, \Throwable $previous = null) {
    if (!$message) {
      $message = json_last_error_msg();
    }
    
    parent::__construct($message, $code, $previous);
  }
}