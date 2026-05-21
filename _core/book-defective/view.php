<?php
/** @var array $defects */
/** @var array $employee */
/** @var array $defect_statuses */
/** @var int $id */
?>
<form action="/applications/" method="post" id="frm0" name="forMain">
	<div class="row align-items-center pb-1 pb-md-0">
		<div class="col-6 col-md-8">
			<h2>Брак</h2>
		</div>
		<div class="col-6 col-md-4 text-end ctrlBtn">
			<button class="btn btn-sm btn-primary" type="button" data-mod="add" data-page="one"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</button>
		</div>
	</div>
</form>

<table class="table books-table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th>#</th>
		<th>Сотрудник</th>
		<th>Филиал</th>
		<th>Название</th>
		<th>Кол-во</th>
		<th class="w-20">Коммент.</th>
		<th>Статус</th>
		<th>Создано</th>
		<th>Изменено</th>
		<th class="w-10">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($defects as $r) {
		$tr_class = ($id == $r['defect_id']) ? "table-success": "";
		$not_enough = ($r['status'] === "defective") ? "": " not-active";
		$mode = ($r['status'] === "defective") ? "edit": "review";
		?>
		<tr class="rws <?php echo $tr_class.$not_enough?>">
			<td class="align-middle" data-label="#"><?php echo $q?></td>
			<td class="align-middle" data-label="Сотрудник"><?php echo $employee[$r['tutor_id']]?></td>
			<td class="align-middle" data-label="Филиал" title="<?php echo $r['branch_id']?>"><?php echo $r['branch_name']?></td>
			<td class="align-middle" data-label="Название" title="<?php echo $r['title']?>"><?php echo mb_substr($r['title'], 0, 20, 'UTF-8');?>...</td>
			<td class="align-middle" data-label="Кол-во"><?php echo $r['qty']?></td>
			<td class="align-middle" data-label="Коммент."><small><?php echo nl2br($r['comment'])?></small></td>
			<td class="align-middle" data-label="Статус"><?php echo $defect_statuses[$r['status']]?></td>
			<td class="align-middle" data-label="Создано"><small><?php echo $r['createdAt']?></small></td>
			<td class="align-middle" data-label="Изменено"><small><?php echo $r['updatedAt']?></small></td>
			
			<td class="align-middle">
				<div class="text-end ctrlBtn" data-pid="<?php echo $r['defect_id']?>">
					<button class="btn btn-success btn-sm" type="button" data-mod="<?php echo $mode?>" data-page="one"><i class="fas fa-pencil-alt"></i></button>
					<?php if ($r['status'] === "defective") { ?>
						<button class="btn btn-danger btn-sm" type="button" data-mod="delete" data-page="delete"><i class="far fa-trash-alt"></i></button>
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