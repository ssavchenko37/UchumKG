<form method="post" id="frm0" name="frmadd" enctype="multipart/form-data" onsubmit="return false">
	<input type="hidden" id="group_id" name="group_id" value="<?php echo $group['group_id']?>">
	<input type="hidden" id="tutor_id" name="tutor_id" value="<?php echo $group['tutor_id'] ?>">
		
	<div class="ibook__header">
		<div class="ibook__title">
			<h2>Группа <?php echo $group['sess_hash']?></h2>
			<p><?php echo $group['created']?></p>
		</div>
		<div class="ibook__tutor">
			<div class="ibook__tutor-ava"><img src="/<?php echo $ava_img?>" alt=""></div>
			<div class="ibook__tutor-name">
				<h3><?php echo $group['name']?></h3>
				<p class="m-0">0<?php echo $group['phone']?></p>
			</div>
		</div>
	</div>
	<br>
	<div class="table-frozen">
		<table class="table table-hover table-bordered border-dark-subtle">
			<thead>
			<tr>
				<td class="align-middle text-center">№</td>
				<td class="align-middle">Ученик</td>
				<?php
				for ($x = 1; $x < $MAXDays; $x++) {
					$dis = '';
					$muin = 'col' . $x;
					?>
					<td class="align-middle entered-td entered" title="col<?php echo $x?>" data-meta_id="<?php echo $meta[$muin]['meta_id']?>">
						<div class="input-group input-group-sm">
							<div class="entered-alt">
								<span><?php echo $meta[$muin]['tdate']?></span>
								<small><?php echo $meta[$muin]['ttime']?></small>
							</div>
							<input type="text" class="form-control form-control-sm " id="<?php echo $muin?>" name="<?php echo $muin?>" value="<?php echo $meta[$muin]['meta_date']?>" <?php echo $dis?>/>
						</div>
					</td>
					<?php
				}
				?>
			</tr>
			</thead>
			<tbody>
			<?php
				$q = 1;
				foreach ($students as $r) {
					?>
					<tr data-send="<?php echo $r['stud_id']?>">
						<td class="align-middle text-center"><?php echo $q?></td>
						<td class="align-middle"><span><?php echo studentShort($r['name']) ?></span></td>
						<?php
						for ($x = 1; $x < $MAXDays; $x++) {
							$cuin = "col" . $x;
							$item = (is_array($items[$cuin][$r['stud_id']])) ? $items[$cuin][$r['stud_id']]: array();
							
							?>
							<td class="align-middle text-center edited edited_val<?php if ($item['is_abs'] == 1) echo ' bg-abs';?>" data-item_id="<?php echo $item['item_id']?>">
								<span data-send="<?php echo $cuin?>"><b><?php echo $item['item_val'] ?></b></span>
							</td>
							<?php
						}
						?>
					</tr>
					<?php
					$q++;
				}
				foreach (array("meta_topic"=>"Тема урока") as $k=>$meta_name) {
					?>
					<tr class="meta-row">
						<td>&nbsp;</td>
						<td class="align-middle text-right"><?php echo $meta_name?></td>
						<?php
						for ($x = 1; $x < $MAXDays; $x++) {
							$muin = 'col' . $x;
							?>
							<td class="<?php if ($k != "meta_topic" ) echo "align-middle ";?>text-center edited edited_meta <?php echo $k?>" data-meta_id="<?php echo $meta[$muin]['meta_id']?>">
								<span data-send="<?php echo $muin?>" data-field="<?php echo $k?>"><b><?php echo !$meta[$muin][$k] ? "" : $meta[$muin][$k] ?></b></span>
							</td>
							<?php
						}
						?>
					</tr>
					<?php
					$q++;
				}
			?>
				<tr class="meta-cancel">
					<td>&nbsp;</td>
					<td class="align-middle text-right">Отмена</td>
					<?php
					for ($x = 1; $x < $MAXDays; $x++) {
						$muin = 'col' . $x;
						?>
						<td class="align-middle text-center" data-meta_id="<?php echo $meta[$muin]['meta_id']?>">
							<button class="btn btn-outline-secondary btn-sm" type="button"><i class="fa-solid fa-ban"></i></button>
						</td>
						<?php
					}
					?>
				</tr>
			</tbody>
		</table>
	</div>
</form>

<div class="modal fade" id="lessonСancelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="lessonСancelModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<form action="" method="post" id="formCansel" name="formCansel">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lessonСancelModalLabel">Отмена урока</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="mode" value="cancel">
					<input type="hidden" id="meta_id" name="meta_id" value="">
					<div class="mb-3">
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" role="switch" id="meta_cancelled" name="meta_cancelled" value=1>
							<label class="form-check-label" for="meta_cancelled">
								<span class="text-secondary">Отменить занятие</span>
							</label>
						</div>
					</div>
					<div class="mb-3">
						<label for="name" class="form-label">Причина отмены:</label>
						<textarea class="form-control" id="meta_note" name="meta_note" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
					<button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Применить</button>
				</div>
			</div>
		</form>
	</div>
</div>