<?php declare(strict_types=1);

namespace HoloRick\Exception;

class GetFileContentsException extends \Exception {
  public function __construct(string $message = 'file_get_contents() returned false.', int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}