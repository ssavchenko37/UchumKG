<?php
namespace Outsider\Book\Repositories;

use PDO;

class OrderRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(int $branchId, int $tutorId, float $total_amount, ?string $delivery_to = null, ?int $paymentId = null): int {
        $this->db->prepare("INSERT INTO tl_bk_orders (branch_id, tutor_id, status, total_amount, delivery_to, payment_id) VALUES (?, ?, 'paid', ?, ?, ?)")
            ->execute([$branchId, $tutorId, $total_amount, $delivery_to, $paymentId]);
        return (int)$this->db->lastInsertId();
    }

    public function addItem(int $o,int $b,int $q,float $p=0): void {
        $this->db->prepare("INSERT INTO tl_bk_order_items (order_id,book_id,qty,price) VALUES (?,?,?,?)")
            ->execute([$o,$b,$q,$p]);
    }
}
