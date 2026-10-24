<?php

namespace App\Entity;

use App\Enum\ProjectPhase;
use App\Enum\ProjectStatus;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'projects')]
#[ORM\Index(name: 'idx_project_client_email', fields: ['client_email'])]
#[ORM\Index(name: 'idx_project_status_type', fields: ['status_type'])]
#[ORM\HasLifecycleCallbacks]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 20, unique: true)]
    private string $project_code;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(length: 100)]
    private string $category;

    #[ORM\Column(length: 120)]
    private string $client_name;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $client_email = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private string $budget;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $due_date;

    #[ORM\Column(enumType: ProjectPhase::class)]
    private ProjectPhase $current_phase = ProjectPhase::DISCOVERY;

    #[ORM\Column(enumType: ProjectStatus::class)]
    private ProjectStatus $status_type = ProjectStatus::PENDING;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cancelled_at = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $cancelled_by = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancellation_reason = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $invoice_sent_at = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $internal_notes = null;

    #[ORM\Column]
    private \DateTimeImmutable $created_at;

    #[ORM\Column]
    private \DateTimeImmutable $updated_at;

    /**
     * @var Collection<int, ProjectFile>
     */
    #[ORM\OneToMany(targetEntity: ProjectFile::class, mappedBy: 'project', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $files;

    /**
     * @var Collection<int, ProjectMessage>
     */
    #[ORM\OneToMany(targetEntity: ProjectMessage::class, mappedBy: 'project', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $messages;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'project', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $payments;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->files = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProjectCode(): string
    {
        return $this->project_code;
    }

    public function setProjectCode(string $projectCode): static
    {
        $this->project_code = $projectCode;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getClientName(): string
    {
        return $this->client_name;
    }

    public function setClientName(string $clientName): static
    {
        $this->client_name = $clientName;

        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->client_email;
    }

    public function setClientEmail(?string $clientEmail): static
    {
        $this->client_email = $clientEmail;

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

    public function getBudget(): string
    {
        return $this->budget;
    }

    public function setBudget(string $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): static
    {
        $this->start_date = $startDate;

        return $this;
    }

    public function getEffectiveStartDate(): \DateTimeImmutable
    {
        return $this->start_date ?? $this->created_at;
    }

    public function getDueDate(): \DateTimeImmutable
    {
        return $this->due_date;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): static
    {
        $this->due_date = $dueDate;

        return $this;
    }

    public function getCurrentPhase(): ProjectPhase
    {
        return $this->current_phase;
    }

    public function setCurrentPhase(ProjectPhase $currentPhase): static
    {
        $this->current_phase = $currentPhase;

        return $this;
    }

    public function getPhase(): ProjectPhase
    {
        return $this->current_phase;
    }

    public function getProgress(): int
    {
        return $this->current_phase->progress();
    }

    public function getStatusType(): ProjectStatus
    {
        return $this->status_type;
    }

    public function setStatusType(ProjectStatus $statusType): static
    {
        $this->status_type = $statusType;

        return $this;
    }

    public function isCancelled(): bool
    {
        return $this->status_type === ProjectStatus::CANCELLED || $this->cancelled_at !== null;
    }

    public function isCompleted(): bool
    {
        return $this->status_type === ProjectStatus::COMPLETED;
    }

    public function isInvoiced(): bool
    {
        return $this->invoice_sent_at !== null;
    }

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelled_at;
    }

    public function setCancelledAt(?\DateTimeImmutable $cancelledAt): static
    {
        $this->cancelled_at = $cancelledAt;

        return $this;
    }

    public function getCancelledBy(): ?User
    {
        return $this->cancelled_by;
    }

    public function setCancelledBy(?User $cancelledBy): static
    {
        $this->cancelled_by = $cancelledBy;

        return $this;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellation_reason;
    }

    public function setCancellationReason(?string $cancellationReason): static
    {
        $this->cancellation_reason = $cancellationReason;

        return $this;
    }

    public function getInvoiceSentAt(): ?\DateTimeImmutable
    {
        return $this->invoice_sent_at;
    }

    public function setInvoiceSentAt(?\DateTimeImmutable $invoiceSentAt): static
    {
        $this->invoice_sent_at = $invoiceSentAt;

        return $this;
    }

    public function getInternalNotes(): ?string
    {
        return $this->internal_notes;
    }

    public function setInternalNotes(?string $internalNotes): static
    {
        $this->internal_notes = $internalNotes;

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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, ProjectFile>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @return Collection<int, ProjectMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }
}
