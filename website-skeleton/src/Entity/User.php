<?php

namespace App\Entity;

use DateTime;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $firstname = null;

    #[ORM\Column(length: 60)]
    private ?string $lastname = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $dateofBirth = null;

    #[ORM\Column]
    #[Assert\Choice(choices: ['f', 'm'])]
    private ?string $gender = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isVerified = false;

    
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $profilePictureFilename = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $coverPhotoFilename = null;

    #[Assert\Image(
        maxWidth: 1200,
        maxHeight: 1200,
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png"],
        allowPortrait: false,
        allowLandscape: false,
        allowSquare: true,
        mimeTypesMessage: "Please upload a valid image (JPEG or PNG).",
        maxSizeMessage: "The file is too large ({{ size }} {{ suffix }}). Max allowed size is {{ limit }} {{ suffix }}."
    )]
    private ?File $profilePictureFile = null;

    #[Assert\Image(
        maxWidth: 2000,
        maxHeight: 2000,
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png"],
        allowPortrait: false,
        allowLandscape: false,
        allowSquare: true,
        mimeTypesMessage: "Please upload a valid image (JPEG or PNG).",
        maxSizeMessage: "The file is too large ({{ size }} {{ suffix }}). Max allowed size is {{ limit }} {{ suffix }}."
    )]
    private ?File $coverPhotoFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $job = null;

    #[ORM\Column(nullable: true)]
    private ?string $school = null;

    #[ORM\Column(nullable: true)]
    private ?string $interests = null;
    // #[ORM\Column(type: 'boolean')]
    // private bool $agreeTerms = false;

    // public function getAgreeTerms(): bool
    // {
    //     return $this->agreeTerms;
    // }

    // public function setAgreeTerms(bool $agreeTerms): self
    // {
    //     $this->agreeTerms = $agreeTerms;

    //     return $this;
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateofBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateofBirth): static
    {
        $this->dateofBirth = $dateofBirth;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function __toString(): string
    {
        return $this->username ?? '';
    }

    #[ORM\OneToMany(targetEntity: FriendShip::class, mappedBy: "user")]
    private Collection $friendships;
    
    #[ORM\OneToMany(targetEntity: FriendShip::class, mappedBy: "friend")]
    private Collection $friendOf;

    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: "user")]
    private Collection $likes;

    public function __construct()
    {
        $this->friendships = new ArrayCollection();
        $this->friendOf = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    /**
     * @return Collection|FriendShip[]
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    /**
     * @return Collection|FriendShip[]
     */
    public function getFriendOf(): Collection
    {
        return $this->friendOf;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function getProfilePictureFilename(): ?string
    {
        return $this->profilePictureFilename;
    }

    public function setProfilePictureFilename(?string $profilePictureFilename): self
    {
        $this->profilePictureFilename = $profilePictureFilename;

        return $this;
    }

    public function getCoverPhotoFilename(): ?string
    {
        return $this->coverPhotoFilename;
    }

    public function setCoverPhotoFilename(?string $coverPhotoFilename): self
    {
        $this->coverPhotoFilename = $coverPhotoFilename;

        return $this;
    }

    // Getters and setters for profilePictureFile and coverPhotoFile

    public function getProfilePictureFile(): ?File
    {
        return $this->profilePictureFile;
    }

    public function setProfilePictureFile(?File $profilePictureFile): self
    {
        $this->profilePictureFile = $profilePictureFile;

        if ($profilePictureFile) {
            // It's important that we remove the old profile picture filename to trigger the upload
            $this->profilePictureFilename = null;
        }

        return $this;
    }

    public function getCoverPhotoFile(): ?File
    {
        return $this->coverPhotoFile;
    }

    public function setCoverPhotoFile(?File $coverPhotoFile): self
    {
        $this->coverPhotoFile = $coverPhotoFile;

        if ($coverPhotoFile) {
            // It's important that we remove the old cover photo filename to trigger the upload
            $this->coverPhotoFilename = null;
        }

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getSchool(): ?string
    {
        return $this->school;
    }

    public function setSchool(?string $school): static
    {
        $this->school = $school;

        return $this;
    }

    public function getInterests(): ?string
    {
        return $this->interests;
    }

    public function setInterests(?string $interests): static
    {
        $this->interests = $interests;

        return $this;
    }
}
