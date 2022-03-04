<!DOCTYPE html>
<html class="aos pw AdminThemeUikit BrandingLogo pListShowActions" lang="en">
<head>
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta name="google" content="notranslate" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Login &bull; siamoalpi.it</title>

	<script>
		var ProcessWire = { config: {"modals":{"large":"15,15,30,30,draggable=false,resizable=true,hide=250,show=100","medium":"50,49,100,100","small":"100,100,200,200","full":"0,0,0,0"},"ukGridWidths":{"84%":"5-6","80%":"4-5","74%":"3-4","65%":"2-3","58%":"3-5","43%":"1-2","36%":"2-5","27%":"1-3","21%":"1-4","17%":"1-5","5%":"1-6"},"LanguageSupport":{"language":{"id":1017,"name":"default","title":"Default"}},"httpHost":"siamoalpi.obake.pro","httpHosts":["siamoalpi.obake.pro","siamoalpi.it","www.siamoalpi.it"],"https":false,"adminTheme":{"logoAction":0,"toggleBehavior":0},"urls":{"root":"\/","admin":"\/gestione\/","modules":"\/wire\/modules\/","core":"\/wire\/core\/","files":"\/site\/assets\/files\/","templates":"\/site\/templates\/","adminTemplates":"\/wire\/modules\/AdminTheme\/AdminThemeUikit\/"},"debug":false,"user":{"id":40,"name":"guest","roles":[]},"page":{"id":23,"name":"login","process":"ProcessLogin"}} }; var config = ProcessWire.config;
	</script>

	<link type='text/css' href='/site/assets/admin.css?v=1632762031' rel='stylesheet' />
	<!-- <link type='text/css' href='/wire/modules/AdminTheme/AdminThemeUikit/uikit-pw/pw.min.css?v=1630086528' rel='stylesheet' /> -->
	<link type='text/css' href='/wire/templates-admin/styles/AdminTheme.css?v=33g' rel='stylesheet' />
	<link type='text/css' href='/site/modules/AdminOnSteroids/styles/aos.min.css?ts=2.0.21_2021090111' rel='stylesheet' />
	<link type='text/css' href='/wire/modules/Jquery/JqueryUI/panel.css' rel='stylesheet' />
	<link type='text/css' href='/wire/modules/LanguageSupport/LanguageSupport.css' rel='stylesheet' />
	<link type='text/css' href='/wire/modules/Process/ProcessLogin/ProcessLogin.css?v=108-1630086528' rel='stylesheet' />
	<link type='text/css' href='/wire/templates-admin/styles/font-awesome/css/font-awesome.min.css?v=33g' rel='stylesheet' />
	<style type='text/css'>.pw-container { max-width: 1600px; }</style>
	<script type='text/javascript' src='/wire/modules/Process/ProcessLogin/what-input.min.js'></script>
	<script type='text/javascript' src='/wire/modules/Jquery/JqueryCore/JqueryCore.js?v=183'></script>
	<script type='text/javascript' src='/wire/modules/Jquery/JqueryUI/JqueryUI.js?v=1630086528'></script>
	<script type='text/javascript' src='/wire/modules/Jquery/JqueryUI/panel.min.js?v=1630086528'></script>
	<script type='text/javascript' src='/wire/modules/Process/ProcessLogin/ProcessLogin.min.js?v=108-1630086528'></script>
	<script type='text/javascript' src='/wire/templates-admin/scripts/inputfields.min.js?v=33g'></script>
	<script type='text/javascript' src='/wire/templates-admin/scripts/main.min.js?v=33g'></script>
	<script type='text/javascript' src='/wire/modules/AdminTheme/AdminThemeUikit/uikit/dist/js/uikit.min.js?v=33g'></script>
	<script type='text/javascript' src='/wire/modules/AdminTheme/AdminThemeUikit/uikit/dist/js/uikit-icons.min.js?v=33g'></script>
	<script type='text/javascript' src='/wire/modules/AdminTheme/AdminThemeUikit/scripts/main.js?v=33g'></script>
<?php // la parte qui sopra copiata dal login page di PW ?>




