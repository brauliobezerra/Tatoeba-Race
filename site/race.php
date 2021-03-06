<?php
	require_once('dao/dao.php');
	require_once('util/lang.php');

	$QTD_SENTENCES = 5;

	if ( isset($_GET['n_sentences']) )
		$QTD_SENTENCES = (int)($_GET['n_sentences']);
	if ( 1 > $QTD_SENTENCES || $QTD_SENTENCES > 25 )
		$QTD_SENTENCES = 5;
	
	if ( isset($_GET['lang']) )
		$lang = $_GET['lang'];
	else
		$lang = 'eng';
	
	if ( isset($_GET['lang_to']) )
		$langTo = $_GET['lang_to'];
	else
		$langTo = null;
	
	$sentences = array($QTD_SENTENCES);
	for ($i = 0; $i < $QTD_SENTENCES; $i++) {
		if ( $langTo == null )
			$sentences[$i] = getRandomSentence($lang);
		else
			$sentences[$i] = getRandomSentenceAndTranslation($lang, $langTo);
	}

	$text = "";
	for ( $i = 0; $i < $QTD_SENTENCES; $i++ ) {
		if ( ! isset($langTo ) ) {
			if ( $i > 0 )
				$text .= getSeparator($lang);
			$text .= $sentences[$i]->text;
		} else {
			if ( $i > 0 )
				$text .= getSeparator($langTo);
			$text .= $sentences[$i][0]->text;
			$text .= getSeparator($lang);
			$text .= $sentences[$i][1]->text;
		}
	}

	// cookies to remember the language choices

	$expire = time() + 30*24*60*60; // 30 days

	setCookie('lang', $lang, $expire);
	if ( isset($langTo) )
		setCookie('langTo', $langTo, $expire);
?>

<?php include_once('include/top.php'); ?>

<script type="text/javascript" src="/script/prototype.js" ></script>
<script type="text/javascript" src="/script/race.js" ></script>

<!-- Initialization script -->
<script type="text/javascript">
	Event.observe(window, 'load', function() {
		$('keyboardInput').value = '';
		race.text = $('text').textContent;
		race.startCountDown();
	});
</script>

<div id="content">
	<p style="text-align: center;">
		<img src="img/<?= $lang ?>.svg" />
		<?php if ( isset($langTo) ) { ?>
			&rarr; <img src="img/<?= $langTo ?>.svg" />
		<?php } ?>
	</p>
	<p id="textP" dir="<?= getDirection($_GET['lang']) ?>">
		<span id="text"><?= nl2br($text) ?></span>
	</p>
	<p>
		<input id="keyboardInput" type="text" disabled="disabled" style="width: 100%;" dir="<?= getDirection($_GET['lang']) ?>"/>
	</p>
	<p>
		<button id="btnPlayAgain" onclick="window.location = window.location" style="display: none;">Play again</button>
	</p>
	<p><label id="countDown"></label></p>
	<p><label id="time"></label></p>
	<p><label id="speed"></label></p>
	<p>
		Change nº of sentences:
		<?php
		foreach ( array(1, 2, 3, 4, 5, 10, 15, 20, 25) as $i ) {
			if ( $langTo == null )
				echo "<a href=\"race.php?lang={$lang}&amp;n_sentences={$i}\" >{$i}</a> ";
			else
				echo "<a href=\"race.php?lang={$lang}&amp;lang_to={$langTo}&amp;n_sentences={$i}\" >{$i}</a> ";
		}
		?>
	</p>
	<p>
		<label id="sentenceLinks"><?php
			for ($i = 0; $i < $QTD_SENTENCES; $i++) {
				if ($langTo == null) {
					echo "<a href='http://tatoeba.org/sentences/show/{$sentences[$i]->id}' title='{$sentences[$i]->text}'>{$sentences[$i]->id}</a> ";
				} else {
					echo "<a href='http://tatoeba.org/sentences/show/{$sentences[$i][0]->id}' title='{$sentences[$i][0]->text}'>{$sentences[$i][0]->id}</a> ";
					echo "<a href='http://tatoeba.org/sentences/show/{$sentences[$i][1]->id}' title='{$sentences[$i][1]->text}'>{$sentences[$i][1]->id}</a> ";
				}
			}
		?></label>
	</p>
</div>

<?php include_once('include/bottom.php'); ?>
