<?php declare(strict_types=1);

use HoloRick\Character\Handler;
use PHPUnit\Framework\TestCase;

final class HandlerTest extends TestCase {
  public function testCanGetCharacter(): void {
    $this->assertIsObject(Handler::getCharacter(1));
  }

  public function testCanGetRandomCharacter(): void {
    $this->assertIsObject(Handler::getRandomCharacter());
  }
}