<form method="post" id="frm0" name="forMain" enctype="multipart/form-data" onsubmit="return false">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Группы</h2>
		</div>
		<?php if ($tldata['umod'] === "a") { ?>
		<div class="col-md-4 text-end ctrlBtn">
			<button class="btn btn-sm btn-primary" type="button" data-mod="add" data-page="one"><i class="fa fa-plus" aria-hidden="true"></i> Добавить группу</button>
		</div>
		<?php } ?>
		
	</div>

	<div class="card border border-secondary-subtle">
		<div class="card-header py-2 bg-light border-bottom border-secondary-subtle">
			<div class="row">
				<div class="col-sm-5 d-flex">
					<label for="schstr" class="col-form-label col-form-label-sm pe-3">Поиск: </label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm" id="schstr" name="schstr"
							   value="<?php if( !empty($schstr) ) echo $schstr;?>" placeholder="поиск по коду"/>
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
		<th>#</th>
		<th class="w-20">Код</th>
		<th class="w-20">Преподаватель</th>
		<th class="w-15">Формат</th>
		<th class="w-20">Адрес</th>
		<th class="w-5">Ученики</th>
		<th class="w-15">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($groups as $r) {
		$tr_class = ($id == $r['group_id']) ? "table-success": "";
		$request = $TL->request_encode('group_id', $r['group_id']);
		$active_class = ($r['is_active'] == 1) ? "default": "not-active";
		$ava_img = (is_file(S_AVA . DIRECTORY_SEPARATOR . $tutors[$r['tutor_id']]['ava_img'])) ? S_AVA . DIRECTORY_SEPARATOR . $tutors[$r['tutor_id']]['ava_img']: S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";
		$request = $TL->request_encode('group_id', $r['group_id']);
		?>
		<tr class="rws <?php echo $active_class?> <?php echo $tr_class?>">
			<td class="align-middle"><?php echo $q?></td>
			<td class="align-middle"><?php echo $r['sess_hash']?></td>
			<td class="align-middle">
				<div class="tutor-ava-name">
					<div class="ava-img"><img src="/<?php echo $ava_img?>" alt=""></div>
					<?php echo $tutors[$r['tutor_id']]['name']?>
				</div>
			</td>
			<td class="align-middle">
				<?php echo $dict['format'][$r['format_id']]['title']?>,
				<?php echo $dict['age'][$r['age_id']]['title']?>
				<br>
				<?php echo $r['stime']?>,
				<?php echo $dict['schedule'][$r['schedule_id']]['title']?>
			</td>
			<td class="align-middle">
				<?php echo $dict['address'][$r['address_id']]['title']?>
			</td>
			<td class="align-middle">
				<?php if ($participants[$r['group_id']] > 0) { ?>
				<a href="/group/?<?php echo $request?>" class="btn btn-sm btn-secondary">
					<i class="fa-solid fa-user-group"></i>&nbsp;&nbsp;<strong><?php echo $participants[$r['group_id']]?></strong>
				</a>
				<?php } ?>
			</td>
			<td class="align-middle">
				<?php if ($tldata['umod'] === "a") { ?>
				<div class="text-end ctrlBtn" data-pid="<?php echo $r['group_id']?>">
					<button class="btn btn-success btn-sm" type="button" data-mod="edit" data-page="one"><i class="fas fa-pencil-alt"></i></button>
					<button class="btn btn-danger btn-sm" type="button" data-mod="delete" data-page="delete"><i class="far fa-trash-alt"></i></button>
				</div>
				<?php } ?>
			</td>
		</tr>
		<?php
		$q++;
	}
	?>
	</tbody>
</table>