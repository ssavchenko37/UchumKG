<?php
/** @var array $tlreq */

require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$bbid = explode('|',$TL->request_decode('bbid', $tlreq));

$branch_id = (empty($bbid[0])) ? 0: $bbid[0];
$book_id = (empty($bbid[1])) ? 0: $bbid[1];

$is_add = ($book_id < 1) ? true : false;
$is_total = ($book_id > 0) ? true : false;
$is_back = ($branch_id > 0 || $book_id > 0)? true : false;

if ($branch_id > 0 && $book_id > 0) {
	$is_back = true;
	$is_add = false;
	$is_total = false;
}

$books = $DB->select('SELECT ST.*, (ST.qty_total - ST.qty_reserved - ST.qty_paid - ST.qty_sold - ST.qty_defect) AS available
	, BK.*
	, BR.name AS branch_name, BR.branch_id
	FROM ?_bk_book_stock ST
	JOIN ?_bk_branches BR ON ST.branch_id=BR.branch_id
	JOIN ?_bk_books BK ON ST.book_id=BK.book_id
	WHERE 1=1 {AND ST.branch_id=?} {AND ST.book_id=?}
	ORDER BY BR.branch_id, BK.title'
	, ($branch_id > 0) ? $branch_id: DBSIMPLE_SKIP, ($book_id > 0) ? $book_id: DBSIMPLE_SKIP
);

$defective = [];
$tmp = $DB->select('SELECT * FROM ?_bk_defects WHERE status=?', 'defective');
foreach ($tmp as $d) {
	$defective[$d['branch_id']][$d['book_id']][] = $d['qty'];
}
