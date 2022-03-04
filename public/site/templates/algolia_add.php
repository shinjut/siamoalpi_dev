<?php 

/** MOTORE CRUD SCHEDE CON ALGOLIA
 * 
 * A. La pagina crea un file json che viene poi inviato ad Algolia
 * B. Cancella le schede con stato di lavorazione ELIMINA
 * 
 * L'id dei record Algolia un prefisso "sa" per identificare le schede aggiunte da Siamo Alpi.
 * Mentre per i record OPAC il prefisso e'  "op".
 * 
 * le immagini vanno prima ridotte a dimensione per l'output e poi consegnate ad Algolia
 * Le immagini le prepara un altro script, attivato con Cron, in "gestionale_algolia-imageresize.php"
 * 
 * Le coordinate geocoding viene elaborato da uno script che chiede tramite google API le coordinate 
 * coi valori importati precedentemente dal Sirbec. Per questo aggiorno le schede che sono state
 * sincronizzate AND geo taggate
 *  
 * Lo script php di algolia e' inserito tramite Composer
*/

// 0 controlla lo stopper se e' attivo, se lo e' blocca tutto
	$error = ($page->counter->stop) ? true : false;
	$jsonName = "algolia.json";
	$filePath = $config->paths->assets . $jsonName;

// 1 prepara la query di ricerca schede

	// selector per trovare le schede da esportare in algolia
	$selector = "template=gestionale_scheda, immagini.count>=1";

	// stato_avanzamento: 1109 in lavorazion, 1111 approvata, 1112 esportata, 2593 eliminata
	$selector .= ", stato_avanzamento!=2593";
	if (!$page->counter->reset) {
		// $debugTimestamp = $page->timestamp - (60 * 60 * 2);
		// $selector .= ", (created|modified>=$page->timestamp), (sync.sirbec=1, sync.geocoding=1, sync.fotoready=1) "; // PRODUCTION
		$selector .= ", (created|modified>=$page->timestamp), (sync.sirbec=1, sync.geocoding=1) ";
	}
	// DEBUG only
	// $selector .= ", limit=50 ";

	// prepare il contenuto del json
	$json = '';
	if (!$error) {
		$schede = $pages->find($selector);
		$fotoFinalWidth = 260; // larghezza delle immagini per l'output di algolia (260 e' la variazione creata da lister (tabella) del backend) // da modificare poi con 600px?

		$jsonBuild = array();

		foreach ($schede as $scheda) {
			$record = array();
				
				// immagine 
					// check if there is our variation
					// PRODUCTION
						/* 
						$optionsVariations = array('width' => $fotoFinalWidth);
						$nVariations = $scheda->immagini->first->getVariations($optionsVariations);
						if ( count($nVariations) === 1) {
							$immagineUrl = $nVariations->first->httpUrl;
						}else{
							$immagineUrl = $scheda->immagini->first->width($fotoFinalWidth)->httpUrl;
						}*/

					// TEMP (liste mi da' 260 per vertical e 260 orizzontali... non penso noi dovremo distinguere tra i due casi. Per ora prendo quello che c'e').
						$nVariations = $scheda->immagini->first->getVariations();
						if (count($nVariations) >= 1) {
							$immagineUrl = $nVariations->last->httpUrl;
						}else{
							$immagineUrl = $scheda->immagini->first->width($fotoFinalWidth)->httpUrl;
						}

				// tema
					$temi = array();
						foreach ($scheda->tema as $tema) {
							$temi[] = $tema->title;
						}

				// tags
					$tags = array();
						foreach ($scheda->tags as $tag) {
							$tags[] = $tag->title;
						}

				// valutazione
					/* assegnamo pesi diversi per i due tipi di valutazione della scheda (etnografica / grafica) dando piu' rilievo alla grafica */
					$voto = ($scheda->valutazione_etnografica->codice) + ($scheda->valutazione_estetica->codice * 2);

				// datazione - sirbec dependant 
					// per avere solo un valore faccio la media dei due anni ...
					$annox = '';
					$anno_start = '';
					$anno_end = '';
					if ($scheda->datazione->anno) {
						// controlla che ci sia solo l'anno e non la data
						if (strstr($scheda->datazione->anno, "/")) {
							$datax = explode('/', $scheda->datazione->anno);
							$anno_start = $datax[2];
						}else{
							$anno_start = $scheda->datazione->anno;
						}
					}

					if ($scheda->datazione->anno_fine) {
						// controlla che ci sia solo l'anno e non la data
						if (strstr($scheda->datazione->anno_fine, "/")) {
							$datax = explode('/', $scheda->datazione->anno_fine);
							$anno_end = $datax[2];
						}else{
							$anno_end = $scheda->datazione->anno_fine;
						}
					}

					if ($anno_end) {
						$annox = ($anno_start + $anno_end) / 2 ;
					}else{
						$annox = $anno_start;
					}

				// luogo - sirbec dependant 
					$geo = (object) array('lat'=> floatval($scheda->mappa->lat), 'lng'=> floatval($scheda->mappa->lng));
					$comune = $scheda->luogo->comune;

				
				// prepare il json
					$record['objectID'] = "sa".$scheda->id ;
					$record['titolo'] = $sanitizer->markupToLine($scheda->title) ;
					$record['descrizione'] = $sanitizer->markupToLine($scheda->descrizione) ;
					$record['immagine'] = $immagineUrl ;
					$record['url'] = 'https://siamoalpi.it/archivio/scheda/?id='.$scheda->id ;
					$record['ente'] = $scheda->parent->title;
					$record['temi'] = $temi ;
					$record['tags'] = $tags ;
					$record['voto'] = $voto ;
					$record['datazione'] = intval($annox) ;
					$record['comune'] = $comune ;
					$record['_geoloc'] = $geo ;	

			$jsonBuild[] = $record;

			// modifica status scheda da sync con sirbec
				if ($scheda->sync->sirbec) {
					$scheda->of(false);
					$scheda->sync->sirbec = '';
					$scheda->save();
				}
		}

		$nSchedePronte = count($jsonBuild);
		$json = json_encode($jsonBuild);
	}

