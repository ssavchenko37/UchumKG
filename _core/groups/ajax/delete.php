<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h4 class="mt-2 mb-5">
			Удалить преподавателя		
		</h4>
		<div class="row mb-5">
			<div class="offset-sm-3 col-sm-9 d-flex align-items-center">
				<div class="confirm-alert pe-4">
					<i class="fa-solid fa-circle-exclamation"></i>
				</div>
				<div class="confirm-text">
					<strong class="fs-5">Внимание!</strong> Вы уверены, что хотите удалить <strong class="fs-5">преподавателя</strong>?<br>
					Преподаватель будет удален, а также все связанные с ним группы.<br>
				</div>
			</div>
		</div>

		<div class="row mb-3">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить, не удалять </button>
				<button type="submit" class="btn btn-primary w-50"> Да, удалить </button>
			</div>
		</div>
		
	</div>
</form>