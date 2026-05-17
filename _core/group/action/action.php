<?php
if ($_POST['mode'] == "cancel") {
	$upd['meta_cancelled'] = $_POST['meta_cancelled']; 
	$upd['meta_cancelled'] = $_POST['meta_note'];
	$DB->query('UPDATE ?_ibook_meta SET ?a WHERE meta_id=?', $upd, $_POST['meta_id']);
}
