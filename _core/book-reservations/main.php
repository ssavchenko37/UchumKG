<?php
/** @var array $tldata */
require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$tutor_id = ($tldata['umod'] === 't') ? $tldata['id']: '';

// $reservations = $DB->select('SELECT r.*
// 	, DATE_FORMAT(r.created_at, \'%d.%m.%y %H:%i\') AS created
// 	, b.title, b.author, b.price
// 	, br.name AS branch_name, br.branch_id
// 	, p.payment_id, p.amount, p.comment
// 	, (ST.qty_total - ST.qty_sold - ST.qty_defect) AS available
// 	FROM ?_bk_reservations r
// 	JOIN ?_bk_books b ON b.book_id = r.book_id
// 	JOIN ?_bk_branches br ON br.branch_id = r.branch_id
// 	JOIN ?_bk_book_stock ST ON ST.book_id = r.book_id AND ST.branch_id = r.branch_id
// 	LEFT JOIN ?_bk_payments p ON r.reservation_id = p.reservation_id
// 	WHERE r.status=?
// 	ORDER BY r.created_at'
// 	, 'active'
// );
//p($reservations);
$reservations = $BS->getPayments();
// p($reservations[0]);
$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches');