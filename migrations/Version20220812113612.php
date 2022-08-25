<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220812113612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ligne (id INT AUTO_INCREMENT NOT NULL, liste_id INT NOT NULL, user_taken_id INT DEFAULT NULL, position INT NOT NULL, contenu VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, lien VARCHAR(255) DEFAULT NULL, INDEX IDX_57F0DB83E85441D8 (liste_id), INDEX IDX_57F0DB83F16C78E4 (user_taken_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liste (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_FCF22AF4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partage (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partage_user (partage_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8DCBD4F7D5CB766D (partage_id), INDEX IDX_8DCBD4F7A76ED395 (user_id), PRIMARY KEY(partage_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partage_liste (partage_id INT NOT NULL, liste_id INT NOT NULL, INDEX IDX_D02BEC7DD5CB766D (partage_id), INDEX IDX_D02BEC7DE85441D8 (liste_id), PRIMARY KEY(partage_id, liste_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB83E85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id)');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB83F16C78E4 FOREIGN KEY (user_taken_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE liste ADD CONSTRAINT FK_FCF22AF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE partage_user ADD CONSTRAINT FK_8DCBD4F7D5CB766D FOREIGN KEY (partage_id) REFERENCES partage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partage_user ADD CONSTRAINT FK_8DCBD4F7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partage_liste ADD CONSTRAINT FK_D02BEC7DD5CB766D FOREIGN KEY (partage_id) REFERENCES partage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partage_liste ADD CONSTRAINT FK_D02BEC7DE85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB83E85441D8');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB83F16C78E4');
        $this->addSql('ALTER TABLE liste DROP FOREIGN KEY FK_FCF22AF4A76ED395');
        $this->addSql('ALTER TABLE partage_user DROP FOREIGN KEY FK_8DCBD4F7D5CB766D');
        $this->addSql('ALTER TABLE partage_user DROP FOREIGN KEY FK_8DCBD4F7A76ED395');
        $this->addSql('ALTER TABLE partage_liste DROP FOREIGN KEY FK_D02BEC7DD5CB766D');
        $this->addSql('ALTER TABLE partage_liste DROP FOREIGN KEY FK_D02BEC7DE85441D8');
        $this->addSql('DROP TABLE ligne');
        $this->addSql('DROP TABLE liste');
        $this->addSql('DROP TABLE partage');
        $this->addSql('DROP TABLE partage_user');
        $this->addSql('DROP TABLE partage_liste');
        $this->addSql('DROP TABLE user');
    }
}
