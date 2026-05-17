<?php
/** @var array $tldata */
/** @var array $branches */
/** @var array $tutors */
/** @var array $orders */
/** @var array $employee */
/** @var array $order_id */
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post" id="frm0" name="forMain">
	<input type="hidden" id="tutor_id" name="tutor_id" value="<?php echo $tldata['id']?>">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Список продаж</h2>
		</div>
	</div>

	<div class="row py-1">
		<div class="col col-md-3">
			<input type="text" class="form-control form-control-sm" id="by_phone" name="by_phone" placeholder="поиск по телефону">
		</div>
		<div class="col col-md-2">
			<select class="form-select form-select-sm" id="by_branch" name="by_branch">
				<option value="0">-- по филиалам</option>
				<?php echo getOptionsK('', $branches)?>
			</select>
		</div>
		<div class="col col-md-2">
			<select class="form-select form-select-sm" id="by_tutor" name="by_tutor">
				<option value="0">-- по сотрудникам</option>
				<?php echo getOptionsK('', $tutors)?>
			</select>
		</div>
		<div class="col col-md-3">
			<select class="form-select form-select-sm" id="by_delivery" name="by_delivery">
				<?php echo getOptionsK('', ['-- по типу передачи','Бронь / Доставка','Бронь / На руки','Прямая / На руки'])?>
			</select>
		</div>
		<!-- <div class="col col-md-1">
			<button class="btn btn-sm btn-secondary" type="submit">Применить</button>
		</div> -->
	</div>
</form>

<table class="table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th>#</th>
		<th>Сотрудник</th>
		<th>Филиал</th>
		<th class="w-20">Доставка</th>
		<th>Телефон</th>
		<th>Кол-во</th>
		<th>Сумма</th>
		<th>Продажа</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($orders as $r) {
		$tr_class = ($order_id == $r['order_id']) ? "table-success": "";
		$sale_type = ($r['reservation_id'] > 0) ? "Бронь": "Прямая";
		$is_delivery = (empty($r['delivery_to'])) ? "На руки": "Доставка";
		$delivery = ($r['where_go'] == "hand") ? "": $r['delivery_to'];
		$delivery_type = 1;
		$delivery_type = ($r['reservation_id'] > 0 && empty($r['delivery_to'])) ? 2: $delivery_type;
		$delivery_type = ($r['reservation_id'] == 0) ? 3: $delivery_type;
		?>
		<tr class="rws <?php echo $tr_class?>"
			data-phone="<?php echo $r['phone']?>"
			data-address="<?php echo $delivery?>"
			data-delivery="<?php echo $delivery_type?>"
			data-branch="<?php echo $r['branch_id']?>"
			data-tutor="<?php echo $r['tutor_id']?>">
			<td class="align-middle"><?php echo $q?></td>
			<td class="align-middle" title="<?php echo $r['tutor_id']?>">
				<?php echo $employee[$r['tutor_id']]?>
				<br><small><?php echo $r['odate']?></small>
			</td>
			<td class="align-middle" title="<?php echo $r['branch_id']?>"><?php echo $r['branch_name']?></td>
			<!-- <td class="align-middle" title="<?php echo $r['book_id']?>"><?php echo $r['title']?><br><small><?php echo $r['author']?></small></td> -->
			<td class="align-middle"><?php echo $delivery?><br><small><?php echo $r['for_courier']?></small></td>
			<td class="align-middle"><?php echo $r['phone']?></td>
			<td class="align-middle"><?php echo $r['qty']?></td>
			<td class="align-middle"><?php echo $r['total_amount']?><br><small><?php echo $r['comment']?></small></td>
			<td class="align-middle"><small><?php echo $sale_type?><br><?php echo $is_delivery?></small></td>
			
			<td class="align-middle">
				<div class="text-end ctrlBtn" data-pid="<?php echo $r['order_id']?>">
					<button class="btn btn-info btn-sm-ico" type="button" data-page="details"><i class="fa-solid fa-circle-info"></i></button>
				</div>
			</td>
		</tr>
		<?php
		$q++;
	}
	?>
	</tbody>
</table>