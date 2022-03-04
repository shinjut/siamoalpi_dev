<nav class="uk-navbar-container uk-margin-bottom" uk-navbar>
	<div class="uk-navbar-left">
		
	</div>
	<div class="uk-navbar-right">
		<?php if($page->editable()){
			echo "<a class='uk-navbar-item' href='$page->editURL'>Modifica Pagina</a>";
		} ?>
		<span id="mariogalli" class="uk-padding-small uk-margin-right navbar-item uk-text-uppercase text-white ">Gruppo Micologico Mario Galli</span>
		<a uk-navbar-toggle-icon="" href="#off-canvas" uk-toggle="" class="uk-navbar-toggle uk-hidden@m uk-icon uk-navbar-toggle-icon"><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="navbar-toggle-icon"><rect y="9" width="20" height="2"></rect><rect y="3" width="20" height="2"></rect><rect y="15" width="20" height="2"></rect></svg></a>
	</div>
</nav>

<!-- modal -->
	<div id="off-canvas" uk-offcanvas="mode: push">
		<div class="uk-offcanvas-bar">
			<button class="uk-offcanvas-close" type="button" uk-close></button>
			<?php echo $nav; ?>
		</div>
					
	</div>
