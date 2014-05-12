<?php

header('Content-type: text/html; charset="utf-8"');
require_once 'init.php';

$signup = new SignUp();

$geburtstag = Date::toMysqlDate($_REQUEST['tag'], $_REQUEST['monat'], $_REQUEST['jahr']);

if ($signup->checkInput($_REQUEST['vorname'], $_REQUEST['nachname'], $_REQUEST['email'], $_REQUEST['email_check'], $_REQUEST['passwort'], $geburtstag, $_REQUEST['geschlecht'])
	AND
	!$signup->ifDuplicateEmail($_REQUEST['email']))
{	
	$signup->insertNewUser(	$_REQUEST['vorname'], 
							$_REQUEST['nachname'], 
							$_REQUEST['email'],  
							$_REQUEST['passwort'], 
							Date::toMysqlDate($_REQUEST['tag'], $_REQUEST['monat'], $_REQUEST['jahr']), 
							$_REQUEST['geschlecht']);
}
elseif ($_REQUEST['status'] == 'activate')
{
	$signup->activateUser($_REQUEST['id'], $_REQUEST['code']);
}
else 
{
	header('loation: login.php');
}
?>


<html>
	<head>
		<title>Bugenbook24.ru</title>
		<link rel="stylesheet" href="<?php echo URL_ROOT?>/style/login.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo URL_ROOT?>/style/style.css" type="text/css" />
	</head>
	<body>
		<div id="headlogin">
			<div class="headcontendlogin">
				<div class ="headerlogologin">
					bugenbook
				</div>
				<div class="menu">
					<form action="<?php echo URL_ROOT?>/" method="post">
					     E-Mail: <input type="text" name="email"/>
					     Passwort: <input type="password" name="passwort"/>
					     <input type="submit" name="absenden" value="Absenden"/>
					</form>
				</div>
				<div class="searchbar">
				</div>
			</div>
		</div>
		<div id="content">
			<div class="signtext">
				<?php 
					if ($_REQUEST['status'] == 'activate')
					{
						?>
						Sie haben die Anmeldung erfolgreich durchgeführt!
						Melden sie sich gleich mit Ihrer E-Mail und Ihrem Passwort an!
						<?php
					}
					else 
					{
						?>
						Sie haben sich erfolgreich angemeldet. Bitte bestätigen Sie jetzt hierzu noch die von uns <br/>
						erhaltene E-Mail die sie unter Ihrer Adresse <?php echo $_REQUEST['email'];?> erhalten haben und <br/>
						klicken Sie auf den Bestätigungslink in der E-mail, um Ihr Konto zu aktivieren.
						<?php
					}
				
				?>
				
			</div>
		</div>
	</body>
</html>