</head>
<body class='id-23 template-admin pw-init AdminThemeUikit role-guest role-nonsuperuser user-guest ProcessLogin pw-guest'>

	<div id='pw-mastheads'>
	<header id='pw-masthead-mobile' class='pw-masthead uk-hidden uk-background-muted'>
		<div class='pw-container uk-container uk-container-expand uk-text-center'>
				<a href='/' class='pw-logo-link'>
					<img class='pw-logo pw-logo-custom' src='/site/modules/AdminThemeBoss/uikit/custom/images/pw-mark.png' alt='' />				</a>
		</div>	
	</header>
	<header id='pw-masthead' class='pw-masthead uk-background-muted' data-pw-height='73'>
		<div class='pw-container uk-container uk-container-expand'>
			<nav class='uk-navbar uk-navbar-container uk-navbar-transparent' uk-navbar>
				<div class='uk-navbar-left'>
					<a class="pw-logo-link uk-logo uk-margin-right" href='/'>
						<img class='pw-logo pw-logo-custom' src='/site/modules/AdminThemeBoss/uikit/custom/images/pw-mark.png' alt='' />					</a>
									</div>
							</nav>
		</div>
	</header>
	<ul class='pw-notices' id='notices'></ul><!--/notices--></div>	


	<!-- MAIN CONTENT -->
	<main id='main' class='pw-container uk-container uk-container-expand uk-margin uk-margin-large-bottom'>
		<div class='pw-content' id='content'>
			
			<header id='pw-content-head'>
				
				
				<div id='pw-content-head-buttons' class='uk-float-right uk-visible@s'>
									</div>

				<h1 class='uk-margin-remove-top'>Login</h1>				
			</header>	
			<!-- login form - START /////////////////////////////////////////////////////////////////////////// -->
				<?php 
				// definiamo quale tipo di utente si registra in base all'URL leggermente oscurato
				//1.1/2 aggiungi campo extra nel form per determinare ruolo da assegnare all'utente		
				$wire->addHookAfter("LoginRegisterProRegister::build", function (HookEvent $event) {
					$form = $event->object->form();
					$page = wire('page'); 
					$input = wire('input');
					$role = $input->get->role;

				/*1.2/2 __ limita gli accessi e controlla che il ruolo sia definito nel campo "codice_textarea", in modo da limitare gli accessi 
					(mi raccomando non dimentichiamoci di togliere i permessi, altrimenti chiunque puo' sceglersi in teoria il ruolo) */

					/* i ruoli sono: grafico / catalogatore / catalogatore_junior / gestore */

					$text = $page->codice_textarea; 
					$arrayAllowedRoles = array();
					foreach (explode("\n", $text) as $line) {
						$arrayAllowedRoles[] = trim($line);
					}

					if (in_array($role, $arrayAllowedRoles)) {
						$assignRole = sha1($role); 
					}else{
						$assignRole = "00001"; // tanto per segnalare qualcosa che non va;
						$log = wire('log');
						$log->save("login-register-pro", "New user registration using role not allowed: $role");
					}

					$fieldCodice = $form->getChildByName('register_codice');
					$fieldCodice->value = $assignRole;
					$fieldCodice->class = "uk-hidden";

				});

				//2/2 aggiungi ruolo in base al campo del form, in base ai ruoli sopra definiti
				$wire->addHook('LoginRegisterPro::createUserReady', function($event) {
					$user = $event->arguments(0); /** @var User $user User about to be saved */
					$values = $event->arguments(1); /** @var array $values Values from form */

					if($values['register_codice'] === sha1("grafico")) { $user->addRole('grafico');	}
					if($values['register_codice'] === sha1("catalogatore")) { $user->addRole('catalogatore');	}
					if($values['register_codice'] === sha1("catalogatore_junior")) { $user->addRole('catalogatore_junior');	}
					if($values['register_codice'] === sha1("gestore")) { $user->addRole('gestore');	}
					if($values['register_codice'] === sha1("operatore")) { $user->addRole('operatore');	}

				});
				?>	

				<section class="uk-container">
					<?php echo "<h2 class='uk-margin-remove-bottom'>".ucfirst($sanitizer->text($input->get->role))."</h2>";
					$loginRegister = $modules->get('LoginRegisterPro');
					
					$loginRegister->setRedirectUrl('/gestione/'); //redirect da attivare in production, altrimenti mi reindirizza sempre nel back-end
					$loginRegister->setMarkup([
					  // error notification
					  'error' =>
					    "<div class='uk-alert-danger' uk-alert>" .
					      "<a class='uk-alert-close' uk-close></a>" .
					      "<p><span uk-icon='warning'></span> {out}</p>" .
					    "</div>",

					   // success or message notification
					  'success' =>
					    "<div class='uk-alert-success' uk-alert>" .
					      "<a class='uk-alert-close' uk-close></a>" .
					      "<p><span uk-icon='check'></span> {out}</p>" .
					    "</div>",

					   // inline error notification (appears below input)
					  'item_error' =>
					    "<div class='uk-text-danger uk-text-small uk-margin-small-top'>" .
					      "<span uk-icon='warning'></span> {out}" .
					    "</div>",

					   // wrapper for list of links generated by module
					  'links_list' =>
					    "<ul class='uk-list uk-list-divider LoginRegisterLinks'>{out}</ul>",

					  // The following adds custom classes to certain input types.
					  'InputfieldText' => [ 'class' => 'uk-input' ],
					  'InputfieldTextarea' => [ 'class' => 'uk-textarea' ],
					  'InputfieldSelect' => [ 'class' => 'uk-select' ],
					  'InputfieldRadios' => [ 'class' => 'uk-radio' ],
					  'InputfieldCheckbox' => [ 'class' => 'uk-checkbox' ],
					  'InputfieldCheckboxes' => [ 'class' => 'uk-checkbox' ],
					  'InputfieldSubmit' => [ 'class' => 'uk-button uk-button-primary' ],

					  // Login form: make name & pass fields side-by-side 50% width
					  'name=login_name' => [ 'columnWidth' => 50 ],
					  'name=login_pass' => [ 'columnWidth' => 50 ]
					]); 
					echo $loginRegister->execute();
					 ?>
				</section>
			<!-- login form - END /////////////////////////////////////////////////////////////////////////// -->


	
	</main>

	<!-- FOOTER -->
