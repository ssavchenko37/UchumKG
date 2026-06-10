<?php
/** @var array $payments */
/** @var int $id */
/** @var array $branches */
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="frm0" name="forMain">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Оплаченные заказы</h2>
		</div>
	</div>

	<div class="row py-1">
		<div class="col-12 col-md-3">
			<input type="text" class="form-control form-control-sm" id="by_phone" name="by_phone" placeholder="поиск по телефону">
		</div>
		<div class="col-12 col-md-3">
			<select class="form-select form-select-sm" id="by_branch" name="by_branch">
				<option value="0">-- по филиалам</option>
				<?php echo getOptionsK('', $branches)?>
			</select>
		</div>
		<div class="col-12 col-md-3">
			<select class="form-select form-select-sm" id="by_available" name="by_available">
				<?php echo getOptionsK('', ['-- по количеству','Кол-во недостаточно','Достаточное кол-во '])?>
			</select>
		</div>
	</div>
</form>

<table class="table books-table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th>#</th>
		<th>Филиал</th>
		<th>Название</th>
		<th>Телефон</th>
		<th>Коммент.</th>
		<th nowrap>Кол-во</th>
		<th class="text-center">Сумма</th>
		<th class="text-center">Платеж</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($payments as $r) {
		$tr_class = ($id == $r['payment_id']) ? "table-success": "";
		$shortage = $r['qty'] - $r['available'];
		$data_shortage = ($shortage > 0) ? 1: 2;

		$not_enough = "default";
		$fullPrice = $r['qty']*$r['price'];
		$amount_color = 'text-success';
		if ($r['paid_sum'] < $fullPrice) {
			$not_enough = "not-enough";
			$amount_color = 'text-danger';
		}
		$paid_date = (empty($r['part_comment'])) ?$r['comment']: $r['part_comment'];
		?>
		<tr class="rws <?php echo $tr_class?> <?php echo $not_enough?>" <?php if ($data_shortage == 1) { echo "style='opacity: 0.5'"; }?>
			data-phone="<?php echo $r['phone']?>"
			data-address="";
			data-branch="<?php echo $r['branch_id']?>"
			data-shortage="<?php echo $data_shortage?>">
			<td class="align-middle" data-label="#"><?php echo $q?></td>
			<td class="align-middle" data-label="Филиал"><?php echo $r['branch_name']?></td>
			<td class="align-middle" data-label="Название"><?php echo $r['title']?><br><small><?php echo $r['author']?></small></td>
			<td class="align-middle" data-label="Телефон"><?php echo $r['phone']?></td>
			<td class="align-middle" data-label="Коммент."><small><?php echo $r['for_courier']?></small>
			<td class="align-middle" data-label="Кол-во"><?php echo $r['qty']?>
				<strong class="text-danger"><?php if ($shortage > 0) { echo " / -" . $shortage; } ?></strong>
			</td>
			<td class="align-middle text-center" data-label="Сумма">
				<strong class="text-success"><?php echo number_format($fullPrice, 2);?></strong>
			</td>
			<td class="align-middle text-center" data-label="Платеж">
				<strong class="<?php echo $amount_color?>"><?php echo $r['paid_sum'];?></strong>
				<small class="surcharge">доплата: <?php echo $r['remain_amount']?></small>
				<small style="white-space: nowrap;"><?php echo $paid_date?></small>
			</td>
			<td class="align-middle" data-label="">
				<?php
				if ($data_shortage == 2) {
					?>
					<div class="text-end ctrlBtn" data-pid="<?php echo $r['payment_id']?>">
						<button class="btn btn-success btn-sm" type="button" data-mod="" data-page="send-book"><i class="fa-solid fa-clipboard-check"></i></button>
					</div>
					<?php
				}
				?>
			</td>

		</tr>
		<?php
		$q++;
	}
	?>
	</tbody>
</table>

<div class="modal fade" id="datesMatchModal" tabindex="-1" aria-labelledby="datesMatchModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<form action="" method="post" id="formCansel" name="formCansel">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="datesMatchModalLabel">Указанная дата уже существует</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div id="verify_duplicate">
						проверяем <span class="loader-dots"></span>
					</div>
					<div id="has_duplicate" style="display: none;">
						<p class="text-danger"><strong>Внимание!</strong> Указанная вами дата уже существует</p>
						<p>Запись с указанной датой платежа:</p>
						<div id="duplicate_entry" class="ps-4"></div>
					</div>
				</div>
				<div class="modal-footer justify-content-end">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
				</div>
			</div>
		</form>
	</div>
</div>