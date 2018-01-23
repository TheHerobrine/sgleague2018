<?php

include_once("./class/Form.class.php");

function getvar(&$var){
	return (isset($var))?$var:null;
}

$database = new Database();

$cursor = $database->req_post("CALL SELECT_GAME_USER_BY_SU(:id_user)", array(
		"id_user" => $_SESSION['sgl_id']
));

$game_users = $cursor->fetchAll();
$cursor->closeCursor();

//  ----- [ Config CS ] --------------------------------------------------

$platform_profile[2]['reggex'] = "/^STEAM_[0-5]:[0-1]:[0-9]+$/";
$platform_profile[2]['error'] = "<div>Attention, ça doit être un truc du style STEAM_0:1:11539914.</div>";
$platform_profile[2]['comment'] = "<div class=\"smallquote\">Votre Steam ID pour Counter Strike (ex : STEAM_0:1:11539914). Pour vous aider : <a target=\"_blank\" href=\"http://steamidfinder.com/\">SteamIDFinder.com</a></div>";
$games_profile[3]['comment-rank'] = "<div class=\"smallquote\">Votre rang Counter Strike : Global Offensive</div>";
$games_profile[3]['rank'] = array(
	"Non Classé",
	"Silver 2",
	"Silver 1",
	"Silver 3",
	"Silver 4",
	"Silver Elite",
	"Silver Elite Master",
	"Gold Nova 1",
	"Gold Nova 2",
	"Gold Nova 3",
	"Gold Nova Master",
	"Master Guardian 1",
	"Master Guardian 2",
	"Master Guardian Elite",
	"Distinguished Master Guardian",
	"Legendary Eagle",
	"Legendary Eagle Master",
	"Supreme Master First Class",
	"The Global Elite"
);

//  ----- [ Config OW / HS ] --------------------------------------------------

$platform_profile[1]['reggex'] = "/#[0-9]{4,5}$/";
$platform_profile[1]['error'] = "<div>Attention, vous avez pas oubliez la partie après le '#' par hasard ?</div>";
$platform_profile[1]['comment'] = "<div class=\"smallquote\">Votre BattleTag pour Hearthstone et Overwatch. On oublie pas la partie après le \"#\" !</div>";
$games_profile[1]['comment-rank'] = "<div class=\"smallquote\">Votre nombre de points Overwatch</div>";
$games_profile[1]['rank'] = array(
	"Non Classé",
	"1850 et moins",
	"1851-2200",
	"2201-2450",
	"2451-2700",
	"2701-3000",
	"3001-3500",
	"3501 et plus",
);
$games_profile[4]['comment-rank'] = "<div class=\"smallquote\">Votre rang HearthStone</div>";
$games_profile[4]['rank'] = array(
	"Non Classé",
	"25",
	"24",
	"23",
	"22",
	"21",
	"20",
	"19",
	"18",
	"17",
	"16",
	"15",
	"14",
	"13",
	"12",
	"11",
	"10",
	"9",
	"8",
	"7",
	"6",
	"5",
	"4",
	"3",
	"2",
	"1",
	"Légende"
);

//  ----- [ Config LOL ] --------------------------------------------------

$platform_profile[4]['comment'] = "<div class=\"smallquote\">Votre nom d'invocateur pour League of Legends.</div>";
$games_profile[2]['comment-rank'] = "<div class=\"smallquote\">Votre rang League of Legends</div>";
$games_profile[2]['rank'] = array(
	"Non Classé",
	"Bronze",
	"Argent",
	"Or",
	"Platine",
	"Diamant",
	"Maitre",
	"Challenge"
);

//  ----- [ Config Discord ] --------------------------------------------------

$reggex_discord = "/#[0-9]{4,5}$/";
$error_discord = "Attention, vous avez pas oubliez la partie après le '#' par hasard ?";

//  ----- [ Global Update ] --------------------------------------------------

$newpass_bool = false;

