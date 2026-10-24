<?php

namespace App\Entity;

use App\Enum\BookingStatus;
use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'bookings')]
#[ORM\Index(name: 'idx_booking_date', fields: ['booking_date'])]
#[ORM\Index(name: 'idx_booking_status', fields: ['status'])]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Inquiry::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Inquiry $inquiry = null;

    #[ORM\Column(length: 120)]
    private string $guest_name;

    #[ORM\Column(length: 180)]
    private string $guest_email;

    #[ORM\Column(length: 80)]
    private string $service;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $duration_min;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $booking_date;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private \DateTimeImmutable $booking_time;

    #[ORM\Column(length: 80)]
    private string $meeting_format;

    #[ORM\Column(enumType: BookingStatus::class)]
    private BookingStatus $status = BookingStatus::PENDING;

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
        return 'BKG-' . str_pad((string)$this->id, 5, '0', STR_PAD_LEFT);
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

    public function getInquiry(): ?Inquiry
    {
        return $this->inquiry;
    }

    public function setInquiry(?Inquiry $inquiry): static
    {
        $this->inquiry = $inquiry;

        return $this;
    }

    public function getGuestName(): string
    {
        return $this->guest_name;
    }

    public function setGuestName(string $guestName): static
    {
        $this->guest_name = $guestName;

        return $this;
    }

    public function getGuestEmail(): string
    {
        return $this->guest_email;
    }

    public function setGuestEmail(string $guestEmail): static
    {
        $this->guest_email = $guestEmail;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getDurationMin(): int
    {
        return $this->duration_min;
    }

    public function setDurationMin(int $durationMin): static
    {
        $this->duration_min = $durationMin;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getBookingDate(): \DateTimeImmutable
    {
        return $this->booking_date;
    }

    public function setBookingDate(\DateTimeImmutable $bookingDate): static
    {
        $this->booking_date = $bookingDate;

        return $this;
    }

    public function getBookingTime(): \DateTimeImmutable
    {
        return $this->booking_time;
    }

    public function setBookingTime(\DateTimeImmutable $bookingTime): static
    {
        $this->booking_time = $bookingTime;

        return $this;
    }

    public function getMeetingFormat(): string
    {
        return $this->meeting_format;
    }

    public function setMeetingFormat(string $meetingFormat): static
    {
        $this->meeting_format = $meetingFormat;

        return $this;
    }

    public function getStatus(): BookingStatus
    {
        return $this->status;
    }

    public function setStatus(BookingStatus $status): static
    {
        $this->status = $status;

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
