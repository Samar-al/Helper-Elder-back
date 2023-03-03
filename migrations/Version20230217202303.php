<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230217202303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F39B52963');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F6B92BD7B');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE0AD8BCD');
        $this->addSql('DROP INDEX IDX_B6BD307F39B52963 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F6B92BD7B ON message');
        $this->addSql('DROP INDEX IDX_B6BD307FE0AD8BCD ON message');
        $this->addSql('ALTER TABLE message ADD user_sender_id INT NOT NULL, ADD user_recipient_id INT NOT NULL, ADD conversation_id INT NOT NULL, DROP user_sender_id_id, DROP user_recipient_id_id, DROP conversation_id_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF6C43E79 FOREIGN KEY (user_sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F69E3F37A FOREIGN KEY (user_recipient_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF6C43E79 ON message (user_sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F69E3F37A ON message (user_recipient_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D9D86650F');
        $this->addSql('DROP INDEX IDX_5A8A6C8D9D86650F ON post');
        $this->addSql('ALTER TABLE post CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6B27AD292');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6CB36958A');
        $this->addSql('DROP INDEX IDX_794381C6B27AD292 ON review');
        $this->addSql('DROP INDEX IDX_794381C6CB36958A ON review');
        $this->addSql('ALTER TABLE review ADD user_giver_id INT NOT NULL, ADD user_taker_id INT NOT NULL, DROP user_giver_id_id, DROP user_taker_id_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C69530F929 FOREIGN KEY (user_giver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6EBA390C3 FOREIGN KEY (user_taker_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_794381C69530F929 ON review (user_giver_id)');
        $this->addSql('CREATE INDEX IDX_794381C6EBA390C3 ON review (user_taker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF6C43E79');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F69E3F37A');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('DROP INDEX IDX_B6BD307FF6C43E79 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F69E3F37A ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396 ON message');
        $this->addSql('ALTER TABLE message ADD user_sender_id_id INT NOT NULL, ADD user_recipient_id_id INT NOT NULL, ADD conversation_id_id INT NOT NULL, DROP user_sender_id, DROP user_recipient_id, DROP conversation_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F39B52963 FOREIGN KEY (user_recipient_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6B92BD7B FOREIGN KEY (conversation_id_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE0AD8BCD FOREIGN KEY (user_sender_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F39B52963 ON message (user_recipient_id_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F6B92BD7B ON message (conversation_id_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FE0AD8BCD ON message (user_sender_id_id)');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C69530F929');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6EBA390C3');
        $this->addSql('DROP INDEX IDX_794381C69530F929 ON review');
        $this->addSql('DROP INDEX IDX_794381C6EBA390C3 ON review');
        $this->addSql('ALTER TABLE review ADD user_giver_id_id INT NOT NULL, ADD user_taker_id_id INT NOT NULL, DROP user_giver_id, DROP user_taker_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6B27AD292 FOREIGN KEY (user_giver_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6CB36958A FOREIGN KEY (user_taker_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_794381C6B27AD292 ON review (user_giver_id_id)');
        $this->addSql('CREATE INDEX IDX_794381C6CB36958A ON review (user_taker_id_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        $this->addSql('ALTER TABLE post CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D9D86650F ON post (user_id_id)');
    }
}
