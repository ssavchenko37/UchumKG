<?php
namespace Outsider\Book\Repositories;

use PDO;

class DefectRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(int $bookId, int $branchId, int $qty, int $tutorId, ?string $comment = null): int 
    {

        $this->db->prepare("
            INSERT INTO tl_bk_defects
            (
                book_id,
                branch_id,
                qty,
                tutor_id,
                comment
            )
            VALUES (?, ?, ?, ?, ?)
        ")->execute([
            $bookId,
            $branchId,
            $qty,
            $tutorId,
            $comment
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $defectId, int $qty, string $comment): void
    {
        $this->db->prepare("
            UPDATE tl_bk_defects
            SET qty = ?, comment = ?
            WHERE defect_id = ?
        ")->execute([$qty, $comment, $defectId]);
    }

    public function markReturned(int $defectId): void
    {
        $this->db->prepare("
            UPDATE tl_bk_defects
            SET status = 'returned'
            WHERE defect_id = ?
        ")->execute([$defectId]);
    }

    public function getForUpdate(int $id): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM tl_bk_defects
            WHERE defect_id = ?
            FOR UPDATE
        ");

        $stmt->execute([$id]);

        $row = $stmt->fetch();

        if (!$row) {
            throw new \Exception("Defect not found");
        }

        return $row;
    }
}