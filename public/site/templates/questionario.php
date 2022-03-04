<?php 
$formName = 'questionario-'.$page->name;
$form = $forms->render("$formName");
require "inc/head.php" ;
/* echo $form->styles; */
echo $form->scripts; 

//note visibili sono a catalogatore
if($page->editable()){
	echo "<style>	#wrap_Inputfield_note{ display: revert !important; } </style>";
}
?>
</head>
<body class='questionario'>

		<section class="uk-container">
			<img src="<?php echo $urls->templates ?>pictures/logo/siamo-alpi-nero-verde.svg" width="375" alt="Siamo Alpi" class="logo uk-padding">

			<div class="uk-margin-large-top uk-width-2xlarge uk-margin-auto uk-padding">

				<?php if($page->editable()){
					echo "<a class='uk-margin uk-button uk-button-primary' href='$page->editURL'>Modifica Pagina</a>";
				}?>
				
				<div class="uk-padding-large-top">
					<?php echo $page->body; ?>
				</div>

				<div class="uk-margin-medium-top uk-margin-large-bottom">
					<?php echo $form; ?>	
				</div>
			</div>
		</section>
	
	<?php require "inc/footer.php" ?>

</body>
</html>
