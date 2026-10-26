<?php

namespace App\Entity;

use App\Enum\ProjectPhase;
use App\Enum\ProjectStatus;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    private ?string $project_code = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: 'Please enter a project title.')]
    #[Assert\Length(max: 200, maxMessage: 'Project title cannot exceed {{ limit }} characters.')]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Please specify a category / service.')]
    #[Assert\Length(max: 100, maxMessage: 'Category cannot exceed {{ limit }} characters.')]
    private ?string $category = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Please enter the client name.')]
    #[Assert\Length(max: 120, maxMessage: 'Client name cannot exceed {{ limit }} characters.')]
    private ?string $client_name = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email(message: 'Please enter a valid client email address.')]
    #[Assert\Length(max: 180, maxMessage: 'Client email cannot exceed {{ limit }} characters.')]
    private ?string $client_email = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Length(max: 120, maxMessage: 'Company name cannot exceed {{ limit }} characters.')]
    private ?string $company = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Assert\NotBlank(message: 'Please enter a budget amount.')]
    #[Assert\Positive(message: 'Budget must be greater than zero.')]
    private ?string $budget = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: 'Please select a target delivery / due date.')]
    private ?\DateTimeImmutable $due_date = null;

    #[ORM\Column(enumType: ProjectPhase::class)]
    #[Assert\NotNull(message: 'Please select a project phase.')]
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

    public function getProjectCode(): ?string
    {
        return $this->project_code;
    }

    public function setProjectCode(?string $projectCode): static
    {
        $this->project_code = $projectCode;

        return $this;
    }

    public function getCode(): string
    {
        return $this->project_code ?? '';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getService(): string
    {
        return $this->category ?? '';
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function setClientName(?string $clientName): static
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

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(?string $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getFormattedBudget(): string
    {
        return '$' . number_format((float) ($this->budget ?? 0), 0);
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

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->due_date;
    }

    public function setDueDate(?\DateTimeImmutable $dueDate): static
    {
        $this->due_date = $dueDate;

        return $this;
    }

    public function getExpectedDelivery(): string
    {
        return $this->due_date ? $this->due_date->format('M d, Y') : '';
    }

    public function getCurrentPhase(): ProjectPhase
    {
        return $this->current_phase;
    }

    public function setCurrentPhase(?ProjectPhase $currentPhase): static
    {
        if ($currentPhase !== null) {
            $this->current_phase = $currentPhase;
        }

        return $this;
    }

    public function getPhase(): ProjectPhase
    {
        return $this->current_phase;
    }

    public function getPhaseName(): string
    {
        return $this->current_phase->label();
    }

    public function getIsCancelled(): bool
    {
        return $this->isCancelled();
    }

    public function getAvatarInitials(): string
    {
        $name = trim($this->client_name ?? 'CL');
        $parts = explode(' ', $name);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    public function getPaymentBadge(): string
    {
        foreach ($this->payments as $payment) {
            if ($payment->getStatus() === \App\Enum\PaymentStatus::VERIFIED) {
                return 'Paid in Full';
            }
        }

        if ($this->invoice_sent_at !== null) {
            return 'Invoice Sent';
        }

        return 'Pending Deposit';
    }

    public function getPaymentBadgeType(): string
    {
        foreach ($this->payments as $payment) {
            if ($payment->getStatus() === \App\Enum\PaymentStatus::VERIFIED) {
                return 'emerald';
            }
        }

        if ($this->invoice_sent_at !== null) {
            return 'blue';
        }

        return 'amber';
    }

    public function getFilesCount(): int
    {
        return $this->files->count();
    }

    public function getMessagesCount(): int
    {
        return $this->messages->count();
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

    public function getCancelledDateFormatted(): ?string
    {
        return $this->cancelled_at ? $this->cancelled_at->format('M d, Y') : null;
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

    public function getStartDateFormatted(): string
    {
        return $this->start_date ? $this->start_date->format('M d, Y') : 'Kickoff';
    }

    public function getLatestPayment(): ?Payment
    {
        if ($this->payments->isEmpty()) {
            return null;
        }

        $payments = $this->payments->toArray();
        usort($payments, fn (Payment $a, Payment $b) => ($b->getId() ?? 0) <=> ($a->getId() ?? 0));

        return $payments[0] ?? null;
    }

    public function getPaymentState(): string
    {
        if ($this->isCancelled()) {
            return 'not_available';
        }

        if ($this->current_phase->value < 5) {
            return 'not_available';
        }

        if ($this->invoice_sent_at === null) {
            return 'complete_no_invoice';
        }

        $latest = $this->getLatestPayment();
        if ($latest !== null) {
            // Map verified status to 'paid' for UI styling
            if ($latest->getStatus() === \App\Enum\PaymentStatus::VERIFIED) {
                return 'paid';
            }

            return $latest->getStatus()->value; // 'pending' or 'rejected'
        }

        return 'invoice_sent';
    }

    public function getPaymentRef(): ?string
    {
        return $this->getLatestPayment()?->getReference() ?? 'PAY-00001';
    }

    public function getPaymentAmount(): ?string
    {
        $latest = $this->getLatestPayment();
        if ($latest !== null) {
            return '$' . number_format((float)$latest->getAmount(), 2);
        }

        return $this->getFormattedBudget();
    }

    /**
     * @return Collection<int, ProjectFile>
     */
    public function getClientVisibleFiles(): Collection
    {
        return $this->files->filter(fn (ProjectFile $file) => $file->isVisibleToClient());
    }
}
