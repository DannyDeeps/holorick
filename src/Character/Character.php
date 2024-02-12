<?php declare(strict_types=1);

namespace HoloRick\Character;

use HoloRick\Exception\JsonDecodeFailedException;
use HoloRick\Exception\JsonEncodeFailedException;

class Character {
  public function __construct(
    public int $id,
    public string $name,
    public string $image,
    public string $species,
    public string $type,
    public string $gender,
    public string $origin,
    public string $location,
    public string $status,
    public string $user_id,
    public string $role_id
  ) {}

  public function toJson(): string {
    $json = json_encode($this);
    if (!$json) {
      throw new JsonEncodeFailedException;
    }

    return $json;
  }
  
  public static function fromJson(string $json): self {
    $obj = json_decode($json);
    if (!is_object($obj)) {
      throw new JsonDecodeFailedException;
    }

    return self::fromObject($obj);
  }

  public static function fromObject(object $obj): self {
    return new self(
      $obj->id ?? 0,
      $obj->name ?? '',
      $obj->image ?? '',
      $obj->species ?? '',
      $obj->type ?? '',
      $obj->gender ?? '',
      $obj->origin ?? '',
      $obj->location ?? '',
      $obj->status ?? '',
      $obj->user_id ?? '',
      $obj->role_id ?? ''
    );
  }
}