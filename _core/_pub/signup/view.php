<div class="page col-12 col-lg-10 col-xxl-9">
	<div class="page__wrap page__wrap--sign">
		<div class="page__title">
			<div class="page__greeting">
				<h2>Группы</h2>
				<p>
					Курс рассчитан на год обучения.<br>
					Ежемесячная стоимость составляет: 4000 сом
				</p>
			</div>
			<div class="page__ctrl">
				<div class="page__pending-app">
					<button type="button" class="pp-sign btn btn-info">Подать ожидающую заявку</button>
				</div>
				<div class="page__tab-nav">
					<div class="btn-group" id="group_type_tab" role="group-tabs" aria-label="Group type tabs">
						<button type="button" data-tab-type="all" class="active btn btn-secondary">Все</button>
						<button type="button" data-tab-type="adult" class="btn btn-secondary">Взрослые</button>
						<button type="button" data-tab-type="child" class="btn btn-secondary">Дети</button>
					  </div>
				</div>
			</div>
		</div>
		<div class="page__groups scroll-box">
			<div class="page__groups-body" id="group_type_pane">
				<?php
				foreach ($result as $k=>$r) {
					?>
					<div class="groups-title"><span class="h4"><?php echo $r['name']?></span><i></i></div>
					<div class="groups-list">
						<?php
						foreach ($r['groups'] as $g) {
							$tutor = $tutors[$g['tutor_id']];
							$group_type = ($g['age_code'] == "CHL") ? "child": "adult";
							$ava_img = (is_file(S_AVA . DIRECTORY_SEPARATOR . $tutor['ava_img'])) ? S_AVA . DIRECTORY_SEPARATOR . $tutor['ava_img']: S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";
							$places_left['qty'] = $g['usrqty'] - $already[$g['group_id']];
							$places_left['class_prefix'] = ($places_left['qty'] > 5) ? "success": "danger";
							$places_left['icon'] = ($places_left['qty'] > 5) ? "fa-solid fa-check": "fa-brands fa-gripfire";
							?>
							<div class="groups-item group" data-group-type="<?php echo $group_type?>">
								<div class="group__format group__format--<?php echo $group_type?>">
									<div><?php echo $dict['format'][$g['format_code']]?> • <?php echo $dict['age'][$g['age_code']]?></div>
								</div>
								<div class="group__main">
									<div class="group__tutor">
										<div class="group__tutor-ava">
											<img src="/<?php echo $ava_img?>" alt="<?php echo $tutor['name']?>">
										</div>
										<strong><?php echo $tutor['name']?></strong>
										<span>Преподаватель</span>
									</div>
									<div class="group__meta">
										<div class="group__meta-item">
											<em><i class="fa-regular fa-calendar"></i></em>
											<div>
												<strong><?php echo $dict['schedule'][$g['schedule_code']]?></strong>
												<time><?php echo date("H:i", strtotime($g['startime']))?></time>
											</div>
										</div>
										<div class="group__meta-item">
											<em><i class="fa-solid fa-location-dot"></i></em>
											<span><?php echo $dict['address'][$g['address_code']]?></span>
										</div>
									</div>
								</div>
								<div class="group__footer">
									<div class="group__qty group__qty--<?php echo $places_left['class_prefix']?>">
										<em><i class="<?php echo $places_left['icon']?>"></i></em>
										<span>Осталось мест: <?php echo $places_left['qty']?></span>
									</div>
									<div class="group-btn">
										<button type="button" data-group-id="<?php echo $g['group_id']?>" class="pp-sign btn btn-sm btn-primary">Записаться</button>
									</div>
								</div>
								<div class="hidden-preview">
									<div class="pp-preview">
										<div class="pp-preview__ava">
											<img src="/<?php echo $ava_img?>" alt="<?php echo $tutor['name']?>">
										</div>
										<div class="pp-preview__info">
											<div class="pp-preview__title"><?php echo $tutor['name']?></div>
											<div class="pp-preview__text"><?php echo $dict['format'][$g['format_code']]?> • <?php echo $dict['age'][$g['age_code']]?></div>
											<div class="pp-preview__time"><?php echo $dict['schedule'][$g['schedule_code']]?> в <?php echo date("H:i", strtotime($g['startime']))?></div>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>	
		</div>
	</div>
</div>

<div class="popup popup-sign" id="popup_sign">
	<div class="popup__content">
		<div class="popup__header">
			<div class="popup__title"><div class="h4">Новая заявка</div></div>
			<div class="popup__close"><i class="fa-solid fa-xmark"></i></div>
		</div>
		<form id="order_form" class="scroll-box" onsubmit="return false">
			<div class="popup__body">
				<input type="hidden" id="appl_id" name="appl_id" value="0">
				<input type="hidden" id="stud_id" name="stud_id" value="0">
				<input type="hidden" id="group_id" name="group_id" value="0">

				<div class="popup__steps-led" id="step_leds">
					<div data-led="1" class="progress"></div>
					<div data-led="2"></div>
					<div data-led="3"></div>
					<div data-led="4"></div>
					<div data-led="5"></div>
				</div>

				<div id="pp-step1" data-step="1" class="popup__step active">
					<div class="popup__lead lead-init">
						<strong>Лист ожидания</strong>
						<p>Вы подаете заявку на подбор группы. Администратор свяжется с вами, как только появится подходящий вариант.</p>
					</div>
					<div class="popup__lead lead-preview"></div>
					<div class="popup__row">
						<div class="popup__col2">
							<input type="text" class="form-control form-control-lg" id="first_name" name="first_name" required>
							<label for="first_name" class="placehold">Ваше имя</label>
						</div>
						<div class="popup__col2">
							<input type="text" class="form-control form-control-lg" id="last_name" name="last_name" required>
							<label for="last_name" class="placehold" class="placeholder">Ваша фамилия</label>
						</div>
						<div class="popup__col2">
							<input type="number" class="form-control form-control-lg" id="age" name="age" required>
							<label for="age" class="placehold">Возраст</label>
						</div>
						<div class="popup__col2">
							<input type="text" class="form-control form-control-lg" id="phone" name="phone" required>
							<label for="phone" class="placehold">Номер телефона</label>
						</div>
					</div>
					<div class="popup__footer">
						<button type="button" class="btn btn-lg btn-info w-100">Далее</button>
					</div>
				</div>

				<div id="pp-step2" data-step="2" class="popup__step">
					<div class="popup__lead">
						<strong>Когда вам удобно?</strong>
						<p>Выберите подходящие дни и время (можно несколько)</p>
					</div>
					<div class="popup__values">
						<div class="popup__row-title">Дни недели:</div>
						<div class="popup__row justify-content-between">
							<?php
							$coursetime = ["mon"=>"ПН","tue"=>"ВТ","wed"=>"СР","thu"=>"ЧТ","fri"=>"ПТ","sat"=>"СБ"];
							foreach ($coursetime as $k=>$r) {
								?>
								<div class="popup__col">
									<input type="checkbox" class="btn-check" id="btn-check-<?php echo $k?>" name="daysweek[]" value="<?php echo $r?>" autocomplete="off">
									<label class="btn btn-sm btn-likecheck" for="btn-check-<?php echo $k?>"><?php echo $r?></label>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<div class="popup__values">
						<div class="popup__row-title">Время начала:</div>
						<div class="popup__row justify-content-between">
							<?php
							$coursetime = ["9","10","11","12","13","14","15","16","17","18","19","20"];
							foreach ($coursetime as $t) {
								?>
								<div class="popup__col">
									<input type="checkbox" class="btn-check" id="btn-check-<?php echo $t?>" name="coursetime[]" value="<?php echo $t?>" autocomplete="off">
									<label class="btn btn-sm btn-likecheck" for="btn-check-<?php echo $t?>"><?php echo $t?>:00</label>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<div class="popup__footer">
						<button type="button" class="btn btn-lg btn-info w-100">Далее</button>
					</div>
				</div>

				<div id="pp-step3" data-step="3" class="popup__step">
					<div class="popup__lead">
						<strong>Несколько вопросов ...</strong>
						<p>Это поможет подобрать лучшую группу</p>
					</div>
					<div class="popup__values">
						<div class="popup__row-title">Кто будет учиться?</div>
						<div class="popup__row justify-content-between">
							<?php
							foreach ($who_will as $k=>$r) {
								?>
								<div class="popup__col">
									<input type="radio" class="btn-check" id="btn-check-<?php echo $k?>" name="whowill" value="<?php echo $k?>" autocomplete="off" required>
									<label class="btn btn-sm btn-likecheck" for="btn-check-<?php echo $k?>"><?php echo $r?></label>
								</div>
								<?php
							}
							?>
						</div>
					</div>

					<div class="popup__values">
						<div class="popup__row-title">Ваш текущий уровень?</div>
						<div class="popup__row flex-nowrap justify-content-between">
							<?php
							foreach ($my_level as $k=>$r) {
								?>
								<div class="popup__col">
									<input type="radio" class="btn-check" id="mylevel-check-<?php echo $k?>" name="mylevel" value="<?php echo $k?>" autocomplete="off" required>
									<label class="btn btn-sm btn-likecheck" for="mylevel-check-<?php echo $k?>"><?php echo $r[0]?></label>
									<small><?php echo $r[1]?></small>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					
					<div class="popup__footer">
						<button type="button" class="btn btn-lg btn-info w-100">Далее</button>
					</div>
				</div>

				<div id="pp-step4" data-step="4" class="popup__step">
					<div class="popup__lead">
						<strong>И напоследок...</strong>
						<p>Есть особые пожелания или вопросы?</p>
					</div>

					<div class="popup__row justify-content-between">
						<div class="popup__col">
							<textarea class="form-control" id="comment" name="comment" placeholder="Например: хочу заниматься только с носителем языка..."></textarea>
						</div>
					</div>

					<div class="popup__footer">
						<button type="button" class="btn btn-lg btn-info w-100">Завершить!</button>
					</div>
				</div>

				<div id="pp-step5" data-step="5" class="popup__step">
					<div class="popup__lead popup__lead--done">
						<strong>Заявка оформлена!</strong>
						<p class="pt-3">Мы получили все данные. Администратор свяжется с вами в ближайшее время для подтверждения.</p>
					</div>
					<div class="popup__footer text-center">
						<button type="button" class="btn  btn-secondary w-75">Закрыть</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>