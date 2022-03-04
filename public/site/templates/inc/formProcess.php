<?php 
/* form UI-kit 03/2020 */

//<input type="password" name="password" value=""  autocomplete="new-password" />
//https://stackoverflow.com/questions/52139123/is-there-a-way-to-disable-chrome-autofill-option-for-angular-form-fields

// settings
	$recipient = "";
	$subject = "Sistema Museale Valtellina - Web Form";
	$notSent = "<p>Messaggio non inviato, prego riprovare o contattare l'amministratore del sito.</p>";
	$multilanguage = "lingua: ".$user->language->country_code;

//1 required field
	$nome = "";
	$cognome = "";
	$email = "";
	$messaggio = "";
	$errori = "";

	//optional
	$oggetto = "";

//2 minifunction
	function errormessage($fieldname){
		return  "<p>Campo <strong>$fieldname</strong> mancante o non corretto, prego controllare</p>";
	}

// honeypot, usually is "citta" // qui e' telefono

	if ($input->post->invia) {
		
		if ($input->post->telefono) { 
			//honeypot
			$session->redirect($homepage->url);
			$errori = 1;
		}else{
			$emailMessage = "";
			foreach ($input->post() as $postKey => $postItem) {
			if ($postKey == "telefono" ) continue; // honeypot
			if ($postKey == "invia" ) continue; 
			if ($postKey == "ppolicy" ) continue; 
			if ($postKey == "messaggio" ) {
				$$postKey = $sanitizer->textArea($postItem); // questo l'ho spostato sotto
			}else{
				$$postKey = $sanitizer->text($postItem);
			} 
			$emailMessage .= $postKey .": ". $postItem. "<br>";
			}
		}

		//check if empy values		
		if (!$nome) 	$errori .= errormessage('nome');
		if (!$cognome) 	$errori .= errormessage('cognome');
		if (!$email) 	$errori .= errormessage('email');
		if (!$messaggio) $errori .= errormessage('messaggio');

		//further checks
		if (!$sanitizer->email($email)) 		$errori .= errormessage('email');
		if (!$sanitizer->checkbox($input->post->ppolicy)) 	$errori .= errormessage('Privacy Policy');


		// if all OK, send ///////////////////////////////////////////////
		if (!$errori) {

			// aggiungi lingua
			$emailMessage .= $multilanguage;

			$mail = wireMail(); 
			// $mail->to($recipient); // activate on line
			$mail->to('giulio@asbesto.de'); // test email
			$mail->header('Reply-To', $email); // da controllare (procache)
			$mail->subject($subject);
			$mail->bodyHTML($emailMessage);
			$mail->addSignature(true);
			if ($mail->send()) {

				// GDPR
				$timestamp = time();
				$shaEmail = sha1($email);
				wire('log')->save('forms_gdpr', "$shaEmail; $timestamp" );

				// redirect
				$session->redirect($page->child->url);
			}else{
				$errori .= $notSent;
			}
		}

	}
