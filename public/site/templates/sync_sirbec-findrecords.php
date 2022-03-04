<?php 
// interrompi l'haversting quando Sirbec non mi da' piu' il resumption token
    if($page->counter->cicli >= 1 && !$page->codice){
        $page->of(false);
        $page->counter->stop = 1;
        $page->save('counter');
    }
// resetta a comando
    if ($page->counter->reset) {
        $page->of(false);
        $page->counter->reset = 0;
        $page->counter->cicli = 0;
        $page->counter->records = 0;
        $page->save('counter');
        $page->codice = ""; // resetta anche resuption token
        $page->save();
    }

if (!$page->counter->stop) {  

    $aoifeed = "http://www.oai.servizirl.it/oai/interfaccia.jsp?verb=ListRecords";
    if ($page->codice) {
        $aoifeed .= "&resumptionToken=" . str_replace("|", "%7C", $page->codice);
    }else{
        $aoifeed .= "&metadataPrefix=pico&set=AFRLSUP";
    }

    $xmlstr = file_get_contents("$aoifeed");
    //$xmlstr = file_get_contents("test-sirbec.xml"); // local
    $xml = new SimpleXMLElement($xmlstr);

    define('PICO', 'http://purl.org/pico/1.0/');
    define('DC', 'http://purl.org/dc/elements/1.1/');
    define('DCTERMS', 'http://purl.org/dc/terms/');

    $token = $xml->ListRecords->resumptionToken;
    $xmlRecords = count($xml->ListRecords->record);

    // interagisci con questa pagina e salva le informazioni della ricerca 
    $nCicli = ($page->counter->cicli) ? $page->counter->cicli + 1 : 1;
    $nRecords = ($page->counter->records) ? $page->counter->records + $xmlRecords : $xmlRecords;

    $page->of(false);
    $page->set('codice', $token);
    $page->save();
    $page->counter->cicli = $nCicli;
    $page->counter->records = $nRecords;
    $page->save('counter');


    if ($xmlRecords) {
        foreach ($xml->ListRecords->record as $records) {

            // 1. inizio a prendere l'idenfier e vedere se lo trovo tra le mie schede
            $identifier =  $records->header->identifier;
            $identifier = str_replace('oai:SIRBeC:F_', '', $identifier);
            $identifier =  $sanitizer->name($identifier); 

            if ($identifier) {
                $scheda = $pages->findOne("template=gestionale_scheda, stato_avanzamento=1112, sync.sirbec!=1, codice_esportato=$identifier");
                if ($scheda->id) {
                    //echo "--- scheda:" . $scheda->title ."<br>";

                    // 2 .inzia a prendere i dati

                        // autore
                            $autore = "";
                            $author = $records->metadata->children(PICO)->record->author; 
                            if ($author) {
                                $authorExplode = explode(";", $author);
                                foreach ($authorExplode as $key => $value) {
                                    if (strstr($value, "AUFN=")) {
                                        $autoreExp = explode("=", $value);
                                        $autore = $autoreExp[1];
                                    }
                                }
                            }

                        // data
                            $secolo = ""; $annoDa = ""; $annoA = "";
                            $datazione = $records->metadata->children(PICO)->record->children(DCTERMS)->created; 
                            $datazioneExplode = explode(";", $datazione);
                            foreach ($datazioneExplode as $key => $value) {
                                if (strstr($value, "DTZG=")) {
                                    $secoloExp = explode("=", $value);
                                    $secolo = $secoloExp[1];
                                }elseif (strstr($value, "DTSI=")) {
                                    $annoDaExp = explode("=", $value);
                                    $annoDa = $annoDaExp[1];
                                }elseif (strstr($value, "DTSF=")) {
                                    $annoAExp = explode("=", $value);
                                    $annoA = $annoAExp[1];
                                }
                            }
                            $annoA = ($annoDa != $annoA) ? $annoA : ""; // segnalamelo solo se diverso da valore 
                                
                        // luogo
                            // adesso facciamo con PVCC PVCL, vedremo di sostituire con  LRCC - LRCL 

                        // titolo - Titolo attribuito (SGLA) 
                            $newTitle = $records->metadata->children(PICO)->record->children(DCTERMS)->alternative;

                        // descrizione - Indicazioni sul soggetto (SGTD) 
                            // dovrei avere "SGTI 'separator' SGTD" (il separator dovrebbe essere | ma non lo vedo, c'e' pero' -)
                                        
                            $subject = $records->metadata->children(PICO)->record->children(DC)->subject;

                        // url scheda sirbec
                            $urlRecord = '';
                            $isReferenced = $records->metadata->children(PICO)->record->children(DCTERMS)->isReferencedBy; 
                            $isReferencedExplode = explode(";", $isReferenced);
                            foreach ($isReferencedExplode as $key => $value) {
                                if (strstr($value, "URL=")) {
                                    $urlExplode = explode("=", $value);
                                    $urlRecord = $urlExplode[1];
                                }
                            }

                        // sync (combo field)

                    // 3. sync
                        $scheda->of(false);

                        $scheda->title = $sanitizer->line($newTitle); 
                        $scheda->link = $sanitizer->line($urlRecord);
                        $scheda->autore = $sanitizer->text($autore);
                        $scheda->datazione->secolo = $sanitizer->line($secolo, 10);
                        $scheda->datazione->anno = $sanitizer->line($annoDa, 10);
                        $scheda->datazione->anno_fine = $sanitizer->line($annoA, 10);
                        $scheda->sync->soggetto = $sanitizer->textarea($subject);

                        $scheda->sync->sirbec = 1;
                        $scheda->save();
                        $scheda->of(true);

                }else{
                    //echo "nessuna scheda trovata <br>";
                }
            }

        }
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
    |-------------------|---------------------------------|---------------------------|
    |      PW field     |            XML syntax           |          dettagli         |
    |-------------------|---------------------------------|---------------------------|
    | title             | dcterms:alternative             |                           |
    | display_name      | dcterms:spacial  da controllare |                           |
    | codice            | identifier                      |                           |
    | descrizione       | dc:subject                      |                           |
    | immagini          | pico:ojcet                      | campo immagine            |
    | link              | dcterms:isReferencedBy          |                           |
    | codice_esportato  | origine DataSource              | sirbec_datasource (F)     |
    | autore            | AUF => AUFN                     |                           |
    |-------------------|---------------------------------|---------------------------|
    | datazione (combo) | DTZ.DTZG; DTS.DTSI; DTS.DTSF;   | secolo, anno, anno_fine   |
    |-------------------|---------------------------------|---------------------------|
    | luogo (combo)     | LRC => LRCC; LRCL               | comune, localita          |
    |-------------------|---------------------------------|---------------------------|
    | sync (combo)      | x, x, soggetto SGTD             | sirbec, algolia, soggetto |
    |                   |                                 |                           |


    http://www.oai.servizirl.it/oai/interfaccia.jsp?verb=ListRecords&metadataPrefix=pico&set=AFRLSUP
    http://www.oai.servizirl.it/oai/interfaccia.jsp?verb=ListRecords&resumptionToken=null%7Cnull%7CAFRLSUP%7Cpico%7C300%7C2021-11-29T10:39:04Z

*/
?>