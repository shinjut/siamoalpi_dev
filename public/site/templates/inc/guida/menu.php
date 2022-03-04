<section class="uk-background-muted">
	
<nav class="uk-navbar-container uk-container" uk-navbar>
	<div class="uk-navbar-left">

		<a class="uk-navbar-item uk-logo uk-text-uppercase" href="<?php echo $homepage->url ?>" >
			<img class="uk-padding-small" src="<?php echo $config->urls->templates?>pictures/siamo-alpi-bianco-verde.png" width="80" alt="Siamo Alpi">
		</a>

	</div>
	<div class="uk-navbar-right">

		<?php if($page->editable()){
			echo "<a class='uk-navbar-item' href='$page->editURL'>Modifica Pagina</a>";
		} ?>

		<a class='uk-padding-small' uk-toggle="target: #modal-menu" ><i class="fas fa-bars"></i></a>
		
	</div>
</nav>

</section>