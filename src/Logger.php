<?php declare(strict_types=1);

namespace HoloRick;

use HoloRick\Exception\PathIsNotADirectoryException;
use HoloRick\Exception\PathDoesNotExistException;
use HoloRick\Exception\PathIsNotWritableException;

class Logger {
  public static function log(string $message, string $logDir): void {
    if (!file_exists($logDir)) throw new PathDoesNotExistException;
    if (!is_dir($logDir)) throw new PathIsNotADirectoryException;
    if (!is_writable($logDir)) throw new PathIsNotWritableException;

    $file = $logDir . '/' . date('Ymd') . '.log';
    $message = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";

    file_put_contents($file, $message, file_exists($file) ? FILE_APPEND : 0);
  }

  public static function debug(string $message, string $logDir): void {
    if (!file_exists($logDir)) throw new PathDoesNotExistException;
    if (!is_dir($logDir)) throw new PathIsNotADirectoryException;
    if (!is_writable($logDir)) throw new PathIsNotWritableException;

    $file = $logDir . '/' . date('Ymd') . '.log';
    $message = '[' . date('Y-m-d H:i:s') . '][DEBUG] ' . $message . "\n";

    file_put_contents($file, $message, file_exists($file) ? FILE_APPEND : 0);
  }

  public static function error(string $message, string $logDir): void {
    if (!file_exists($logDir)) throw new PathDoesNotExistException;
    if (!is_dir($logDir)) throw new PathIsNotADirectoryException;
    if (!is_writable($logDir)) throw new PathIsNotWritableException;

    $file = $logDir . '/' . date('Ymd') . '.log';
    $message = '[' . date('Y-m-d H:i:s') . '][ERROR] ' . $message . "\n";

    file_put_contents($file, $message, file_exists($file) ? FILE_APPEND : 0);
  }
}