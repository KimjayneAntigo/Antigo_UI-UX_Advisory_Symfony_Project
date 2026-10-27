<?php

namespace App\Controller\Admin;

use App\Entity\Inquiry;
use App\Enum\InquiryStatus;
use App\Form\InquiryType;
use App\Repository\InquiryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/inquiries')]
final class InquiryController extends AbstractController
{
    #[Route('', name: 'admin_inquiries', methods: ['GET'])]
    public function index(InquiryRepository $inquiryRepository, Request $request): Response
    {
        $q = trim($request->query->getString('q'));
        $statusParam = $request->query->getString('status');
        $statusEnum = $statusParam !== '' ? InquiryStatus::tryFrom($statusParam) : null;

        if ($q !== '' || $statusEnum !== null) {
            $inquiries = $inquiryRepository->search($q !== '' ? $q : null, $statusEnum);
        } else {
            $inquiries = $inquiryRepository->findBy([], ['created_at' => 'DESC']);
        }

        // Global stats across all inquiries for dashboard tabs
        $allInquiries = $inquiryRepository->findAll();
        $stats = [
            'total' => count($allInquiries),
            'new' => 0,
            'reviewed' => 0,
            'contacted' => 0,
            'converted' => 0,
        ];

        foreach ($allInquiries as $inq) {
            match ($inq->getStatus()) {
                InquiryStatus::NEW => $stats['new']++,
                InquiryStatus::REVIEWED => $stats['reviewed']++,
                InquiryStatus::CONTACTED => $stats['contacted']++,
                InquiryStatus::CONVERTED => $stats['converted']++,
                default => null,
            };
        }

        return $this->render('admin/inquiry/index.html.twig', [
            'pageTitle' => 'Inquiry Leads Pipeline',
            'inquiries' => $inquiries,
            'stats' => $stats,
            'currentQuery' => $q,
            'currentStatus' => $statusParam,
        ]);
    }

    #[Route('/new', name: 'admin_inquiry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inquiry = new Inquiry();
        $form = $this->createForm(InquiryType::class, $inquiry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inquiry);
            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', sprintf('Inquiry lead %s was successfully created.', $inquiry->getReference()));

            return $this->redirectToRoute('admin_inquiries', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/inquiry/new.html.twig', [
            'pageTitle' => 'New Inquiry Lead',
            'inquiry' => $inquiry,
            'form' => $form,
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
        ]);
    }

    #[Route('/{id}', name: 'admin_inquiry_show', methods: ['GET'])]
    public function show(Inquiry $inquiry): Response
    {
        return $this->render('admin/inquiry/show.html.twig', [
            'pageTitle' => $inquiry->getReference() . ' — ' . $inquiry->getName(),
            'inquiry' => $inquiry,
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_inquiry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inquiry $inquiry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InquiryType::class, $inquiry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', sprintf('Inquiry lead %s was successfully updated.', $inquiry->getReference()));

            return $this->redirectToRoute('admin_inquiries', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/inquiry/edit.html.twig', [
            'pageTitle' => 'Edit ' . $inquiry->getReference(),
            'inquiry' => $inquiry,
            'form' => $form,
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
        ]);
    }

    #[Route('/{id}', name: 'admin_inquiry_delete', methods: ['POST'])]
    public function delete(Request $request, Inquiry $inquiry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inquiry->getId(), $request->getPayload()->getString('_token'))) {
            $ref = $inquiry->getReference();
            $entityManager->remove($inquiry);
            $entityManager->flush();

            // Beyond the lesson: User feedback via flash message
            $this->addFlash('success', sprintf('Inquiry lead %s was successfully deleted.', $ref));
        }

        return $this->redirectToRoute('admin_inquiries', [], Response::HTTP_SEE_OTHER);
    }
}
