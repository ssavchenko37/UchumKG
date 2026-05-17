<?php
/** @var array $tldata */
/** @var string $userName */
/** @var string $sCore */
/** @var string $sMod */
/** @var string $last */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<title>Учим КГ</title>
	<meta charset="utf-8">
	<meta name="keywords" content="">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<link rel="shortcut icon" href="/images/favicons/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="/images/favicons/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/images/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192" href="/images/favicons/android-chrome-192x192.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/images/favicons/android-chrome-96x96.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/images/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/images/favicons/favicon-16x16.png">
	<link rel="manifest" href="/images/favicons/manifest.webmanifest">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="images/favicons/mstile-144x144.png">
	<meta name="msapplication-config" content="images/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/assets/css/all.min.css">
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/flatpickr.min.css">
	<link rel="stylesheet" href="/assets/css/main.css?v=<?php echo time()?>">
</head>
<body>
	<header class="header">
		<div class="container-fluid">
			<div class="header__wrap">
				<div class="header__lside">
					<div class="header__nav-toggle">
						<button type="button" class="btn" id="nav-toggle"><span><em></em></span></button>
					</div>
					<div class="header__logo">
						<img src="/images/logo.png" alt="Logo">
					</div>
				</div>
				<div class="header__rside">
					<ul class="navbar-nav navbar-nav-icons flex-row align-items-center">
						<li class="nav-item ps-2 pe-0 dropdown">
							<a class="nav-link d-flex align-items-center dropdown-toggle pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="header__user"><?php echo $userName?></span>
							</a>
								
							<div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
								<div class="bg-white dark__bg-1000 rounded-2 py-2">
									<strong class="dropdown-item"><?php echo $userName?></strong>
									<div class="dropdown-divider"></div>
									<?php if ( $tldata['umod'] == "t") { ?>
										<a class="dropdown-item" href="/t/profile">Профиль</a>
										<a class="dropdown-item" href="/t/avatar">Изменить фото</a>
										<a class="dropdown-item" href="/t/password/">Изменить пароль</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="/t/settings/">Настройки</a>
									<?php } ?>
									<?php if ( $tldata['umod'] == "s") { ?>
										<a class="dropdown-item" href="/s/profile">Профиль</a>
										<a class="dropdown-item" href="/s/avatar">Изменить фото</a>
										<a class="dropdown-item" href="/s/password/">Изменить пароль</a>
										<div class="dropdown-divider"></div>
									<?php } ?>
									<a class="dropdown-item" href="/logout/">Выйти</a>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</header>
	<main class="main">
		<div class="main__wrap container-fluid" data-layout="container">
			<div class="main__nav">
				<div class="navspace">
					<ul class="nav" id="navbarVerticalNav">
						<li class="nav__item">
							<a class="nav__link" href="/" role="button">
								<span class="nav__icon"><i class="fa-solid fa-house"></i></span>
								<span class="nav__text">Главная</span>
							</a>
						</li>

						<?php
						if (is_file("_nav/" . $tldata['umod'] . ".php")) {
							include "_nav/" . $tldata['umod'] . ".php";
						} else {
							include "_nav/default.php";
						}
						?>
						<li class="nav__label mt-4">
							<span></span>
						</li>
						<li class="nav__item">
							<a class="nav__link" href="/logout/" role="button">
								<span class="nav__icon"><i class="fa-solid fa-right-from-bracket"></i></span>
								<span class="nav__text">Выйти</span>
							</a>
						</li>
					</ul>
				</div>	
			</div>
			<div class="main__work">
				<div id="workspace" class="ws">
					<?php
					// p(S_ROOT . "/_core/" .$sMod . "/" . $sCore . "/view.php");
					if (is_file(S_ROOT . "/_core/" . $sMod . "/" . $sCore . "/view.php")) {
						include S_ROOT . "/_core/" .$sMod . "/" . $sCore . "/view.php";
					}
					// p(S_ROOT . "/_core/" . $sCore . "/view.php");
					if (is_file(S_ROOT . "/_core/" . $sCore . "/view.php") && $globalMode) {
						include S_ROOT . "/_core/" . $sCore . "/view.php";
					}
					include 'aside.php';
					?>
				</div>
			</div>
		</div>
	</main>

	<script>const pathlast = '<?php echo $last?>'</script>
	<script src="/assets/js/bootstrap.bundle.js"></script>
	<script src="/assets/js/all.min.js"></script>
	<script src="/assets/js/flatpickr.js"></script>
	<script src="/assets/js/flatpickr_ru.js"></script>
	<script src="/assets/js/main.js?v=<?php echo time()?>"></script>
	<?php if (is_file(S_ROOT . "/_core/" . $sCore . "/script.js") && $globalMode) { ?>
	<script src="<?php echo "/_core/" . $sCore . "/script.js?v=" . time()?>"></script>
	<?php } ?>
	<?php if (is_file(S_ROOT . "/_core/" . $sMod . "/" . $sCore . "/script.js")) { ?>
	<script src="<?php echo "/_core/" . $sMod . "/" . $sCore . "/script.js?v=" . time()?>"></script>
	<?php } ?>
  </body>
</html>