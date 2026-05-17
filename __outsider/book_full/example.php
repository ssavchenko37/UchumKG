<?php
require __DIR__ . '/bootstrap.php';

use Outsider\Book\Services\BookService;

$s=new BookService();
$id=$s->reserve(1,1,10,2);
$s->transfer(1,1,2,1,10);

echo "done";
