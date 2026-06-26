<div class="row align-items-center">
	<div class="col-md-8">
		<h2>История ученика</h2>
		<h3 class="mt-4"><?php echo $student['name']?></h3>
		<p>
			<span class="text-secondary">Телефон:</span> <?php echo $student['phone']?>,
			<span class="text-secondary">Дата рождения:</span> <?php echo $student['birthday']?>
		</p>
	</div>
</div>
<table class="table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th class="w-5">#</th>
		<th class="w-20">№ обращения</th>
		<th class="w-15">Статус обращения</th>
		<th class="w-20">№ группы</th>
		<th class="w-15">Статус группы</th>
		<th class="w-25">Преподаватель</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($rows as $r) {
		$tr_class = ($id == $r['appl_id']) ? "table-success": "";
		$active_class = ($r['is_active'] == 1) ? "default": "not-active";
		$ava_img = (is_file(S_AVA . DIRECTORY_SEPARATOR . $r['ava_img'])) ? S_AVA . DIRECTORY_SEPARATOR . $r['ava_img']: S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";
		?>
		<tr class="rws <?php echo $active_class?> <?php echo $tr_class?>">
			<td class="align-middle"><?php echo $q?></td>
			<td class="align-middle"><?php echo $r['app_hash']?></td>
			<td class="align-middle"><?php echo $dict['appltype'][$r['']]?></td>
			<td class="align-middle"><?php echo $r['sess_hash']?></td>
			<td class="align-middle"><?php echo $dict['gstatus'][$r['group_code']]?></td>
			<td class="align-middle">
				<div class="tutor-ava-name">
					<div class="ava-img"><img src="/<?php echo $ava_img?>" alt=""></div>
					<?php echo $r['tutor_name']?>
				</div>
			</td>
		</tr>
		<?php
		$q++;
	}
	?>
	</tbody>
</table>