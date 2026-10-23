<?php

namespace App\Mock;

/**
 * Canonical repository of mocked projects across Admin and Client views.
 * Uses real database schema column names:
 * - title, status_type, current_phase, progress, invoice_sent_at, cancelled_at, cancellation_reason, budget, created_at.
 */
class MockProjects
{
    /**
     * @return array<int|string, array>
     */
    public static function getAll(): array
    {
        return [
            1 => [
                'id' => 1,
                'code' => 'PRJ-3003',
                'title' => 'Cebu Tourism Responsive Website',
                'status_type' => 'in_design', // schema enum: pending | in_design | completed | cancelled
                'status' => 'In Progress',
                'current_phase' => 3,
                'phase_name' => 'UI/UX Design',
                'progress' => 60,
                'service' => 'Web Design & Design Systems',
                'created_at' => '2026-06-10',
                'start_date' => 'Jun 10, 2026',
                'expected_delivery' => 'Sep 25, 2026',
                'budget' => '$45,000',
                'invoice_sent_at' => null,
                'cancelled_at' => null,
                'cancelled_by' => null,
                'cancellation_reason' => null,
                'is_cancelled' => false,
                'paymentState' => 'not_available', // 1: not_available | 2: complete_no_invoice | 3: invoice_sent | 4: pending/paid/rejected
                'payment_badge' => 'No invoice',
                'payment_badge_type' => 'neutral',
                'payment_ref' => null,
                'client_name' => 'Ana Reyes',
                'company' => 'Cebu Tourism Board',
                'client_email' => 'ana@cebutourism.ph',
                'client_role' => 'Director of Digital Media',
                'avatar_initials' => 'AR',
                'is_registered_user' => true,
                'files' => [
                    [
                        'id' => 1,
                        'name' => 'Cebu_Tourism_Figma_Design_Tokens_v2.json',
                        'size' => '340 KB',
                        'uploaded_by' => 'Kimberly Jayne Antigo',
                        'time_ago' => '2 hours ago',
                        'icon' => 'lucide:file-code-2',
                        'client_visible' => true,
                        'removed_notice' => null,
                    ],
                    [
                        'id' => 2,
                        'name' => 'Cebu_Tourism_HiFi_Prototypes_Rev2.fig',
                        'size' => '18.6 MB',
                        'uploaded_by' => 'Kimberly Jayne Antigo',
                        'time_ago' => 'Yesterday, 3:15 PM',
                        'icon' => 'lucide:figma',
                        'client_visible' => true,
                        'removed_notice' => null,
                    ],
                    [
                        'id' => 3,
                        'name' => 'Cebu_Tourism_Sitemap_and_Information_Arch.pdf',
                        'size' => '4.2 MB',
                        'uploaded_by' => 'Kimberly Jayne Antigo',
                        'time_ago' => 'Jul 18, 2026',
                        'icon' => 'lucide:file-text',
                        'client_visible' => true,
                        'removed_notice' => null,
                    ],
                    [
                        'id' => 4,
                        'name' => 'Deprecated_Draft_Wireframes_Initial.pdf',
                        'size' => '8.1 MB',
                        'uploaded_by' => 'Kimberly Jayne Antigo',
                        'time_ago' => 'Jun 12, 2026',
                        'icon' => 'lucide:file-text',
                        'client_visible' => false,
                        'removed_notice' => 'Removed from client view on Jul 15, 2026',
                    ],
                ],
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'Kimberly Jayne Antigo',
                        'role' => 'admin',
                        'message' => "Hi Ana! I've uploaded the high-fidelity responsive prototypes and color system tokens for the portal.",
                        'time' => 'Jul 24, 2026 &bull; 11:20 AM',
                    ],
                    [
                        'id' => 2,
                        'sender' => 'Ana Reyes',
                        'role' => 'client',
                        'message' => "Thanks Kimberly! The destination card grid and mobile navigation look wonderful. The board reviewed them today.",
                        'time' => 'Jul 25, 2026 &bull; 2:45 PM',
                    ],
                    [
                        'id' => 3,
                        'sender' => 'Kimberly Jayne Antigo',
                        'role' => 'admin',
                        'message' => "Glad you like them! We're on track for the interactive prototyping review next week.",
                        'time' => 'Jul 26, 2026 &bull; 9:15 AM',
                    ],
                ],
                'internal_notes' => "Cebu Tourism Board priority: Ensure WCAG 2.1 AA color contrast compliance for regional attraction portals.",
            ],
            2 => [
                'id' => 2,
                'code' => 'PRJ-3045',
                'title' => 'Visayas Health Care – Design Systems',
                'status_type' => 'completed',
                'status' => 'Delivered',
                'current_phase' => 5,
                'phase_name' => 'Delivered',
                'progress' => 100,
                'service' => 'Design System Architecture',
                'created_at' => '2026-05-15',
                'start_date' => 'May 15, 2026',
                'expected_delivery' => 'Aug 01, 2026',
                'budget' => '$95,000',
                'invoice_sent_at' => '2026-08-01 10:00:00',
                'cancelled_at' => null,
                'cancelled_by' => null,
                'cancellation_reason' => null,
                'is_cancelled' => false,
                'paymentState' => 'pending', // Pending verification
                'payment_badge' => 'Pending verification: PAY-00011',
                'payment_badge_type' => 'amber',
                'payment_ref' => 'PAY-00011',
                'payment_amount' => '$95,000.00',
                'payment_method' => 'Bank Wire (USD)',
                'payment_submitted_at' => 'Aug 02, 2026',
                'client_name' => 'Dr. Roberto Mendoza',
                'company' => 'Visayas Health Care Network',
                'client_email' => 'r.mendoza@visayashealth.org',
                'client_role' => 'Chief Technology Officer',
                'avatar_initials' => 'RM',
                'is_registered_user' => true,
                'files' => [], // Empty state as specified
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'Kimberly Jayne Antigo',
                        'role' => 'admin',
                        'message' => "All final design system specifications, token schemas, and documentation handovers have been delivered.",
                        'time' => 'Aug 01, 2026 &bull; 10:00 AM',
                    ],
                    [
                        'id' => 2,
                        'sender' => 'Dr. Roberto Mendoza',
                        'role' => 'client',
                        'message' => "Payment reference PAY-00011 has been submitted via bank wire. Awaiting studio verification.",
                        'time' => 'Aug 02, 2026 &bull; 3:30 PM',
                    ],
                ],
                'internal_notes' => "Handover sprint complete. Client submitted wire confirmation for PAY-00011 ($95,000). Pending studio bank statement match.",
            ],
            3 => [
                'id' => 3,
                'code' => 'PRJ-3001',
                'title' => 'Pesolink Banking Redesign',
                'status_type' => 'cancelled',
                'status' => 'Cancelled',
                'current_phase' => 2,
                'phase_name' => 'Wireframing',
                'progress' => 40,
                'service' => 'Fintech Mobile & Web Redesign',
                'created_at' => '2026-06-14',
                'start_date' => 'Jun 14, 2026',
                'expected_delivery' => 'Nov 15, 2026',
                'budget' => '$120,000',
                'invoice_sent_at' => null,
                'cancelled_at' => '2026-08-14 09:30:00',
                'cancelled_date_formatted' => 'Aug 14, 2026',
                'cancelled_by' => 'Kimberly Jayne Antigo',
                'cancellation_reason' => 'Client requested project freeze due to internal corporate restructuring.',
                'is_cancelled' => true,
                'paymentState' => 'cancelled',
                'payment_badge' => 'No invoice',
                'payment_badge_type' => 'neutral',
                'payment_ref' => null,
                'client_name' => 'Maria Santos',
                'company' => 'Pesolink Technologies',
                'client_email' => 'maria@pesolink.ph',
                'client_role' => 'Product Lead',
                'avatar_initials' => 'MS',
                'is_registered_user' => true,
                'files' => [
                    [
                        'id' => 10,
                        'name' => 'Pesolink_Banking_Wireframes_Sprint1.pdf',
                        'size' => '12.4 MB',
                        'uploaded_by' => 'Kimberly Jayne Antigo',
                        'time_ago' => 'Jul 05, 2026',
                        'icon' => 'lucide:file-text',
                        'client_visible' => true,
                        'removed_notice' => null,
                    ],
                    [
                        'id' => 11,
                        'name' => 'User_Research_Interview_Syntheses.pdf',
                        'size' => '3.8 MB',
                        'uploaded_by' => 'Kimberly Jayne Antigo',
                        'time_ago' => 'Jun 28, 2026',
                        'icon' => 'lucide:file-text',
                        'client_visible' => true,
                        'removed_notice' => null,
                    ],
                ],
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'Maria Santos',
                        'role' => 'client',
                        'message' => "Hi Kimberly, we need to pause our sprint due to leadership changes and department budget freezes.",
                        'time' => 'Aug 13, 2026 &bull; 4:15 PM',
                    ],
                    [
                        'id' => 2,
                        'sender' => 'Kimberly Jayne Antigo',
                        'role' => 'admin',
                        'message' => "Understood Maria. All sprint wireframes and research artifacts have been archived safely.",
                        'time' => 'Aug 14, 2026 &bull; 9:30 AM',
                    ],
                ],
                'internal_notes' => "Cancelled on Aug 14, 2026. Mutual release signed. Sprint deposit retained under contract terms.",
            ],
        ];
    }

    /**
     * @param string|int $id
     * @return array|null
     */
    public static function getById(string|int $id): ?array
    {
        $all = self::getAll();
        
        foreach ($all as $key => $project) {
            if ((string)$key === (string)$id || $project['code'] === strtoupper((string)$id)) {
                return $project;
            }
        }

        return null;
    }
}
