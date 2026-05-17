<form method="post" id="frm0" name="forMain" enctype="multipart/form-data" onsubmit="return false">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Преподаватели</h2>
		</div>
		<div class="col-md-4 text-end ctrlBtn">
			<button class="btn btn-sm btn-primary" type="button" data-mod="add" data-page="one"><i class="fa fa-plus" aria-hidden="true"></i> Добавить преподавателя</button>
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
		<th class="w-5">ID</th>
		<th class="w-30">Преподаватель</th>
		<th class="w-20">Телефон</th>
		<th class="w-10 text-center">Групп</th>
		<th class="w-10 text-center">Активных</th>
		<th class="w-15">&nbsp;</th>
		<th class="w-20">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($tutors as $r) {
		$tr_class = ($id == $r['tutor_id']) ? "table-success": "";
		$request = $TL->request_encode('tutor_id', $r['tutor_id']);
		$ava_img = (is_file(S_AVA . DIRECTORY_SEPARATOR . $r['ava_img'])) ? S_AVA . DIRECTORY_SEPARATOR . $r['ava_img']: S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";
		$group_total = (is_array($tutors_groups[$r['tutor_id']])) ? count($tutors_groups[$r['tutor_id']]): 0;
		$group_active = (is_array($active_groups[$r['tutor_id']])) ? count($active_groups[$r['tutor_id']]): 0;
		?>
		<tr class="rws <?php echo $tr_class?>" data-meta=<?php echo $meta?>>
			<td class="align-middle"><?php echo $r['tutor_id']?></td>
			<td class="align-middle">
				<div class="tutor-ava-name">
					<div class="ava-img"><img src="/<?php echo $ava_img?>" alt=""></div>
					<?php echo $r['name']?>
				</div>
			</td>
			<td class="align-middle"><?php echo $r['phone']?></td>
			<td class="align-middle text-center"><?php echo $group_total?></td>
			<td class="align-middle text-center"><?php echo $group_active?></td>
			<td>&nbsp;</td>
			<td class="align-middle">
				<div class="text-end ctrlBtn" data-pid="<?php echo $r['tutor_id']?>">
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