<?php
// src/Sponsor.php
class Sponsor {
    private $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO sponsors (company_name, contact_person, email, phone, industry, sponsor_type, status)
                VALUES (:company_name, :contact_person, :email, :phone, :industry, :sponsor_type, :status)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':company_name' => $data['company_name'] ?? '',
            ':contact_person' => $data['contact_person'] ?? null,
            ':email' => $data['email'] ?? null,
            ':phone' => $data['phone'] ?? null,
            ':industry' => $data['industry'] ?? null,
            ':sponsor_type' => $data['sponsor_type'] ?? null,
            ':status' => $data['status'] ?? 'new'
        ]);
        return $this->pdo->lastInsertId();
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM sponsors ORDER BY added_on DESC");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sponsors WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $sql = "UPDATE sponsors SET
                company_name = :company_name,
                contact_person = :contact_person,
                email = :email,
                phone = :phone,
                industry = :industry,
                sponsor_type = :sponsor_type,
                status = :status
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':company_name'=>$data['company_name'],
            ':contact_person'=>$data['contact_person'],
            ':email'=>$data['email'],
            ':phone'=>$data['phone'],
            ':industry'=>$data['industry'],
            ':sponsor_type'=>$data['sponsor_type'],
            ':status'=>$data['status'],
            ':id'=>$id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM sponsors WHERE id = :id");
        return $stmt->execute([':id'=>$id]);
    }

    public function createBatch($dataArray) {
        $sql = "INSERT INTO sponsors (company_name, contact_person, email, phone, industry, sponsor_type, status)
                VALUES (:company_name, :contact_person, :email, :phone, :industry, :sponsor_type, :status)";
        $stmt = $this->pdo->prepare($sql);
        
        $this->pdo->beginTransaction();
        $count = 0;
        $errors = [];
        
        foreach ($dataArray as $index => $data) {
            try {
                $stmt->execute([
                    ':company_name' => $data['company_name'] ?? '',
                    ':contact_person' => $data['contact_person'] ?? null,
                    ':email' => $data['email'] ?? null,
                    ':phone' => $data['phone'] ?? null,
                    ':industry' => $data['industry'] ?? null,
                    ':sponsor_type' => $data['sponsor_type'] ?? null,
                    ':status' => $data['status'] ?? 'new'
                ]);
                $count++;
            } catch (PDOException $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }
        
        $this->pdo->commit();
        return ['success' => $count, 'errors' => $errors];
    }
}
