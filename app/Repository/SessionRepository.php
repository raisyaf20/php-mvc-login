<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Repository;

use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;

class SessionRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conection)
    {
        $this->conn = $conection;
    }

    public function save(Session $session): Session
    {
        $stmt = $this->conn->prepare("INSERT INTO sessions (id,user_id) VALUES (?, ?)");

        $stmt->execute([$session->id, $session->user_id]);
        return $session;
    }
    public function findById(string $id): ?Session
    {
        $stmt = $this->conn->prepare("SELECT id, user_id FROM sessions WHERE id = ?");
        $stmt->execute([$id]);

        try {
            if ($row = $stmt->fetch()) {
                $session = new Session;
                $session->id = $row['id'];
                $session->user_id = $row['user_id'];
                return $session;
            } else {
                return null;
            }
        } finally {
            $stmt->closeCursor();
        }
    }
    public function deleteById(string $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM sessions WHERE id = ?');
        $stmt->execute([$id]);
    }
    public function deleteAll(): void
    {
        $this->conn->exec('DELETE FROM sessions');
    }
}
