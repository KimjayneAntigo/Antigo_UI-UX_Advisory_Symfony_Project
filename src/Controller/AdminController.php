<?php

namespace App\Controller;

use App\Mock\MockProjects;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_overview')]
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

    #[Route('/admin/inquiries', name: 'admin_inquiries')]
    public function inquiries(): Response
    {
        return $this->render('admin/inquiries.html.twig', [
            'pageTitle' => 'Inquiry Leads Pipeline',
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'stats' => [
                'total' => 4,
                'new' => 1,
                'reviewed' => 1,
                'contacted' => 1,
                'converted' => 1
            ],
            'inquiries' => [
                [
                    'id' => 1,
                    'ref_code' => 'INQ-8821',
                    'name' => 'Ana Reyes',
                    'company' => 'Cebu Tourism Board',
                    'email' => 'ana@cebutourism.ph',
                    'service' => 'Web Design & Portal Development',
                    'budget' => '$45,000',
                    'status' => 'new',
                    'created_at' => 'Oct 2, 2026',
                    'message' => 'Looking for complete UX overhaul of regional tourism portal before Q1 peak.'
                ],
                [
                    'id' => 2,
                    'ref_code' => 'INQ-8819',
                    'name' => 'Carlos Gomez',
                    'company' => 'Fintech Vanguard',
                    'email' => 'carlos@fintechvanguard.com',
                    'service' => 'Mobile App UI/UX & Design System',
                    'budget' => '$80,000',
                    'status' => 'reviewed',
                    'created_at' => 'Sep 29, 2026',
                    'message' => 'Need full cross-platform design system tokens and high-fidelity prototype.'
                ],
                [
                    'id' => 3,
                    'ref_code' => 'INQ-8814',
                    'name' => 'Elena Rostova',
                    'company' => 'Nordic Logistics',
                    'email' => 'e.rostova@nordiclogistics.se',
                    'service' => 'Enterprise Dashboard UX Audit',
                    'budget' => '$35,000',
                    'status' => 'contacted',
                    'created_at' => 'Sep 25, 2026',
                    'message' => 'Consultation requested for optimizing dispatch dispatcher dashboard ergonomics.'
                ],
                [
                    'id' => 4,
                    'ref_code' => 'INQ-8802',
                    'name' => 'Maria Santos',
                    'company' => 'Pesolink Technologies',
                    'email' => 'maria@pesolink.ph',
                    'service' => 'Fintech Banking Redesign',
                    'budget' => '$120,000',
                    'status' => 'converted',
                    'created_at' => 'Sep 15, 2026',
                    'message' => 'Successfully converted to active project PRJ-3001.'
                ],
            ]
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

    #[Route('/admin/projects', name: 'admin_projects')]
    public function projects(): Response
    {
        return $this->render('admin/projects.html.twig', [
            'pageTitle' => 'Projects Portfolio',
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'stats' => [
                'all' => 3,
                'active' => 1,
                'review' => 0,
                'completed' => 1
            ],
            'projects' => [
                [
                    'id' => 1,
                    'code' => 'PRJ-3003',
                    'title' => 'Cebu Tourism Responsive Website',
                    'client' => 'Ana Reyes',
                    'company' => 'Cebu Tourism Board',
                    'service' => 'Web Design & Design Systems',
                    'stage' => 'UI/UX Design',
                    'progress' => 60,
                    'delivery_date' => 'Sep 25, 2026',
                    'budget' => '$45,000',
                    'status' => 'active',
                    'files_count' => 4,
                    'messages_count' => 3
                ],
                [
                    'id' => 2,
                    'code' => 'PRJ-3045',
                    'title' => 'Visayas Health Care – Design Systems',
                    'client' => 'Dr. Roberto Mendoza',
                    'company' => 'Visayas Health Care Network',
                    'service' => 'Design System Architecture',
                    'stage' => 'Delivered',
                    'progress' => 100,
                    'delivery_date' => 'Aug 01, 2026',
                    'budget' => '$95,000',
                    'status' => 'completed',
                    'files_count' => 0,
                    'messages_count' => 2
                ],
                [
                    'id' => 3,
                    'code' => 'PRJ-3001',
                    'title' => 'Pesolink Banking Redesign',
                    'client' => 'Maria Santos',
                    'company' => 'Pesolink Technologies',
                    'service' => 'Fintech Mobile & Web Redesign',
                    'stage' => 'Wireframing',
                    'progress' => 40,
                    'delivery_date' => 'Nov 15, 2026',
                    'budget' => '$120,000',
                    'status' => 'cancelled',
                    'files_count' => 2,
                    'messages_count' => 2
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

    #[Route('/admin/projects/{id}', name: 'admin_project_detail')]
    public function projectDetail(string $id): Response
    {
        $project = MockProjects::getById($id);

        if (!$project) {
            return $this->render('error/404.html.twig', [
                'pageTitle' => 'Project Not Found',
                'requestedId' => $id,
                'inquiriesPendingCount' => 1,
                'paymentsPendingCount' => 2,
            ], new Response('', Response::HTTP_NOT_FOUND));
        }

        return $this->render('admin/project_detail.html.twig', [
            'pageTitle' => $project['title'] . ' (' . $project['code'] . ') — Studio Admin',
            'inquiriesPendingCount' => 1,
            'paymentsPendingCount' => 2,
            'project' => $project,
        ]);
    }
}
