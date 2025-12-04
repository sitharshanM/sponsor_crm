<?php
// src/User.php
class User {
    private $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO users (username, email, password_hash, full_name)
                VALUES (:username, :email, :password_hash, :full_name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':full_name' => $data['full_name'] ?? null
        ]);
        return $this->pdo->lastInsertId();
    }

    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT id, username, email, full_name, created_on FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password_hash']);
    }

    public function updateLastLogin($id) {
        $stmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT id, username, email, full_name, created_on, last_login FROM users ORDER BY created_on DESC");
        return $stmt->fetchAll();
    }
}

