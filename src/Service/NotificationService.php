<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Project;
use App\Entity\User;
use App\Enum\MessageRole;
use App\Enum\PaymentStatus;
use App\Enum\UserRole;
use App\Repository\InquiryRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProjectMessageRepository;
use App\Repository\ProjectRepository;

/**
 * Provides live layout notification metrics and feed items for admin and client views.
 * Registered as a Twig global 'notifications' so templates access counts directly.
 */
class NotificationService
{
    public function __construct(
        private readonly InquiryRepository $inquiryRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly ProjectMessageRepository $projectMessageRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly CurrentUserProvider $currentUserProvider,
    ) {
    }

    // ==========================================
    // ADMIN NOTIFICATIONS & BADGES
    // ==========================================

    public function getAdminNewInquiriesCount(): int
    {
        return $this->inquiryRepository->countNew();
    }

    public function getAdminPendingPaymentsCount(): int
    {
        return $this->paymentRepository->countPending();
    }

    public function getAdminUnreadMessagesCount(): int
    {
        $admin = $this->currentUserProvider->getAdmin();
        if (!$admin) {
            return 0;
        }

        return $this->projectMessageRepository->countUnreadForUser($admin);
    }

    public function getAdminTotalNotificationsCount(): int
    {
        return $this->getAdminNewInquiriesCount()
            + $this->getAdminPendingPaymentsCount()
            + $this->getAdminUnreadMessagesCount();
    }

    /**
     * Short item list for the admin topbar notification bell dropdown.
     *
     * @return list<array{title: string, subtitle: string, time: string, type: string, url: string, icon: string, color: string}>
     */
    public function getAdminNotifications(): array
    {
        $items = [];

        // 1. New inquiries (up to 3)
        $newInquiries = $this->inquiryRepository->findNew();
        foreach (array_slice($newInquiries, 0, 3) as $inquiry) {
            $company = $inquiry->getCompany() ? $inquiry->getCompany() . ' • ' : '';
            $items[] = [
                'title' => 'New Lead: ' . $inquiry->getName(),
                'subtitle' => $company . $inquiry->getService() . ' • ' . $inquiry->getBudget(),
                'time' => $inquiry->getCreatedAt()->format('M d'),
                'type' => 'inquiry',
                'url' => '/admin/inquiries',
                'icon' => 'lucide:inbox',
                'color' => 'amber',
            ];
        }

        // 2. Pending payments (up to 3)
        $pendingPayments = $this->paymentRepository->findBy(['status' => PaymentStatus::PENDING], ['submitted_at' => 'DESC'], 3);
        foreach ($pendingPayments as $payment) {
            $projTitle = $payment->getProject() ? $payment->getProject()->getTitle() : 'Direct Advisory';
            $items[] = [
                'title' => 'Payment: ' . $payment->getFormattedAmount(),
                'subtitle' => $projTitle . ' (' . $payment->getMethod()->label() . ')',
                'time' => $payment->getSubmittedAt()->format('M d'),
                'type' => 'payment',
                'url' => '/admin/payments',
                'icon' => 'lucide:receipt',
                'color' => 'emerald',
            ];
        }

        // 3. Unread client messages indicator
        $unreadMsgCount = $this->getAdminUnreadMessagesCount();
        if ($unreadMsgCount > 0) {
            $items[] = [
                'title' => 'Unread Client Messages (' . $unreadMsgCount . ')',
                'subtitle' => 'Clients have sent inquiries or responses to sprint threads.',
                'time' => 'Recent',
                'type' => 'message',
                'url' => '/admin/projects',
                'icon' => 'lucide:message-square',
                'color' => 'blue',
            ];
        }

        return $items;
    }

    // ==========================================
    // CLIENT NOTIFICATIONS & BADGES
    // ==========================================

    public function getClientUnreadMessagesCount(): int
    {
        $client = $this->currentUserProvider->getClient();
        if (!$client) {
            return 0;
        }

        return $this->projectMessageRepository->countUnreadForUser($client);
    }

    /**
     * @return list<Project>
     */
    public function getClientInvoiceReadyProjects(): array
    {
        $client = $this->currentUserProvider->getClient();
        if (!$client) {
            return [];
        }

        return $this->projectRepository->findAwaitingPaymentForClient($client);
    }

    public function getClientInvoiceReadyCount(): int
    {
        return count($this->getClientInvoiceReadyProjects());
    }

    /**
     * @return list<Payment>
     */
    public function getClientRecentlyReviewedPayments(): array
    {
        $client = $this->currentUserProvider->getClient();
        if (!$client) {
            return [];
        }

        return $this->paymentRepository->findRecentlyReviewedForUser($client, 14);
    }

    public function getClientTotalNotificationsCount(): int
    {
        return $this->getClientUnreadMessagesCount()
            + $this->getClientInvoiceReadyCount()
            + count($this->getClientRecentlyReviewedPayments());
    }

    /**
     * Short item list for the client topbar notification bell dropdown.
     *
     * @return list<array{title: string, subtitle: string, time: string, type: string, url: string, icon: string, color: string, badge: ?string}>
     */
    public function getClientNotifications(): array
    {
        $items = [];

        // 1. Invoice Ready Projects
        foreach ($this->getClientInvoiceReadyProjects() as $project) {
            $items[] = [
                'title' => 'Invoice Ready',
                'subtitle' => 'Your project "' . $project->getTitle() . '" is completed — click to review.',
                'badge' => $project->getProjectCode(),
                'time' => $project->getInvoiceSentAt() ? $project->getInvoiceSentAt()->format('M d') : 'Recent',
                'type' => 'invoice',
                'url' => '/client/projects/' . $project->getId(),
                'icon' => 'lucide:receipt',
                'color' => 'amber',
            ];
        }

        // 2. Unread designer messages
        $unreadMsg = $this->getClientUnreadMessagesCount();
        if ($unreadMsg > 0) {
            $items[] = [
                'title' => 'Message from Kimberly (' . $unreadMsg . ' unread)',
                'subtitle' => 'New feedback or deliverable note posted to your sprint.',
                'badge' => null,
                'time' => 'Recent',
                'type' => 'message',
                'url' => '/client/messages',
                'icon' => 'lucide:message-square',
                'color' => 'purple',
            ];
        }

        // 3. Recently reviewed payments
        foreach ($this->getClientRecentlyReviewedPayments() as $payment) {
            $statusLabel = $payment->getStatus()->label();
            $projTitle = $payment->getProject() ? $payment->getProject()->getTitle() : 'Advisory Project';
            $items[] = [
                'title' => 'Payment ' . $statusLabel,
                'subtitle' => $payment->getFormattedAmount() . ' for ' . $projTitle . ' marked ' . strtolower($statusLabel) . '.',
                'badge' => $payment->getReference(),
                'time' => $payment->getReviewedAt() ? $payment->getReviewedAt()->format('M d') : 'Recent',
                'type' => 'payment',
                'url' => $payment->getProject() ? '/client/projects/' . $payment->getProject()->getId() : '/client/dashboard',
                'icon' => 'lucide:check-circle',
                'color' => 'emerald',
            ];
        }

        return $items;
    }
}
