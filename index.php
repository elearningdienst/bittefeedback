<!DOCTYPE HTML>
<!--
	Story by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>BitteFeedback - offenes Online-Feedback-Tool</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

		<!-- Wrapper -->
			<div id="wrapper" class="divided">

				<!-- One -->
					<section class="banner style2 content-align-left fullscreen">
						<div class="content">
							
<p><?php 
			$included = true;
			if (isset($_POST['title']) || isset($_GET['code']) || isset($_GET['secret'])) {
				require('config.php');
				require('dbsetup.php');
			}

			if (isset($_POST['title'])) {
				require('register.php');
			}
			else if (isset($_GET['code'])) {
				require('feedback.php');
			}
			else if (isset($_GET['secret'])) {
				require('results.php');
			}
			else {
				require('frontpage.php');
			}
		?></p>
							
						</div>
						<div class="image">
							<img src="images/back.jpg" alt="" />
						</div>
					</section>

				

				<!-- Footer -->
					<footer class="wrapper style1 align-center">
						<div class="inner">
					
<p>Mit BitteFeedback.de kannst Du einfach und unkompliziert Feedback geben oder erfragen. Es werden keine persönlichen Daten erhoben. Eine Registrierung ist nicht erforderlich. Alle Eintragungen werden nach 14 Tagen gelöscht. Nähere Informationen findest Du im <a href=/impressum.html>Impressum und den Hinweisen zum Datenschutz</a>		
							<p><small>BitteFeedback.de beruht auf freier Software. Der Code stammt von <a href="http://lgms.nl/" target="blank">Luc Gommans</a>. Du findest ihn <a href="https://github.com/lgommans" target="blank">bei Github</a>. Diese Installation habe ich für meine persönliche Nutzung im <a href="https://ebildungslabor.de target="blank">eBildungslabor</a> online gestellt und dazu den Code leicht modifiziert, ins Deutsche übersetzt und mithilfe eines Templates von <a href="https://html5up.net">HTML5 UP</a> gestaltet. Du findest <a href=>hier die angepasste Version</a></small> - z.B. für eine eigene Installation.</p>
						</div>
					</footer>

			</div>

		<!-- Scripts -->
			
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>
