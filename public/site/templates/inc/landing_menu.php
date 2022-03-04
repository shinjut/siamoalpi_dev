<?php $landingHome = $pages->findOne("name=sa");  ?>

<!-- navigation START -->
<div x-data="{ open: false }">
<nav class="relative z-10">
	<div class="flex justify-between items-center">
		<a class="" href="<?php echo $landingHome->child->url ?>">
			<img class="h-36 pt-0 lg:pt-3" src="<?php echo $config->urls->templates ?>pictures/logo/siamo-alpi-bianco.svg" alt="Siamo Alpi" width="">
		</a>
		<div class="" x-data="{ burger: true }">
			<button
			aria-expanded="false" aria-controls="menu"
			 class="navbar-burger flex items-center px-6 " @click="open = ! open" >
				<svg class="text-white block h-6 w-6" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
					<title>Mobile burger</title>
					<path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
				</svg>
			</button>
		</div>
	</div>
</nav>


<div  x-cloak class="navbar-menu fixed top-0 right-0 bottom-0 w-5/6 max-w-sm z-50" x-show="open"
x-transition:enter="transform duration-300"
x-transition:enter-start="opacity-0 translate-x-5/6 "
x-transition:enter-end="opacity-100 -translate-x-5/6 "
x-transition:leave="transform duration-300"
x-transition:leave-start="opacity-100 -translate-x-5/6 "
x-transition:leave-end="opacity-0 translate-x-5/6 "
>
	<div class="navbar-backdrop fixed inset-0 bg-blu-sa-100 opacity-20"></div>
	<nav class="relative flex flex-col py-8 w-full h-full bg-blu-sa-500 opacity-80 border-r overflow-y-auto">
		<div>
			<button class="pl-6 pt-6" x-on:click="open = ! open">
				<svg xmlns="http://www.w3.org/2000/svg" class=" h-8 w-8 stroke-current text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>

			<ul id="menu" class="mt-20 text-white font-serif uppercase text-3xl lg:text-4xl">
				<?php foreach ($landingHome->children as $lmenu) {
					echo "<li class='mb-4 lg:mb-4 w-auto'><a class='ml-4 lg:ml-8 py-0 pb-0 lg:pb-2 border-b-2 border-blu-sa-500 hover:border-white' href='$lmenu->url'>$lmenu->title</a></li>";
				} 
				if($page->editable()){
					echo "<li class='mb-4 lg:mb-4 w-auto'><a class='ml-4 lg:ml-8 px-3 lg:pb-2 border-b-2 text-verde-sa bg-white lowercase' href='$page->editUrl'>edit</a></li>";

				}
				?>
			</ul>
		</div>

	</nav>
</div>
</div>
<!-- navigation END -->