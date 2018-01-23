<?php

global $csrf_check;

include_once("./class/Database.class.php");

$database = new Database();

// 1 - Overwatch
// 2 - League of Legends
// 3 - Counter Strike
// 4 - Hearthstone

$games = array(1, 2, 3, 4);

$games_name = array(
	"Overwatch",
	"League of Legends",
	"Counter Strike",
	"Hearthstone");

$games_short = array(
	"ow",
	"lol",
	"csgo",
	"hs");

$games_quote = array(
	"Ryu ga waga teki wo kurau !",
	"Captain Teemo on duty !",
	"Rush B my friend ! Don't stop, don't stop...",
	"You face Jaraxxus,<br />eredar lord of the burning legion !");


$games_profile[1]['rank'] = array(
	"Non Classé",
	"Bronze (1-1499)",
	"Silver (1500-1999)",
	"Gold (2000-2499)",
	"Platinum (2500-2999)",
	"Diamond (3000-3499)",
	"Master (3500-3999)",
	"Grand Master (4000-5000)"
);

$games_profile[2]['rank'] = array(
	"Non Classé",
	"Bronze",
	"Argent",
	"Or",
	"Platine",
	"Diamant",
	"Maitre",
	"Challenger"
);

$games_profile[3]['rank'] = array(
	"Non Classé",
	"Silver 1",
	"Silver 2",
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

$games_profile[4]['rank'] = array(
	"Non Classé",
	"Division 5 (25-21)",
	"Division 4 (20-16)",
	"Division 3 (15-11)",
	"Division 2 (10-6)",
	"Division 1 (5-1)"
);


?>
<div id="content">

<?php
$single = false;
$get_game = 0;

if (isset($_GET["gpage"]))
{
	$get_game = intval($_GET["gpage"]);
	if (in_array($get_game, $games))
	{
		echo '<div class="top_ban" style="background-image: url(\'./style/img/ban/top_'.$games_short[$get_game-1].'.png\');"></div>';


?>
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Tournoi <?=$single?$games_name[$get_game-1]:''?></h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>
<?php

	echo $games_quote[$get_game-1];

?>
				<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<br />
<?php
if (isset($_SESSION["sgl_id"]))
{
	if ($_GET["action"]=="create")
	{
		$database->req_post('CALL INSERT_SGL_TEAM(:id_user, :game)',
		array(
			"id_user" => $_SESSION['sgl_id'],
			"game" => $get_game
		));
	}

	$temp = $database->req_post('SELECT G_NAME, G_RANK_DESCRIPTION, G_NUM_PLAYERS, G_NUM_SUBSTITUTES, G_ID_P, P_PSEUDO_NAME FROM T_GAME, T_PLATFORM WHERE P_UID=G_ID_P AND G_UID = :game',
		array(
			"game" => $get_game
		));
	$data = $temp->fetch();

	$game_data = $data;

	$temp = $database->req_post('SELECT GU_ID_ST FROM T_GAME_USER WHERE GU_ID_SU=:id_user AND GU_ID_G = :game',
		array(
			"id_user" => $_SESSION['sgl_id'],
			"game" => $get_game
		));
	$data = $temp->fetch();

	$is_team = $data["GU_ID_ST"] > 0;
	$team_id = $data["GU_ID_ST"];
	$url_game = "&amp;gpage=".$get_game;




	echo '<p id="'.$games_short[$get_game-1].'"><table class="line_table"><tr><td><hr class="line" /></td><td><img src="./style/img/games/'.$games_short[$get_game-1].'.png" alt="'.$games_name[$get_game-1].'" /></td><td><hr class="line" /></td></tr></table></p>';


	switch($get_game)
	{
		case 1:
		?>
			<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 6 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Lundis (début le 26 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 34 équipes l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">12 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p><br />
		<?php
			break;
		case 2:
		?>
	<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 5 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Mardis (début le 27 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 102 équipes l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">5 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p><br />
		<?php
			break;
		case 3:
		?>
	<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 5 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Mercredis (début le 28 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 62 équipes l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">12 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p><br />
		<?php
			break;
		case 4:
		?>
	<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> Tournoi solo
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Dimanches (début le 25 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 384 joueurs l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">5 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p><br />
		<?php
			break;
	}

	if ($is_team)
	{
		echo '<p style="text-align:center; font-weight: bold">Yay ! <b>Vous êtes inscrit</b> à ce tournoi ! Un premier pas vers la victoire...</p>';
		echo '<p style="text-align: center;" class="smallquote">Plus qu\'à hard train jusqu\'à fin Février. [ <a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'&amp;action=del">Se désinscrire du tournoi</a> ]</p><br />';
		//echo '<p style="text-align: center;" class="smallquote">Le tournoi a commencé... Bon courage !</p><br />';

		$temp = $database->req_post('SELECT ST_TAG, ST_NAME, ST_STATUS, ST_RANK, ST_ID_LEAD_SU FROM T_SGL_TEAM WHERE ST_UID=:team_id',
			array(
				"team_id" => $team_id
			));
		$data = $temp->fetch();

		echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Equipe</td><td><hr class="line" /></td></tr></table></p>';

		$is_lead = ($data["ST_ID_LEAD_SU"] == $_SESSION["sgl_id"]);

		if($is_lead)
		{
			echo '<div class="form"><form action="index.php?page=games'.$url_game.'&amp;game='.$get_game.'" method="post">
				<table class="form_table">
					<tr><td><h3>Nom d\'équipe :</h3></td><td><input value="'.htmlspecialchars($data["ST_NAME"]).'" name="teamname" type="text"><br />
					<div class="smallquote">Le nom de votre équipe, genre "Télécom Bretagne Gaming"</div></td></tr>
					<tr><td><h3>TAG d\'équipe :</h3></td><td><input value="'.htmlspecialchars($data["ST_TAG"]).'" name="teamtag" type="text"><br />
					'.$error_teamtag.'
					<div class="smallquote">Votre tag en 3 ou 4 caractères, genre "TBG" ou "TBG2" (que des lettres et des chiffres par contre !)</div></td></tr>
				</table><br /><br />
				<input type="hidden" name="sent" value="sent">
				<button type="submit" value="Submit">Mettre à jour</button>
				</form></div><br /><br /><br />';
		}
		else
		{
			echo '
				<table class="form_table">
					<tr><td><h3>Nom d\'équipe :</h3></td><td><input value="'.htmlspecialchars($data["ST_NAME"]).'" disabled="disabled" name="teamname" type="text"><br />
					<div class="smallquote">Le nom de votre équipe, genre "Télécom Bretagne Gaming"</div></td></tr>
					<tr><td><h3>TAG d\'équipe :</h3></td><td><input value="'.htmlspecialchars($data["ST_TAG"]).'" disabled="disabled" name="teamtag" type="text"><br />
					<div class="smallquote">Votre tag en 3 ou 4 caractères, genre "TBG" ou "TBG2" (que des lettres et des chiffres par contre !)</div></td></tr>
				</table>
				</div><br /><br /><br />';
		}

		echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Joueurs</td><td><hr class="line" /></td></tr></table></p>';

		$temp = $database->req_post('SELECT SU_LOGIN, SU_UID, SU_MAIL, PU_PSEUDO, GU_RANK, S_NAME, SU_FIRST_NAME, SU_LAST_NAME, GU_TYPE FROM T_SGL_USER
			JOIN T_GAME_USER ON SU_UID=GU_ID_SU JOIN T_GAME ON GU_ID_G=G_UID LEFT JOIN T_PLATFORM_USER ON PU_ID_SU=SU_UID AND PU_ID_P=G_ID_P LEFT JOIN T_SCHOOL ON SU_ID_S=S_UID WHERE GU_ID_ST=:team_id',
			array(
				"team_id" => $team_id
			));

		$player_type = array("Aucun", "Capitaine", "Joueur", "Remplaçant");

		$nplayer = 0;
		$nreps = 0;

		echo '<div style="text-align: center">';

		$data = $temp->fetchAll();


		$current_player = 0;

		$hash = md5(strtolower(trim($data[$current_player]["SU_MAIL"])));
		echo '<span class="playercard">
		<a style="float:left" href="https://www.gravatar.com/'.$hash.'"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></a>
		<span class="playername">'.htmlspecialchars($data[$current_player]["SU_LOGIN"]).'</span>
		('.htmlspecialchars($data[$current_player]["SU_FIRST_NAME"]).' '.htmlspecialchars($data[$current_player]["SU_LAST_NAME"]).', '.htmlspecialchars($data[$current_player]["S_NAME"]).')
		<span class="playertype">'.$player_type[1].'</span><br />
		<span class="playerplatform">'.$game_data["P_PSEUDO_NAME"].' : '.htmlspecialchars($data[$current_player]["PU_PSEUDO"]).'</span>
		<span class="playerrank">'.$games_profile[$get_game]['rank'][$data[$current_player]["GU_RANK"]].'</span>
		</span><br />';

		$current_player++;

		$randhash= time();

		for ($iplayer=1;$iplayer<$game_data["G_NUM_PLAYERS"];$iplayer++)
		{
			if (isset($data[$current_player]))
			{
				if($data[$current_player]["GU_TYPE"] == 2)
				{
					$hash = md5(strtolower(trim($data[$current_player]["SU_MAIL"])));
					echo '<span class="playercard">
					<a style="float:left" href="https://www.gravatar.com/'.$hash.'"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></a>
					<span class="playername">'.htmlspecialchars($data[$current_player]["SU_LOGIN"]).'</span>
					('.htmlspecialchars($data[$current_player]["SU_FIRST_NAME"]).' '.htmlspecialchars($data[$current_player]["SU_LAST_NAME"]).', '.htmlspecialchars($data[$current_player]["S_NAME"]).')
					<span class="playertype">'.$player_type[2].'</span><br />
					<span class="playerplatform">'.$game_data["P_PSEUDO_NAME"].' : '.htmlspecialchars($data[$current_player]["PU_PSEUDO"]).'</span>
					<span class="playerrank">'.$games_profile[$get_game]['rank'][$data[$current_player]["GU_RANK"]].'</span>
					</span><br />';

					$current_player++;

					continue;
				}
			}
			
			$hash = md5("player".$iplayer.$randhash);
			echo '<span class="playercard">
			<a style="float:left" href="https://www.gravatar.com/'.$hash.'"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></a>
			<span class="playerbutton"><a href="#">Ajouter</a></span>
			<span class="playertype">'.$player_type[2].'</span><br />
			</span><br />';
		}

		for ($iplayer=0;$iplayer<$game_data["G_NUM_SUBSTITUTES"];$iplayer++)
		{
			if (isset($data[$current_player]))
			{
				if($data[$current_player]["GU_TYPE"] == 3)
				{
					$hash = md5(strtolower(trim($data[$current_player]["SU_MAIL"])));
					echo '<span class="playercard">
					<a style="float:left" href="https://www.gravatar.com/'.$hash.'"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></a>
					<span class="playername">'.htmlspecialchars($data[$current_player]["SU_LOGIN"]).'</span>
					('.htmlspecialchars($data[$current_player]["SU_FIRST_NAME"]).' '.htmlspecialchars($data[$current_player]["SU_LAST_NAME"]).', '.htmlspecialchars($data[$current_player]["S_NAME"]).')
					<span class="playertype">'.$player_type[3].'</span><br />
					<span class="playerplatform">'.$game_data["P_PSEUDO_NAME"].' : '.htmlspecialchars($data[$current_player]["PU_PSEUDO"]).'</span>
					<span class="playerrank">'.$games_profile[$get_game]['rank'][$data[$current_player]["GU_RANK"]].'</span>
					</span><br />';

					$current_player++;

					continue;
				}
			}
			
			$hash = md5("substitute".$iplayer.$randhash);
			echo '<span class="playercard">
			<a style="float:left" href="https://www.gravatar.com/'.$hash.'"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></a>
			<span class="playerbutton"><a href="#">Ajouter</a></span>
			<span class="playertype">'.$player_type[3].'</span><br />
			</span><br />';
		}
		

		if ($lasttype <= 2)
		{
			for ($j=0; $j<($games_team[$i]-$nplayer); $j++)
			{
				if ($is_lead)
				{
					//echo '<span class="buttoncard" onclick="morphIntoTextField(this, '.$games[$i].')()" data-type="2" data-game="'.$games[$i].'">Ajouter un joueur</span><br />';
					echo '<span class="playercard"></span><br />';
				}
				else
				{
					echo '<span class="playercard"></span><br />';
				}
			}
			echo "<br /><br />";
			$lasttype = 2;
		}

		for ($j=0; $j<($games_reps[$i]-$nreps); $j++)
		{
			if ($is_lead)
			{
				//echo '<span class="buttoncard" onclick="morphIntoTextField(this, '.$games[$i].')()" data-type="3" data-game="'.$games[$i].'">Ajouter un remplaçant</span><br />';
				echo '<span class="playercard"></span><br />';
			}
			else
			{
				echo '<span class="playercard"></span><br />';
			}
		}

		echo '</div>';

	// TODO : confirm delete participation
	}
	else
	{
		if ($game_data["G_NUM_PLAYERS"] > 1)
		{
			echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Rejoindre une équipe</td><td><hr class="line" /></td></tr></table></p>';
			echo '<br /><p style="text-align: center;" class="smallquote">Si vous souhaitez <span style="font-weight:bold;">rejoindre une équipe</span>,<br /><u>votre chef d\'équipe doit d\'abord vous inviter</u>.</p><br /<br />';

			/*

			$temp = $database->req('SELECT sgl_users.login, sgl_users.id FROM sgl_teams, sgl_users WHERE sgl_teams.user="'.$_SESSION["sgl_id"].'" AND sgl_teams.lead = sgl_users.id AND game="'.$games[$i].'" AND sgl_teams.register = 0');

			while($data = $temp->fetch())
			{
				//echo '<p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'&accept='.$data["id"].'" class="button">Accepter l\'invitation de '.htmlspecialchars($data["login"]).'</a></p><br />';
			}
			*/
		}

		echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Inscription</td><td><hr class="line" /></td></tr></table></p>';

		if ($game_data["G_NUM_PLAYERS"] > 1)
		{
			echo '<br /><p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;action=create" class="button">Créer son équipe</a></p><br />';
		}
		else
		{
			echo '<br /><p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;action=create" class="button">S\'inscrire à la compétition</a></p>';
		}

		if ($game_data["G_NUM_PLAYERS"] > 1)
		{
			echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Chercher des joueurs</td><td><hr class="line" /></td></tr></table></p>';
		}
	}
}
else
{
	echo '<p style="text-align: center;">Vous devez d\'abord vous inscrire sur le site pour pouvoir participer...<br /><br /><a href="index.php?page=register" class="button">S\'inscrire à la SGL</a></p>';
}

echo '<br /><br />';

echo '<p style="text-align:center;"><b><i class="fa fa-question-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> Des questions ? Besoin d\'aide ? A le recherche de joueurs ?<br />Venez nous rejoindre sur <a target="_blank" href="https://discord.gg/sgnw">Discord</a> :D !</p>';




	}
}
?>
		<br />
	</div>
</div>
