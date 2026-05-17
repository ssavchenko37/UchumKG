<?php
/** @var array $branches */
?>
<div class="row align-items-center">
	<div class="col-md-8">
		<h2>Прямая продажа</h2>
	</div>
</div>
<br>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="direct">
	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">

		<div class="row mb-3">
			<label for="branch_id" class="col-sm-3 col-form-label text-end">Филиал:</label>
			<div class="col-sm-9">
				<select class="form-select" id="branch_id" name="branch_id">
					<option value=""> -- </option>
					<?php echo getOptionsK('', $branches)?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="book_id" class="col-sm-3 col-form-label text-end">Книга:</label>
			<div class="col-sm-9">
				<select class="form-select" id="book_id" name="book_id">
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="price" class="col-sm-3 col-form-label text-end">Цена экземпляра:</label>
			<div class="col-sm-9">
				<input type="number" class="form-control" id="price" name="price" value="">
			</div>
		</div>

		<div class="row mb-3">
			<label for="qty" class="col-sm-3 col-form-label text-end">Количество:</label>
			<div class="col-sm-9">
				<input type="number" class="form-control" id="qty" name="qty" value="">
				<small class="text-secondary" id="max_qty"></small>
			</div>
		</div>

		<div class="row mb-3">
			<label for="amount" class="col-sm-3 col-form-label text-end">Оплачено:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="amount" name="amount" value="">
			</div>
		</div>

		<div class="row mb-3">
			<label for="phone" class="col-sm-3 col-form-label text-end">Телефон:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="phone" name="phone" value="">
			</div>
		</div>

		<div class="row mb-3">
			<label for="comment" class="col-sm-3 col-form-label text-end">Комментарий:</label>
			<div class="col-sm-9">
				<textarea class="form-control" id="comment" name="comment" placeholder=""></textarea>
			</div>
		</div>

		<div class="row mb-3">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button type="submit" class="btn btn-primary w-50"> Сохранить </button>
			</div>
		</div>

	</div>

</form>