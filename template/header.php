<!DOCTYPE html>
<html>
	<head>
		<title>Student Gaming League</title>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<link rel="icon" type="image/png" href="./style/img/favicon.png" />
		<link rel="stylesheet" media="screen,print" href="./style/style.css?ver=2.1" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js" charset="utf-8"></script>
		<script type="text/javascript" src="https://rawgit.com/patriciogonzalezvivo/glslCanvas/master/dist/GlslCanvas.js"></script>
		<meta property="og:title" content="Student Gaming League" />
		<meta property="og:url" content="https://league.sgnw.fr/" />
		<meta property="og:type" content="website" />
		<meta property="og:description" content="Prêt à rejoindre la première league esport étudiante ? Venez défendre les couleurs de votre école jusqu'au podium !" />
		<meta property="og:image" content="http://league.sgnw.fr/style/img/social.png?v=2018" />
		<meta property="og:image:secure_url" content="https://league.sgnw.fr/style/img/social.png?v=2018" />
		<meta property="og:image:type" content="image/png" />

		<!-- Facebook Pixel Code -->
		<script>
		  !function(f,b,e,v,n,t,s)
		  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		  n.queue=[];t=b.createElement(e);t.async=!0;
		  t.src=v;s=b.getElementsByTagName(e)[0];
		  s.parentNode.insertBefore(t,s)}(window, document,'script',
		  'https://connect.facebook.net/en_US/fbevents.js');
		  fbq('init', '144764022857455');
		  fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		  src="https://www.facebook.com/tr?id=144764022857455&ev=PageView&noscript=1"
		/></noscript>
		<!-- End Facebook Pixel Code -->

		<?=isset($page_head)?$page_head:'';?>
		<?=isset($page_script)?'<script src="./pages/'.CURRENT_PAGE.'/script.js?v=2018-2" charset="utf-8"></script>':''?>
	</head>
	<body>
		<div id="topmenu">
			<div class="page">
				<div class="left">
					<div id="social">
						<span class="media"><a target="_blank" href="https://www.facebook.com/StudentGamingNetwork/"><i class="fa fa-facebook-square"></i></a></span>
						<span class="media"><a target="_blank" href="https://twitter.com/Student_GN"><i class="fa fa-twitter-square"></i></a></span>
						<span class="media"><a target="_blank" href="https://discord.gg/sgnw"><img src="./style/img/icon/discord.png" alt="discord"></a></span>
						<span class="media"><a target="_blank" href="https://steamcommunity.com/groups/sgnw"><i class="fa fa-steam-square"></i></a></li></span>
					</div>
					<div id="meta">
<?php
if (isset($_SESSION["sgl_id"]))
{
	echo "<script>
  fbq('track', 'ViewContent', {
    content_ids: '".$_SESSION["sgl_id"]."',
  });
</script>";
?>
						<a href="index.php?page=account">
							<i class="fa fa-angle-right" aria-hidden="true"></i>
							<?=htmlspecialchars($_SESSION["sgl_login"])?>
						</a>
						<a href="index.php?page=connect&amp;disconnect=1">
							<i class="fa fa-angle-right" aria-hidden="true"></i>
							Déconnexion
						</a>
<?php
}
else
{
?>
						<a href="index.php?page=connect">
							<i class="fa fa-angle-right" aria-hidden="true"></i>
							Connexion
						</a>
						<a href="index.php?page=register">
							<i class="fa fa-angle-right" aria-hidden="true"></i>
							Inscription
						</a>
<?php
}
?>
					</div>
				</div>
				<div class="right">
					<div id="menu">
<?php

$gpage = 0;

if (!isset($page_tab))
{
	$page_tab = "home";
}
else
{
	if(($page_tab == "games") && isset($_GET["gpage"]))
	{
		$gpage = intval($_GET["gpage"]);
	}
}

?>
						<a href="index.php" <?=$page_tab=="home"?'class="selected"':''?>>Accueil</a>
						<a href="index.php?page=games&amp;gpage=2" <?=$gpage==2?'class="selected"':''?>>L<i>eague of </i>L<i>egends</i></a>
						<a href="index.php?page=games&amp;gpage=4" <?=$gpage==4?'class="selected"':''?>>H<i>earthstone</i></a>
						<a href="index.php?page=games&amp;gpage=3" <?=$gpage==3?'class="selected"':''?>>C<i>ounter </i>S<i>trike</i></a>
						<a href="index.php?page=games&amp;gpage=1" <?=$gpage==1?'class="selected"':''?>>O<i>verwatch</i></a>
						<a href="index.php?page=contact" <?=$page_tab=="contact"?'class="selected"':''?>>Contact</a>
					</div>
				</div>
			</div>
		</div>
		<div class="page">
			<div id="header">
				<div class="partners left">
					<a class="smplink" target="_blank" href="https://shadow.tech/"><img src="./style/img/partners/sml_shadow.png" alt="Shadow" /></a>
					<a class="smplink" target="_blank" href="https://www.facebook.com/starxium/"><img src="./style/img/partners/sml_stx.png" alt="Starxium 20XX" /></a>
				</div>
				<div id="logo">
					<a href="index.php">
						<img src="./style/img/logo.png" alt="logo" id="logoImg"/>
					</a>
				</div>
				<div class="partners right">
					<a class="smplink" target="_blank" href="https://www.twitch.tv/"><img src="./style/img/partners/sml_twitch.png" alt="Twitch" /></a>
					<a class="smplink" target="_blank" href="http://www.fnatic.com/"><img src="./style/img/partners/sml_fnatic.png" alt="Fnatic" /></a>
				</div>
			</div>﻿