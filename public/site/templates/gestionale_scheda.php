<?php if($page->editable()){
	$session->redirect("$page->editURL");
} 
/*
echo  '

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
</head>
<body>
	<h1>'.$page->title.'</h1>
	<img src="'.$page->immagini->first->url.'">
</body>
</html>
';

*/





/**
 * CAMPI del template "gestionale_scheda"
	|=========================|==========|===============|
	| title                   |          |               |
	| tema                    | PageRef  |               |
	| stato_avanzamento       | PageRef  |               |
	| tags                    | PageRef  |               |
	| codice_esportato        | text     | Codice Sirbec |
	| descrizione             | textarea |               |
	| immagini                | image    |               |
	| file                    | file     |               |
	| valutazione_etnografica | PageRef  |               |
	| valutazione_estetica    | PageRef  |               |
	| **********              |          |               |
*/