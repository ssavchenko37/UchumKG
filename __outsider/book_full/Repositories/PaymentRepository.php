<?php
namespace Outsider\Book\Repositories;

use PDO;

class PaymentRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getForUpdate(int $paymentId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM tl_bk_payments
            WHERE payment_id = ?
            FOR UPDATE
        ");
        $stmt->execute([$paymentId]);

        $row = $stmt->fetch();

        if (!$row) {
            throw new \Exception("Payment not found");
        }

        return $row;
    }

    public function create(
        int $reservationId,
        int $tutorId,
        ?float $amount,
        ?float $paid_amount,
        string $method,
        ?string $phone = null,
        ?string $comment = null
    ): int {
        $this->db->prepare("
            INSERT INTO tl_bk_payments (reservation_id, tutor_id, amount, expected_amount, paid_amount, method, phone, comment, status)
            VALUES (?, ?, ?, NULL, ?, ?, ?, ?, 'new')
        ")->execute([$reservationId, $tutorId, $amount, $paid_amount, $method, $phone, $comment]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $paymentId, float $surcharge, ?string $comment = null): void
    {
        $this->db->prepare("
            UPDATE tl_bk_payments
            SET amount = amount + ?, comment = ?
            WHERE payment_id = ?
        ")->execute([$surcharge, $comment, $paymentId]);
    }

    public function confirm(int $paymentId): void
    {
        $this->db->prepare("
            UPDATE tl_bk_payments
            SET status = 'confirmed'
            WHERE payment_id = ?
        ")->execute([$paymentId]);
    }

    public function markUsed(int $paymentId): void
    {
        $this->db->prepare("
            UPDATE tl_bk_payments
            SET status = 'used'
            WHERE payment_id = ?
        ")->execute([$paymentId]);
    }

    public function updatePaidAmount(int $paymentId, float $paidAmount, string $status): void 
    {
        $this->db->prepare("
            UPDATE tl_bk_payments
            SET
                paid_amount = ?,
                status = ?
            WHERE payment_id = ?
        ")->execute([
            $paidAmount,
            $status,
            $paymentId
        ]);
    }
}