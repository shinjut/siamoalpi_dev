<?php 
$soggetto = $sanitizer->entities("Condivisione URL da: " . $infoMuseo['nome']);
$mailtoURL = "mailto:?subject=$soggetto&body=".$page->httpUrl; 
?>

<div class="condividi">
	<p class='uk-text-bold'><?php echo $traduzioni->findOne("name=condividi")->title ?>
	</p>
	<ul class='uk-nav'>
		<li><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $page->httpUrl ?>">Facebook</a></li>
		<li><a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo $page->httpUrl ?>">Twitter</a></li>
		<li><a href="<?php echo $mailtoURL ?>">E-mail</a></li>
	</ul>
</div>

