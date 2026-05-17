<?php
/** @var array $books */
/** @var int $id */
?>
<form action="/applications/" method="post" id="frm0" name="forMain">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h2>Книги</h2>
		</div>
		<div class="col-md-4 text-end ctrlBtn">
			<button class="btn btn-sm btn-primary" type="button" data-mod="add" data-page="one"><i class="fa fa-plus" aria-hidden="true"></i> Добавить книгу</button>
		</div>
	</div>
</form>

<table class="table table-striped table-hover border-secondary-subtle">
	<thead>
	<tr class="fixed-row sticky-tr">
		<th>#</th>
		<th class="w-30">Название</th>
		<th>Автор</th>
		<th>ISBN</th>
		<th>Цена</th>
		<th class="w-10">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$q = 1;
	foreach ($books as $r) {
		$tr_class = ($id == $r['book_id']) ? "table-success": "";
		?>
		<tr class="rws <?php echo $tr_class?>">
			<td class="align-middle"><?php echo $q?></td>
			<td class="align-middle" title="<?php echo $r['book_id']?>"><?php echo $r['title']?></td>
			<td class="align-middle"><?php echo $r['author']?></td>
			<td class="align-middle"><?php echo $r['isbn']?></td>
			<td class="align-middle"><?php echo $r['price']?></td>
			<td class="align-middle">
				<div class="text-end ctrlBtn" data-pid="<?php echo $r['book_id']?>">
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