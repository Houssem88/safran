
		<!-- php includes -->
		<!-- menu -->
		<?php 
			require "header.php";
		?>

		<?php 

			/*Des données ont-t-elles été saisies ou non ?*/
			/*	Gestion des infos manquantes  ou invalides dans le formulaire d'inscription */
			if(!empty($_POST)){
				
				$errors = array();
				require_once "bd.php";
				/*VALIDATIONS DES INFORMATIONS SAISIES*/
				/*USERNAME*/
				if(empty($_POST['username_input']) || !preg_match('/^[a-z0-9_]+$/', $_POST['username_input'])){
					$errors['username'] = "Pseudo incorrecte.";
				}
				//verifier si ce username n'existe pas deja dans la BD :
				else{
					$req = $pdo->prepare('SELECT id_user FROM user WHERE user_name = ?');
					$req->execute([$_POST['username_input']]);
					$user = $req->fetch();
					
					if($user){
						//ce user existe dans la bd => erreur :
						$errors ['username'] = "cet nom d'utilisateur existe déjà.";
					}
				}

				/*EMAIL*/
				if(empty($_POST['email_input']) || !filter_var($_POST['email_input'], FILTER_VALIDATE_EMAIL)){
					$errors['email'] = "Email invalide.";
				}
				//verifier si cet email existe déja dans la BD :
				//verifier si ce username n'existe pas deja dans la BD :
				else{
					$req = $pdo->prepare('SELECT id_user FROM user WHERE user_email = ?');
					$req->execute([$_POST['email_input']]);
					$email = $req->fetch();
					
					if($email){
						//ce user existe dans la bd => erreur :
						$errors ['email'] = "cet email existe déjà.";
					}
				}

				/*MOT DE PASSE : Valide ou pas + comparaison avec celui de la confirmation */
				if(empty($_POST['password_input'])){
					$errors['password'] = "Mot de passe invalide";
				}
			}

			if(!empty($errors)){
	
				//faire une requette préparée :
				$req = $pdo->prepare("INSERT INTO user SET user_name = ?, user_password = ?, user_email = ?");

				//hasher le mot de passe :
				$hashed_password = password_hash($_POST['password_input'], PASSWORD_BCRYPT);
				
				$req->execute([$_POST['username_input'], $hashed_password, $_POST['email_input']]);
			}
		?>

		<div class="form_registration">
		<h1>Inscription</h1>

		<!-- Messages d'erreurs -->
		<?php if(!empty($errors)) : ?>
			<div class="error-bloc">
				
			<p>Vous n'avez rempli le formulaire correctement</p>
			<ul>
				<?php  foreach($errors as $error) : ?>
					<li> <?= $error; ?> </li>
				<?php endforeach; ?>
			</ul>
			</div>
		<?php endif; ?>


			<form method="POST" action="registration.php">
				<label div="label_registration" for="username_input">Nom d'utilisateur</label>
				<input type="text" name="username_input">

				<label div="label_registration" for="email_input">Email</label>
				<input type="text" name="email_input">

				<label div="label_registration" for="password_input">Mot de passe</label>
				<input type="password" name="password_input">

				<label div="label_registration" for="password_input_confirm">Confirmer le mot de passe</label>
				<input type="password" name="password_input_confirm">

				<button style="submit" class="btn btn-green btn-create" name="register_btn">créer mon compte</button>

			</form>
			

		</div>
		<?php require "footer.php"; ?>