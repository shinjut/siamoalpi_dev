<?php 
/** Aggiorna le coordinate delle schede interrogando Google Maps
 * info qui: https://developers.google.com/maps/documentation/geocoding/requests-geocoding?hl=it#json
 *  
 * 
 * */


if (!$page->counter->stop) {  

    // creata apposita chiave per accedere via cronjob/server (limitata da IP)
    $key = $keyGmapsGeolocation; // spostao in config.php
    $googleUrl = "https://maps.googleapis.com/maps/api/geocode/";
   
    // cerca le schede
    $schede = $pages->find("template=gestionale_scheda, stato_avanzamento=1112, sync.sirbec=1, sync.geocoding!=1, sync.map_desync!=1, limit=15 ");
    if (count($schede)) {
        //echo "schede: " . count($schede);
        foreach ($schede as $scheda) {
            // prendi la localita'
            $indirizzo = '';
            if ($scheda->luogo->localita) {
                $indirizzo .= $scheda->luogo->localita . ", " ; 
            }elseif ($scheda->luogo->comune) {
                $indirizzo .= $scheda->luogo->comune . ", "; 
            }
            if ($indirizzo) {
                $indirizzo .= " SO, Italia";
                $indirizzo = urlencode($indirizzo);

                //star query
                $query = "{$googleUrl}json?address={$indirizzo}&key={$key}";

                $f = file_get_contents("$query", false);
                $json = json_decode($f);

                if ($json) {
                    foreach ($json as $records) {
                        foreach ($records as $results) {
                            $address = $results->formatted_address;
                            $lat = $results->geometry->location->lat;
                            $lng = $results->geometry->location->lng;
                        }

                    }
                    // aggiorna scheda
                    if ($lat && $lng) {
                        $scheda->of(false);
                        $scheda->mappa->address = $address;
                        $scheda->save('mappa');
                        $scheda->mappa->lat = $lat;
                        $scheda->mappa->lng = $lng;
                        $scheda->save('mappa'); // non so perche' ma devo salvare due volte ...
                        $scheda->sync->geocoding = 1;
                        $scheda->save('sync');
                        $scheda->of(true);
                    }
                }
            }
            // echo "lat:$lat - long: $lng"; // lat:46.146413 - long: 9.5717867

            // halt for X seconds for every loop
            sleep(1); 
        }

    }else{
        echo "nessuna scheda trovata <br>";
    }
}else{
    echo "ricerca interrotta";
}
die()
/* 
 # guida ####################################################################
 
 /* CAMPI ProcessWire
    # gestionale_sirbec-query
    |------------------------------|--------------------------------|
    |           PW field           |          descrizione           |
    |------------------------------|--------------------------------|
    | title                        |                                |
    | codice_esportato             | stringa di inizio ricerca      |
    | sirbec_datasource (page ref) | codice datasource (page->name) |
    | codice                       | resumption token               |
    | codice_textarea              | termini di ricerca             |
    |------------------------------|--------------------------------|
    | counter                      | cicli, records, reset, stop    |
    |------------------------------|--------------------------------|
    
    # gestionale_sirbec-importazione-scheda
    |-------------------|---------------------------------|--------------------------------------------------|
    |      PW field     |            XML syntax           |                     dettagli                     |
    |-------------------|---------------------------------|--------------------------------------------------|
    | title             | dcterms:alternative             |                                                  |
    | display_name      | dcterms:spacial  da controllare |                                                  |
    | codice            | identifier                      |                                                  |
    | descrizione       | dc:subject                      |                                                  |
    | immagini          | pico:ojcet                      | campo immagine                                   |
    | link              | dcterms:isReferencedBy          |                                                  |
    | codice_esportato  | origine DataSource              | sirbec_datasource (F)                            |
    | autore            | AUF => AUFN                     |                                                  |
    |-------------------|---------------------------------|--------------------------------------------------|
    | datazione (combo) | DTZ.DTZG; DTS.DTSI; DTS.DTSF;   | secolo, anno, anno_fine                          |
    |-------------------|---------------------------------|--------------------------------------------------|
    | luogo (combo)     | LRC => LRCC; LRCL               | comune, localita                                 |
    |-------------------|---------------------------------|--------------------------------------------------|
    | sync (combo)      | x, x, x, soggetto SGTD, x       | sirbec, algolia, geocoding, soggetto, map_desync |
    |-------------------|---------------------------------|--------------------------------------------------|
    | mappa             |                                 | address, lng, lat                                |
    |                   |                                 |                                                  |


    http://www.oai.servizirl.it/oai/interfaccia.jsp?verb=ListRecords&metadataPrefix=pico&set=AFRLSUP
    http://www.oai.servizirl.it/oai/interfaccia.jsp?verb=ListRecords&resumptionToken=null%7Cnull%7CAFRLSUP%7Cpico%7C300%7C2021-11-29T10:39:04Z

*/
?>