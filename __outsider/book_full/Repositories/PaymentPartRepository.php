<?php
namespace Outsider\Book\Repositories;

use PDO;

class PaymentPartRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // создать часть платежа
    public function create(int $paymentId, float $amount, string $method, ?string $comment = null): int 
    {

        $this->db->prepare("
            INSERT INTO tl_bk_payment_parts
            (
                payment_id,
                amount,
                method,
                comment
            )
            VALUES (?, ?, ?, ?)
        ")->execute([
            $paymentId,
            $amount,
            $method,
            $comment
        ]);

        return (int)$this->db->lastInsertId();
    }

    // сумма частей
    public function getTotal(int $paymentId): float
    {
        $stmt = $this->db->prepare("
            SELECT IFNULL(SUM(amount),0)
            FROM tl_bk_payment_parts
            WHERE payment_id = ?
        ");

        $stmt->execute([$paymentId]);

        return (float)$stmt->fetchColumn();
    }
}