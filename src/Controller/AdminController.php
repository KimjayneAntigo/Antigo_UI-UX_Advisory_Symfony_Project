<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Mock\MockProjects;
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
    public function overview(): Response
    {
        return $this->render('admin/overview.html.twig', [
            'pageTitle' => 'Studio Command Center',
            'activePipelineValue' => '$620,000',
            'activeProjectsCount' => 1,
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'scheduledSessionsCount' => 2,
            'activeSprints' => [
                [
                    'id' => 2,
                    'code' => 'PRJ-3003',
                    'title' => 'Cebu Tourism Responsive Website',
                    'client' => 'Ana Reyes (Cebu Tourism Board)',
                    'phase' => 'Discovery & Research',
                    'status_badge' => 'Discovery',
                    'progress' => 20,
                    'budget' => '$45,000',
                    'updated' => '2 hours ago'
                ],
                [
                    'id' => 1,
                    'code' => 'PRJ-3001',
                    'title' => 'Pesolink Banking Redesign',
                    'client' => 'Maria Santos (Pesolink)',
                    'phase' => 'Design System & Mobile App',
                    'status_badge' => 'Design',
                    'progress' => 65,
                    'budget' => '$120,000',
                    'updated' => 'Yesterday'
                ]
            ],
            'upcomingAgenda' => [
                [
                    'client' => 'Angel Queen Tumapon',
                    'service' => 'Design Systems',
                    'date' => '2026-09-10',
                    'time' => '10:00 AM',
                    'format' => 'Video Call (Google Meet)',
                    'status' => 'Confirmed'
                ],
                [
                    'client' => 'Demo Client',
                    'service' => 'UI Design Consultation',
                    'date' => '2026-09-10',
                    'time' => '10:00 AM',
                    'format' => 'Video Call (Google Meet)',
                    'status' => 'Pending'
                ],
            ],
        ]);
    }



    #[Route('/admin/payments', name: 'admin_payments')]
    public function payments(): Response
    {
        return $this->render('admin/payments.html.twig', [
            'pageTitle' => 'Payments & Verification',
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'stats' => [
                'total' => 3,
                'pending' => 2,
                'verified' => 1
            ],
            'payments' => [
                [
                    'id' => 101,
                    'ref' => 'PAY-9042',
                    'project_code' => 'PRJ-3001',
                    'project_title' => 'Pesolink Banking Redesign',
                    'client_name' => 'Maria Santos',
                    'client_company' => 'Pesolink',
                    'amount' => '$25,000',
                    'method' => 'Wire Transfer',
                    'status' => 'pending',
                    'submitted_at' => 'Today, 2:20 PM',
                    'receipt_file' => 'wire_receipt_pesolink_oct3.pdf'
                ],
                [
                    'id' => 102,
                    'ref' => 'PAY-9038',
                    'project_code' => 'PRJ-3003',
                    'project_title' => 'Cebu Tourism Responsive Website',
                    'client_name' => 'Ana Reyes',
                    'client_company' => 'Cebu Tourism Board',
                    'amount' => '$15,000',
                    'method' => 'Stripe ACH',
                    'status' => 'pending',
                    'submitted_at' => 'Oct 2, 2026',
                    'receipt_file' => 'stripe_inv_cebu_deposit.pdf'
                ],
                [
                    'id' => 103,
                    'ref' => 'PAY-8991',
                    'project_code' => 'PRJ-2998',
                    'project_title' => 'Brand Refresh — Web',
                    'client_name' => 'Maria Santos',
                    'client_company' => 'Pesolink',
                    'amount' => '$40,000',
                    'method' => 'Bank Wire (USD)',
                    'status' => 'verified',
                    'submitted_at' => 'Sep 18, 2026',
                    'receipt_file' => 'final_settlement_receipt.pdf'
                ],
            ]
        ]);
    }

    #[Route('/admin/bookings', name: 'admin_bookings')]
    public function bookings(): Response
    {
        return $this->render('admin/bookings.html.twig', [
            'pageTitle' => 'Scheduled Consultations',
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'stats' => [
                'total' => 3,
                'confirmed' => 2,
                'pending' => 1
            ],
            'bookings' => [
                [
                    'id' => 1,
                    'client_name' => 'Angel Queen Tumapon',
                    'email' => 'angel@tumapon.design',
                    'service' => 'Design Systems Architecture',
                    'date' => '2026-10-08',
                    'time' => '10:00 AM - 11:30 AM',
                    'format' => 'Google Meet',
                    'status' => 'confirmed',
                    'notes' => 'Token naming conventions and component hierarchy alignment.'
                ],
                [
                    'id' => 2,
                    'client_name' => 'Carlos Gomez',
                    'email' => 'carlos@fintechvanguard.com',
                    'service' => 'Sprint Kickoff & Product Strategy',
                    'date' => '2026-10-10',
                    'time' => '02:00 PM - 03:00 PM',
                    'format' => 'Zoom Meeting',
                    'status' => 'confirmed',
                    'notes' => 'Review milestone sprint deliverables for banking app.'
                ],
                [
                    'id' => 3,
                    'client_name' => 'David Lee',
                    'email' => 'david@archstone.io',
                    'service' => 'UX Diagnostic & Heuristic Evaluation',
                    'date' => '2026-10-12',
                    'time' => '11:00 AM - 12:00 PM',
                    'format' => 'Google Meet',
                    'status' => 'pending',
                    'notes' => 'New consultation request awaiting studio confirmation.'
                ],
            ]
        ]);
    }



    #[Route('/admin/clients', name: 'admin_clients')]
    public function clients(): Response
    {
        return $this->render('admin/clients.html.twig', [
            'pageTitle' => 'Clients Directory',
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'stats' => [
                'total' => 4,
                'active' => 3
            ],
            'clients' => [
                [
                    'id' => 1,
                    'name' => 'Maria Santos',
                    'company' => 'Pesolink Technologies',
                    'role' => 'Product Lead',
                    'email' => 'maria@pesolink.ph',
                    'phone' => '+63 917 000 1234',
                    'avatar_initials' => 'MS',
                    'active_projects' => 2,
                    'total_spend' => '$245,000',
                    'joined' => 'Jun 14, 2026'
                ],
                [
                    'id' => 2,
                    'name' => 'Ana Reyes',
                    'company' => 'Cebu Tourism Board',
                    'role' => 'Director of Digital Media',
                    'email' => 'ana@cebutourism.ph',
                    'phone' => '+63 918 555 8899',
                    'avatar_initials' => 'AR',
                    'active_projects' => 1,
                    'total_spend' => '$45,000',
                    'joined' => 'Aug 10, 2026'
                ],
                [
                    'id' => 3,
                    'name' => 'Carlos Gomez',
                    'company' => 'Fintech Vanguard',
                    'role' => 'VP Engineering',
                    'email' => 'carlos@fintechvanguard.com',
                    'phone' => '+1 415 882 0192',
                    'avatar_initials' => 'CG',
                    'active_projects' => 1,
                    'total_spend' => '$80,000',
                    'joined' => 'Sep 01, 2026'
                ],
                [
                    'id' => 4,
                    'name' => 'Angel Queen Tumapon',
                    'company' => 'Independent Consultant',
                    'role' => 'Principal Architect',
                    'email' => 'angel@tumapon.design',
                    'phone' => '+63 920 111 2233',
                    'avatar_initials' => 'AT',
                    'active_projects' => 0,
                    'total_spend' => '$3,500',
                    'joined' => 'Sep 10, 2026'
                ],
            ]
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
