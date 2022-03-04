<?php 

$radiceURL = 'https://biblioteche.provinciasondrio.gov.it/';
$error_message = '';

if (!$page->counter->stop) {

    $shelfJson = $radiceURL . 'data/jshelf/widget/' . $page->codice . '/'; 

    // metodo CURL fallito ... // soluzione trovata qui https://stackoverflow.com/questions/2548451/php-file-get-contents-behaves-differently-to-browser
    $opts = array('http' =>
        array(
            'method'  => 'GET',
            'user_agent '  => "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2) Gecko/20100301 Ubuntu/9.10 (karmic) Firefox/3.6",
            'header' => array(
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*\/*;q=0.8'
            ), 
        )
    );
    $context  = stream_context_create($opts);
    $f = file_get_contents("$shelfJson", false, $context);
    $json = json_decode($f);

    if ($json) {
        foreach ($json as $record) {

            // 1 .trova il codice OPAC
                $explodeURL = explode(':catalog:', $record->opac_url);
                $codice = $sanitizer->int($explodeURL[1]);
                if ($codice) {
                    // 2. inizio importazione
                        //check if appuntamento is already there
                        $alreadyImported = $pages->findOne("template=gestionale_opac-importazione-scheda, codice=$codice")->title;

                        if (!$alreadyImported) {
                            $p = new Page();

                            //define template & parent
                            $p->template = "gestionale_opac-importazione-scheda";

                            $p->title = $sanitizer->text($record->cover_title);
                            $p->name  = $sanitizer->pageName($p->title, true);
                            $p->parent = $page;

                            $p->autore = $sanitizer->text($sanitizer->unentities($record->full_author));
                            $p->descrizione = $sanitizer->text($record->abstract);
                            $p->link = $sanitizer->url($radiceURL . $record->opac_url);
                            $p->codice = $codice;

                            // add images (add url in text fild)
                            $p->codice_esportato = $sanitizer->url($record->cover_url);

                            $p->save();
                            echo 'new page <a href="' . $p->editUrl . '" target="_blank">' . $p->path . '</a><br>';
                        }

                }else{
                    $error_message .= "codice catalogo OPAC non trovato " . print_r($explodeURL, true) . "\n";
                }
        }
    }else{
        $error_message .= "L'URL json interrogato non da' risultati";
        // manda una mail di notifica?
    }
}else{
    echo "ricerca interrotta";
}

// notifica admin in caso di problemi
if ($error_message) {
    $mail = wireMail();
    $mail->sendSingle(true);
    $mail->to('admin@siamoalpi.it'); 
    $mail->subject("Problema con harvesting, pagina: $page->name");
    $mail->body($error_message);
    $mail->send();
}

die()
/* 
 # guida ####################################################################
 
 /* CAMPI ProcessWire
    # gestionale_opac-query
    |-----------------|-----------------------------|
    |     PW field    |         descrizione         |
    |-----------------|-----------------------------|
    | title           | titolo                      |
    | codice          | codice dello scaffale       |
    |-----------------|-----------------------------|
    | counter         | cicli, records, reset, stop |
    |-----------------|-----------------------------|
    
    # gestionale_opac-importazione-scheda
    |------------------|----------------------------------|
    |     PW field     |           Json syntax            |
    |------------------|----------------------------------|
    | title            | cover_title                      |
    | autore           | full_author                      |
    | codice           | ID opac, da prendere in opac_url |
    | descrizione      | abstract                         |
    | codice_esportato | cover_url                        |
    | link             | opac_url                         |
    |------------------|----------------------------------|


Note tecniche di Giulio Bonanome

    https://<opac_url>/data/jsonDataApi?type=sh&shelfid=<shelf_widget_id_esistente>
    &page=<numero_risultati>
    &sort=<ordinamento_risultati_ricerca>
    &ttl=<time_to_live_apc_cache_in_secondi>
     
    Versione compatta https://<opac_url>/data/jshelf/widget/<shelf_widget_id_esistente>/<numero_risultati>/<time_to_live_apc_cache_in_secondi>
    Esempio: https://opac.provincia.brescia.it/data/jshelf/widget/1825/20/100



*/
?>