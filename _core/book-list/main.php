<?php
if (($_POST['mode'] ?? '')) {
    include "action/action.php";
}

$books = $DB->select('SELECT * FROM ?_bk_books ORDER BY created_at DESC');