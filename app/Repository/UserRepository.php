<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Repository;

use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;

class UserRepository
{

    private \PDO $connection;

    public function __construct(\PDO $conn)
    {
        $this->connection = $conn;
    }

    public function save(User $user): User
    {
        $statment = $this->connection->prepare("INSERT INTO users(id,name,password) VALUES (?,?,?)");
        $statment->execute([
            $user->id,
            $user->name,
            $user->password,
        ]);
        return $user;
    }

    public function updateUser(User $user): User
    {
        $stmt = $this->connection->prepare('UPDATE users SET name = ?, password = ? WHERE id = ?');
        $stmt->execute([$user->name, $user->password, $user->id]);
        return $user;
    }

    public function findById(string $id): ?User
    {
        $stmt = $this->connection->prepare("SELECT id, name, password FROM users WHERE id = ?");
        $stmt->execute([$id]);

        try {
            if ($row = $stmt->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->password = $row['password'];
                return $user;
            } else {
                return null;
            }
        } finally {
            $stmt->closeCursor();
        }
    }


    public function deleteAll(): void
    {
        $this->connection->exec('DELETE from users');
    }
}
