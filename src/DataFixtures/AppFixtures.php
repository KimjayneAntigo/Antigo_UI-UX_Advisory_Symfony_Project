<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Inquiry;
use App\Entity\Payment;
use App\Entity\Project;
use App\Entity\ProjectFile;
use App\Entity\ProjectMessage;
use App\Entity\User;
use App\Enum\BookingStatus;
use App\Enum\InquiryStatus;
use App\Enum\MessageRole;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Enum\ProjectPhase;
use App\Enum\ProjectStatus;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * Dev passwords (see docs/dev-credentials.md):
     * Admin: Advisory2026!
     * Client: Client2026!
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // -------------------------------------------------------------
        // 1. USERS
        // -------------------------------------------------------------
        $admin = new User();
        $admin->setName('Kimberly Jayne Antigo');
        $admin->setEmail('admin@antigo.com');
        $admin->setRole(UserRole::ADMIN);
        $admin->setCompany('Antigo UI/UX Advisory Studio');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Advisory2026!'));
        $manager->persist($admin);

        $demoClient = new User();
        $demoClient->setName('Demo Client');
        $demoClient->setEmail('demo@client.com');
        $demoClient->setRole(UserRole::CLIENT);
        $demoClient->setCompany('Visayas Health Care Network');
        $demoClient->setPassword($this->passwordHasher->hashPassword($demoClient, 'Client2026!'));
        $manager->persist($demoClient);

        $ana = new User();
        $ana->setName('Ana Reyes');
        $ana->setEmail('ana@cebutourism.ph');
        $ana->setRole(UserRole::CLIENT);
        $ana->setCompany('Cebu Tourism Board');
        $ana->setPassword($this->passwordHasher->hashPassword($ana, 'Client2026!'));
        $manager->persist($ana);

        $maria = new User();
        $maria->setName('Maria Santos');
        $maria->setEmail('maria@pesolink.ph');
        $maria->setRole(UserRole::CLIENT);
        $maria->setCompany('Pesolink Technologies');
        $maria->setPassword($this->passwordHasher->hashPassword($maria, 'Client2026!'));
        $manager->persist($maria);

        // -------------------------------------------------------------
        // 2. PROJECTS (Exactly matches IDs 1, 2, 3)
        // -------------------------------------------------------------
        // Project 1: Cebu Tourism Responsive Website
        $p1 = new Project();
        $p1->setProjectCode('PRJ-3003');
        $p1->setTitle('Cebu Tourism Responsive Website');
        $p1->setCategory('Web Design & Design Systems');
        $p1->setClientName('Ana Reyes');
        $p1->setClientEmail('ana@cebutourism.ph');
        $p1->setCompany('Cebu Tourism Board');
        $p1->setUser($ana);
        $p1->setBudget('45000.00');
        $p1->setStartDate(new \DateTimeImmutable('2026-06-10'));
        $p1->setDueDate(new \DateTimeImmutable('2026-09-25'));
        $p1->setCurrentPhase(ProjectPhase::DESIGN); // Phase 3 (60%)
        $p1->setStatusType(ProjectStatus::IN_DESIGN);
        $p1->setInternalNotes('Cebu Tourism Board priority: Ensure WCAG 2.1 AA color contrast compliance for regional attraction portals.');
        $p1->setCreatedAt(new \DateTimeImmutable('2026-06-10 08:30:00'));
        $manager->persist($p1);

        // Project 2: Visayas Health Care – Design Systems
        $p2 = new Project();
        $p2->setProjectCode('PRJ-3045');
        $p2->setTitle('Visayas Health Care – Design Systems');
        $p2->setCategory('Design System Architecture');
        $p2->setClientName('Demo Client');
        $p2->setClientEmail('demo@client.com');
        $p2->setCompany('Visayas Health Care Network');
        $p2->setUser($demoClient);
        $p2->setBudget('95000.00');
        $p2->setStartDate(new \DateTimeImmutable('2026-05-15'));
        $p2->setDueDate(new \DateTimeImmutable('2026-08-01'));
        $p2->setCurrentPhase(ProjectPhase::DELIVERED); // Phase 5 (100%)
        $p2->setStatusType(ProjectStatus::COMPLETED);
        $p2->setInvoiceSentAt(new \DateTimeImmutable('2026-08-01 10:00:00'));
        $p2->setInternalNotes('Handover sprint complete. Client submitted wire confirmation for PAY-00001 ($95,000). Pending studio bank statement match.');
        $p2->setCreatedAt(new \DateTimeImmutable('2026-05-15 09:00:00'));
        $manager->persist($p2);

        // Project 3: Pesolink Banking Redesign
        $p3 = new Project();
        $p3->setProjectCode('PRJ-3001');
        $p3->setTitle('Pesolink Banking Redesign');
        $p3->setCategory('Fintech Mobile & Web Redesign');
        $p3->setClientName('Maria Santos');
        $p3->setClientEmail('maria@pesolink.ph');
        $p3->setCompany('Pesolink Technologies');
        $p3->setUser($maria);
        $p3->setBudget('120000.00');
        $p3->setStartDate(new \DateTimeImmutable('2026-06-14'));
        $p3->setDueDate(new \DateTimeImmutable('2026-11-15'));
        $p3->setCurrentPhase(ProjectPhase::WIREFRAMING); // Phase 2 (40%)
        $p3->setStatusType(ProjectStatus::CANCELLED);
        $p3->setCancelledAt(new \DateTimeImmutable('2026-08-14 09:30:00'));
        $p3->setCancelledBy($admin);
        $p3->setCancellationReason('Client requested project freeze due to internal corporate restructuring.');
        $p3->setInternalNotes('Cancelled on Aug 14, 2026. Mutual release signed. Sprint deposit retained under contract terms.');
        $p3->setCreatedAt(new \DateTimeImmutable('2026-06-14 10:15:00'));
        $manager->persist($p3);

        // -------------------------------------------------------------
        // 3. PROJECT FILES (3-4 files, one with deleted_by_client_at)
        // -------------------------------------------------------------
        $f1 = new ProjectFile();
        $f1->setProject($p1);
        $f1->setName('Cebu_Tourism_Figma_Design_Tokens_v2.json');
        $f1->setSizeBytes(348160);
        $f1->setUploadedBy($admin);
        $f1->setUploadedAt(new \DateTimeImmutable('2026-07-24 10:00:00'));
        $manager->persist($f1);

        $f2 = new ProjectFile();
        $f2->setProject($p1);
        $f2->setName('Cebu_Tourism_HiFi_Prototypes_Rev2.fig');
        $f2->setSizeBytes(19503513);
        $f2->setUploadedBy($admin);
        $f2->setUploadedAt(new \DateTimeImmutable('2026-07-24 11:30:00'));
        $manager->persist($f2);

        $f3 = new ProjectFile();
        $f3->setProject($p1);
        $f3->setName('Cebu_Tourism_Sitemap_and_Information_Arch.pdf');
        $f3->setSizeBytes(4404019);
        $f3->setUploadedBy($admin);
        $f3->setUploadedAt(new \DateTimeImmutable('2026-07-18 14:15:00'));
        $manager->persist($f3);

        $f4 = new ProjectFile();
        $f4->setProject($p1);
        $f4->setName('Deprecated_Draft_Wireframes_Initial.pdf');
        $f4->setSizeBytes(8493465);
        $f4->setUploadedBy($admin);
        $f4->setUploadedAt(new \DateTimeImmutable('2026-06-12 09:00:00'));
        $f4->setDeletedByClientAt(new \DateTimeImmutable('2026-07-15 14:00:00')); // Soft deleted for client
        $manager->persist($f4);

        // -------------------------------------------------------------
        // 4. PROJECT MESSAGES (6-8 messages mixing read/unread)
        // -------------------------------------------------------------
        $m1 = new ProjectMessage();
        $m1->setProject($p1);
        $m1->setSender($admin);
        $m1->setRole(MessageRole::DESIGNER);
        $m1->setMessage("Hi Ana! I've uploaded the high-fidelity responsive prototypes and color system tokens for the portal.");
        $m1->setCreatedAt(new \DateTimeImmutable('2026-07-24 11:20:00'));
        $m1->setReadAt(new \DateTimeImmutable('2026-07-24 11:45:00'));
        $manager->persist($m1);

        $m2 = new ProjectMessage();
        $m2->setProject($p1);
        $m2->setSender($ana);
        $m2->setRole(MessageRole::CLIENT);
        $m2->setMessage('Thanks Kimberly! The destination card grid and mobile navigation look wonderful. The board reviewed them today.');
        $m2->setCreatedAt(new \DateTimeImmutable('2026-07-25 14:45:00'));
        $m2->setReadAt(new \DateTimeImmutable('2026-07-25 15:00:00'));
        $manager->persist($m2);

        $m3 = new ProjectMessage();
        $m3->setProject($p1);
        $m3->setSender($admin);
        $m3->setRole(MessageRole::DESIGNER);
        $m3->setMessage("Glad you like them! We're on track for the interactive prototyping review next week.");
        $m3->setCreatedAt(new \DateTimeImmutable('2026-07-26 09:15:00'));
        $m3->setReadAt(null); // Unread by client
        $manager->persist($m3);

        $m4 = new ProjectMessage();
        $m4->setProject($p2);
        $m4->setSender($admin);
        $m4->setRole(MessageRole::DESIGNER);
        $m4->setMessage('All final design system specifications, token schemas, and documentation handovers have been delivered.');
        $m4->setCreatedAt(new \DateTimeImmutable('2026-08-01 10:00:00'));
        $m4->setReadAt(new \DateTimeImmutable('2026-08-01 10:30:00'));
        $manager->persist($m4);

        $m5 = new ProjectMessage();
        $m5->setProject($p2);
        $m5->setSender($demoClient);
        $m5->setRole(MessageRole::CLIENT);
        $m5->setMessage('Payment reference PAY-00001 has been submitted via bank wire. Awaiting studio verification.');
        $m5->setCreatedAt(new \DateTimeImmutable('2026-08-02 15:30:00'));
        $m5->setReadAt(null); // Unread by admin
        $manager->persist($m5);

        $m6 = new ProjectMessage();
        $m6->setProject($p3);
        $m6->setSender($maria);
        $m6->setRole(MessageRole::CLIENT);
        $m6->setMessage('Hi Kimberly, we need to pause our sprint due to leadership changes and department budget freezes.');
        $m6->setCreatedAt(new \DateTimeImmutable('2026-08-13 16:15:00'));
        $m6->setReadAt(new \DateTimeImmutable('2026-08-13 16:30:00'));
        $manager->persist($m6);

        $m7 = new ProjectMessage();
        $m7->setProject($p3);
        $m7->setSender($admin);
        $m7->setRole(MessageRole::DESIGNER);
        $m7->setMessage('Understood Maria. All sprint wireframes and research artifacts have been archived safely.');
        $m7->setCreatedAt(new \DateTimeImmutable('2026-08-14 09:30:00'));
        $m7->setReadAt(new \DateTimeImmutable('2026-08-14 09:45:00'));
        $manager->persist($m7);

        // -------------------------------------------------------------
        // 5. INQUIRIES (3 inquiries: new, reviewed, contacted)
        // -------------------------------------------------------------
        $inq1 = new Inquiry();
        $inq1->setName('David Lim');
        $inq1->setEmail('david.lim@metrofin.com');
        $inq1->setPhone('+63 917 555 0192');
        $inq1->setCompany('Metro Financial Group');
        $inq1->setService('Fintech Mobile App');
        $inq1->setBudget('$25,000 – $50,000');
        $inq1->setTimeline('3-4 months');
        $inq1->setDescription('Complete redesign of our consumer retail banking and remittance application.');
        $inq1->setStatus(InquiryStatus::NEW);
        $manager->persist($inq1);

        $inq2 = new Inquiry();
        $inq2->setName('Clara Gomez');
        $inq2->setEmail('clara@islandretail.ph');
        $inq2->setPhone('+63 918 555 4482');
        $inq2->setCompany('Island Retail Brands');
        $inq2->setService('E-Commerce Design System');
        $inq2->setBudget('$10,000 – $25,000');
        $inq2->setTimeline('2 months');
        $inq2->setDescription('Unified multi-brand design tokens, UI kit, and storefront component library in Figma.');
        $inq2->setStatus(InquiryStatus::REVIEWED);
        $manager->persist($inq2);

        $inq3 = new Inquiry();
        $inq3->setUser($ana);
        $inq3->setName('Ana Reyes');
        $inq3->setEmail('ana@cebutourism.ph');
        $inq3->setCompany('Cebu Tourism Board');
        $inq3->setService('Tourism Portal Strategy');
        $inq3->setBudget('$50,000+');
        $inq3->setTimeline('6 months');
        $inq3->setDescription('Phase 2 regional attraction expansion and multilingual tourism booking workflows.');
        $inq3->setStatus(InquiryStatus::CONTACTED);
        $manager->persist($inq3);

        // -------------------------------------------------------------
        // 6. BOOKINGS (2 bookings)
        // -------------------------------------------------------------
        $bkg1 = new Booking();
        $bkg1->setUser($demoClient);
        $bkg1->setGuestName('Dr. Roberto Mendoza');
        $bkg1->setGuestEmail('demo@client.com');
        $bkg1->setService('Design System Architecture Consultation');
        $bkg1->setDurationMin(45);
        $bkg1->setPrice('350.00');
        $bkg1->setBookingDate(new \DateTimeImmutable('+2 days'));
        $bkg1->setBookingTime(new \DateTimeImmutable('10:00:00'));
        $bkg1->setMeetingFormat('Google Meet Video Call');
        $bkg1->setStatus(BookingStatus::CONFIRMED);
        $manager->persist($bkg1);

        $bkg2 = new Booking();
        $bkg2->setGuestName('James Tan');
        $bkg2->setGuestEmail('james@ventureph.io');
        $bkg2->setService('Executive UX Advisory');
        $bkg2->setDurationMin(30);
        $bkg2->setPrice('250.00');
        $bkg2->setBookingDate(new \DateTimeImmutable('+5 days'));
        $bkg2->setBookingTime(new \DateTimeImmutable('14:30:00'));
        $bkg2->setMeetingFormat('Zoom Video Call');
        $bkg2->setStatus(BookingStatus::PENDING);
        $manager->persist($bkg2);

        // -------------------------------------------------------------
        // 7. PAYMENTS (1 payment: for Project 2, PAY-00001)
        // -------------------------------------------------------------
        $pay1 = new Payment();
        $pay1->setProject($p2);
        $pay1->setUser($demoClient);
        $pay1->setAmount('95000.00');
        $pay1->setPaymentMethod(PaymentMethod::BANK_TRANSFER);
        $pay1->setStatus(PaymentStatus::PENDING);
        $pay1->setAdminNotes('Client submitted bank wire reference for final deliverable milestone. Pending studio bank statement match.');
        $pay1->setSubmittedAt(new \DateTimeImmutable('2026-08-02 15:30:00'));
        $manager->persist($pay1);

        $manager->flush();
    }
}
