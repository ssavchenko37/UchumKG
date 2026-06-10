<?php
namespace Outsider\Book\Services;

use Outsider\Book\Core\Database;
use Outsider\Book\Repositories\BookStockRepository;
use Outsider\Book\Repositories\ReservationRepository;
use Outsider\Book\Repositories\PaymentRepository;
use Outsider\Book\Repositories\PaymentPartRepository;
use Outsider\Book\Repositories\OrderRepository;
use Outsider\Book\Repositories\DefectRepository;
use Outsider\Book\Repositories\LogRepository;
use PDO;

class BookService
{
	private PDO $db;
	private BookStockRepository $stock;
	private ReservationRepository $res;
	private PaymentRepository $payments;
	private PaymentPartRepository $paymentParts;
	private OrderRepository $orders;
	private DefectRepository $defects;
	private LogRepository $logs;

	public function __construct()
	{
		$this->db = Database::get();
		$this->stock = new BookStockRepository($this->db);
		$this->res = new ReservationRepository($this->db);
		$this->payments = new PaymentRepository($this->db);
		$this->paymentParts = new PaymentPartRepository($this->db);
		$this->orders = new OrderRepository($this->db);
		$this->defects = new DefectRepository($this->db);
		$this->logs = new LogRepository($this->db);
	}

	public function begin(): void
	{
		if (!$this->db->inTransaction()) {
			$this->db->beginTransaction();
		}
	}

	public function commit(): void
	{
		if ($this->db->inTransaction()) {
			$this->db->commit();
		}
	}

	public function rollback(): void
	{
		if ($this->db->inTransaction()) {
			$this->db->rollBack();
		}
	}

	// private function available(array $s){return $s['qty_total']-$s['qty_reserved']-$s['qty_sold']-$s['qty_defect'];}
	private function available(array $s){return $s['qty_total']-$s['qty_paid']-$s['qty_sold']-$s['qty_defect'];}
	private function availableR(array $s){return $s['qty_total']-$s['qty_reserved']-$s['qty_paid']-$s['qty_sold']-$s['qty_defect'];}

