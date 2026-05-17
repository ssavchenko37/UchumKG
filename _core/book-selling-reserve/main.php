<?php
require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$payments = $DB->select('SELECT r.*
	, b.title, b.author, b.price
	, br.name AS branch_name, br.branch_id
	, p.payment_id, p.amount, p.comment
	, (ST.qty_total - ST.qty_sold - ST.qty_defect) AS available
	FROM ?_bk_payments p
	JOIN ?_bk_reservations r ON r.reservation_id = p.reservation_id
	JOIN ?_bk_book_stock ST ON ST.book_id = r.book_id AND ST.branch_id = r.branch_id
	JOIN ?_bk_books b ON b.book_id = r.book_id
	JOIN ?_bk_branches br ON br.branch_id = r.branch_id
	WHERE r.status=? AND p.status IN(?a) AND where_go=?
	ORDER BY r.created_at'
	, 'active', ['confirmed','partial'], 'hand'
);
// p($payments);
$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches');