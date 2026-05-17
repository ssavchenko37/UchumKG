<?php
namespace Outsider\Book\Repositories;

use PDO;

class ReservationRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

   public function getForUpdate(int $id): array {
        $stmt=$this->db->prepare("SELECT * FROM tl_bk_reservations WHERE reservation_id=? FOR UPDATE");
        $stmt->execute([$id]);
        $row=$stmt->fetch();
        if(!$row) throw new \Exception("Reservation not found");
        return $row;
    }

    public function create(int $b,int $br,int $t,int $q,string $p,string $where,string $deliveryTo, string $forCourier): int {
        $this->db->prepare("INSERT INTO tl_bk_reservations (book_id,branch_id,admin_id,qty,phone,where_go,delivery_to,for_courier,expires_at) VALUES (?,?,?,?,?,?,?,?,NOW()+INTERVAL 6 DAY)")
            ->execute([$b,$br,$t,$q,$p,$where,$deliveryTo,$forCourier]);
        return (int)$this->db->lastInsertId();
    }

    public function updateBranch(int $id, int $br): void {
        $this->db->prepare("UPDATE tl_bk_reservations SET branch_id = ? WHERE reservation_id=?")->execute([$br,$id]);
    }

    public function update(int $id, int $diff, string $phone, string $where, string $deliveryTo, string $forCourier): void {
        $this->db->prepare("UPDATE tl_bk_reservations SET qty = qty + ?, phone=?, where_go=?, delivery_to=?, for_courier=? WHERE reservation_id=?")
            ->execute([$diff,$phone,$where,$deliveryTo,$forCourier,$id]);
    }

    public function complete(int $id): void {
        $this->db->prepare("UPDATE tl_bk_reservations SET status='completed' WHERE reservation_id=?")->execute([$id]);
    }

    public function cancel(int $id): void {
        $this->db->prepare("UPDATE tl_bk_reservations SET status='cancelled' WHERE reservation_id=?")->execute([$id]);
    }
}
