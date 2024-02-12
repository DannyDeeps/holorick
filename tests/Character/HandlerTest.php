<?php declare(strict_types=1);

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

use HoloRick\Character\Character;
use HoloRick\Character\Handler;

final class HandlerTest extends TestCase {
  public function testGetCharacter(): Character {
    $character = Handler::getCharacter(1);
    $this->assertInstanceOf(Character::class, $character);
    return $character;
  }

  public function testGetRandomCharacter(): void {
    $character = Handler::getRandomCharacter();
    $this->assertInstanceOf(Character::class, $character);
  }

  #[Depends('testGetCharacter')]
  public function testGetCharacterEmbed(Character $character): void {
    $embed = Handler::getCharacterEmbed($character);

    $this->assertIsObject($embed);

    foreach (['title', 'image', 'fields'] as $property) {
      $this->assertObjectHasProperty($property, $embed);
    }
  
    $this->assertIsObject($embed->image);
    $this->assertObjectHasProperty('url', $embed->image);

    $this->assertIsArray($embed->fields);

    foreach ($embed->fields as $field) {
      $this->assertIsObject($field);

      foreach (['name', 'value', 'inline'] as $property) {
        $this->assertObjectHasProperty($property, $field);
      }
    }
  }
}