	public function adjustStock(int $bookId, int $branchId, int $qtyDiff, int $tutorId, string $reason = 'manual'): void
	{
		// if ($branchId !== 10 && $qtyDiff === 0) {
		// 	return;
		// }

		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			if ($reason !== 'receive') {
				$stock = $this->stock->getForUpdate($bookId, $branchId);
				$newTotal = $stock['qty_total'] + $qtyDiff;
				if ($newTotal < ($stock['qty_reserved'] + $stock['qty_sold'])) {
					// throw new \Exception("Cannot reduce below reserved/sold");
					$reason = $reason . " с дефицитом";
				}
			}

			$this->stock->changeTotal($bookId, $branchId, $qtyDiff);

			$this->logs->log(
				'book_stock',
				$bookId,
				'adjust',
				$tutorId,
				[
					'branch' => $branchId,
					'diff' => $qtyDiff,
					'reason' => $reason
				]
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function receive(int $bookId, int $branchId, int $qty, int $tutorId): void
	{
		if ($qty < 0) {
			throw new \Exception("Invalid qty");
		}
		$this->adjustStock($bookId, $branchId, $qty, $tutorId, 'receive');
	}

	public function reserve(int $b, int $br, int $t, int $q, string $phone, string $where, ?string $deliveryTo = null, ?string $forCourier = null)
	{
		$ownTransaction = !$this->db->inTransaction();

		if ($ownTransaction) {
			$this->db->beginTransaction();
		}
		try{
			$s=$this->stock->getForUpdate($b,$br);
			//if($this->available($s)<$q) throw new \Exception("no stock");
			$this->stock->increaseReserved($b,$br,$q);
			$id=$this->res->create($b,$br,$t,$q,$phone,$where,$deliveryTo,$forCourier);
			$this->logs->log('reservation',$id,'create',$t,['qty'=>$q]);
			if ($ownTransaction) {
				$this->db->commit();
			}
			return $id;
		}catch(\Throwable $e){
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function updateReservation(int $reservationId, int $diff, string $phone, int $t, int $br, string $where, ?string $deliveryTo = null, ?string $forCourier = null): void
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			$res = $this->res->getForUpdate($reservationId);

			if ($res['status'] !== 'active') {
				throw new \Exception("Reservation not active");
			}

			if ($res['branch_id'] != $br) {
				$this->stock->decreasePaid($res['book_id'], $res['branch_id'], $res['qty']);
				$this->stock->increasePaid($res['book_id'], $br, $res['qty']);
				$this->res->updateBranch($reservationId,$br);
				$res['branch_id'] = $br;
			}

			// меням доступные $diff - может быть отрицательным
			$this->stock->increasePaid(
				$res['book_id'],
				$res['branch_id'],
				$diff
			);

			$this->res->update($reservationId,$diff,$phone,$where,$deliveryTo,$forCourier);
			
			$this->logs->log(
				'reservation',
				$reservationId,
				'update',
				$t,
				['qty'=>$diff,'phone'=>$phone]
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function cancelReservation(int $reservationId, int $tutorId): void
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			$res = $this->res->getForUpdate($reservationId);

			if ($res['status'] !== 'active') {
				throw new \Exception("Reservation not active");
			}

			// возвращаем в доступные
			$this->stock->decreasePaid(
				$res['book_id'],
				$res['branch_id'],
				$res['qty']
			);

			$this->res->cancel($reservationId);

			$this->logs->log(
				'reservation',
				$reservationId,
				'cancel',
				$tutorId
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function transfer(int $b, int $from, int $to, int $q, int $t)
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}
		try{
			$s1=$this->stock->getForUpdate($b,$from);
			$this->stock->getForUpdate($b,$to);
			if($this->availableR($s1)<$q) throw new \Exception("no stock");
			$this->stock->decreaseTotal($b,$from,$q);
			$this->stock->increaseTotal($b,$to,$q);
			$this->logs->log('transfer',$b,'move',$t,['from'=>$from,'to'=>$to,'qty'=>$q]);
			if ($ownTransaction) {
				$this->db->commit();
			}
		}catch(\Throwable $e){
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function defect(int $bookId, int $branchId, int $qty, int $tutorId, ?string $comment = null): int
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {

			$stock = $this->stock->getForUpdate($bookId, $branchId);

			$available = $stock['qty_total'] - $stock['qty_paid'] - $stock['qty_sold'];

			if ($available < $qty) {
				throw new \Exception("Not enough stock");
			}

			// списываем
			$this->stock->decreaseTotal(
				$bookId,
				$branchId,
				$qty
			);

			// создаём дефект
			$defectId = $this->defects->create($bookId, $branchId, $qty, $tutorId, $comment);

			$this->logs->log(
				'defect',
				$defectId,
				'create',
				$tutorId,
				[
					'book_id' => $bookId,
					'branch_id' => $branchId,
					'qty' => $qty
				]
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

			return $defectId;

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function updateDefect (int $defectId, int $tutorId, int $qty, ?string $comment = null, ?int $returned = 0): void
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {

			$defect = $this->defects->getForUpdate($defectId);

			if ($defect['status'] !== 'defective') {
				throw new \Exception("Already processed");
			}

			$qty_diff = $defect['qty'] - $qty;
			if ($qty_diff > 0 ) {
				$this->stock->increaseTotal(
					$defect['book_id'],
					$defect['branch_id'],
					$qty_diff
				);
			} else {
				$this->stock->decreaseTotal(
					$defect['book_id'],
					$defect['branch_id'],
					$qty_diff
				);
			}

			$this->defects->update($defectId, $qty, $comment);

			if ($returned > 0) {
				$this->defects->markReturned($defectId);
			}

			$this->logs->log(
				'defect',
				$defectId,
				'update defect',
				$tutorId,
				[
					'book_id' => $defect['book_id'],
					'branch_id' => $defect['branch_id'],
					'qty' => $qty,
					'oldqty' => $defect['qty']
				]
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function sell(int $bookId, int $branchId, float $total_amount, int $tutorId, int $qty, ?int $paymentId = null): int
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}
		
		try {
			$stock = $this->stock->getForUpdate($bookId, $branchId);

			$available = $stock['qty_total'] - $stock['qty_paid'] - $stock['qty_sold'] - $stock['qty_defect'];

			if ($available < $qty) {
				throw new \Exception("Not enough stock");
			}

			$this->stock->increaseSold($bookId, $branchId, $qty);

			$payment = null;
			if ($paymentId) {
				$payment = $this->validateForUse($paymentId);
			}

			$orderId = $this->orders->create($branchId, $tutorId, $total_amount, null, $paymentId);
			$this->orders->addItem($orderId, $bookId, $qty, $total_amount);

			if ($paymentId) {
				$this->markUsed($paymentId);
			}

			$this->logs->log(
				'order',
				$orderId,
				'sell',
				$tutorId,
				['book_id' => $bookId, 'qty' => $qty]
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

			return $orderId;

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function sellFromReservation(int $reservationId, int $tutorId, float $total_amount, ?string $delivery_to = null, ?int $paymentId = null): int
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			$res = $this->res->getForUpdate($reservationId);

			if ($res['status'] !== 'active') {
				throw new \Exception("Invalid reservation");
			}

			// блокируем склад
			$this->stock->getForUpdate(
				$res['book_id'],
				$res['branch_id']
			);

			// переводим paid -> sold
			$this->stock->decreasePaid(
				$res['book_id'],
				$res['branch_id'],
				$res['qty']
			);

			$this->stock->increaseSold(
				$res['book_id'],
				$res['branch_id'],
				$res['qty']
			);

			$payment = null;
			if ($paymentId) {
				$payment = $this->validateForUse($paymentId);
			}

			$orderId = $this->orders->create(
				$res['branch_id'],
				$tutorId,
				$total_amount,
				$delivery_to,
				$paymentId
			);

			$this->orders->addItem(
				$orderId,
				$res['book_id'],
				$res['qty'],
				$total_amount
			);

			if ($paymentId) {
				$this->markUsed($paymentId);
			}

			$this->res->complete($reservationId);

			$this->logs->log(
				'order',
				$orderId,
				'sell_from_reservation',
				$tutorId
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

			return $orderId;

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function createPayment(int $reservationId, ?float $amount, ?float $paid_amount, string $method, int $tutorId, ?string $phone = null, ?string $comment = null): int
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			if ($reservationId > 0) {
				$res = $this->res->getForUpdate($reservationId);

				if ($res['status'] !== 'active') {
					throw new \Exception("Invalid reservation");
				}

				// переводим reserved -> paid
				$this->stock->decreaseReserved(
					$res['book_id'],
					$res['branch_id'],
					$res['qty']
				);

				$this->stock->increasePaid(
					$res['book_id'],
					$res['branch_id'],
					$res['qty']
				);
			}
			
			$paymentId = $this->payments->create($reservationId, $tutorId, $amount, $paid_amount, $method, $phone, $comment);

			$this->logs->log('payment', $paymentId, 'create', $tutorId, [
				'amount' => $amount,
				'method' => $method
			]);

			if ($ownTransaction) {
				$this->db->commit();
			}

			return $paymentId;

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function updatePayment(int $paymentId, float $surcharge, int $tutorId, ?string $comment = null): void
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			$payment = $this->payments->getForUpdate($paymentId);

			if (!in_array($payment['status'],['new','confirmed'])) {
				throw new \Exception("Invalid payment status");
			}

			$this->payments->update($paymentId, $surcharge, $comment);

			$this->logs->log('payment', $paymentId, 'surcharge', $tutorId);

			if ($ownTransaction) {
				$this->db->commit();
			}

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function confirmPayment(int $paymentId, int $tutorId): void
	{
		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			$payment = $this->payments->getForUpdate($paymentId);

			if ($payment['status'] !== 'new') {
				throw new \Exception("Invalid payment status");
			}

			$this->payments->confirm($paymentId);

			$this->logs->log('payment', $paymentId, 'confirm', $tutorId);

			if ($ownTransaction) {
				$this->db->commit();
			}

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function validateForUse(int $paymentId): array
	{
		$payment = $this->payments->getForUpdate($paymentId);

		if ($payment['status'] !== 'confirmed') {
			throw new \Exception("Payment not confirmed");
		}

		return $payment;
	}

	public function markUsed(int $paymentId): void
	{
		$this->payments->markUsed($paymentId);
	}

	public function createPartialPayment(int $reservationId, int $tutorId, float $expectedAmount, string $method, ?string $phone = null, ?string $comment = null): int 
	{

		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			if ($reservationId > 0) {
				$res = $this->res->getForUpdate($reservationId);

				if ($res['status'] !== 'active') {
					throw new \Exception("Invalid reservation");
				}

				// переводим reserved -> paid
				$this->stock->decreaseReserved(
					$res['book_id'],
					$res['branch_id'],
					$res['qty']
				);

				$this->stock->increasePaid(
					$res['book_id'],
					$res['branch_id'],
					$res['qty']
				);
			}

			$stmt = $this->db->prepare("
				INSERT INTO tl_bk_payments(reservation_id, tutor_id, amount, expected_amount, paid_amount, method, phone, comment, status)
				VALUES(?, ?, 0, ?, 0, ?, ?, ?, 'new')
			");

			$stmt->execute([$reservationId,$tutorId,$expectedAmount,$method,$phone,$comment]);

			$paymentId = (int)$this->db->lastInsertId();

			$this->logs->log(
				'payment',
				$paymentId,
				'create_partial'
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

			return $paymentId;

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function addPaymentPart(int $paymentId, int $tutorId, float $amount, string $method, ?string $comment = null): array 
	{

		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {

			$payment = $this->payments->getForUpdate($paymentId);

			if (!in_array($payment['status'], ['new', 'partial'])) {
				throw new \Exception('Payment closed');
			}

			$newPaid = $payment['paid_amount'] + $amount;

			$overpayment = 0;
			if (
				$payment['expected_amount']
				&& $newPaid > $payment['expected_amount']
			) {
				$overpayment = $newPaid - $payment['expected_amount'];
			}

			$partId = $this->paymentParts->create(
				$paymentId,
				$amount,
				$method,
				$comment,
				[
					'tutorId'=> $tutorId
				]
			);

			$this->recalculatePayment($paymentId);

			$this->logs->log(
				'payment',
				$paymentId,
				'add_part',
				null,
				[
					'part_id' => $partId,
					'amount' => $amount
				]
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

			return [
				'part_id' => $partId,
				'overpayment' => $overpayment
			];

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}

	public function updateExpectedAmount(int $paymentId, float $expectedAmount)
	{
		$payment = $this->payments->getForUpdate($paymentId);

		$status = 'partial';

		if ($payment['expected_amount'] > 0) {
			if ($payment['paid_amount'] >= $expectedAmount) {
				$status = 'confirmed';
			}

			$this->db->prepare("
				UPDATE tl_bk_payments
				SET expected_amount = ?, status = ?
				WHERE payment_id = ? AND expected_amount IS NOT NULL
			")->execute([$expectedAmount, $status, $paymentId]);
		}
	}

	public function recalculatePayment(int $paymentId): void
	{
		$payment = $this->payments->getForUpdate($paymentId);

		$partsTotal = $this->paymentParts->getTotal($paymentId);
		$paid = (float)$payment['amount'] + $partsTotal;

		$status = 'partial';

		if ($paid <= 0) {
			$status = 'new';
		}

		if ($payment['expected_amount'] > 0 && $paid >= $payment['expected_amount']) {
			$status = 'confirmed';
		}

		$this->payments->updatePaidAmount(
			$paymentId,
			$paid,
			$status
		);
	}

	public function convertToPartialPayment(int $paymentId, float $expectedAmount): void 
	{
		$this->db->prepare("UPDATE tl_bk_payments
			SET expected_amount = ?, paid_amount = 0, status = 'partial'
			WHERE payment_id = ?
		")->execute([$expectedAmount,$paymentId]);
	}

	public function convertSingleToPartial(int $paymentId, float $newExpectedAmount): void
	{
		$payment = $this->payments->getForUpdate($paymentId);

		if ($payment['expected_amount'] !== null) {
			return; // уже partial
		}

		$this->db->prepare("UPDATE tl_bk_payments
			SET expected_amount = ?, paid_amount = amount, status = 'partial'
			WHERE payment_id = ?
		")->execute([$newExpectedAmount,$paymentId]);
	}

	public function getPayments(string $status = 'active'): array
	{

		$stmt = $this->db->prepare("
			SELECT 
				r.*,

				DATE_FORMAT(
					r.created_at,
					'%d.%m.%y %H:%i'
				) AS created,

				b.title,
				b.author,
				b.price,

				br.name AS branch_name,
				br.branch_id,

				p.payment_id,
				p.status AS payment_status,

				p.method,

				p.amount,
				p.expected_amount,
				p.paid_amount,

				p.comment,

				CASE
					WHEN p.expected_amount IS NULL
					THEN 'single'
					ELSE 'partial'
				END AS payment_type,

				IF(
					p.expected_amount IS NULL,
					p.amount,
					p.paid_amount
				) AS paid_sum,

				CASE
					WHEN p.expected_amount IS NOT NULL
					THEN GREATEST(
						p.expected_amount - p.paid_amount,
						0
					)
					ELSE 0
				END AS remain_amount,

				(
					ST.qty_total
					- ST.qty_sold
					- ST.qty_defect
				) AS available,

				CASE
					WHEN p.expected_amount IS NOT NULL
					THEN (
						SELECT pp.comment
						FROM tl_bk_payment_parts pp
						WHERE pp.payment_id = p.payment_id
						ORDER BY pp.part_id DESC
						LIMIT 1
					)
					ELSE p.comment
				END AS payment_comment

			FROM tl_bk_reservations r

			JOIN tl_bk_books b
				ON b.book_id = r.book_id

			JOIN tl_bk_branches br
				ON br.branch_id = r.branch_id

			JOIN tl_bk_book_stock ST
				ON ST.book_id = r.book_id
				AND ST.branch_id = r.branch_id

			LEFT JOIN tl_bk_payments p
				ON r.reservation_id = p.reservation_id

			WHERE r.status = ?

			GROUP BY r.reservation_id

			ORDER BY r.created_at DESC
		");

		$stmt->execute([$status]);

		return $stmt->fetchAll();
	}

	public function getPayment(int $reservationId): array
	{
		// =====================================
		// ОСНОВНАЯ ЗАЯВКА
		// =====================================

		$stmt = $this->db->prepare("
			SELECT 
				r.*,

				DATE_FORMAT(
					r.created_at,
					'%d.%m.%y %H:%i'
				) AS created,

				b.title,
				b.author,
				b.price,

				br.name AS branch_name,
				br.branch_id,

				p.payment_id,
				p.status AS payment_status,

				p.method,

				p.amount,
				p.expected_amount,
				p.paid_amount,

				p.comment,

				CASE
					WHEN p.expected_amount IS NULL
					THEN 'single'
					ELSE 'partial'
				END AS payment_type,

				CASE
					WHEN p.expected_amount IS NOT NULL
					THEN GREATEST(
						p.expected_amount - p.paid_amount,
						0
					)
					ELSE 0
				END AS remain_amount,

				(
					ST.qty_total
					- ST.qty_sold
					- ST.qty_defect
				) AS available

			FROM tl_bk_reservations r

			JOIN tl_bk_books b
				ON b.book_id = r.book_id

			JOIN tl_bk_branches br
				ON br.branch_id = r.branch_id

			JOIN tl_bk_book_stock ST
				ON ST.book_id = r.book_id
				AND ST.branch_id = r.branch_id

			LEFT JOIN tl_bk_payments p
				ON r.reservation_id = p.reservation_id

			WHERE r.reservation_id = ?

			LIMIT 1
		");

		$stmt->execute([$reservationId]);

		$payment = $stmt->fetch();

		if (!$payment) {
			throw new \Exception('Payment not found');
		}

		// =====================================
		// ЧАСТИЧНЫЕ ПЛАТЕЖИ
		// =====================================

		$history = [];

		if (!empty($payment['payment_id']) && $payment['payment_type'] === 'partial') {

			$stmt = $this->db->prepare("
				SELECT
					part_id,
					amount,
					method,
					comment,

					DATE_FORMAT(
						created_at,
						'%d.%m.%y %H:%i'
					) AS created

				FROM tl_bk_payment_parts

				WHERE payment_id = ?

				ORDER BY part_id ASC
			");

			$stmt->execute([
				$payment['payment_id']
			]);

			$parts = $stmt->fetchAll();

			$payment['history'] = [];

			if ($payment['amount'] > 0 && $payment['expected_amount'] !== null) {
				$payment['history'][] = [
					'created' => $payment['created'],
					'amount' => $payment['amount'],
					'comment' => $payment['comment'],
					'type' => 'initial'
				];
			}

			foreach ($parts as $part) {
				$payment['history'][] = [
					'created' => $part['created'],
					'amount' => $part['amount'],
					'comment' => $part['comment'],
					'type' => 'part'
				];
			}
		}

		return $payment;
	}

	public function rollbackSale(int $orderId, int $tutorId): void 
	{

		$ownTransaction = !$this->db->inTransaction();
		if ($ownTransaction) {
			$this->db->beginTransaction();
		}

		try {
			// ====ORDER=================================
			$stmt = $this->db->prepare("SELECT * FROM tl_bk_orders WHERE order_id = ? LIMIT 1");
			$stmt->execute([$orderId]);
			$order = $stmt->fetch();
			if (!$order) { throw new \Exception('Order not found'); }

			// ====ORDER ITEM=================================
			$stmt = $this->db->prepare("SELECT * FROM tl_bk_order_items WHERE order_id = ? LIMIT 1");
			$stmt->execute([$orderId]);
			$item = $stmt->fetch();
			if (!$item) { throw new \Exception('Order item not found'); }

			// ====PAYMENT=================================
			$stmt = $this->db->prepare("SELECT * FROM tl_bk_payments WHERE payment_id = ? LIMIT 1");
			$stmt->execute([$order['payment_id']]);
			$payment = $stmt->fetch();

			// ====RESERVATION=================================
			$stmt = $this->db->prepare("SELECT * FROM tl_bk_reservations WHERE reservation_id = ? LIMIT 1");
			$stmt->execute([$payment['reservation_id']]);
			$reservation = $stmt->fetch();
			if (!$reservation) {
				throw new \Exception('Reservation not found');
			}

			// ====STOCK=================================
			$stock = $this->stock->getForUpdate($reservation['book_id'], $reservation['branch_id']);

			// ====SOLD=================================
			$this->db->prepare("
				UPDATE tl_bk_book_stock
				SET
					qty_sold = qty_sold - ?,
					qty_paid = qty_paid + ?
				WHERE
					book_id = ?
					AND branch_id = ?
			")->execute([
				$item['qty'],
				$item['qty'],
				$reservation['book_id'],
				$reservation['branch_id']
			]);

			// ====RESERVATION -> active=================================
			$this->db->prepare("UPDATE tl_bk_reservations SET status = 'active' WHERE reservation_id = ?")
				->execute([$reservation['reservation_id']]);

			// ====PAYMENT -> confirmed=================================
			$this->db->prepare("UPDATE tl_bk_payments SET status = 'confirmed' WHERE payment_id = ?")
				->execute([$payment['payment_id']]);

			// ====DELETE ORDER ITEMS=================================
			$this->db->prepare("DELETE FROM tl_bk_order_items WHERE order_id = ?")
				->execute([$orderId]);

			// ====DELETE ORDER=================================
			$this->db->prepare("DELETE FROM tl_bk_orders WHERE order_id = ?")
				->execute([$orderId]);

			// ====LOG=================================
			$this->logs->log(
				'order',
				$orderId,
				'rollback_sale',
				$tutorId
			);

			if ($ownTransaction) {
				$this->db->commit();
			}

		} catch (\Throwable $e) {
			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}
			throw $e;
		}
	}
}
