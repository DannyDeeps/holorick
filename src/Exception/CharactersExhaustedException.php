<?php declare(strict_types=1);

namespace HoloRick\Exception;

class CharactersExhaustedException extends \Exception {
  public function __construct(string $message = 'No more available characters.',int $code = 0, \Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}