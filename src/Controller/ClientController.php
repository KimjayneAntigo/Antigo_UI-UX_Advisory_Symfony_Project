<?php

namespace App\Controller;

use App\Mock\MockProjects;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'client_dashboard')]
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

    #[Route('/client/settings', name: 'client_settings')]
    public function settings(): Response
    {
        return $this->render('client/settings.html.twig', [
            'pageTitle' => 'Settings',
            'unreadMessagesCount' => 2,
            'user' => [
                'name' => 'Maria Santos',
                'email' => 'maria@pesolink.ph',
                'company' => 'Pesolink Technologies Inc.',
                'phone' => '+63 917 000 1234',
                'timezone' => 'Asia/Manila (GMT+8)'
            ]
        ]);
    }

    #[Route('/client/projects/{id}', name: 'client_project_detail')]
    public function projectDetail(string $id, Request $request): Response
    {
        $project = MockProjects::getById($id);

        if (!$project) {
            return $this->render('error/404.html.twig', [
                'pageTitle' => 'Project Not Found — Client Portal',
                'requestedId' => $id,
                'unreadMessagesCount' => 2,
            ], new Response('', Response::HTTP_NOT_FOUND));
        }

        // Allow previewing the 4 payment states via ?payment_state= if specified
        if ($paymentStateOverride = $request->query->get('payment_state')) {
            $project['paymentState'] = $paymentStateOverride;
        }

        return $this->render('client/project_detail.html.twig', [
            'pageTitle' => $project['title'] . ' — Project Workspace',
            'unreadMessagesCount' => 2,
            'project' => $project,
        ]);
    }
}
