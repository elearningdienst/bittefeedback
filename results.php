<?php 
	if (!$included) {
		exit;
	}
?>
<section>
	<style>
		
		.star {
			font-size: 16pt;
		}
	</style>
<?php 
	$result = $db->query('SELECT p.id, p.title, p.speaker, p.link, COUNT(pq.id)
		FROM presentations p
		INNER JOIN presentation_questions pq ON pq.presentationid = p.id
		WHERE secret = "' . $db->escape_string($_GET['secret']) . '"')
		or die('Bitte versuche es in wenigen Minuten erneut.');
	
	$row = $result->fetch_row();
	if ($row[0] === NULL) {
		die('Dieser Link ist ungültig. <a href="./">Zurück zur Homepage</a>');
	}

	$presentationid = $row[0];
	$title = $row[1];
	$speaker = $row[2];
	$link = $row[3];
	$questionCount = $row[4];

	echo '<h1>Eingereichtes Feedback zu <i>' . htmlspecialchars($title) . '</i></h1>';
	
	echo "<br><br>";

	if (isset($_GET['question'])) {
		$question_n = intval($_GET['question']);
	}
	else {
		$question_n = 0;
	}

	if ($question_n > 0) {
		echo "<input type=button value='Vorherige Frage' onclick='location=\"?secret="
			. htmlspecialchars($_GET['secret']) . "&question=" . ($question_n - 1) . "\";'>";
	}

	if ($question_n < $questionCount - 1) {
		echo "<input type=button value='Nächste Frage' onclick='location=\"?secret="
			. htmlspecialchars($_GET['secret']) . "&question=" . ($question_n + 1) . "\";'>";
	}

	echo "<br><br>";

	$dbSecret = $db->escape_string($_GET['secret']);
	$result = $db->query("SELECT pq.question, pq.type, pqr.response
		FROM presentations p
		LEFT JOIN presentation_feedback pf ON pf.presentationid = p.id
		LEFT JOIN presentation_question_responses pqr ON pqr.feedbackid = pf.id
		LEFT JOIN presentation_questions pq ON pq.sequenceNumber = pqr.sequenceNumber AND pq.presentationid = p.id
		WHERE p.secret = '$dbSecret' AND pqr.sequenceNumber = $question_n
		ORDER BY pf.id
		") or die('Database error 71598.');

	if ($result->num_rows == 0) {
		die('Noch keine Antworten');
	}
	$responses = [];
	$avg = 0;
	$nonBlankResponses = 0;
	while ($row = $result->fetch_row()) {
		list($question, $type, $response) = $row;
		$responses[] = $response;
		if ($type == 1 && $response != -1) {
			// 5-star rating
			$avg += intval($response);
			$nonBlankResponses++;
		}
	}

	echo "Frage " . ($question_n + 1) . ": '<i>" . htmlspecialchars($question) . "</i>'<br>";

	if ($type == 1) {
		// 5-star rating
		$avg = round($avg / $nonBlankResponses * 10) / 10;
		echo "Durchschnittliche Bewertung: " . $avg . " von 5. Individuelle Bewertungen:<br>";
		$wstar = '&#9734;'; // white star
		$bstar = '&#9733;'; // black star
		foreach ($responses as $n=>$response) {
			echo "#$n: ";
			$response = intval($response);
			$i = 0;
			while ($i < 5) {
				echo "<span class='star'>" . ($response - 1 >= $i ? $bstar : $wstar) . "</span>";
				$i++;
			}
			if ($response === -1) {
				echo " (blank)";
			}
			echo "<br>";
		}
	}
	
	else if ($type == 2) {
		foreach ($responses as $n=>$response) {
			if (empty($response)) {
				$response = '<i>(blank)</i>';
			}
			else {
				$response = htmlspecialchars($response);
			}
			echo "#$n: $response<hr>";
		}
	}
?>
</section>
