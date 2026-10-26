<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Project;
use App\Entity\ProjectFile;
use App\Entity\ProjectMessage;
use App\Enum\MessageRole;
use App\Enum\PaymentStatus;
use App\Enum\ProjectStatus;
use App\Form\CancelProjectType;
use App\Form\ChangePasswordType;
use App\Form\MessageType;
use App\Form\PaymentType;
use App\Form\ProfileType;
use App\Mock\MockProjects;
use App\Repository\ProjectFileRepository;
use App\Repository\ProjectRepository;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'client_dashboard')]
    #[Route('/client/dashboard', name: 'client_dashboard_alias')]
    public function dashboard(): Response
    {
        return $this->render('client/dashboard.html.twig', [
            'pageTitle' => 'Client Workspace',
            'clientName' => 'Maria Santos',
            'clientFirstName' => 'Maria',
            'unreadMessagesCount' => 2,
            'stats' => [
                'activeProjects' => 2,
                'filesShared' => 14,
                'unreadMessages' => 2,
            ],
            'projects' => [
                [
                    'id' => 1,
                    'title' => 'Pesolink Banking Redesign',
                    'started' => 'Jun 14',
                    'phase' => 'Design phase',
                    'status_badge' => 'DESIGN',
                    'status_color' => 'blue',
                    'progress' => 65,
                ],
                [
                    'id' => 2,
                    'title' => 'Mobile App — Phase 2',
                    'started' => 'Jul 2',
                    'phase' => 'Testing phase',
                    'status_badge' => 'TESTING',
                    'status_color' => 'amber',
                    'progress' => 85,
                ],
                [
                    'id' => 3,
                    'title' => 'Brand Refresh — Web',
                    'started' => 'Apr 30',
                    'phase' => 'Delivered Apr 30',
                    'status_badge' => 'DELIVERED',
                    'status_color' => 'emerald',
                    'progress' => 100,
                ],
            ],
            'recentActivity' => [
                ['text' => 'Kimberly uploaded "Final_UI_Kit_v3.fig"', 'time' => '2 hours ago'],
                ['text' => 'New message from Kimberly', 'time' => 'Yesterday, 4:12 PM'],
                ['text' => 'Project status updated to "Design"', 'time' => 'Aug 2'],
                ['text' => 'Consultation call completed', 'time' => 'Jul 28'],
            ],
        ]);
    }

    #[Route('/client/projects', name: 'client_projects')]
    public function projects(): Response
    {
        $all = MockProjects::getAll();
        $projectsList = [];
        foreach ($all as $p) {
            $projectsList[] = [
                'id' => $p['id'],
                'code' => $p['code'],
                'title' => $p['title'],
                'service' => $p['service'],
                'stage' => $p['phase_name'],
                'progress' => $p['progress'],
                'delivery_date' => $p['expected_delivery'],
                'budget' => $p['budget'],
                'status' => $p['status_type'],
                'files_count' => count($p['files']),
                'unread_messages' => 1,
            ];
        }

        return $this->render('client/projects.html.twig', [
            'pageTitle' => 'My Projects',
            'unreadMessagesCount' => 2,
            'projects' => $projectsList,
        ]);
    }

    #[Route('/client/messages', name: 'client_messages')]
    public function messages(): Response
    {
        return $this->render('client/messages.html.twig', [
            'pageTitle' => 'Messages',
            'unreadMessagesCount' => 2,
            'activeConversation' => [
                'project_title' => 'Pesolink Banking Redesign',
                'advisor_name' => 'Kimberly Jayne',
                'advisor_role' => 'Founder & Lead Designer',
                'messages' => [
                    [
                        'sender' => 'Maria Santos',
                        'role' => 'client',
                        'text' => 'This looks great! Can we tweak the color of the primary CTA button?',
                        'time' => 'Yesterday, 5:03 PM'
                    ],
                    [
                        'sender' => 'Kimberly Jayne',
                        'role' => 'designer',
                        'text' => 'Of course — I\'ll try a couple of variants and share them tomorrow.',
                        'time' => 'Yesterday, 5:20 PM'
                    ],
                    [
                        'sender' => 'Kimberly Jayne',
                        'role' => 'designer',
                        'text' => 'I\'ve uploaded the updated variant kit to Files for review.',
                        'time' => 'Today, 10:14 AM'
                    ],
                ]
            ],
            'conversationsList' => [
                [
                    'project' => 'Pesolink Banking Redesign',
                    'last_msg' => 'I\'ve uploaded the updated variant kit...',
                    'time' => '10:14 AM',
                    'unread' => 1,
                    'active' => true
                ],
                [
                    'project' => 'Mobile App — Phase 2',
                    'last_msg' => 'Usability test round 1 results ready.',
                    'time' => 'Aug 2',
                    'unread' => 1,
                    'active' => false
                ],
                [
                    'project' => 'Brand Refresh — Web',
                    'last_msg' => 'Final archive downloaded and verified.',
                    'time' => 'Jul 20',
                    'unread' => 0,
                    'active' => false
                ],
            ]
        ]);
    }

    #[Route('/client/files', name: 'client_files')]
    public function files(): Response
    {
        return $this->render('client/files.html.twig', [
            'pageTitle' => 'Files & Deliverables',
            'unreadMessagesCount' => 2,
            'files' => [
                [
                    'name' => 'Final_UI_Kit_v3.fig',
                    'project' => 'Pesolink Banking Redesign',
                    'uploaded_by' => 'Kimberly',
                    'time' => '2 hours ago',
                    'size' => '24.2 MB',
                    'type' => 'figma'
                ],
                [
                    'name' => 'Usability_Test_Report.pdf',
                    'project' => 'Pesolink Banking Redesign',
                    'uploaded_by' => 'Kimberly',
                    'time' => 'Jul 28',
                    'size' => '4.8 MB',
                    'type' => 'pdf'
                ],
                [
                    'name' => 'Brand_Assets.zip',
                    'project' => 'Brand Refresh — Web',
                    'uploaded_by' => 'Maria Santos',
                    'time' => 'Jul 16',
                    'size' => '115 MB',
                    'type' => 'zip'
                ],
                [
                    'name' => 'Design_Token_Specifications.json',
                    'project' => 'Pesolink Banking Redesign',
                    'uploaded_by' => 'Kimberly',
                    'time' => 'Jul 10',
                    'size' => '340 KB',
                    'type' => 'code'
                ],
            ]
        ]);
    }

    #[Route('/client/settings', name: 'client_settings', methods: ['GET', 'POST'])]
    public function settings(
        Request $request,
        EntityManagerInterface $entityManager,
        CurrentUserProvider $currentUserProvider,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $currentUserProvider->getClient();
        $profileForm = $this->createForm(ProfileType::class, $user);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        if ($request->isMethod('POST')) {
            if ($request->request->has('profile')) {
                $profileForm->handleRequest($request);
                if ($profileForm->isSubmitted() && $profileForm->isValid()) {
                    $entityManager->flush();

                    // Beyond the lesson: User feedback via flash message
                    $this->addFlash('success', 'Your organization profile information has been updated.');

                    return $this->redirectToRoute('client_settings', [], Response::HTTP_SEE_OTHER);
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
                        $this->addFlash('success', 'Your account password has been changed successfully.');

                        return $this->redirectToRoute('client_settings', [], Response::HTTP_SEE_OTHER);
                    }
                }
            }
        }

        return $this->render('client/settings.html.twig', [
            'pageTitle' => 'Account & Workspace Settings',
            'unreadMessagesCount' => 2,
            'user' => $user,
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }

    #[Route('/client/projects/{id}', name: 'client_project_detail', methods: ['GET'])]
    public function projectDetail(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->render('error/404.html.twig', [
                'pageTitle' => 'Project Not Found — Client Portal',
                'requestedId' => (string)$id,
                'unreadMessagesCount' => 2,
            ], new Response('', Response::HTTP_NOT_FOUND));
        }

        $messageForm = $this->createForm(MessageType::class);
        $paymentForm = $this->createForm(PaymentType::class);
        $cancelForm = $this->createForm(CancelProjectType::class);

        return $this->render('client/project_detail.html.twig', [
            'pageTitle' => $project->getTitle() . ' — Project Workspace',
            'unreadMessagesCount' => 2,
            'project' => $project,
            'messageForm' => $messageForm->createView(),
            'paymentForm' => $paymentForm->createView(),
            'cancelForm' => $cancelForm->createView(),
            'currentUser' => $currentUserProvider->getClient(),
        ]);
    }

    #[Route('/client/projects/{id}/message', name: 'client_project_message', methods: ['POST'])]
    public function sendMessage(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Project not found.');
        }

        // Beyond the lesson: Refuse messages on cancelled projects
        if ($project->isCancelled()) {
            $this->addFlash('error', 'Messaging is locked for cancelled sprint records.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $message = new ProjectMessage();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setProject($project);
            $message->setSender($currentUserProvider->getClient());
            $message->setRole(MessageRole::CLIENT);

            $entityManager->persist($message);
            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', 'Your message has been delivered to Kimberly.');
        } else {
            $this->addFlash('error', 'Please enter a valid message before sending.');
        }

        return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/client/projects/{id}/payment', name: 'client_project_payment', methods: ['POST'])]
    public function submitPayment(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Project not found.');
        }

        $client = $currentUserProvider->getClient();

        // Beyond the lesson: Security rule — project must belong to the logged-in client
        if ($project->getUser()?->getId() !== $client?->getId()) {
            $this->addFlash('error', 'You can only submit payment for your own projects.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        // Beyond the lesson: Security rule — must be completed and invoiced
        if (!$project->isCompleted() || $project->getInvoiceSentAt() === null) {
            $this->addFlash('error', 'Payment can only be submitted for completed projects with an issued milestone invoice.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        // Beyond the lesson: Security rule — no pending or verified payment can exist
        foreach ($project->getPayments() as $existingPayment) {
            if (in_array($existingPayment->getStatus(), [PaymentStatus::PENDING, PaymentStatus::VERIFIED], true)) {
                $this->addFlash('error', sprintf('Payment reference %s is already on file for this project.', $existingPayment->getReference()));
                return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payment = new Payment();
            $payment->setProject($project);
            $payment->setUser($client);
            // Beyond the lesson: Security rule — amount is NEVER read from request, always set from project budget
            $payment->setAmount($project->getBudget());
            $payment->setPaymentMethod($form->get('method')->getData());
            $payment->setStatus(PaymentStatus::PENDING);
            $payment->setSubmittedAt(new \DateTimeImmutable());

            $entityManager->persist($payment);
            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message showing reference
            $this->addFlash('success', sprintf('Payment reference %s submitted successfully for %s USD. The studio will verify your payment.', $payment->getReference(), $project->getFormattedBudget()));
        } else {
            $this->addFlash('error', 'Please select a valid payment method.');
        }

        return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/client/projects/{id}/cancel', name: 'client_project_cancel', methods: ['POST'])]
    public function cancelProject(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Project not found.');
        }

        $client = $currentUserProvider->getClient();

        // Beyond the lesson: Client can only cancel their own project
        if ($project->getUser()?->getId() !== $client?->getId()) {
            $this->addFlash('error', 'You can only cancel your own projects.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        // Beyond the lesson: Refuse cancellation if already completed or cancelled
        if ($project->isCompleted() || $project->isCancelled()) {
            $this->addFlash('error', 'Cannot cancel a project that is already completed or cancelled.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(CancelProjectType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reason = trim((string)$form->get('reason')->getData());
            if ($reason === '') {
                $reason = 'Client requested project freeze.';
            }

            $project->setStatusType(ProjectStatus::CANCELLED);
            $project->setCancelledAt(new \DateTimeImmutable());
            $project->setCancelledBy($client);
            $project->setCancellationReason($reason);

            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', sprintf('Sprint %s has been cancelled.', $project->getProjectCode()));
        } else {
            $this->addFlash('error', 'Invalid cancellation request.');
        }

        return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    // Beyond the lesson: Secure project file download for client
    #[Route('/client/projects/{id}/files/{fileId}/download', name: 'client_project_file_download', methods: ['GET'])]
    public function downloadFile(
        int $id,
        int $fileId,
        ProjectRepository $projectRepository,
        ProjectFileRepository $fileRepository,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Project not found.');
        }

        $client = $currentUserProvider->getClient();

        // Beyond the lesson: Check project ownership
        if ($project->getUser()?->getId() !== $client?->getId()) {
            $this->addFlash('error', 'You can only download files from your own project workspace.');
            return $this->redirectToRoute('client_projects', [], Response::HTTP_SEE_OTHER);
        }

        // Beyond the lesson: None of these actions work on a cancelled project
        if ($project->isCancelled()) {
            $this->addFlash('error', 'File downloads are locked for cancelled sprint records.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $file = $fileRepository->find($fileId);
        if (!$file || $file->getProject()->getId() !== $project->getId()) {
            throw $this->createNotFoundException('File not found in project workspace.');
        }

        // Beyond the lesson: Client cannot download files they removed from their view
        if (!$file->isVisibleToClient()) {
            $this->addFlash('error', 'This file has been removed from your workspace view.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $fullPath = $this->getParameter('kernel.project_dir') . '/' . $file->getFilePath();
        if (!file_exists($fullPath)) {
            throw $this->createNotFoundException('The file asset does not exist on disk.');
        }

        $response = new BinaryFileResponse($fullPath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getName());

        return $response;
    }

    // Beyond the lesson: Remove file from client view (POST with CSRF)
    #[Route('/client/projects/{id}/files/{fileId}/remove-view', name: 'client_project_file_remove_view', methods: ['POST'])]
    public function removeFileView(
        int $id,
        int $fileId,
        Request $request,
        ProjectRepository $projectRepository,
        ProjectFileRepository $fileRepository,
        EntityManagerInterface $entityManager,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Project not found.');
        }

        $client = $currentUserProvider->getClient();

        // Beyond the lesson: Check project ownership
        if ($project->getUser()?->getId() !== $client?->getId()) {
            $this->addFlash('error', 'You can only manage files from your own project workspace.');
            return $this->redirectToRoute('client_projects', [], Response::HTTP_SEE_OTHER);
        }

        // Beyond the lesson: None of these actions work on a cancelled project
        if ($project->isCancelled()) {
            $this->addFlash('error', 'File management is locked for cancelled sprint records.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $file = $fileRepository->find($fileId);
        if (!$file || $file->getProject()->getId() !== $project->getId()) {
            throw $this->createNotFoundException('File not found.');
        }

        // CSRF validation: isCsrfTokenValid('remove_file_' . $file->getId(), $request->getPayload()->getString('_token'))
        $submittedToken = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('remove_file_' . $file->getId(), $submittedToken)) {
            $this->addFlash('error', 'Invalid security token.');
            return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $file->setDeletedByClientAt(new \DateTimeImmutable());
        $entityManager->flush();

        // Beyond the lesson: User feedback via flash message
        $this->addFlash('success', sprintf('File "%s" has been removed from your workspace view.', $file->getName()));

        return $this->redirectToRoute('client_project_detail', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
