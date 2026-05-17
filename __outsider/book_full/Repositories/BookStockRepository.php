<?php
namespace Outsider\Book\Repositories;

use PDO;

class BookStockRepository
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function getForUpdate(int $bookId, int $branchId): array
	{
		$stmt = $this->db->prepare("
			SELECT * FROM tl_bk_book_stock
			WHERE book_id = ? AND branch_id = ?
			FOR UPDATE
		");
		$stmt->execute([$bookId, $branchId]);
		$row = $stmt->fetch();
		if (!$row) throw new \Exception("Stock not found");
		return $row;
	}

	public function changeTotal(int $bookId, int $branchId, int $qtyDiff): void
	{
		$stmt = $this->db->prepare("
			SELECT * FROM tl_bk_book_stock
			WHERE book_id = ? AND branch_id = ?
			FOR UPDATE
		");
		$stmt->execute([$bookId, $branchId]);

		$row = $stmt->fetch();

		if ($row) {
			$this->db->prepare("
				UPDATE tl_bk_book_stock
				SET qty_total = qty_total + ?
				WHERE book_id = ? AND branch_id = ?
			")->execute([$qtyDiff, $bookId, $branchId]);

		} else {
			if ($qtyDiff < 0) {
				throw new \Exception("Cannot decrease non-existing stock");
			}

			$this->db->prepare("
				INSERT INTO tl_bk_book_stock (book_id, branch_id, qty_total)
				VALUES (?, ?, ?)
			")->execute([$bookId, $branchId, $qtyDiff]);
		}
	}

	public function increaseReserved(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_reserved = qty_reserved + ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}

	public function decreaseReserved(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_reserved = qty_reserved - ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}

	public function increaseDefective(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_defect = qty_defect + ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}

	public function decreaseDefective(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_defect = qty_defect - ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}

	public function increasePaid(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_paid = qty_paid + ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}
	public function decreasePaid(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_paid = qty_paid - ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}

	public function increaseSold(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_sold = qty_sold + ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}

	public function increaseTotal(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_total = qty_total + ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}

	public function decreaseTotal(int $b, int $br, int $q): void {
		$this->db->prepare("UPDATE tl_bk_book_stock SET qty_total = qty_total - ? WHERE book_id=? AND branch_id=?")
			->execute([$q,$b,$br]);
	}
}
