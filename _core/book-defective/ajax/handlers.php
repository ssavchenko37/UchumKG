<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re = [];

// if ($post['mode'] === "get-branches") {
// 	$branches = $DB->select('SELECT ST.*, (ST.qty_total - ST.qty_paid - ST.qty_sold - ST.qty_defect) AS available
// 		, BR.name AS branch_name
// 		FROM ?_bk_book_stock ST
// 		INNER JOIN ?_bk_branches BR ON ST.branch_id=BR.branch_id
// 		WHERE book_id=?
// 		ORDER BY BR.branch_id'
// 		, $post['bookID']
// 	);

// 	foreach ($branches as $r) {
// 		if ($r['available'] > 0) {
// 			$re[$r['branch_id']] = $r['branch_name'];
// 		}
// 	}
// }

if ($post['mode'] === "get-max") {
	$re['max'] = $DB->selectCell('SELECT (qty_total - qty_paid - qty_sold - qty_defect) AS available FROM ?_bk_book_stock WHERE branch_id=? AND book_id=?', $post['branchID'], $post['bookID']);
}

echo json_encode($re, JSON_UNESCAPED_UNICODE);
exit;