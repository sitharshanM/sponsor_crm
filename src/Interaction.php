<?php
// src/Interaction.php
class Interaction {
    private $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO interactions (sponsor_id, interaction_type, notes, next_followup_date)
                VALUES (:sponsor_id, :interaction_type, :notes, :next_followup_date)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':sponsor_id'=>$data['sponsor_id'],
            ':interaction_type'=>$data['interaction_type'],
            ':notes'=>$data['notes'],
            ':next_followup_date'=>!empty($data['next_followup_date']) ? $data['next_followup_date'] : null
        ]);
        return $this->pdo->lastInsertId();
    }

    public function forSponsor($sponsor_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM interactions WHERE sponsor_id = :id ORDER BY created_on DESC");
        $stmt->execute([':id'=>$sponsor_id]);
        return $stmt->fetchAll();
    }
}
