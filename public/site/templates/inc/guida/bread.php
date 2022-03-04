<div class="uk-margin-small-top">
	
	<ul class="uk-breadcrumb " >
		<?php 
		foreach($page->parents("id!=1")->append($page) as $parent) {
			$active = ($parent->id == $page->id) ? "is-active" : "";
			echo "<li class='$active'><a href='{$parent->url}'>";
			echo $parent->title;
			echo "</a></li> ";
		}
		?>
	</ul>

</div>