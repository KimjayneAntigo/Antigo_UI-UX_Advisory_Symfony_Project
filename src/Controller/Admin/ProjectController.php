<?php

namespace App\Controller\Admin;

use App\Config\ProjectPhases;
use App\Entity\Project;
use App\Enum\ProjectPhase;
use App\Enum\ProjectStatus;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/projects')]
final class ProjectController extends AbstractController
{
    #[Route('', name: 'admin_projects', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findBy([], ['created_at' => 'DESC']);

        // Stats summary for the portfolio header
        $stats = [
            'all' => count($projects),
            'active' => 0,
            'review' => 0,
            'completed' => 0,
        ];

        $featuredProject = null;

        foreach ($projects as $p) {
            if ($p->isCompleted()) {
                $stats['completed']++;
            } elseif ($p->isCancelled()) {
                // Cancelled projects are not counted in active pipeline
            } else {
                $stats['active']++;
                if ($p->getCurrentPhase() === ProjectPhase::PROTOTYPING) {
                    $stats['review']++;
                }
                if ($featuredProject === null) {
                    $featuredProject = $p;
                }
            }
        }

        return $this->render('admin/project/index.html.twig', [
            'pageTitle' => 'Projects Portfolio & Sprints',
            'projects' => $projects,
            'featuredProject' => $featuredProject ?? ($projects[0] ?? null),
            'stats' => $stats,
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
        ]);
    }

    #[Route('/new', name: 'admin_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Beyond the lesson: Auto-generate a unique project code if not provided
            if (!$project->getProjectCode()) {
                $project->setProjectCode($projectRepository->generateNextProjectCode());
            }

            // Set initial status based on phase
            if ($project->getCurrentPhase() === ProjectPhase::DELIVERED) {
                $project->setStatusType(ProjectStatus::COMPLETED);
            } else {
                $project->setStatusType(ProjectStatus::IN_DESIGN);
            }

            $entityManager->persist($project);
            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', sprintf('Project %s (%s) was successfully created.', $project->getProjectCode(), $project->getTitle()));

            return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/project/new.html.twig', [
            'pageTitle' => 'Create New Project Sprint',
            'project' => $project,
            'form' => $form,
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
        ]);
    }

    #[Route('/{id}', name: 'admin_project_detail', methods: ['GET'])]
    #[Route('/{id}/show', name: 'admin_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        // Renders the rich Antigo project detail view with real Project entity
        return $this->render('admin/project/show.html.twig', [
            'pageTitle' => $project->getTitle() . ' (' . $project->getProjectCode() . ') — Studio Admin',
            'project' => $project,
            'project_phases' => ProjectPhases::getAll(),
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Keep status synchronized if marked Delivered
            if ($project->getCurrentPhase() === ProjectPhase::DELIVERED && !$project->isCancelled()) {
                $project->setStatusType(ProjectStatus::COMPLETED);
            }

            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', sprintf('Project %s was successfully updated.', $project->getProjectCode()));

            return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/project/edit.html.twig', [
            'pageTitle' => 'Edit ' . $project->getProjectCode() . ' — ' . $project->getTitle(),
            'project' => $project,
            'form' => $form,
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
        ]);
    }

    #[Route('/{id}', name: 'admin_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->getPayload()->getString('_token'))) {
            // Beyond the lesson: Business rule — projects can only be deleted if they are cancelled
            if (!$project->isCancelled()) {
                $this->addFlash('error', sprintf('Project %s cannot be deleted because it is not cancelled.', $project->getProjectCode()));
                return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
            }

            $code = $project->getProjectCode();
            $entityManager->remove($project);
            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', sprintf('Project %s was permanently deleted.', $code));
        }

        return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
    }

    // =========================================================================
    // Part C — Project actions (// Beyond the lesson:)
    // =========================================================================

    #[Route('/{id}/cancel', name: 'admin_project_cancel', methods: ['POST'])]
    public function cancel(
        Request $request,
        Project $project,
        EntityManagerInterface $entityManager,
        CurrentUserProvider $currentUserProvider
    ): Response {
        if ($this->isCsrfTokenValid('cancel' . $project->getId(), $request->getPayload()->getString('_token'))) {
            // Beyond the lesson: Refuse cancellation if already completed or cancelled
            if ($project->isCompleted() || $project->isCancelled()) {
                $this->addFlash('error', 'Cannot cancel a project that is already completed or cancelled.');
                return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
            }

            $reason = trim($request->getPayload()->getString('cancellation_reason'));
            if ($reason === '') {
                $reason = 'Client requested project freeze.';
            }

            $project->setStatusType(ProjectStatus::CANCELLED);
            $project->setCancelledAt(new \DateTimeImmutable());
            $project->setCancelledBy($currentUserProvider->getAdmin());
            $project->setCancellationReason($reason);

            $entityManager->flush();

            $this->addFlash('success', sprintf('Project %s has been cancelled.', $project->getProjectCode()));
        }

        return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/complete', name: 'admin_project_complete', methods: ['POST'])]
    public function complete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('complete' . $project->getId(), $request->getPayload()->getString('_token'))) {
            // Beyond the lesson: Refuse completion if cancelled
            if ($project->isCancelled()) {
                $this->addFlash('error', 'Cannot mark a cancelled project as complete.');
                return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
            }

            // Beyond the lesson: Mark completed and advance phase to Delivered
            $project->setStatusType(ProjectStatus::COMPLETED);
            $project->setCurrentPhase(ProjectPhase::DELIVERED);

            $entityManager->flush();

            $this->addFlash('success', sprintf('Project %s has been completed and marked as delivered.', $project->getProjectCode()));
        }

        return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/send-invoice', name: 'admin_project_send_invoice', methods: ['POST'])]
    public function sendInvoice(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('send-invoice' . $project->getId(), $request->getPayload()->getString('_token'))) {
            // Beyond the lesson: Invoices are allowed only if completed and invoiceSentAt is null
            if (!$project->isCompleted() || $project->getInvoiceSentAt() !== null) {
                $this->addFlash('error', 'Invoices can only be sent for completed projects that have not yet been invoiced.');
                return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
            }

            $project->setInvoiceSentAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', sprintf('Final invoice for project %s has been recorded as sent.', $project->getProjectCode()));
        }

        return $this->redirectToRoute('admin_project_detail', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
    }
}
