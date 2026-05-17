<?php
/** @var array $sPath */
/** @var array $tldata */
/** @var string $sCore */
/** @var string $sMod */
/** @var string $last */
/** @var bool $globalMain */
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
	<link rel="shortcut icon" href="images/favicons/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="images/favicons/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="180x180" href="images/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192" href="images/favicons/android-chrome-192x192.png">
	<link rel="icon" type="image/png" sizes="96x96" href="images/favicons/android-chrome-96x96.png">
	<link rel="icon" type="image/png" sizes="32x32" href="images/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="images/favicons/favicon-16x16.png">
	<link rel="manifest" href="images/favicons/manifest.webmanifest">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="images/favicons/mstile-144x144.png">
	<meta name="msapplication-config" content="images/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
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
	<main class="main innex">
		<div class="innex__bg"></div>
		<div class="page-nav">
			<div class="page-nav__item">
				<div class="page-nav__logo">
					<img src="/images/logo.png" alt="Logo">
				</div>
				<?php if (!empty( $sPath[1] )) { ?>
					<a href="/" class="page-nav__link"><i class="fa-solid fa-angle-left"></i></a>
				<?php } ?>
			</div>
			<div class="page-nav__item">
				<?php if ($sPath[1] === "public-offer") { ?>
					<button type="button" class="page-nav__link"><i class="fa-solid fa-magnifying-glass"></i></button>
				<?php } ?>
				<?php if ($sPath[1] === "signup") { ?>
					<a href="/public-offer/" class="page-nav__link"><i class="fa-solid fa-handshake-simple"></i></a>
				<?php } ?>
				<?php if ($sPath[1] === "trainings") { ?>
					<a href="/signup/" class="page-nav__link"><i class="fa-solid fa-arrows-down-to-people"></i></a>
					<a href="/public-offer/" class="page-nav__link"><i class="fa-solid fa-handshake-simple"></i></a>
				<?php } ?>
				
				<?php if (!empty( $sPath[1] )) { ?>
					<button type="button" class="page-nav__link"><i class="fa-regular fa-circle-question"></i></button>
				<?php } ?>
				<?php if (empty( $sPath[1] )) { ?>
					<a href="/login/" class="page-nav__link"><i class="fa-solid fa-user"></i></a>
				<?php } ?>
				
			</div>
		</div>
		<div class="container">
			<div class="row main-row">
				<?php
				if (is_file(S_ROOT . "/_core/" . $sMod . "/" . $sCore . "/view.php")) {
					include S_ROOT . "/_core/" .$sMod . "/" . $sCore . "/view.php";
				}
				if (is_file(S_ROOT . "/_core/" . $sCore . "/view.php") && $globalMode) {
					include S_ROOT . "/_core/" . $sCore . "/view.php";
				}
				?>
			</div>
		</div>
	</main>

	<script>const pathlast = '<?php echo $last?>'</script>
	<script src="/assets/js/bootstrap.bundle.js"></script>
	<script src="/assets/js/all.min.js"></script>
	<script src="/assets/js/flatpickr.js"></script>
	<script src="/assets/js/flatpickr_ru.js"></script>
	<script src="/assets/js/main.js?v=<?php echo time()?>"></script>
	<?php if (is_file(S_ROOT . "/_core/" . $sCore . "/script.js") && $globalMain) { ?>
	<script src="<?php echo "/_core/" . $sCore . "/script.js?v=" . time()?>"></script>
	<?php } ?>
	<?php if (is_file(S_ROOT . "/_core/" . $sMod . "/" . $sCore . "/script.js")) { ?>
	<script src="<?php echo "/_core/" . $sMod . "/" . $sCore . "/script.js?v=" . time()?>"></script>
	<?php } ?>
  </body>
</html>