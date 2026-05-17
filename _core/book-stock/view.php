<?php
/** @var int $branch_id */
/** @var int $book_id */
/** @var array $books */
/** @var array $defective */
/** @var bool $is_back */
/** @var bool $is_add */
/** @var bool $is_total */
/** @var int $id */
?>
<form action="/book-stock-list/" method="post" id="frm0" name="forMain">
	<input type="hidden" id="view_branch_id" name="view_branch_id" value="<?php echo $branch_id?>">
	<input type="hidden" id="view_book_id" name="view_book_id" value="<?php echo $book_id?>">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Остатки книг</h2>
		</div>
		<div class="col-md-4 text-end ctrlBtn">
			<?php if ($is_back) { ?>
			<a href="/book-stock/" class="btn btn-sm btn-secondary"><i class="fa-solid fa-angle-left"></i> Назад к списку</a>
			<?php } ?>
			<?php if ($is_add) {?>
			<button class="btn btn-sm btn-primary" type="button" data-mod="add" data-page="one"><i class="fa fa-plus" aria-hidden="true"></i> Добавить книгу</button>
			<?php } ?>
			<?php if ($is_total) { ?>
			<button class="btn btn-sm btn-primary" type="button" data-mod="" data-page="transfer"><i class="fa fa-plus" aria-hidden="true"></i> Трансфер книги</button>
			<?php } ?>

		</div>
	</div>
</form>

<table class="table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th>#</th>
		<th>Филиал</th>
		<th class="w-30">Название</th>
		<th class="text-center">Всего</th>
		<th class="text-center">Оплачено</th>
		<th class="text-center">Продано</th>
		<th class="text-center">Брак</th>
		<th class="text-center">Доступно</th>
		<th class="w-10">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$grand_total = $grand_reserved = $grand_paid = $grand_sold = $grand_defect = $grand_available = 0;
	$q = 1;
	foreach ($books as $r) {
		$tr_class = ($id == $r['stock_id']) ? "table-success": "";
		$available = ($r['available'] > 0) ? 'text-success': 'text-danger';
		$branch_request = $TL->request_encode('bbid', $r['branch_id'] . "|" . $book_id);
		$book_request = $TL->request_encode('bbid', $branch_id . "|" . $r['book_id']);
		if ($is_total) {
			$grand_total = $grand_total + $r['qty_total'];
			$grand_reserved = $grand_reserved + $r['qty_reserved'];
			$grand_paid = $grand_paid + $r['qty_paid'];
			$grand_sold = $grand_sold + $r['qty_sold'];
			$grand_defect = $grand_defect + $r['qty_defect'];
			$grand_available = $grand_available + $r['available'];
		}
		$defectives = $defective[$r['branch_id']][$r['book_id']];
		$defect = is_array($defectives) ? array_sum($defectives): 0;
		?>
		<tr class="rws <?php echo $tr_class?>">
			<td class="align-middle" title="<?php echo $r['stock_id']?>"><?php echo $q?></td>
			<td class="align-middle" title="<?php echo $r['branch_id']?>">
				<?php if (empty($branch_id)) { ?>
				<a href="/book-stock/?<?php echo $branch_request?>">
					<?php echo $r['branch_name']?>
				</a>
				<?php } else { ?>
				<?php echo $r['branch_name']?>
				<?php } ?>
			</td>	
			<td class="align-middle" title="<?php echo $r['book_id']?>">
				<?php if (empty($book_id)) { ?>
				<a href="/book-stock/?<?php echo $book_request?>">
					<?php echo $r['title']?><br><small><?php echo $r['author']?></small>
				</a>
				<?php } else { ?>
				<?php echo $r['title']?><br><small><?php echo $r['author']?></small>
				<?php } ?>
			</td>
			<td class="align-middle text-center"><?php echo $r['qty_total']?></td>
			<td class="align-middle text-center"><?php echo $r['qty_paid']?></td>
			<td class="align-middle text-center"><?php echo $r['qty_sold']?></td>
			<td class="align-middle text-center"><?php echo $defect?></td>
			<td class="align-middle text-center <?php echo $available?>"><strong><?php echo $r['available']?></strong></td>
			<td class="align-middle">
				<div class="text-end ctrlBtn" data-pid="<?php echo $r['stock_id']?>">
					<button class="btn btn-success btn-sm" type="button" data-mod="edit" data-page="one"><i class="fas fa-pencil-alt"></i></button>
					<button class="btn btn-danger btn-sm" type="button" data-mod="delete" data-page="delete"><i class="far fa-trash-alt"></i></button>
				</div>
			</td>
		</tr>
		<?php
		$q++;
	}
	if ($is_total) {
		?>
		<tr>
			<td colspan="3" class="text-end"> Итого:</td>
			<td class="text-center"><?php echo $grand_total?></td>
			<td class="text-center"><?php echo $grand_reserved?></td>
			<td class="text-center"><?php echo $grand_paid?></td>
			<td class="text-center"><?php echo $grand_sold?></td>
			<td class="text-center"><?php echo $grand_defect?></td>
			<td class="text-center"><?php echo $grand_available?></td>
			<td>&nbsp;</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>
