<?php

namespace App\Entity;

use App\Enum\InquiryStatus;
use App\Repository\InquiryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InquiryRepository::class)]
#[ORM\Table(name: 'inquiries')]
#[ORM\Index(name: 'idx_inquiry_email', fields: ['email'])]
#[ORM\Index(name: 'idx_inquiry_status', fields: ['status'])]
class Inquiry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'inquiries')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Please enter a contact name.')]
    #[Assert\Length(max: 120, maxMessage: 'Name cannot be longer than {{ limit }} characters.')]
    private ?string $name = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Please enter an email address.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[Assert\Length(max: 180, maxMessage: 'Email cannot be longer than {{ limit }} characters.')]
    private ?string $email = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(max: 30, maxMessage: 'Phone number cannot exceed {{ limit }} characters.')]
    private ?string $phone = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Length(max: 120, maxMessage: 'Company name cannot exceed {{ limit }} characters.')]
    private ?string $company = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Please specify the requested service.')]
    #[Assert\Length(max: 80, maxMessage: 'Service cannot exceed {{ limit }} characters.')]
    private ?string $service = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Please specify the estimated budget.')]
    #[Assert\Length(max: 80, maxMessage: 'Budget cannot exceed {{ limit }} characters.')]
    private ?string $budget = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Please specify an estimated timeline.')]
    #[Assert\Length(max: 80, maxMessage: 'Timeline cannot exceed {{ limit }} characters.')]
    private ?string $timeline = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Please provide a project description.')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $attached_file = null;

    #[ORM\Column(enumType: InquiryStatus::class)]
    #[Assert\NotNull(message: 'Please select a valid inquiry status.')]
    private InquiryStatus $status = InquiryStatus::NEW;

    #[ORM\Column]
    private \DateTimeImmutable $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return 'INQ-' . str_pad((string)$this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(?string $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Alias for getBudget() to support 'budgetRange' naming in forms/views.
     */
    public function getBudgetRange(): ?string
    {
        return $this->budget;
    }

    /**
     * Alias for setBudget() to support 'budgetRange' naming in forms/views.
     */
    public function setBudgetRange(?string $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getTimeline(): ?string
    {
        return $this->timeline;
    }

    public function setTimeline(?string $timeline): static
    {
        $this->timeline = $timeline;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAttachedFile(): ?string
    {
        return $this->attached_file;
    }

    public function setAttachedFile(?string $attachedFile): static
    {
        $this->attached_file = $attachedFile;

        return $this;
    }

    public function getStatus(): InquiryStatus
    {
        return $this->status;
    }

    public function setStatus(?InquiryStatus $status): static
    {
        if ($status !== null) {
            $this->status = $status;
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->created_at = $createdAt;

        return $this;
    }
}
