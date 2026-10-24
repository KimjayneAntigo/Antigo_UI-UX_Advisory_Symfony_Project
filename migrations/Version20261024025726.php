<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261024025726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bookings (id INT AUTO_INCREMENT NOT NULL, guest_name VARCHAR(120) NOT NULL, guest_email VARCHAR(180) NOT NULL, service VARCHAR(80) NOT NULL, duration_min SMALLINT NOT NULL, price NUMERIC(10, 2) NOT NULL, booking_date DATE NOT NULL, booking_time TIME NOT NULL, meeting_format VARCHAR(80) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, user_id INT DEFAULT NULL, inquiry_id INT DEFAULT NULL, INDEX idx_booking_date (booking_date), INDEX idx_booking_status (status), INDEX IDX_7A853C35A76ED395 (user_id), INDEX IDX_7A853C35A7AD6D71 (inquiry_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inquiries (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(30) DEFAULT NULL, company VARCHAR(120) DEFAULT NULL, service VARCHAR(80) NOT NULL, budget VARCHAR(80) NOT NULL, timeline VARCHAR(80) NOT NULL, description LONGTEXT NOT NULL, attached_file VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, user_id INT DEFAULT NULL, INDEX idx_inquiry_email (email), INDEX idx_inquiry_status (status), INDEX IDX_1CCE4D5A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payments (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_method VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, admin_notes LONGTEXT DEFAULT NULL, submitted_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, verified_at DATETIME DEFAULT NULL, project_id INT NOT NULL, user_id INT NOT NULL, INDEX idx_payment_status (status), INDEX IDX_65D29B32166D1F9C (project_id), INDEX IDX_65D29B32A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_files (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, size_bytes INT NOT NULL, file_path VARCHAR(500) DEFAULT NULL, uploaded_at DATETIME NOT NULL, deleted_by_client_at DATETIME DEFAULT NULL, project_id INT NOT NULL, uploaded_by_id INT DEFAULT NULL, INDEX IDX_156049C7166D1F9C (project_id), INDEX IDX_156049C7A2B28FE8 (uploaded_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_messages (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, project_id INT NOT NULL, sender_id INT DEFAULT NULL, INDEX IDX_E64C954D166D1F9C (project_id), INDEX IDX_E64C954DF624B39D (sender_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, project_code VARCHAR(20) NOT NULL, title VARCHAR(200) NOT NULL, category VARCHAR(100) NOT NULL, client_name VARCHAR(120) NOT NULL, client_email VARCHAR(180) DEFAULT NULL, company VARCHAR(120) DEFAULT NULL, budget NUMERIC(12, 2) NOT NULL, start_date DATE DEFAULT NULL, due_date DATE NOT NULL, current_phase INT NOT NULL, status_type VARCHAR(255) NOT NULL, cancelled_at DATETIME DEFAULT NULL, cancellation_reason LONGTEXT DEFAULT NULL, invoice_sent_at DATETIME DEFAULT NULL, internal_notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT DEFAULT NULL, cancelled_by_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5C93B3A44E84F880 (project_code), INDEX idx_project_client_email (client_email), INDEX idx_project_status_type (status_type), INDEX IDX_5C93B3A4A76ED395 (user_id), INDEX IDX_5C93B3A4187B2D12 (cancelled_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, password_hash VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, company VARCHAR(120) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35A7AD6D71 FOREIGN KEY (inquiry_id) REFERENCES inquiries (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE inquiries ADD CONSTRAINT FK_1CCE4D5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_files ADD CONSTRAINT FK_156049C7166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_files ADD CONSTRAINT FK_156049C7A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_messages ADD CONSTRAINT FK_E64C954D166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_messages ADD CONSTRAINT FK_E64C954DF624B39D FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4187B2D12 FOREIGN KEY (cancelled_by_id) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35A76ED395');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35A7AD6D71');
        $this->addSql('ALTER TABLE inquiries DROP FOREIGN KEY FK_1CCE4D5A76ED395');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B32166D1F9C');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B32A76ED395');
        $this->addSql('ALTER TABLE project_files DROP FOREIGN KEY FK_156049C7166D1F9C');
        $this->addSql('ALTER TABLE project_files DROP FOREIGN KEY FK_156049C7A2B28FE8');
        $this->addSql('ALTER TABLE project_messages DROP FOREIGN KEY FK_E64C954D166D1F9C');
        $this->addSql('ALTER TABLE project_messages DROP FOREIGN KEY FK_E64C954DF624B39D');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4A76ED395');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4187B2D12');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE inquiries');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE project_files');
        $this->addSql('DROP TABLE project_messages');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
