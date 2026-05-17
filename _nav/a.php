<?php
/** @var array $tldata */
?>
<li class="nav__label">
	<span>Ученики</span>
</li>
<li class="nav__item">
	<a class="nav__link" href="/applications/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-person-arrow-down-to-line"></i></span>
		<span class="nav__text">Обращения</span>
	</a>
</li>
<li class="nav__item">
	<a class="nav__link" href="/students/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-users"></i></span>
		<span class="nav__text">Участники</span>
	</a>
</li>
						
<li class="nav__label">
	<span>Преподаватели</span>
</li>
<li class="nav__item">
	<a class="nav__link" href="/tutors/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-person-chalkboard"></i></span>
		<span class="nav__text">Преподаватели</span>
	</a>
</li>

<!-- <li class="nav__item">
	<a class="nav__link" href="/activity-sess/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-person-circle-check"></i></span>
		<span class="nav__text">Активные сессии</span>
	</a>
</li> -->


<li class="nav__label">
	<span>Обучение</span>
</li>
<li class="nav__item">
	<a class="nav__link" href="/groups/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-people-group"></i></span>
		<span class="nav__text">Группы</span>
	</a>
</li>
<!-- <li class="nav__item">
	<a class="nav__link" href="/activity-group/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-users-gear"></i></span>
		<span class="nav__text">Активные группы</span>
	</a>
</li> -->

<li class="nav__label">
	<span>Книги</span>
</li>
<li class="nav__item">
	<a class="nav__link" href="/book-list/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-book"></i></span>
		<span class="nav__text">Cправочник книг</span>
	</a>
</li>

<?php if ($tldata['usr']['root'] === 1) { ?>
<li class="nav__item">
	<a class="nav__link" href="/book-defective/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-book-skull"></i></span>
		<span class="nav__text">Брак</span>
	</a>
</li>

<?php } ?>
<li class="nav__item">
	<a class="nav__link" href="/book-stock/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-layer-group"></i></span>
		<span class="nav__text">Остатки по филиалам</span>
	</a>
</li>
<li class="nav__item">
	<a class="nav__link" href="/book-reservations/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-book-bookmark"></i></span>
		<span class="nav__text">Бронирование</span>
	</a>
</li>

<li class="nav__item">
	<a class="nav__link" href="/book-selling-delivery/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
		<span class="nav__text">Доставка</span>
	</a>
</li>
<li class="nav__item">
	<a class="nav__link" href="/book-selling-reserve/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
		<span class="nav__text">Оплаченные заказы</span>
	</a>
</li>
<li class="nav__item">
	<a class="nav__link" href="/book-selling/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
		<span class="nav__text">Прямая продажа</span>
	</a>
</li>


<li class="nav__item">
	<a class="nav__link" href="/book-sold/" role="button">
		<span class="nav__icon"><i class="fa-solid fa-receipt"></i></span>
		<span class="nav__text">Список продаж</span>
	</a>
</li>
