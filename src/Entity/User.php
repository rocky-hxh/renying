<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table('test_users')]
class User
{
    public const ACTIVE_STATUS = [0, 1];
    public const MEMBER_STATUS = [0, 1];
    public const USER_TYPES = [1, 2, 3];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Choice(choices: User::ACTIVE_STATUS, message: 'Choose a valid active status.')]
    private ?bool $isActive = null;

    #[ORM\Column]
    #[Assert\Choice(choices: User::MEMBER_STATUS, message: 'Choose a valid member status.')]
    private ?bool $isMember = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Choice(choices: User::USER_TYPES, message: 'Choose a valid user type.')]
    private ?int $userType = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastLoginAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isIsMember(): ?bool
    {
        return $this->isMember;
    }

    public function setIsMember(bool $isMember): self
    {
        $this->isMember = $isMember;

        return $this;
    }

    public function getUserType(): ?int
    {
        return $this->userType;
    }

    public function setUserType(int $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }
}
