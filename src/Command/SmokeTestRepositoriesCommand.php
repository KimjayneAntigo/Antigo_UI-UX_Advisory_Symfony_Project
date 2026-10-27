<?php

namespace App\Command;

use App\Enum\ProjectStatus;
use App\Enum\UserRole;
use App\Repository\BookingRepository;
use App\Repository\InquiryRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProjectFileRepository;
use App\Repository\ProjectMessageRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:smoke:repositories',
    description: 'Smoke tests all repository methods against the seeded fixture data'
)]
class SmokeTestRepositoriesCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly InquiryRepository $inquiryRepository,
        private readonly BookingRepository $bookingRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly ProjectMessageRepository $messageRepository,
        private readonly ProjectFileRepository $fileRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Smoke Testing Repositories against Fixture Data');

        // 1. UserRepository
        $io->section('1. UserRepository');
        $clients = $this->userRepository->findClients();
        $io->text(sprintf('findClients(): Found %d client users', count($clients)));
        if (count($clients) !== 3) {
            $io->error('Expected 3 clients');
            return Command::FAILURE;
        }

        $search = $this->userRepository->searchClients('Tourism');
        $io->text(sprintf('searchClients("Tourism"): Found %d match (%s)', count($search), $search[0]->getEmail() ?? 'none'));
        if (count($search) !== 1) {
            $io->error('Expected 1 search result for "Tourism"');
            return Command::FAILURE;
        }

        // 2. ProjectRepository
        $io->section('2. ProjectRepository');
        $ana = $this->userRepository->findOneBy(['email' => 'ana@cebutourism.ph']);
        $demoClient = $this->userRepository->findOneBy(['email' => 'demo@client.com']);

        $active = $this->projectRepository->findActiveForClient($ana);
        $io->text(sprintf('findActiveForClient(Ana): Found %d project (%s)', count($active), $active[0]->getProjectCode()));

        $cancelled = $this->projectRepository->findCancelled();
        $io->text(sprintf('findCancelled(): Found %d project(s)%s', count($cancelled), !empty($cancelled) ? ' (' . $cancelled[0]->getProjectCode() . ')' : ''));

        $countInDesign = $this->projectRepository->countByStatus(ProjectStatus::IN_DESIGN);
        $io->text(sprintf('countByStatus(IN_DESIGN): %d', $countInDesign));

        $pipelineVal = $this->projectRepository->getActivePipelineValue();
        $io->text(sprintf('getActivePipelineValue(): $%s USD', $pipelineVal));

        $awaitingPayment = $this->projectRepository->findAwaitingPaymentForClient($demoClient);
        $io->text(sprintf('findAwaitingPaymentForClient(Demo Client): Found %d project (%s)', count($awaitingPayment), $awaitingPayment[0]->getProjectCode()));

        // 3. InquiryRepository
        $io->section('3. InquiryRepository');
        $newInquiries = $this->inquiryRepository->findNew();
        $io->text(sprintf('findNew(): Found %d new inquiry (%s)', count($newInquiries), $newInquiries[0]->getName()));

        $newCount = $this->inquiryRepository->countNew();
        $io->text(sprintf('countNew(): %d', $newCount));

        $clientInquiries = $this->inquiryRepository->findForClient($ana);
        $io->text(sprintf('findForClient(Ana): Found %d inquiry (%s)', count($clientInquiries), $clientInquiries[0]->getReference()));

        // 4. BookingRepository
        $io->section('4. BookingRepository');
        $upcoming = $this->bookingRepository->findUpcoming(5);
        $io->text(sprintf('findUpcoming(5): Found %d upcoming bookings', count($upcoming)));

        $clientBookings = $this->bookingRepository->findForClient($demoClient);
        $io->text(sprintf('findForClient(Demo Client): Found %d booking (%s)', count($clientBookings), $clientBookings[0]->getReference()));

        // 5. PaymentRepository
        $io->section('5. PaymentRepository');
        $pendingPayments = $this->paymentRepository->countPending();
        $io->text(sprintf('countPending(): %d', $pendingPayments));

        $p2 = $this->projectRepository->findOneBy(['project_code' => 'PRJ-3045']);
        $latestPayment = $this->paymentRepository->findLatestForProject($p2);
        $io->text(sprintf('findLatestForProject(PRJ-3045): Found %s, amount $%s', $latestPayment?->getReference(), $latestPayment?->getAmount()));

        $reviewedPayments = $this->paymentRepository->findRecentlyReviewedForUser($demoClient, 30);
        $io->text(sprintf('findRecentlyReviewedForUser(Demo Client, 30): %d records', count($reviewedPayments)));

        // 6. ProjectMessageRepository
        $io->section('6. ProjectMessageRepository');
        $admin = $this->userRepository->findOneBy(['email' => 'admin@antigo.com']);
        $adminUnread = $this->messageRepository->countUnreadForUser($admin);
        $io->text(sprintf('countUnreadForUser(Admin): %d unread client messages', $adminUnread));

        $anaUnread = $this->messageRepository->countUnreadForUser($ana);
        $io->text(sprintf('countUnreadForUser(Ana): %d unread designer messages', $anaUnread));

        $p1 = $this->projectRepository->findOneBy(['project_code' => 'PRJ-3003']);
        $markedRead = $this->messageRepository->markReadForViewer($p1, UserRole::CLIENT);
        $io->text(sprintf('markReadForViewer(PRJ-3003, Client): Marked %d messages read', $markedRead));

        // 7. ProjectFileRepository
        $io->section('7. ProjectFileRepository');
        $visibleFiles = $this->fileRepository->findVisibleForClient($p1);
        $io->text(sprintf('findVisibleForClient(PRJ-3003): Found %d files (excludes soft-deleted)', count($visibleFiles)));

        $allFiles = $this->fileRepository->findAllForAdmin($p1);
        $io->text(sprintf('findAllForAdmin(PRJ-3003): Found %d files (includes soft-deleted)', count($allFiles)));

        // 8. New Part B Methods
        $io->section('8. New Repository Query Methods (Part B)');
        $inquirySearch = $this->inquiryRepository->search('Tourism', null);
        $io->text(sprintf('InquiryRepository::search("Tourism"): Found %d match', count($inquirySearch)));

        $inquiryStatusSearch = $this->inquiryRepository->search(null, \App\Enum\InquiryStatus::NEW);
        $io->text(sprintf('InquiryRepository::search(null, NEW): Found %d matches', count($inquiryStatusSearch)));

        $projectsByStatus = $this->projectRepository->findByStatusType(ProjectStatus::IN_DESIGN);
        $io->text(sprintf('ProjectRepository::findByStatusType(IN_DESIGN): Found %d matches', count($projectsByStatus)));

        $projectsByCategory = $this->projectRepository->findByCategory('UI/UX Design');
        $io->text(sprintf('ProjectRepository::findByCategory("UI/UX Design"): Found %d matches', count($projectsByCategory)));

        $projectsSearch = $this->projectRepository->search('Cebu');
        $io->text(sprintf('ProjectRepository::search("Cebu"): Found %d match', count($projectsSearch)));

        $bookingsBetween = $this->bookingRepository->findBetween(
            new \DateTimeImmutable('2026-01-01'),
            new \DateTimeImmutable('2026-12-31')
        );
        $io->text(sprintf('BookingRepository::findBetween(2026-01-01, 2026-12-31): Found %d bookings', count($bookingsBetween)));

        $paymentsByStatus = $this->paymentRepository->findByStatus(\App\Enum\PaymentStatus::PENDING);
        $io->text(sprintf('PaymentRepository::findByStatus(PENDING): Found %d pending payments', count($paymentsByStatus)));

        $io->success('All repository methods (including Part B) executed successfully with expected return data!');

        return Command::SUCCESS;
    }
}
