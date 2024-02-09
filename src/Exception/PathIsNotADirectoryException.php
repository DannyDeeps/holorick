<?php declare(strict_types=1);

namespace HoloRick\Exception;

class PathIsNotADirectoryException extends \Exception {
  public function __construct(string $message = 'Path given is not a valid directory.', $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}