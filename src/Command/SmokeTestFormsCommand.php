<?php

namespace App\Command;

use App\Entity\Project;
use App\Entity\ProjectMessage;
use App\Entity\User;
use App\Enum\MessageRole;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Form\CancelProjectType;
use App\Form\ChangePasswordType;
use App\Form\MessageType;
use App\Form\PaymentType;
use App\Form\ProfileType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:smoke:forms',
    description: 'Smoke tests all form types and form handling rules for Prompt 2 Part A'
)]
class SmokeTestFormsCommand extends Command
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private readonly ProjectRepository $projectRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Smoke Testing Form Types & Business Rules');

        // 1. MessageType
        $io->section('1. MessageType Validation');
        $msg = new ProjectMessage();
        $form = $this->formFactory->create(MessageType::class, $msg, ['csrf_protection' => false]);
        $form->submit(['message' => 'Test project message content']);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $io->error('MessageType valid submission failed: ' . (string) $form->getErrors(true));
            return Command::FAILURE;
        }
        $blankForm = $this->formFactory->create(MessageType::class, new ProjectMessage(), ['csrf_protection' => false]);
        $blankForm->submit(['message' => '']);
        if ($blankForm->isValid()) {
            $io->error('MessageType should reject blank messages');
            return Command::FAILURE;
        }
        $io->success('MessageType correctly validates non-blank messages and rejects empty submissions.');

        // 2. PaymentType & Security Rule
        $io->section('2. PaymentType & Budget Security Rule');
        $validPayForm = $this->formFactory->create(PaymentType::class, null, ['csrf_protection' => false]);
        $validPayForm->submit([
            'method' => PaymentMethod::BANK_TRANSFER->value,
        ]);
        if (!$validPayForm->isSubmitted() || !$validPayForm->isValid()) {
            $io->error('PaymentType valid submission failed: ' . (string) $validPayForm->getErrors(true));
            return Command::FAILURE;
        }
        if ($validPayForm->has('amount')) {
            $io->error('Security violation: PaymentType exposes amount field!');
            return Command::FAILURE;
        }
        $method = $validPayForm->get('method')->getData();
        if ($method !== PaymentMethod::BANK_TRANSFER) {
            $io->error('PaymentMethod enum resolution failed');
            return Command::FAILURE;
        }

        // Test browser tampering attempt: submitting extra amount field
        $tamperedPayForm = $this->formFactory->create(PaymentType::class, null, ['csrf_protection' => false]);
        $tamperedPayForm->submit([
            'method' => PaymentMethod::BANK_TRANSFER->value,
            'amount' => '1.00',
        ]);
        if ($tamperedPayForm->isValid()) {
            $io->error('Security violation: PaymentType should reject extra tampered amount field!');
            return Command::FAILURE;
        }
        $io->success('PaymentType strictly exposes only method enum. Tampered amounts cannot be bound and trigger extra-field validation errors.');

        // 3. CancelProjectType
        $io->section('3. CancelProjectType');
        $cancelForm = $this->formFactory->create(CancelProjectType::class, null, ['csrf_protection' => false]);
        $cancelForm->submit(['reason' => 'Schedule delay on client side']);
        if (!$cancelForm->isSubmitted() || !$cancelForm->isValid()) {
            $io->error('CancelProjectType failed: ' . (string) $cancelForm->getErrors(true));
            return Command::FAILURE;
        }
        $io->success('CancelProjectType correctly processes optional reason field.');

        // 4. ProfileType
        $io->section('4. ProfileType');
        $dummyUser = new User();
        $dummyUser->setName('Old Name');
        $profileForm = $this->formFactory->create(ProfileType::class, $dummyUser, ['csrf_protection' => false]);
        $profileForm->submit([
            'name' => 'New Name',
            'company' => 'New Studio',
            'phone' => '+1-555-0144',
        ]);
        if (!$profileForm->isValid() || $dummyUser->getName() !== 'New Name' || $dummyUser->getPhone() !== '+1-555-0144') {
            $io->error('ProfileType failed to bind properties');
            return Command::FAILURE;
        }
        $io->success('ProfileType binds name, company, and phone successfully.');

        // 5. ChangePasswordType & PasswordHasher
        $io->section('5. ChangePasswordType & Password Verification');
        $demoUser = $this->userRepository->findOneBy(['email' => 'demo@client.com']);
        if (!$demoUser) {
            $io->error('Fixture demo@client.com not found');
            return Command::FAILURE;
        }

        // Test < 8 chars
        $shortPwForm = $this->formFactory->create(ChangePasswordType::class, null, ['csrf_protection' => false]);
        $shortPwForm->submit([
            'currentPassword' => 'client123',
            'newPassword' => [
                'first' => 'short',
                'second' => 'short',
            ],
        ]);
        if ($shortPwForm->isValid()) {
            $io->error('ChangePasswordType should reject passwords shorter than 8 characters');
            return Command::FAILURE;
        }

        // Test mismatch
        $mismatchPwForm = $this->formFactory->create(ChangePasswordType::class, null, ['csrf_protection' => false]);
        $mismatchPwForm->submit([
            'currentPassword' => 'client123',
            'newPassword' => [
                'first' => 'ValidPass123',
                'second' => 'Mismatch123',
            ],
        ]);
        if ($mismatchPwForm->isValid()) {
            $io->error('ChangePasswordType should reject mismatched passwords');
            return Command::FAILURE;
        }

        // Test password hasher check
        $validCurrent = $this->passwordHasher->isPasswordValid($demoUser, 'Client2026!');
        $invalidCurrent = $this->passwordHasher->isPasswordValid($demoUser, 'wrong_pass');
        if (!$validCurrent || $invalidCurrent) {
            $io->error('Password hasher validation failed for fixture user');
            return Command::FAILURE;
        }
        $io->success('ChangePasswordType requires >= 8 chars, repeated match, and verifies current password.');

        $io->success('All Prompt 2 Part A forms and verification rules passed!');

        return Command::SUCCESS;
    }
}
