<?php declare(strict_types=1);

namespace HoloRick\Exception;

class PathIsNotWritableException extends \Exception {
  public function __construct(string $message = 'Path given is not writable.', $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}