// 2. check if it's all valid. Scrivi il json
	if ($json) {
		$result = json_decode($json);
		switch (json_last_error()) {
		       case JSON_ERROR_NONE:
		           $error = ''; // JSON is valid // No error has occurred
		           break;
		       case JSON_ERROR_DEPTH:
		           $error = 'The maximum stack depth has been exceeded.';
		           break;
		       case JSON_ERROR_STATE_MISMATCH:
		           $error = 'Invalid or malformed JSON.';
		           break;
		       case JSON_ERROR_CTRL_CHAR:
		           $error = 'Control character error, possibly incorrectly encoded.';
		           break;
		       case JSON_ERROR_SYNTAX:
		           $error = 'Syntax error, malformed JSON.';
		           break;
		       // PHP >= 5.3.3
		       case JSON_ERROR_UTF8:
		           $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
		           break;
		       // PHP >= 5.5.0
		       case JSON_ERROR_RECURSION:
		           $error = 'One or more recursive references in the value to be encoded.';
		           break;
		       // PHP >= 5.5.0
		       case JSON_ERROR_INF_OR_NAN:
		           $error = 'One or more NAN or INF values in the value to be encoded.';
		           break;
		       case JSON_ERROR_UNSUPPORTED_TYPE:
		           $error = 'A value of a type that cannot be encoded was given.';
		           break;
		       default:
		           $error = 'Unknown JSON error occured.';
		           break;
		   }
		if (!$error) {
			// URL 
			$algoliaURL = $config->urls->httpAssets . $jsonName;
			echo $algoliaURL; // controlla che sia tutto OK
			$algoliaJson = fopen("$filePath", "w");
			fwrite($algoliaJson, $json);
			fclose($algoliaJson);
		}else{
			echo "ERRORE!: ";
			// /* test / DEBUG -- controlla quello che viene scritto in modo da trovare l'errore */
			// echo $error;
			// $algoliaJson = fopen("$filePath", "w");
			// fwrite($algoliaJson, $json);
			// fclose($algoliaJson);

			$mail = wireMail();
			$mail->sendSingle(true);
			$mail->to('admin@siamoalpi.it'); 
			$mail->subject("Problema con Algolia, pagina: $page->name");
			$mail->body($error);
			$mail->send();
		}
	}

// 3. aggiorna questa pagina
	if (!$error) {
		$page->of(false);
		$page->timestamp = time();
		$page->counter->records = $nSchedePronte;
		$page->save();
	}

// 4. manda tutto ad algolia
	//if ($error == "pippo") { // DEBUG
	if (!$error) {

	$client = \Algolia\AlgoliaSearch\SearchClient::create('NK1J7ES7IV', '15310a01b90b40aa75122bf82fec47d9');
	$index = $client->initIndex('siamoAlpi');

	$records = json_decode(file_get_contents("$algoliaURL"), true);

	$index->saveObjects($records, ['autoGenerateObjectIDIfNotExist' => true]);

	}


exit;


/**
 * CAMPI template
 * 
	|===========|=============================|
	| title     |                             |
	| timestamp | datetime                    |
	| counter   | cicli, records, reset, stop |
	|           |                             |
 *
 * 
*/
?>