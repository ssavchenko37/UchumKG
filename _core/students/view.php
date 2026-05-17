<form method="post" id="frm0" name="forMain" enctype="multipart/form-data" onsubmit="return false">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Участники</h2>
		</div>
	</div>

	<div class="card border border-secondary-subtle">
		<div class="card-header py-2 bg-light border-bottom border-secondary-subtle">
			<div class="row">
				<div class="col-sm-5 d-flex">
					<label for="schstr" class="col-form-label col-form-label-sm pe-3">Поиск: </label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm" id="schstr" name="schstr"
							   value="<?php if( !empty($schstr) ) echo $schstr;?>" placeholder="поиск по имени или по коду"/>
						<button class="btn btn-sm btn-secondary" type="button" id="btn_schstr">Поиск</button>
					</div>
				</div>
				<div class="col-sm-3">
					<button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse" href="#collapseCBody" role="button" aria-expanded="false" aria-controls="collapseCBody">
						Фильтры
					</button>
				</div>
			</div>
		</div>
		<div class="collapse" id="collapseCBody">
			<div class="card-body py-3">
				<div class="close-abs">
					<button type="button" class="btn-close close-card-body" data-bs-toggle="collapse" href="#collapseCBody" role="button" aria-expanded="false" aria-controls="collapseCBody"></button>
				</div>
				
				<div class="row">
					<div class="col-sm-3">
						<div class="status_filter">
							<label for="filter_tutor" class="form-label form-label">Преподаватель: </label>
							<select class="form-control form-control-sm" id="filter_tutor" name="filter_tutor">
								<?php if ($tldata['umod'] == "a") { ?>
									<option value="0">Просмотреть все</option>
								<?php } ?>
								<?php echo getOptionsK($filter_tutor, $filter_tutors) ?>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="status_filter">
							<label for="filter_grm" class="form-label form-label-sm">Поток: </label>
							<select class="form-control form-control-sm" id="filter_grm" name="filter_grm">
								<option value="0">Просмотреть все</option>
								<?php echo getOptionsK($filter_grm, $filter_groupments) ?>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="status_filter">
							<label for="filter_grup" class="form-label form-label-sm">Группа: </label>
							<select class="form-control form-control-sm" id="filter_grup" name="filter_grup">
								<option value="0">Просмотреть все</option>
								<?php echo getOptionsK($filter_grup, $filter_groups) ?>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="status_filter">
							<label for="filter_subject" class="form-label form-label-sm">Предмет: </label>
							<select class="form-control form-control-sm" id="filter_subject" name="filter_subject">
								<option value="0">Просмотреть все</option>
								<?php echo getOptionsK($filter_subject, $filter_subjects) ?>
							</select>
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-sm-6 text-start">
						<a href="/applications/" class="btn btn-sm btn-info">Очистить</a>
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
</form>
<br>
<table class="table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th class="w-5">#</th>
		<th class="w-25">ФИО</th>
		<th class="w-10">№ первый</th>
		<th class="w-10">Телефон</th>
		<th class="w-15">Дата рождения</th>
		<th class="w-15">Оферта</th>
		<th class="w-10">История</th>
		<th class="w-10"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($students as $r) {
		$tr_class = ($id == $r['stud_id']) ? "table-success": "";
		$request = $TL->request_encode('stud_id', $r['stud_id']);
		$offer = ($r['sign_offer'] > 0) ? "<small>" . $r['odate'] . "</small>" : "<small class=\"text-danger\">Нет</small>";
		?>
		<tr class="rws <?php echo $tr_class?>" data-meta=<?php echo $meta?>>
			<td class="align-middle"><?php echo $q?></td>
			<td class="align-middle" title="<?php echo $r['stud_id']?>"><?php echo buildFIO($r)?></td>
			<td class="align-middle"><?php echo $r['first_hash']?></td>
			<td class="align-middle"><?php echo $r['phone']?></td>
			<td class="align-middle"><?php echo $r['bdate']?></td>
			<td class="align-middle"><?php echo $offer?></td>
			<td class="align-middle">
				<?php if ($records[$r['stud_id']] > 0) { ?>
					<a href="/history/?<?php echo $request?>" class="btn btn-sm btn-secondary">
						<i class="fa-regular fa-rectangle-list"></i>&nbsp;&nbsp;<strong><?php echo $records[$r['stud_id']]?></strong>
					</a>
				<?php } ?>
			</td>
			<td class="align-middle">
				<div class="text-end ctrlBtn" data-pid="<?php echo $r['stud_id']?>">
					<button class="btn btn-success btn-sm" type="button" data-mod="edit" data-page="one"><i class="fas fa-pencil-alt"></i></button>
					<button class="btn btn-danger btn-sm" type="button" data-mod="delete" data-page="delete"><i class="far fa-trash-alt"></i></button>
				</div>
			</td>
		</tr>
		<?php
		$q++;
	}
	?>
	</tbody>
</table>