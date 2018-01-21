<?php

global $csrf_check;

if ((isset($_GET["disconnect"])) AND $csrf_check)
{
	session_destroy();
?>
<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Déconnexion</h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>A summoner has left the game<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<br />
		<p style="text-align: center;"><a href="index.php" class="button">Revenir à la page d'accueil</a></p>
		<br />
	</div>
</div>
<?php
}
else if (isset($_GET["recover"]))
{
	if (isset($_POST["sent"]))
	{
		include_once("./generic/randomstr.php");
		$resetsalt = random_str(20);

		include_once("./class/Form.class.php");

		$fields = array(
			'login' => array('type' => 'string', 'length' => '128'),
			'resetsalt' => array('type' => 'value', 'value' => $resetsalt)
		);

		//TODO_QUERY: doit fonctionner aussi avec le mail
		$query = "UPDATE t_sgl_user SET SU_RESETPASS=:resetsalt WHERE LOWER(SU_LOGIN)=LOWER(:login) AND SU_ACTIVATION IS NULL";

		$form = new Form(new Database(), $query, $fields);

		if($form->is_valid())
		{
			$temp = $form->send();
		}

		$fields = array(
			'login' => array('type' => 'string', 'length' => '128')
		);

		//TODO_QUERY: doit fonctionner aussi avec le mail
		$query = "SELECT SU_MAIL, SU_LOGIN FROM t_sgl_user WHERE LOWER(SU_LOGIN)=LOWER(:login) AND SU_ACTIVATION IS NULL";

		$database = new Database();
		$form = new Form($database, $query, $fields);

		if($form->is_valid())
		{
			$return = $form->send();
			$data = $return->fetch();

			if ($data["SU_MAIL"])
			{
				$subject = "Regénération de votre mot de passe";
				$content = "Alors comme ça on a oublié son mot de passe ?\n\n
Pas de soucis, il suffit de cliquer sur ce lien pour en recevoir un nouveau : <https://".SERVER_ADDR.SERVER_REP."/index.php?page=activation&wvp=".$data["SU_LOGIN"]."&key=".$resetsalt.">\n
Si vous avez des problèmes de connexion, n'hésitez pas à passer sur discord ! <https://discord.gg/sgnw>\n\nL'équipe de la Student Gaming League 2018";

				include_once("./class/Mail.class.php");
				new Mail($data["SU_MAIL"], $subject, $content);

				$flag_recover = true;
			}

		}
		else
		{
			$error_code = $form->unvalidated_code;
		}

		//TODO_ALGO: message si l'utilisateur n'existe pas
		
	}

	?>
<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Oubli de mot de passe</h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>Mundo say his own name a lot,<br />or else he forget! Has happened before.<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<?php if(isset($flag_recover)){?>
		<br /><p style="text-align:center; font-weight: bold">C'est bon, <b>tout est réglé</b> ! On viens de vous envoyer un <b>mail</b> pour <b>regénérer un mot de passe</b>.</p><br />
		<?php }else{ ?>	
		<p style="text-align: center;">La prochaine fois, faites comme mundo pour ne plus oublier votre mot de passe ! Quoique le dire à voix haute n'est peut être pas une super idée...</p>
		<?php } ?>
		<div class="form">
			<form action="index.php?page=connect&recover=1" method="post">
				<table class="form_table">
					<tr><td><h3>Login :</h3></td><td><input name="login" type="mail" /><br />
					<div class="smallquote">Pour regénérer un mot passe, il me faut votre login.</div></td></tr>
				</table>
				<br /><br />
				<input type="hidden" name="sent" value="sent">
				<button type="submit" value="Submit">Promis, je n'oublierai pas celui là</button>
			</form>
		</div>
		<br />
	</div>
</div>
	<?php

}
else
{
	$connect_flag = false;

	if (isset($_POST["sent"]))
	{
		include_once("./class/Form.class.php");

		$fields = array(
			'login' => array('type' => 'string', 'length' => '128'),
			'pass' => array('type' => 'string', 'length' => '128'),
			'salt' => array('type' => 'value', 'value' => CONFIG_SALT)
		);

		$query = "CALL CONNECT_USER(:login, :pass, :salt)";

		$form = new Form(new Database(), $query, $fields);
		if($form->is_valid())
		{
			$return = $form->send();
		
			$data = $return->fetch();

			if ($data["RESULT"])
			{
				$connect_flag = true;

				$_SESSION["sgl_id"] = $data["SU_UID"];
				$_SESSION["sgl_login"] = $data["SU_LOGIN"];
				$_SESSION["sgl_type"] = $data["SU_TYPE"];
			}
		}
	}


	if ($connect_flag)
	{
?>

<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Connexion réussie</h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i><?=htmlspecialchars($data["SU_LOGIN"])?> used password.<br />It's super effective!<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<br />
		<p style="text-align: center;"><a href="index.php" class="button">Revenir à la page d'accueil</a></p>
		<br />
	</div>
</div>

<?php
	}
	else
	{

?>

<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Connexion</h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>Welcome back, commander.<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<?=isset($_POST["sent"])?"<div class=\"error\" style=\"text-align:center;\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>Hum... Vous êtes sûr que c'est le bon mot de passe ? Et que votre compte est activé ?</div>":""?>
		<div class="form">
			<form action="index.php?page=connect" method="post">
				<table class="form_table">
					<tr><td><h3>Pseudo :</h3></td><td><input type="text" name="login" /><br />
					<div class="smallquote">Ou votre mail, ça marche aussi ! Pour quand vous avez oublié votre pseudo...</div></td></tr>
					<tr><td><h3>Password :</h3></td><td><input type="password" name="pass" /><br />
					<div class="smallquote">J'espère que vous l'avez pas oublié celui-là. Si ? <a href="index.php?page=connect&amp;recover=1">Ne vous inquiétez pas, on va le retrouver.</a></div></td></tr>
				</table>
				<br /><br />
				<input type="hidden" name="sent" value="sent">
				<button type="submit" value="Submit">Se connecter</button>
			</form>
		</div>
		<br />
	</div>
</div>

<?php

	}
}

?>