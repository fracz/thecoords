<?php

declare(strict_types=1);

namespace App\Domain\User;

use Assert\Assertion;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Persistence\DoctrineUserRepository")
 * @ORM\Table(name="users")
 */
class User implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private ?UuidInterface $id;

    /**
     * @ORM\Column(name="short_unique_id", type="string", length=32, unique=true, options={"fixed" = true})
     */
    private string $shortUniqueId;

    /**
     * @ORM\Column(name="access_token", type="string", length=255)
     */
    private string $accessToken;

    /**
     * @ORM\Column(name="refresh_token", type="string", length=255, nullable=true)
     */
    private ?string $refreshToken;

    /**
     * @ORM\Column(name="token_expiration_time", type="utcdatetime", nullable=true)
     */
    private ?\DateTime $tokenExpirationTime;

    /**
     * @ORM\Column(name="regulations_accepted", type="boolean", options={"default": 0})
     */
    private bool $regulationsAccepted = false;

    /**
     * @ORM\Column(name="timezone", type="string", length=50, nullable=true)
     */
    private ?string $timezone;

    /**
     * @ORM\Column(name="locale", type="string", length=5, nullable=true)
     */
    private ?string $locale;

    /**
     * @ORM\Column(name="settings", type="string", length=1024, nullable=true)
     */
    private $settings;

    public function __construct(string $shortUniqueId)
    {
        $this->id = Uuid::uuid4();
        $this->shortUniqueId = $shortUniqueId;
        $this->accessToken = '';
        $this->refreshToken = '';
        $this->tokenExpirationTime = new \DateTime();
        $this->setTimezone(null);
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getShortUniqueId(): string
    {
        return $this->shortUniqueId;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getTokenExpirationTime(): ?\DateTime
    {
        return $this->tokenExpirationTime;
    }

    public function setAccessToken(string $accessToken): User
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function setRefreshToken(?string $refreshToken): User
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function setTokenExpirationTime(?\DateTime $tokenExpirationTime): User
    {
        $this->tokenExpirationTime = $tokenExpirationTime;
        return $this;
    }

    public function isRegulationsAccepted(): bool
    {
        return $this->regulationsAccepted;
    }

    public function setRegulationsAccepted(bool $regulationsAccepted): User
    {
        $this->regulationsAccepted = $regulationsAccepted;
        return $this;
    }

    public function getTimezone(): \DateTimeZone
    {
        $timezone = $this->timezone ?: date_default_timezone_get();
        try {
            return new \DateTimeZone($timezone);
        } catch (\Exception $e) {
            return new \DateTimeZone(date_default_timezone_get());
        }
    }

    public function setTimezone(?string $timezone): self
    {
        try {
            new \DateTimeZone($timezone ?: date_default_timezone_get());
            $this->timezone = $timezone;
        } catch (\Exception $e) {
            $this->timezone = date_default_timezone_get();
        }
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): User
    {
        $this->locale = $locale;
        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings ? json_decode($this->settings, true) : [];
    }


    public function setSettings(array $settings): self
    {
        Assertion::isArray($settings);
        $settings = array_intersect_key($settings, array_flip(['']));
//        if ($settings['defaultCountryCode'] ?? false) {
//            $util = PhoneNumberUtil::getInstance();
//            Assertion::inArray($settings['defaultCountryCode'], $util->getSupportedRegions());
//        }
        $this->settings = json_encode($settings, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'shortUniqueId' => $this->shortUniqueId,
            'isRegulationsAccepted' => $this->regulationsAccepted,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'settings' => $this->getSettings() ?: new \stdClass(),
        ];
    }
}
