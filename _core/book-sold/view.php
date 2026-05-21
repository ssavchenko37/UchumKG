<?php
/** @var array $tldata */
/** @var array $branches */
/** @var array $tutors */
/** @var array $orders */
/** @var array $employee */
/** @var array $order_id */
/** @var array $sort_by */
/** @var array $sort_val */
?>
<form action="/book-sold/" method="post" id="frm0" name="forMain">
	<input type="hidden" id="tutor_id" name="tutor_id" value="<?php echo $tldata['id']?>">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Список продаж</h2>
		</div>
	</div>

	<div class="card border border-secondary-subtle">
		<div class="card-header py-2 bg-light border-bottom border-secondary-subtle">
			<div class="row">
				<div class="col">
					<label class="form-label form-label">Быстрый поиск по: </label>
				</div>
				<div class="col">
					<input type="text" class="form-control form-control-sm" id="by_phone" name="by_phone" placeholder="по телефону">
				</div>
				<div class="col">
					<select class="form-select form-select-sm" id="by_branch" name="by_branch">
						<option value="0">-- по филиалам</option>
						<?php echo getOptionsK('', $branches)?>
					</select>
				</div>
				<div class="col">
					<select class="form-select form-select-sm" id="by_tutor" name="by_tutor">
						<option value="0">-- по сотрудникам</option>
						<?php echo getOptionsK('', $tutors)?>
					</select>
				</div>
				<div class="col">
					<select class="form-select form-select-sm" id="by_delivery" name="by_delivery">
						<?php echo getOptionsK('', ['-- по типу передачи','Бронь / Доставка','Бронь / На руки','Прямая / На руки'])?>
					</select>
				</div>
				<div class="col text-end">
					<button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse" href="#collapseCBody" role="button" aria-expanded="false" aria-controls="collapseCBody">
						Фильтры
					</button>
				</div>
			</div>

		</div>
		<div class="collapse" id="collapseCBody">
			<div class="card-body py-2">
				<div class="close-abs">
					<button type="button" class="btn-close close-card-body" data-bs-toggle="collapse" href="#collapseCBody" role="button" aria-expanded="false" aria-controls="collapseCBody"></button>
				</div>
				<div class="row">
					<div class="col-sm-2">
						<label class="form-label form-label">Сортировать по: </label>
					</div>
					<div class="col-sm-3">
						<div class="status_filter">
							<label for="sort_by_sale" class="form-label form-label">по дате продажи: </label>
							<select class="form-control form-control-sm" id="sort_by_sale" name="sort_by_sale">
								<?php echo getOptionsK($sort_val['sort_by_sale'], $sort_by['sale']) ?>
							</select>
						</div>
					</div>
					<!-- <div class="col-sm-3">
						<div class="status_filter">
							<label for="sort_by_employee" class="form-label form-label-sm">по имени сотрудника: </label>
							<select class="form-control form-control-sm" id="sort_by_employee" name="sort_by_employee">
								<?php echo getOptionsK($sort_val['sort_by_employee'], $sort_by['employee']) ?>
							</select>
						</div>
					</div> -->
					<div class="col-sm-3">
						<div class="status_filter">
							<label for="sort_by_paid" class="form-label form-label">по дате оплаты: </label>
							<select class="form-control form-control-sm" id="sort_by_paid" name="sort_by_paid">
								<?php echo getOptionsK($sort_val['sort_by_paid'], $sort_by['paid']) ?>
							</select>
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-sm-6 text-start">
						<a href="/book-sold/" class="btn btn-sm btn-info">Очистить</a>
					</div>
					<div class="col-sm-6 text-end">
						<button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="collapse" href="#collapseCBody" role="button" aria-expanded="false" aria-controls="collapseCBody">
							Закрыть
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<nav class="pt-2" aria-label="Page navigation example">
		<ul class="pagination pagination-sm justify-content-center">
			<li class="page-item">
				<a class="page-link" href="#" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<li class="page-item active"><a class="page-link" href="#">1</a></li>
			<li class="page-item"><a class="page-link" href="#">2</a></li>
			<li class="page-item"><a class="page-link" href="#">3</a></li>
			<li class="page-item">
				<a class="page-link" href="#" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
	  </ul>
	</nav>
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

<div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-labelledby="cancelSaleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<form action="" method="post" id="formCansel" name="formCansel">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="cancelSaleModalLabel">Отменить продажу / доставку</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p class="text-danger"><strong>Внимание!</strong> Данная запись о продаже вернется в состояние брони.</p>
					<p> Заказ можно потом снова продать из этой брони. <br>Деньги остаются у нас</p>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
					<button type="submit" class="btn btn-danger" data-bs-dismiss="modal" form="order_details">Да, отменить продажу</button>
				</div>
			</div>
		</form>
	</div>
</div>