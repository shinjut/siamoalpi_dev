<?php 

/** RIDIMENSIONA LE IMMAGINI PER ALGOLIA
 * 
 * Script da attivare con un CronJob in modo che le immagini
 * siano pronte quando l'altro script che generea il json (sempre per algolia)
 * non debba stare a ridimensionare 1000+ immagini. In pratica riduco il load del server.
*/



	// prepare il contenuto del json, con le schede aventi almento una immagine
	$json = '[';
	$schede = $pages->find("template=gestionale_scheda, limit=20, sort=random, stato_avanzamento!=2593");
	$fotoFinalWidth = 260; // larghezza delle immagini per l'output di algolia (260 e' la variazione creata da lister (tabella) del backend) // da modificare poi con 600px?
	foreach ($schede as $scheda) {
		if (count($scheda->immagini)) {
			
			// immagine 
				// check if there is our variation
				$optionsVariations = array('width' => $fotoFinalWidth);
				$nVariations = $scheda->immagini->first->getVariations($optionsVariations);
				if ( count($nVariations) != 1) {
					$newImage = $scheda->immagini->first->width($fotoFinalWidth);

					// update scheda
					$scheda->of(false);
					$scheda->sync->fotoready = 1;
					$scheda->save('sync');

					// echo $newImage->httpUrl;
					// echo "<br>";
				} else{
					// echo "scheda gia' pronta";
					// echo "<br>";
				}
			}
		}
	exit;


?>