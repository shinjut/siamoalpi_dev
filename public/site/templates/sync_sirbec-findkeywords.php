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
        $aoifeed .= "&metadataPrefix=pico&set=" . strtoupper($page->sirbec_datasource->name); // &metadataPrefix=pico&set=AFRLSUP 
    }


    $schedeSirbec = WireArray();

    $xmlstr = file_get_contents("$aoifeed");
    //$xmlstr = file_get_contents("test-sirbec.xml"); // local
    $xml = new SimpleXMLElement($xmlstr);

    define('PICO', 'http://purl.org/pico/1.0/');
    define('DC', 'http://purl.org/dc/elements/1.1/');
    define('DCTERMS', 'http://purl.org/dc/terms/');

    $token = $xml->ListRecords->resumptionToken;
    $xmlRecords = count($xml->ListRecords->record);

    // interagisci con questa e salva le informazioni della ricerca 
    $nCicli = ($page->counter->cicli) ? $page->counter->cicli + 1 : 1;
    $nRecords = ($page->counter->records) ? $page->counter->records + $xmlRecords : $xmlRecords;

    $page->of(false);
    $page->set('codice', $token);
    $page->save();
    $page->counter->cicli = $nCicli;
    $page->counter->records = $nRecords;
    $page->save('counter');


    // termini da inserire nella ricerca
    $keywords =  trim(str_replace("\n", '|' , $page->codice_textarea));

    if ($xmlRecords) {
        foreach ($xml->ListRecords->record as $records) {

            // 1. inizia ad estrapolare i titoli e descrizioni, per poi cercare i termini di ricerca

            // titolo da esplodere
            $dcTitle = $records->metadata->children(PICO)->record->children(DC)->title;
            $titles = explode(";", $dcTitle);
            $finalTitle = '';
            $nTitles = count($titles);
            $counter = 1;
            foreach ($titles as $key => $value) {
                $explodeTitle = explode("=", $value);
                $finalTitle .= $explodeTitle[1];
                if ($counter >= 1 && $counter < ($nTitles-1)) {
                    $finalTitle .= " / ";
                }
                $counter++;
            }
            //echo $finalTitle;
            $description = $records->metadata->children(PICO)->record->children(DCTERMS)->alternative;
            $subject = $records->metadata->children(PICO)->record->children(DC)->subject;

            // 2. li inserisco in un WireData in modo da poter fare una ricerca con la function di PW
            $unicaStringa = $finalTitle . " " . $description . " " . $subject; 

            // 3.1 cerco i miei termini di ricerca
            if(preg_match("($keywords)", $unicaStringa) === 1) { 

                //prelevo tutti gli altri dati
               
                    $identifier =  $records->header->identifier;
                    $urlFoto = $records->metadata->children(PICO)->record->object; // url foto
                    $collection = $records->metadata->children(PICO)->record->children(DCTERMS)->isPartOf; // extra info

                    // url scheda da esplodere
                    $isReferenced = $records->metadata->children(PICO)->record->children(DCTERMS)->isReferencedBy; 
                    $isReferencedExplode = explode(";", $isReferenced);
                    foreach ($isReferencedExplode as $key => $value) {
                        if (strstr($value, "URL=")) {
                            $urlExplode = explode("=", $value);
                            $urlRecord = $urlExplode[1];
                        }
                    }

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

                    // datazione
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
                        $annoA = ($annoDa != $annoA) ? $annoA : ""; // segnalamelo solo se diverso da valore precedente "anno Da"

                // inizio importazione
                    //check if appuntamento is already there
                    $alreadyImported = $pages->findOne("template=gestionale_sirbec-importazione-scheda, codice=$identifier")->title;
                    if (!$alreadyImported) {
                        $p = new Page();

                        //define template & parent
                        $p->template = "gestionale_sirbec-importazione-scheda";

                        $p->title = $sanitizer->text($finalTitle);
                        $p->name  = $sanitizer->pageName($p->title, true);
                        $p->parent = $page;

                        $p->display_name = $sanitizer->text($collection);
                        $p->descrizione = $sanitizer->text($subject);
                        $p->link = $sanitizer->url($urlRecord);
                        $p->autore = $sanitizer->text($autore);

                        // codice univoco & codice datasource
                        $p->codice = $sanitizer->text($identifier);
                        $p->codice_esportato = $page->sirbec_datasource->name;

                        // dataziome
                        $p->datazione->secolo = $sanitizer->text($secolo);
                        $p->datazione->anno = $sanitizer->text($annoDa);
                        $p->datazione->anno_fine  = $sanitizer->text($annoA);


                        // add images
                        $p->save();
                        if(strlen($urlFoto)){
                            $p->immagini->add((string)$urlFoto);
                        } 

                        $p->save();
                        echo 'new page <a href="' . $p->editUrl . '" target="_blank">' . $p->path . '</a><br>';
                    }
            } 
            // 3.2 oppure aggiorno le schede SA coi dati assegnati in Sirbec
            // da fare ...
        }
    }else{
        echo "no records found";
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
    |-------------------|---------------------------------|-------------------------|
    |      PW field     |            XML syntax           |         dettagli        |
    |-------------------|---------------------------------|-------------------------|
    | title             | dcterms:alternative             |                         |
    | display_name      | dcterms:spacial  da controllare |                         |
    | codice            | identifier                      |                         |
    | descrizione       | dc:subject                      |                         |
    | immagini          | pico:ojcet                      | campo immagine          |
    | link              | dcterms:isReferencedBy          |                         |
    | codice_esportato  | origine DataSource              | sirbec_datasource (F)   |
    | autore            | AUF => AUFN                     |                         |
    |-------------------|---------------------------------|-------------------------|
    | datazione (combo) | DTZ.DTZG; DTS.DTSI; DTS.DTSF;   | secolo, anno, anno_fine |
    |-------------------|---------------------------------|-------------------------|
    | luogo (combo)     | LRC => LRCC; LRCL               | comune, localita        |
    |-------------------|---------------------------------|-------------------------|
    | sync (combo)      |                                 | sirbec, algolia         |
    |                   |                                 |                         |


    http://www.oai.servizirl.it/oai/interfaccia.jsp?verb=ListRecords&metadataPrefix=pico&set=AFRLSUP
    http://www.oai.servizirl.it/oai/interfaccia.jsp?verb=ListRecords&resumptionToken=null%7Cnull%7CAFRLSUP%7Cpico%7C300%7C2021-11-29T10:39:04Z

*/
?>