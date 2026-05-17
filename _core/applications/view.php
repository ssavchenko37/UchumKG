<form action="/applications/" method="post" id="frm0" name="forMain">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Обращения</h2>
		</div>
	</div>

	<div class="card border border-secondary-subtle">
		<div class="card-header py-2 bg-light border-bottom border-secondary-subtle">
			<div class="row">
				<div class="col-sm-5 d-flex">
					<label for="schstr" class="col-form-label col-form-label-sm pe-3">Поиск: </label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm" id="schstr" name="schstr"
							   value="<?php if( !empty($schstr) ) echo $schstr;?>" placeholder="search by Student name"/>
						<button class="btn btn-sm btn-secondary" type="submit" id="btn_schstr">Поиск</button>
					</div>
				</div>
				<div class="col-sm-3">
					<button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse" href="#collapseCBody" role="button" aria-expanded="false" aria-controls="collapseCBody">
						Фильтры
					</button>
				</div>
				<div class="col-sm-4 text-end">
					<a href="/applications/" class="btn btn-sm btn-info">Очистить</a>
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
<div class="application mt-3 mb-2" id="applicationForm">
	<form action="" method="post" id="appl-form">
		<input type="hidden" id="stud_id" name="stud_id">
		<input type="hidden" id="first_hash" name="first_hash">
		<div class="row">
			<div class="col-md-4">
				<input type="text" class="form-control form-control-sm" id="stud_name" name="stud_name" placeholder="ФИО">
			</div>
			<div class="col-md-3">
				<input type="text" class="form-control form-control-sm" id="stud_tel" name="stud_tel" placeholder="Телефон">
			</div>
			<div class="col-md-3">
				<input type="text" class="form-control form-control-sm" id="stud_birthday" name="stud_birthday" placeholder="Дата рождения">
			</div>
			<div class="col-md-2">
				<button class="btn btn-sm btn-primary" type="submit" name="mode" value="add_application">Сохранить</button>
				<button class="btn btn-sm btn-secondary" type="reset">
					<i class="fa-solid fa-ban"></i>
				</button>
			</div>
		</div>
	</form>
	<div class="duplicates" id="duplicateBox">
		<div class="close-abs">
			<button type="button" class="btn-close close-card-body" role="button"></button>
		</div>
		<div class="duplicates__list">
		</div>
	</div>
</div>
<table class="table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th>#</th>
		<th class="w-20">ФИО</th>
		<th>№ ученика</th>
		<th>Телефон</th>
		<th>Возраст</th>
		<th class="w-20">Отметки</th>
		<th>Оферта</th>
		<th>Переход</th>
		<th>История</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($applications as $r) {
		$tr_class = ($id == $r['appl_id']) ? "table-success": "";
		$request = $TL->request_encode('stud_id', $r['stud_id']);
		$offer = ($r['sign_offer'] > 0) ? "<small>" . $r['odate'] . "</small>" : "<small class=\"text-danger\">Нет</small>";
		?>
		<tr class="rws <?php echo $tr_class?>" data-meta=<?php echo $meta?>>
			<td class="align-middle"><?php echo $q?></td>
			<td class="align-middle" title="<?php echo $r['stud_id']?>"><?php echo buildFIO($r)?></td>
			<td class="align-middle"><?php echo $r['app_hash']?></td>
			<td class="align-middle"><?php echo $r['phone']?></td>
			<td class="align-middle text-center"><?php echo $r['age']?></td>
			<td class="align-middle">
				<div class="ctrlBtn" data-pid="<?php echo $r['appl_id']?>">
					<button class="btn btn-link btn-xs" type="button" data-mod="details" data-page="details">
						<?php echo $appltype[$r['appl_type']]?><br>
						<?php echo $studtype[$r['stud_type']]?>; <?php echo $my_level[$r['mylevel']][0]?>; <?php echo $who_will[$r['whowill']]?><br>
						<?php if ($r['appl_type'] === "pending") {
							echo $r['daysweek'] . "; " . $r['coursetime'];
						} ?>
					</button>
				</div>
			</td>
			<td class="align-middle"><?php echo$offer?></td>
			<td class="align-middle">
				<div class="ctrlBtn" data-pid="<?php echo $r['appl_id']?>">
					<?php if ($r['group_id'] > 0) { ?>
						<button class="btn btn-success btn-xs" type="button" data-mod="link" data-page="link-group"><?php echo $r['sess_hash']?></button>
					<?php } else { ?>
						<button class="btn btn-secondary btn-sm" type="button" data-group-id="<?php echo $r['gid']?>" data-mod="link" data-page="link-group">не задан</button>
					<?php } ?>
				</div>
			</td>
			<td class="align-middle">
				<?php if ($records[$r['stud_id']] > 0) { ?>
					<a href="/history/?<?php echo $request?>" class="btn btn-sm btn-secondary">
						<i class="fa-regular fa-rectangle-list"></i>&nbsp;&nbsp;<strong><?php echo $records[$r['stud_id']]?></strong>
					</a>
				<?php } ?>
			</td>
		</tr>
		<?php
		$q++;
	}
	?>
	</tbody>
</table>

<div class="offcanvas offcanvas-end viewer" tabindex="-1" id="detailsOne" aria-labelledby="detailsOneLabel">
	<div class="offcanvas-header">
		<div class="viewer__close">
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
	</div>
	<div class="offcanvas-body" id="offcanvas_body">
	</div>
</div>