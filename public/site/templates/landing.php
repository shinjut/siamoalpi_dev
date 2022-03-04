<?php require "inc/landing_head.php" ?>
</head>
<body class='landing-home bg-verde-sa antialiased'>

	<section class="relative ">

		<?php include 'inc/landing_menu.php' ?>

		<?php $bgFolder = $config->urls->templates . "pictures/bg-landing/bg/" ; ?>

		<!-- colonna B - content MOBILE INTRO start -->
			<div id="b1m" class="md:hidden w-full h96 pb-3 ">
				<img class="" src="<?php echo $bgFolder ?>bg-mobile-top_c.jpg" alt="archivio Garlaschelli">
			</div>
		<!-- colonna B - content MOBILE INTRO end -->

		<div class="relative md:absolute top-0 w-full flex">

			<!-- colonna A -->
			<div class="invisible md:visible w-1/12 md:w-1/5 ">
				<?php 
				if ($page->name == "home") require "inc/landing/home_colonna-a.php" ;
				if ($page->name == "il-progetto" || $page->name == "partecipa") require "inc/landing/progetto_colonna-a.php" ;
				if ($page->name == "le-fasi") require "inc/landing/fasi_colonna-a.php" ;
				?>
			</div>

			<div class="w-full px md:px-0 md:w-3/5 ">
				<!-- blocco medium -->
				<div id="b1" class="invisible md:visible mb-14 md:mb-0 h-0 md:h-97 relative justify-center">
					<?php 
					if ($page->name == "home") require "inc/landing/home_colonna-b.php" ;
					if ($page->name == "il-progetto" || $page->name == "partecipa") require "inc/landing/progetto_colonna-b.php" ;
					if ($page->name == "le-fasi") require "inc/landing/fasi_colonna-b.php" ;
					 ?>
				</div>
				<div class="flex justify-center">
					<div id="b2" class="w-full md:w-99  text-white">
						<div class="landing-body text-base">
							<p class="text-5xlb md:text-8xl uppercase pb-8 md:pb-16 font-serif tracking-tight "><?php echo $page->titleH1 ?></p>
							<?php echo $page->body ?>
						</div>
					</div>
				</div>

				<div class="h-0 md:h-auto overflow-hidden w-full">
					<?php include 'inc/landing_footer.php' ?>
				</div>
			</div>

			<!-- colonna C -->
			<div class="invisible md:visible w-1/12 md:w-1/5 ">
				<?php 
					if ($page->name == "home") require "inc/landing/home_colonna-c.php" ;
					if ($page->name == "il-progetto" || $page->name == "partecipa") require "inc/landing/progetto_colonna-c.php" ;
					if ($page->name == "le-fasi") require "inc/landing/fasi_colonna-c.php" ;

				 ?>
			</div>
		</div>


	</section>

	<!-- MOBILE FOOTER start -->
	<section>
		<div id="b4" class="md:hidden pt-12 pb-8">
			<img class="w-full" src="<?php echo $bgFolder ?>bg-mobile-bottom_c.jpg" alt="archivio Garlaschelli">
		</div>

		<!-- aggiungi ancora il footer -->
		<div class="h-auto md:h-0 overflow-hidden">
			<?php include 'inc/landing_footer-mobile.php' ?>
		</div>
	</section>
	<!-- MOBILE FOOTER end -->


				
				
	<div id="credits" class="relative overflow-visible visible md:invisible z-0 ">
		<!-- absolute setting in .css file -->
		<p class="credits absolute  right-0 pl-3 pb-0 mb-0 text-blu-sa-700 text-xxs transform rotate-270 w-80 z-0">FOTO: archivio Garlaschelli | design: simoneronzio.com</p>
	</div>


	<?php require "inc/scripts.php" ?>

</body>
</html>
