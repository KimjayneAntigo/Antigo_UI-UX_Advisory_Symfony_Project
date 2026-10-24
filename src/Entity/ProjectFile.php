<?php

namespace App\Entity;

use App\Repository\ProjectFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectFileRepository::class)]
#[ORM\Table(name: 'project_files')]
class ProjectFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $uploaded_by = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::INTEGER)]
    private int $size_bytes;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $file_path = null;

    #[ORM\Column]
    private \DateTimeImmutable $uploaded_at;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleted_by_client_at = null;

    public function __construct()
    {
        $this->uploaded_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploaded_by;
    }

    public function setUploadedBy(?User $uploadedBy): static
    {
        $this->uploaded_by = $uploadedBy;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSizeBytes(): int
    {
        return $this->size_bytes;
    }

    public function setSizeBytes(int $sizeBytes): static
    {
        $this->size_bytes = $sizeBytes;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->file_path = $filePath;

        return $this;
    }

    public function getUploadedAt(): \DateTimeImmutable
    {
        return $this->uploaded_at;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static
    {
        $this->uploaded_at = $uploadedAt;

        return $this;
    }

    public function getDeletedByClientAt(): ?\DateTimeImmutable
    {
        return $this->deleted_by_client_at;
    }

    public function setDeletedByClientAt(?\DateTimeImmutable $deletedByClientAt): static
    {
        $this->deleted_by_client_at = $deletedByClientAt;

        return $this;
    }

    public function isVisibleToClient(): bool
    {
        return $this->deleted_by_client_at === null;
    }
}
