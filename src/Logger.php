<?php declare(strict_types=1);

namespace HoloRick;

class Logger {
  public static function getFilePath(string $fileName = ''): string {
    if (!$fileName) {
      $fileName = date('Ymd');
    }

    $path = LOG_DIR . "/$fileName.log";

    return $path;
  }

  public static function log(string $message, string $context = ''): void {
    $filePath = self::getFilePath();
    $timestamp = date('Y-m-d H:i:s');

    if ($context) {
      $context = '[' . strtoupper($context) . ']';
    }

    $message = "[$timestamp]$context $message\n";

    file_put_contents($filePath, $message, file_exists($filePath) ? FILE_APPEND : 0);
  }

  public static function debug(string $message): void {
    self::log($message, 'debug');
  }

  public static function error(\Exception $exception): void {
    self::log($exception->getMessage(), 'error');
  }
}