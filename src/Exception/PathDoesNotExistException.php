<?php declare(strict_types=1);

namespace HoloRick\Exception;

class PathDoesNotExistException extends \Exception {
  public function __construct(string $message = 'Path given does not exist.', $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}