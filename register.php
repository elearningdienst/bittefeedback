<?php 
	if (!$included) {
		exit;
	}
?>

<?php 
	if (isset($_POST['name'])) {
		$code = bin2hex(openssl_random_pseudo_bytes(3));
		$secret = bin2hex(openssl_random_pseudo_bytes(5));
		$title = $db->escape_string($_POST['title']);
		$speaker = $db->escape_string($_POST['name']);
		$link = $db->escape_string($_POST['link']);
		$time = time();
		$db->query("INSERT INTO presentations (code, secret, datetime, title, speaker, link) VALUES('$code', '$secret', $time, '$title', '$speaker', '$link')") or die('Database error 148');

		$id = $db->insert_id;
		$types = [];
		foreach ($_POST['type'] as $type) {
			$types[] = $type;
		}
		$questionNumber = 0;
		foreach ($_POST['question'] as $question) {
			$type = intval($types[$questionNumber]);
			$question = $db->escape_string($question);
			$db->query("INSERT INTO presentation_questions (presentationid, sequenceNumber, question, type) VALUES($id, $questionNumber, '$question', $type)") or die('Database error 4184');
			$questionNumber++;
		}

		$publink = "https://bittefeedback.de/?code=$code";
		$privlink = "https://bittefeedback.de/?secret=$secret";
		$email = "<h1>Super, das hat geklappt!</h1><p><strong>Bitte notiere Dir die folgenden Angaben gut. Wenn Du sie verlierst, hast Du keinen Zugriff zu Deinem Feedback.</strong></p><h3>Code für Dein Feedback-Formular: $code </h3>(Direkter Link: <a href='$publink'>$publink</a>)<br><br>"
			. "<h3>Link zu den Feedback-Ergebnissen: <a href='$privlink'>$privlink</a></h3>Entscheide selbst, ob Du den Ergebnislink ebenfalls teilen oder nur für Dich behalten willst.";

		if (!empty($_POST['email'])) {
			$addr = md5($_POST['email'] . round($time / 60));
			$t = time();
			$db->query("INSERT INTO presentation_maillimit (emailaddress, datetime)
				VALUES('$addr', $t)")
				or die('Error 51. Did you email to this address within the last minute? If so, try again in a minute.');
			if (!mail($_POST['email'], 'Your presentation feedback codes', $email,
					"Content-Type: text/html\r\nFrom: " . $email_from)) {
				echo "Sending the email failed.";
			}
			else {
				echo 'A copy of this page has been emailed to you.<br><br>';
			}
		}

		echo nl2br($email);
	}
	else {
		?>
			
			<form method=post>
			<h1>Informationen für Dein Feedback-Formular</h1>
			<h3>Rahmendaten</h3>
			<p>Titel Deiner Präsentation/ Name der Veranstaltung<br>
			<input name=title value="<?php echo htmlspecialchars($_POST['title']); ?>" maxlength=255><br>
			</p>
			<p>Dein Name<br>
			<input name=name maxlength=255><br>
			</p>
<p>Weiterführender Link z.B. zu Deiner Website, Deinem Twitter-Account ... (optional, wird Feedback-Gebenden nach dem Ausfüllen des Fedback-Formulars angezeigt)<br>
			<input name=link maxlength=255><br>
			</p>

			<h3>Fragen</h3>
			<p>Du kannst zwischen zwei Fragetypen auswählen: Sternen-Bewertung oder offene Beantwortung als Textfeld.
			<div id="questions"></div>
			<input type=button value='Frage hinzufügen' onclick='addQuestion();'><br>
			</p>
			<hr>

<p>Mit Klick auf 'Erstellen' wird Dein Feedback-Formular veröffentlicht. Es ist für 14 Tage gültig und wird danach gelöscht.</p>

			
			<p>
			<input type=submit value=Erstellen></p>

		<script>
			function $(q) {
				return document.querySelector(q);
			}
			function addQuestion() {
				qcounter++;
				var question = document.createElement('div');
				question.innerHTML = 'Frage ' + qcounter.toString() + '<br><input maxlength=255 name="question[]"><br>'
					+ '<select name="type[]"><option value=1>Sterne vergeben</option><option value=2>Textfeld</option></select><br><br>';
				$("#questions").appendChild(question);
			}
			qcounter = 0;
			addQuestion();
		</script>
		<?php 
	}
?>

