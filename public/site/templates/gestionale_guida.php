<?php require "inc/guida/head.php" ?>
<body class='gestionale_guida'>

	<?php require "inc/guida/menu.php" ?>

		<section class="uk-container ">
			<?php require "inc/guida/bread.php" ?>
		</section>

		<section class="uk-container">

			<!-- usiamo lo stesso template per guida e calendario -->
			<?php if ($page->name == "calendario"){ ?>
				<!-- calendario -->
				<iframe src="https://calendar.google.com/calendar/embed?src=sm2k4pndb28bptgj150874pfe8%40group.calendar.google.com&ctz=Europe%2FRome&bgcolor=%23ffffff" style="border: 0" width="100%" height="600" frameborder="0" scrolling="no"></iframe>
				<p>
					Se desideri accedere a questo calendario col tuo account gmail, scrivi a admin@siamoalpi.it.
				</p>
				
			<?php }else{ ?>
				<!-- guida -->
			
				<div class="uk-grid uk-padding-top-small" uk-grid>
					<div class="uk-width-2-3@m uk-width-1-1@s">
						<h1><?php echo $page->title ?></h1>
						<?php echo $page->body ?>
						<hr class="uk-margin-medium">
						<?php
						$showComments = true;
						if (count($page->children) || $page->template == "gestionale_guida-pagina-downloads"){
							
							echo '<dl class="uk-description-list">';
							if ($page->template == "gestionale_guida-pagina-downloads") {
								//mostrami i file della pagina
								foreach ($page->file_downloads as $file) {
									$ext = ($file->ext() == "pdf") ? "pdf" : "text";

								    echo "<dt><a href='$file->url'><span uk-icon='icon: file-$ext'></span> $file->name</a></dt>";
								}

							 }else{			
								//mostrami le sottopagine
								foreach ($page->children as $child) {
								    echo "<dt><a href='$child->url'>$child->title</a></dt>";
								    echo "<dd>".$sanitizer->text($child->body, ['type' => 'sentence', 'maxLength' => 250, 'more' => '...'] )."</dd>";
								}
								$showComments = false;

							 }
							echo '</dl>';

						}
						if ($showComments) {
							echo "<h2 class='uk-h2 uk-margin-large-top'>Forum/Commenti</h2>";
							echo $page->comments->renderAll(); 
						}
						?>
					</div>

					<div class="uk-width-1-3@m uk-width-1-1@s" >
						<div class="uk-margin-large-left" uk-sticky>
							
							<h3>MENU</h3>

							<?php 
							$guidaGestionale = $pages->get(1050);
							$guidaEtnografica = $pages->get(1051) ?>

							<H4><?php echo $guidaGestionale->title ?></H4>
								<ul class="uk-list  uk-list-striped uk-text-small">
									<?php foreach ($guidaGestionale->children as $guida) {
										echo "<li><a href='$guida->url'>$guida->title</a></li>";
									} ?>
								</ul>

							<H4><?php echo $guidaEtnografica->title ?></H4>
								<ul class="uk-list  uk-list-striped uk-text-small">
									<?php foreach ($guidaEtnografica->children as $guida) {
										echo "<li><a href='$guida->url'>$guida->title</a></li>";
									} ?>
								</ul>

						</div>
					</div>
				</div>
			<?php } ?>
		</section>

	
	<?php
	
	//echo $page->template;

	require "inc/guida/footer.php" ?>

</body>
</html>
