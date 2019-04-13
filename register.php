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
        //Change the links to meet your own domain
		$publink = "http://feedback.elearningdienst.de/?code=$code";
		$privlink = "http://feedback.elearningdienst.de/?secret=$secret";
        //This QR-Link uses the tool qr https://github.com/TheMisir/qr you have to edit the preferences to meet your own installation.
        //Feel free to use the server https://qr.elearningdienst.de but remember to change the last part of the string to meet your feedback server domain
        $qrlink = "https://qr.elearningdienst.de/#text=http%3A%2F%2Ffeedback.elearningdienst.de%2F%3Fcode%3D$code";

              function shortenURL($publink) {
                        //This Code comes original from YOURLS, look at sample-remote-api-call.txt 
                        // EDIT THIS: the query parameters

                        $signature = 'yoursignaturehere';              // API Key 
                        $keyword = '';                                 // optional keyword
                        $title   = 'Feedback-Kurzlink';                // optional, if omitted YOURLS will lookup title with an HTTP request
                        $format  = 'simple';                           // output format: 'json', 'xml' or 'simple'

                        // EDIT THIS: the URL of the API file
                        $api_url = 'http://short.dom/yourls-api.php';

                        // Init the CURL session
                        $ch = curl_init();
                        curl_setopt( $ch, CURLOPT_URL, $api_url );
                        curl_setopt( $ch, CURLOPT_HEADER, 0 );            // No header in the result
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // Return, do not echo result
                        curl_setopt( $ch, CURLOPT_POST, 1 );              // This is a POST request
                        curl_setopt( $ch, CURLOPT_POSTFIELDS, array(      // Data to POST
                        'url'      => $publink,
                        //'keyword'  => $keyword,
                        'title'    => $title,
                        'format'   => $format,
                        'action'   => 'shorturl',
                        'signature'=> $signature
                        ) );

                        // Fetch and return content
                        $shortURL = curl_exec($ch);
                        curl_close($ch);

                        // Return the result.  
                        return ($shortURL); 
                        }
              
              //Short Public Link
              $shortURL=shortenURL($publink);
              //Short Private Link
              $shortprivateURL=shortenURL($privlink);

              $Part1 = "<h1>Super, das hat geklappt!</h1><p>" .
                         "<strong>Bitte notiere Dir die folgenden Angaben gut. Wenn Du sie verlierst, hast Du keinen Zugriff zu Deinem Feedback.</strong></p>";
              $Part2 = "<h3>Code für Dein Feedback-Formular: <strong>$code</strong></h3>" .
                         " <strong>Direkter Link:</strong> <a href='$publink'>$publink</a><br>" .
                         " <strong>Kurzer Link:</strong> <a href='$shortURL'>$shortURL</a><br>" .
                         "<strong>Link als QR-Code (öffnet in neuer Seite):</strong> <a href='$qrlink' target=\"_blank\">hier klicken</a><br><br>";
              $Part3 =   "<h3>Link zu den Feedback-Ergebnissen:</h3>" .
                         "<strong>Direkter Link:</strong> <a href='$privlink'>$privlink</a><br>" .
                         "<strong>Kurzer Link:</strong> <a href='$shortprivateURL'>$shortprivateURL</a><br><br>" .
                         "<strong>Entscheide selbst, ob Du den Ergebnislink ebenfalls teilen oder nur für Dich behalten willst.</strong>";

                //Ab hier Darstellung der "Abschlussseite"
                echo $Part1;
                echo $Part2;
                //Hier könnte man sich direkt den QR-Code des Kurzlinks anzeigen lassen
                // Der QR Code wurd über das YOURLS erzeugt (es ist ein entsprechendes Plugin erfoderlich)
                //$qrcodelink= $shortURL.".qr";
		        //echo '<img src="'.$qrcodelink.'" alt="">';
                echo $Part3;

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
