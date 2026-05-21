<?php
/** @var array $tldata */
/** @var array $reservations */
/** @var array $branches */
/** @var array $where_translate */
/** @var int $id */

?>
<form action="/book-stock-list/" method="post" id="frm0" name="forMain">
	<input type="hidden" id="tutor_id" name="tutor_id" value="<?php echo $tldata['id']?>">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Бронирование</h2>
		</div>
		<div class="col-md-4 text-end ctrlBtn">
			<button class="btn btn-sm btn-primary" type="button" data-mod="add" data-page="one"><i class="fa fa-plus" aria-hidden="true"></i> Создать бронь</button>
		</div>
	</div>

	<div class="row py-1">
		<div class="col-6 col-md-3">
			<input type="text" class="form-control form-control-sm" id="by_phone" name="by_phone" placeholder="поиск по телефону">
		</div>
		<div class="col-6 col-md-3">
			<select class="form-select form-select-sm" id="by_delivery" name="by_delivery">
				<?php echo getOptionsK('', ['0'=>'-- по доставке','hand'=>'Самовывоз','capital'=>'Доставка по городу','region'=>'Доставка в регион'])?>
			</select>
		</div>
		<div class="col-6 col-md-3">
			<select class="form-select form-select-sm" id="by_branch" name="by_branch">
				<option value="0">-- по филиалам</option>
				<?php echo getOptionsK('', $branches)?>
			</select>
		</div>
		<div class="col-6 col-md-3">
			<select class="form-select form-select-sm" id="by_available" name="by_available">
				<?php echo getOptionsK('', ['-- по количеству','Кол-во недостаточно','Достаточное кол-во'])?>
			</select>
		</div>
	</div>
</form>

<table class="table books-table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th>#</th>
		<th>Филиал</th>
		<th class="w-20">Название</th>
		<th>Телефон</th>
		<th class="w-20">Доставка</th>
		<th nowrap>Кол-во<br><small style="display: none;">надо / есть / дефицит</small></th>
		<th class="text-center">Сумма</th>
		<th class="text-center">Платеж</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($reservations as $r) {
		$tr_class = ($id == $r['reservation_id']) ? " table-success": "";
		$shortage = $r['qty'] - $r['available'];
		$shortage = ($shortage > 0) ? $shortage : 0;
		$disable_class = ($shortage > 0) ? " not-active": "";
		$data_shortage = ($shortage > 0) ? 1: 2;

		$cost = $r['qty']*$r['price'];
		$surcharge = 0;
		$not_enough = "default";
		$amount_color = 'text-success';
		if ($r['amount'] < $cost && $r['payment_id']) {
			$surcharge = $cost - $r['amount'];
			$not_enough = "not-enough";
			$amount_color = 'text-danger';
		}
		$delivery = ($r['where_go'] == "hand") ? "": $r['delivery_to'];
		?>
		<tr class="rws<?php echo $tr_class?> <?php echo $disable_class?> <?php echo $not_enough?>"
			data-phone="<?php echo $r['phone']?>"
			data-address="<?php echo $delivery?>"
			data-delivery="<?php echo $r['where_go']?>"
			data-branch="<?php echo $r['branch_id']?>"
			data-shortage="<?php echo $data_shortage?>"> 
			<td class="align-middle" data-label="#">
				<?php echo $q?>
				<div class="where-go-marker">
					<div class="marker marker--<?php echo $r['where_go']?>"><?php echo $where_translate[$r['where_go']]?></div>
				</div>
				
			</td>
			<td class="align-middle" data-label="Филиал" title="<?php echo $r['reservation_id']?>">
				<?php echo $r['branch_name']?><br><small><?php echo $r['created']?></small>
			</td>
			<td class="align-middle" data-label="Название" title="<?php echo $r['title'] . " " . $r['author']?>"><?php echo $r['title']?></td>
			<td class="align-middle" data-label="Телефон"><?php echo $r['phone']?></td>
			<td class="align-middle" data-label="Доставка"><?php echo $delivery?><br><small><?php echo $r['for_courier']?></small>
			</td>
			<td class="align-middle" data-label="Кол-во"><?php echo $r['qty']?>
				<strong class="text-danger"><?php if ($shortage > 0) { echo " / -" . $shortage; } ?></strong>
			</td>
			<td class="align-middle text-center" data-label="Сумма">
				<strong class="text-success"><?php echo number_format($cost, 2);?></strong>
			</td>
			<td class="align-middle text-center" data-label="Платеж">
				<?php if (empty($r['payment_id']) && $shortage <= 0) { ?>
				<div class="ctrlBtn" data-pid="<?php echo $r['reservation_id']?>">
					<button class="btn btn-info btn-sm" type="button" data-mod="payment" data-page="payment"><i class="fa-regular fa-credit-card"></i></button>
				</div>
				<?php } elseif ($r['payment_id'] > 0 && $surcharge > 0) { ?>
				<strong class="text-danger"><?php echo $r['paid_sum'];?></strong>
				<?php } else { ?>
				<strong class="text-success"><?php echo $r['amount'];?></strong>
				<?php } ?>
				<br><small><?php echo $r['comment']?></small>
			</td>
			<td class="align-middle" data-label="" nowrap>
				<div class="ctrlBtn" data-pid="<?php echo $r['reservation_id']?>">
					<button class="btn btn-success btn-sm" type="button" data-mod="edit" data-page="one"><i class="fas fa-pencil-alt"></i></button>
					<button class="btn btn-danger btn-sm" type="button" data-mod="delete" data-page="delete"><i class="far fa-trash-alt"></i></button>
					<?php if (empty($r['payment_id'])) { ?>
					
					<?php } ?>
				</div>
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