if (isset($_POST["sent"]))
{

	$form_check = true;

	$birth = '';
	if(isset($_POST['byear']) && isset($_POST['bmonth']) && isset($_POST['bday']))
	{
		$birth = date('Y-m-d',mktime(0,0,0, (int)$_POST['bmonth'], (int)$_POST['bday'], (int)$_POST['byear']));
	}
	else
	{
		$form_check = false;
	}

	$gender = (int)$_POST['gender'];
	$gender = in_array($gender, array(0,1,2,3))?$gender:0;

	//TODO: Verification for game pseudo

	if($form_check)
	{
		$fields = array(
			'mail' => array('type' => 'mail'),
			'school' => array('type' => 'string', 'length' => '256'),
			'first' => array('type' => 'string', 'length' => '128'),
			'name' => array('type' => 'string', 'length' => '128'),
			'gender' => array('type' => 'value', 'value' => $gender),
			'birth' => array('type' => 'value', 'value' => $birth),
		);

		$query = "CALL UPDATE_SGL_USER_INFORMATION(:login, :pass, :salt, :configsalt, :mail, :activation, :school)";

		$database = new Database();
		$form = new Form($database, $query, $fields);
		if($form->is_valid())
		{
			$return = $form->send();
			$return->closeCursor();
		}
	}

//  ----- [ Pass Update ] --------------------------------------------------

	$old_pass =	isset($_POST['oldpass']) ?	$_POST['oldpass'] : '';
	$new_pass =	isset($_POST['newpass']) ?	$_POST['newpass'] : '';
	if ($old_pass != '')
	{

		$check_pass = 0;
		if (strlen($new_pass) == 0)
		{
			$check_pass = -1;
			$error_pass = "Non vraiment, c'est plus sécuritaire si vous en mettez un :/";
		}
		else if (strlen($new_pass) <= 8)
		{
			$check_pass = -2;
			$error_pass = "On a dit au moins 8 caractères ! C'est pour que la NSA puisse pas le décrypter è_é !";
		}
		else if (!(preg_match('/[A-Za-z]/', $new_pass) && preg_match('/[0-9]/', $new_pass)))
		{
			$check_pass = -3;
			$error_pass = "On a dit au moins une lettre et un chiffre ! Si vous m'écoutez pas aussi :( ...";
		}

		include_once("./generic/randomstr.php");
		$salt = random_str(100);

		if ($check_pass < 0)
		{
			$newpass_bool = true;
			$error_pass = "<div class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>".$error_pass."</div>";
		}
		else
		{
			$fields = array(
				'id_user' => array('type' => 'value', 'value' => $_SESSION['sgl_id']),
				'old_pass' => array('type' => 'value', 'value' => $old_pass),
				'new_pass' => array('type' => 'value', 'value' => $new_pass),
				'new_salt' => array('type' => 'string', 'length' => $salt),
				'config_salt' => array('type' => 'value', 'value' => CONFIG_SALT)
			);

			$query = "CALL UPDATE_SGL_USER_PASS( :id_user, :old_pass, NULL, :new_pass, :new_salt, :config_salt)";

			$database = new Database();
			$form = new Form($database, $query, $fields);
			if($form->is_valid())
			{
				$return = $form->send();
				$return->closeCursor();
			}
		}
	}
}

$cursor = $database->req_post('CALL SELECT_SGL_USER_INFORMATION(:id_user, :id_parent)', array(
	"id_user" => $_SESSION['sgl_id'],
	"id_parent" =>$_SESSION['sgl_id']
));

$user_data = $cursor->fetch();
$cursor->closeCursor();

$birth_day = intval(date('d', strtotime($user_data["SU_BIRTH_DATE"])));
$birth_month = intval(date('m', strtotime($user_data["SU_BIRTH_DATE"])));
$birth_year = intval(date('Y', strtotime($user_data["SU_BIRTH_DATE"])));

?>