<footer id='pw-footer' class='uk-margin'>
	<div class='pw-container uk-container uk-container-expand'>
		<div class='uk-grid' uk-grid>
			<div class='uk-width-1-3@m uk-flex-last@m uk-text-right@m uk-text-center'>
				<div id='pw-uk-debug-toggle' class='uk-text-small'></div>
			</div>	
			<div class='uk-width-2-3@m uk-flex-first@m uk-text-center uk-text-left@m'>
				<p class='uk-margin-remove'>ProcessWire</p>
			</div>	
		</div>	
			</div>
	</footer>

	
	<script>
		ProcessWireAdminTheme.init();

		// password fiels.... 
				//menu sticky a scomparsa


		passField = document.getElementById("wrap_register_pass");
		passField.classList.remove("InputfieldStateCollapsed");
		// UIkit.util.on('#stiki', 'active', function () {
		    // navbar.classList.add("uk-box-shadow-small", "myStiki");
		// });
		// UIkit.util.on('#stiki', 'inactive', function () {
		    // navbar.classList.remove("uk-box-shadow-small", "myStiki");
		// });

		// stiki     = document.getElementById("stiki");
		// navbar    = document.getElementById("newNavbar");
		// UIkit.util.on('#stiki', 'active', function () {
		//     navbar.classList.add("uk-box-shadow-small", "myStiki");
		// });
		// UIkit.util.on('#stiki', 'inactive', function () {
		//     navbar.classList.remove("uk-box-shadow-small", "myStiki");
		// });

	</script>

</body>
</html>




<!-- ************************************** -->








	<?php// require "inc/footer.php" ?>



<?php 
/*
<main>
  <?php
  $loginRegister = $modules->get('LoginRegisterPro');
  if($user->isLoggedin()) {
    // logged-in user: let it auto-detect what to display
    echo $loginRegister->execute();
  } else {
    // user not logged-in: tell it to display registration form
    echo $loginRegister->execute('register');
  }
  ?>
</main>

<aside>
  <?php
  // displays login form only if user not already logged in
  if(!$user->isLoggedIn()) {
    echo $loginRegister->execute('login');
  }
  ?>
</aside>

*/ ?>