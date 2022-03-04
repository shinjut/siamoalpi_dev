<?php 
/* Le informazioni della scheda vengono prelevate tramite il get nell'URL, impostato su Algolia */
$schedaId = $sanitizer->int($input->get->id);
if ($schedaId) {
	$scheda = $pages->get($schedaId);
	$schedaOK = true;
}else{
	$schedaOK = false;
}

require "inc/landing_head.php" ?>
</head>
<body class='landing-home bg-verde-sa antialiased'>

<?php if ($schedaOK) { ?>
	<section>
		<h1><?php echo $scheda->title ?></h1>
		<img src="<?php echo $scheda->immagini->first->url ?>">
	</section>
<?php } else { ?>
	<section>
		<h1>La ricerca non ha ottenuto risultati</h1>
	</section>
<?php } ?>

</body>
</html>