<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Profil</h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>Glorious PC gaming master race<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<br />
		<div class="form">
			<form action="index.php?page=account" method="post" autocomplete="off">
				<table class="form_table">
					<tr><td><h3>Pseudo :</h3></td><td><input type="text" name="login" value="<?=htmlspecialchars($user_data["SU_LOGIN"])?>" disabled="disabled" /><br />
					<div class="smallquote">Non, c'est même pas la peine d'essayer de le changer.</div></td></tr>
				</table>

				<p><table class="line_table"><tr><td><hr class="line" /></td><td>Modification du mot de passe</td><td><hr class="line" /></td></tr></table></p>
				<table class="form_table">
					<tr><td><h3>Ancien :</h3></td><td><input type="password" name="oldpass" /><br />
					<div class="smallquote">Juste pour être sûr que c'est bien vous et pas votre mère qui essaie de vous empecher de venir jouer.</div></td></tr>
					<tr><td><h3>Nouveau :</h3></td><td><input type="password" name="newpass" /><br />
					<?=$newpass_bool?$error_pass:''?>
					<div class="smallquote">On va dire au moins 8 caractères chiffres + lettres. 100% incraquable par la NSA.</div></td></tr>
				</table>

				<?php
				for ($igame=0; $igame<count($game_users); $igame++)
				{
					?>
				<p><table class="line_table"><tr><td><hr class="line" /></td><td><?=$game_users[$igame]['P_NAME']?></td><td><hr class="line" /></td></tr></table></p>
				<table class="form_table">
					<tr>
						<td><h3><?=$game_users[$igame]['P_PSEUDO_NAME']?> :</h3></td>
						<td><input type="text" name="p_name_<?=$game_users[$igame]['P_UID']?>" value="<?=$game_users[$igame]['PU_PSEUDO']?>" />
							<?=$platform_profile[$game_users[$igame]["P_UID"]]['comment']?></td>
					</tr>
					<?php if($game_users[$igame]['G_UID'])
					{
						do
						{
							?>
						<tr>
							<td><h3><?=$game_users[$igame]['G_NAME']?> :</h3></td>
							<td>
								<select name="g_rang_<?=$game['G_UID']?>">
									<?php
									foreach ($games_profile[$game_users[$igame]["G_UID"]]['rank'] as $key => $rank)
									{ ?>
										<option value="<?=$key?>" <?=(isset($game['user']['GU_RANK'])?$game['user']['GU_RANK']:'')==$key?'selected':''?>><?=$rank?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
					<?php
						}while($game_users[$igame+1]["P_UID"] == $game_users[$igame]["P_UID"] && ($igame++ || true));
					} ?>
				</table>
				<?php } ?>
				<p><table class="line_table"><tr><td><hr class="line" /></td><td>Informations personnelles</td><td><hr class="line" /></td></tr></table></p>
				<table class="form_table">
					<tr><td><h3>Mail :</h3></td><td><input type="mail" name="mail" value="<?=htmlspecialchars($user_data["SU_MAIL"])?>"/><br />
					<div class="smallquote">Essayez de mettre votre mail étudiant, comme ça vous n'aurez pas à scanner votre carte étudiante.</div></td></tr>
					<tr><td><h3>Ecole :</h3></td><td><input type="text" name="school" value="<?=htmlspecialchars($user_data["S_NAME"])?>"/><br />
					<div class="smallquote">Pour ceux qui n'écoutent rien : on doit être étudiant pour participer à la SGL !</div></td></tr>
					<tr><td><h3>Pseudo IRL :</h3></td><td>
					<input style="width: 237px" type="text" placeholder="Prénom" name="first" value="<?=htmlspecialchars($user_data["SU_FIRST_NAME"])?>"/>
					<input style="width: 237px" type="text" placeholder="Nom" name="name" value="<?=htmlspecialchars($user_data["SU_LAST_NAME"])?>"/><br />
					<div class="smallquote">Comme ça ou pourra faire des affichages stylés genre "Prénom (aka Pseudo) Nom"</div></td></tr>
					<tr><td><h3>Genre :</h3></td><td>
					<input type="radio" name="gender" id="radio_m" value="1" <?=($user_data["SU_GENDER"] == 1)?'checked="checked"':''?>> <label for="radio_m">Homme</label> |
					<input type="radio" name="gender" id="radio_f" value="2" <?=($user_data["SU_GENDER"] == 2)?'checked="checked"':''?>> <label for="radio_f">Femme</label> |
					<input type="radio" name="gender" id="radio_a" value="3" <?=($user_data["SU_GENDER"] == 3)?'checked="checked"':''?>> <label for="radio_a">Hélicoptère Apache</label> |
					<input type="radio" name="gender" id="radio_o" value="0" <?=($user_data["SU_GENDER"] == 0)?'checked="checked"':''?>> <label for="radio_o">Inconnu / Ne sais pas / Autres</label><br />
					<div class="smallquote">On me dit dans l'oreillette que c'est pour faire des statistiques.</div></td></tr>
					<tr><td><h3>Naissance :</h3></td><td>
					<select name="bday" style="width: 100px">
						<?php for ($i=1; $i<=31; $i++)
						{echo '<option value="'.$i.'" '.($i==$birth_day?'selected="selected"':'').'>'.str_pad($i, 2, '0', STR_PAD_LEFT).'</option>';}?>
					</select>
					<select name="bmonth" style="width: 200px">
						<?php $months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
						for ($i=0; $i<12; $i++)
						{echo '<option value="'.($i+1).'" '.(($i+1)==$birth_month?'selected="selected"':'').'>'.$months[$i].'</option>';}?>
					</select>
					<select name="byear" style="width: 100px">
						<?php for ($i=1970; $i<=2005; $i++)
						{echo '<option value="'.$i.'" '.($i==$birth_year?'selected="selected"':'').'>'.$i.'</option>';}?>
					</select><br />
					<div class="smallquote">C'est pour vous souhaiter le bon anniversaire le moment venu !</div></td></tr>
				</table>
				<br /><br />
				<input type="hidden" name="sent" value="sent">
				<button type="submit" value="Submit">Mettre à jour</button>
			</form>
		</div>
	</div>
</div>
