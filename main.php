<?php


$content = new Content();
$signup = new SignUp();

$login = new Login();

$checkforms = new CheckForms();

$email = '';
$passwort = '';
#Common::dump($_REQUEST);



if ($_REQUEST['email']) 
{
	$email = $_REQUEST['email'];
}
else
{
	$email = $_SESSION['nutzer']['email'];
}
if ($_REQUEST['passwort'])
{
	$passwort = $_REQUEST['passwort'];
}
else
{
	$passwort = $_SESSION['nutzer']['passwort'];
}
$check = $login->checkLogin($email, $passwort);
#Common::dump($check, '', true);
if($check)
{
	$login->setSession();
}	
else
{
	header('location: login.php');
}

$checkforms->processInput();
#Common::dump($_SESSION);
?>

<html>
	<head>
		<title>bugenbook24.ru</title>
		<link rel="stylesheet" href="<?php echo URL_ROOT?>/style/style.css" type="text/css"
			title="Default Style" />
		<script src="js/jquery-1.7.2.min.js" type="text/javascript"></script> 
     	<script src="js/beitrag.js" type="text/javascript"></script>
     	<script type="text/javascript" src="<?php echo URL_ROOT?>/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
     	<link rel="stylesheet" href="<?php echo URL_ROOT?>/js/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
	</head>
	<body>
<?php

	$content->header();

?>
	<div id="contend">
	<?php 
		$content->leftMenu();
		$content->middlebox();
	?>
	</div>
	</body>
</html>