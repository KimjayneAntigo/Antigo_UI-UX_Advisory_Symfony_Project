<?php

namespace App\Controller;

use App\Enum\BookingStatus;
use App\Enum\PaymentStatus;
use App\Enum\ProjectStatus;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Mock\MockProjects;
use App\Repository\BookingRepository;
use App\Repository\InquiryRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_overview')]
    #[Route('/admin/dashboard', name: 'admin_overview_alias')]
    public function overview(
        ProjectRepository $projectRepository,
        InquiryRepository $inquiryRepository,
        BookingRepository $bookingRepository,
        PaymentRepository $paymentRepository
    ): Response {
        $rawPipeline = $projectRepository->getActivePipelineValue();
        $formattedPipeline = '$' . number_format((float) $rawPipeline, 0);

        $activeProjects = $projectRepository->findActive();
        $upcomingSessions = $bookingRepository->findUpcoming(5);

        $activeSprints = [];
        foreach ($activeProjects as $p) {
            $activeSprints[] = [
                'id' => $p->getId(),
                'code' => $p->getProjectCode(),
                'title' => $p->getTitle(),
                'client' => $p->getClientName() . ($p->getCompany() ? ' (' . $p->getCompany() . ')' : ''),
                'phase' => $p->getCurrentPhase()->label(),
                'status_badge' => $p->getCurrentPhase()->badge(),
                'progress' => $p->getProgress(),
                'budget' => $p->getFormattedBudget(),
                'updated' => $p->getUpdatedAt()->format('M d'),
            ];
        }

        $upcomingAgenda = [];
        foreach ($upcomingSessions as $b) {
            $upcomingAgenda[] = [
                'client' => $b->getUser() ? $b->getUser()->getFullName() : $b->getGuestName(),
                'service' => $b->getService(),
                'date' => $b->getBookingDate()->format('Y-m-d'),
                'time' => $b->getBookingTime()->format('g:i A'),
                'format' => $b->getMeetingFormat(),
                'status' => $b->getStatus()->label(),
            ];
        }

        return $this->render('admin/overview.html.twig', [
            'pageTitle' => 'Studio Command Center',
            'activePipelineValue' => $formattedPipeline,
            'activeProjectsCount' => count($activeProjects),
            'inquiriesPendingCount' => $inquiryRepository->countNew(),
            'paymentsPendingCount' => $paymentRepository->countPending(),
            'scheduledSessionsCount' => count($upcomingSessions),
            'activeSprints' => $activeSprints,
            'upcomingAgenda' => $upcomingAgenda,
        ]);
    }

    #[Route('/admin/search', name: 'admin_global_search', methods: ['GET'])]
    public function search(
        Request $request,
        UserRepository $userRepository,
        InquiryRepository $inquiryRepository,
        ProjectRepository $projectRepository
    ): Response {
        $q = trim($request->query->getString('q'));

        $clients = [];
        $inquiries = [];
        $projects = [];

        if ($q !== '') {
            $clients = $userRepository->searchClients($q);
            $inquiries = $inquiryRepository->search($q, null);
            $projects = $projectRepository->search($q);
        }

        $totalMatches = count($clients) + count($inquiries) + count($projects);

        return $this->render('admin/search_results.html.twig', [
            'pageTitle' => 'Studio Search Results',
            'currentQuery' => $q,
            'query' => $q,
            'clients' => $clients,
            'inquiries' => $inquiries,
            'projects' => $projects,
            'totalMatches' => $totalMatches,
        ]);
    }

    #[Route('/admin/payments', name: 'admin_payments', methods: ['GET'])]
    public function payments(PaymentRepository $paymentRepository, Request $request): Response
    {
        $statusParam = $request->query->getString('status');
        $q = trim($request->query->getString('q'));
        $statusEnum = $statusParam !== '' ? PaymentStatus::tryFrom($statusParam) : null;

        if ($statusEnum !== null) {
            $payments = $paymentRepository->findByStatus($statusEnum);
        } else {
            $payments = $paymentRepository->findBy([], ['submitted_at' => 'DESC']);
        }

        if ($q !== '') {
            $lowerQ = strtolower($q);
            $payments = array_filter($payments, function ($pm) use ($lowerQ) {
                return str_contains(strtolower($pm->getReference()), $lowerQ)
                    || ($pm->getProject() && str_contains(strtolower($pm->getProject()->getTitle()), $lowerQ))
                    || ($pm->getUser() && str_contains(strtolower($pm->getUser()->getFullName()), $lowerQ));
            });
        }

        $allPayments = $paymentRepository->findAll();
        $stats = [
            'total' => count($allPayments),
            'pending' => 0,
            'verified' => 0,
            'rejected' => 0,
        ];
        foreach ($allPayments as $pm) {
            match ($pm->getStatus()) {
                PaymentStatus::PENDING => $stats['pending']++,
                PaymentStatus::VERIFIED => $stats['verified']++,
                PaymentStatus::REJECTED => $stats['rejected']++,
            };
        }

        return $this->render('admin/payments.html.twig', [
            'pageTitle' => 'Payments & Verification',
            'payments' => $payments,
            'stats' => $stats,
            'currentQuery' => $q,
            'currentStatus' => $statusParam,
        ]);
    }

    #[Route('/admin/bookings', name: 'admin_bookings', methods: ['GET'])]
    public function bookings(BookingRepository $bookingRepository, Request $request): Response
    {
        $statusParam = $request->query->getString('status');
        $q = trim($request->query->getString('q'));
        $fromParam = $request->query->getString('from');
        $toParam = $request->query->getString('to');

        if ($fromParam !== '' && $toParam !== '') {
            try {
                $from = new \DateTimeImmutable($fromParam);
                $to = new \DateTimeImmutable($toParam);
                $bookings = $bookingRepository->findBetween($from, $to);
            } catch (\Exception) {
                $bookings = $bookingRepository->findBy([], ['booking_date' => 'ASC', 'booking_time' => 'ASC']);
            }
        } else {
            $bookings = $bookingRepository->findBy([], ['booking_date' => 'ASC', 'booking_time' => 'ASC']);
        }

        if ($statusParam !== '') {
            $statusEnum = BookingStatus::tryFrom($statusParam);
            if ($statusEnum !== null) {
                $bookings = array_filter($bookings, fn($b) => $b->getStatus() === $statusEnum);
            }
        }

        if ($q !== '') {
            $lowerQ = strtolower($q);
            $bookings = array_filter($bookings, function ($b) use ($lowerQ) {
                return str_contains(strtolower($b->getGuestName()), $lowerQ)
                    || str_contains(strtolower($b->getService()), $lowerQ)
                    || ($b->getUser() && str_contains(strtolower($b->getUser()->getFullName()), $lowerQ));
            });
        }

        $allBookings = $bookingRepository->findAll();
        $stats = [
            'total' => count($allBookings),
            'confirmed' => 0,
            'pending' => 0,
            'cancelled' => 0,
        ];
        foreach ($allBookings as $b) {
            match ($b->getStatus()) {
                BookingStatus::CONFIRMED => $stats['confirmed']++,
                BookingStatus::PENDING => $stats['pending']++,
                BookingStatus::CANCELLED => $stats['cancelled']++,
                default => null,
            };
        }

        return $this->render('admin/bookings.html.twig', [
            'pageTitle' => 'Scheduled Consultations',
            'bookings' => $bookings,
            'stats' => $stats,
            'currentQuery' => $q,
            'currentStatus' => $statusParam,
        ]);
    }

    #[Route('/admin/clients', name: 'admin_clients', methods: ['GET'])]
    public function clients(UserRepository $userRepository, Request $request): Response
    {
        $q = trim($request->query->getString('q'));
        if ($q !== '') {
            $clients = $userRepository->searchClients($q);
        } else {
            $clients = $userRepository->findClients();
        }

        return $this->render('admin/clients.html.twig', [
            'pageTitle' => 'Clients Directory',
            'clients' => $clients,
            'currentQuery' => $q,
            'stats' => [
                'total' => count($clients),
                'active' => count($clients),
            ],
        ]);
    }

    #[Route('/admin/settings', name: 'admin_settings', methods: ['GET', 'POST'])]
    public function settings(
        Request $request,
        EntityManagerInterface $entityManager,
        CurrentUserProvider $currentUserProvider,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $currentUserProvider->getAdmin();
        $profileForm = $this->createForm(ProfileType::class, $user);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        if ($request->isMethod('POST')) {
            if ($request->request->has('profile')) {
                $profileForm->handleRequest($request);
                if ($profileForm->isSubmitted() && $profileForm->isValid()) {
                    $entityManager->flush();

                    // Beyond the lesson: User feedback via flash message
                    $this->addFlash('success', 'Studio profile information updated successfully.');

                    return $this->redirectToRoute('admin_settings', [], Response::HTTP_SEE_OTHER);
                }
            } elseif ($request->request->has('change_password')) {
                $passwordForm->handleRequest($request);
                if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                    $currentPassword = (string)$passwordForm->get('currentPassword')->getData();
                    $newPassword = (string)$passwordForm->get('newPassword')->getData();

                    if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                        $passwordForm->get('currentPassword')->addError(new FormError('Current password is incorrect.'));
                    } else {
                        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                        $entityManager->flush();

                        // Beyond the lesson: User feedback via flash message
                        $this->addFlash('success', 'Studio master password has been changed successfully.');

                        return $this->redirectToRoute('admin_settings', [], Response::HTTP_SEE_OTHER);
                    }
                }
            }
        }

        return $this->render('admin/settings.html.twig', [
            'pageTitle' => 'Studio Settings & Profile — Admin',
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'user' => $user,
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
