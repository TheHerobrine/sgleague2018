<?php

$check_data = 1;
$error_data = '';

if ((isset($_GET["key"])) AND (isset($_GET["mvp"])))
{
	if ((!(preg_match("/[^A-Za-z0-9\!\?\.\-\#_]/", $_GET["key"]))) && (strlen($_GET["key"]) == 20))
	{
		include_once("./class/Database.class.php");

		$database = new Database();

		$temp = $database->req_post('SELECT SU_UID, SU_MAIL, SU_ACTIVATION FROM T_SGL_USER WHERE LOWER(SU_LOGIN)=LOWER(:login)', array(
			"login" => $_GET["mvp"]
		));

		$data = $temp->fetch();

		if ($data["SU_ACTIVATION"] == $_GET["key"])
		{
			$database->req_post('UPDATE T_SGL_USER SET SU_ACTIVATION=NULL, SU_MAIL=:mail, SU_ACTIVMAIL=:mail WHERE SU_UID=:id_user', array(
				"mail" => $data["SU_MAIL"],
				"id_user" => $data["SU_UID"],
			));
		}
		else
		{
			if (strlen($data["SU_ACTIVATION"]) == 20)
			{
				$check_data = -4;
				$error_data = "Ce n'est pas la bonne clé d'activation !";
			}
			else
			{
				$check_data = -3;
				$error_data = "Hum... Vous avez pas déjà activé votre compte ?";
			}
			
		}
	}
	else
	{
		$check_data = -2;
		$error_data = "La clé d'activation n'est pas du bon format... Qu'est ce que vous avez trafiqué ?";
	}
}
else if ((isset($_GET["key"])) AND (isset($_GET["wvp"])))
{
	if ((!(preg_match("/[^A-Za-z0-9\!\?\.\-\#_]/", $_GET["key"]))) && (strlen($_GET["key"]) == 20))
	{
		include_once("./class/Form.class.php");
		include_once("./generic/randomstr.php");
		$new_pass = random_str(10);

		$new_salt = random_str(100);

		$fields = array(
			'wvp' => array('type' => 'string', 'length' => '128'),
			'key' => array('type' => 'string', 'length' => '128'),
			'config_salt' => array('type' => 'value', 'value' => CONFIG_SALT),
			'new_salt' => array('type' => 'value', 'value' => $new_salt),
			'new_pass' => array('type' => 'value', 'value' => $new_pass)
		);

		$query = "CALL UPDATE_SGL_USER_RESET_PASS(:wvp, :new_salt, :config_salt, :key, :new_pass)";

		$form = new Form(new Database(), $query, $fields, METHOD_GET);
		
		if($form->is_valid())
		{
			$return = $form->send();

			debug("data", $return);
			$data = $return->fetch();

			if ($data["RESULT"])
			{
				$subject = "Votre nouveau mot de passe !";
				$content = "Et le voici, tout beau, tout neuf : ".$new_pass."\n
Ne le perdez pas celui là ! Vous pouvez vous connecter ici : <https://".SERVER_ADDR.SERVER_REP."/index.php?page=connect>\n\nL'équipe de la Student Gaming League 2018";

				include_once("./class/Mail.class.php");
				new Mail($data["SU_MAIL"], $subject, $content);
			}
			else
			{
				$check_data = -3;
				$error_data = "Hum... Vous avez vraiment perdu votre mot de passe ?";
			}
		}
	}
	else
	{
		$check_data = -2;
		$error_data = "La clé n'est pas du bon format... Qu'est ce que vous avez trafiqué ?";
	}
}
else
{
	$check_data = -1;
	$error_data = "Comment vous avez atteri là en fait ? Il manque des choses dans l'URL...";
}

?>

<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Activation</h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>Il a activé ! Reported hax.<br/>Good VACation...<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<?=($check_data<0)?'<div class="error" style="text-align:center;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>'.$error_data.'</div>':
		'<p style="text-align:center;">'.(isset($_GET["mvp"])?'C\'est bon, vous êtes activé :D ! Vous pouvez vous connecter !':'Votre nouveau mot de passe a été envoyé par mail ;)').'</p>'?>
		<br />
		<p style="text-align: center;"><a href="index.php" class="button">Revenir à la page d'accueil</a></p>
		<br />
	</div>
</div>