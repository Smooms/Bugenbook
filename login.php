<?php

header('Content-type: text/html; charset="utf-8"');
require_once 'init.php';

$signup = new SignUp();


#Common::dump($_REQUEST);
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
		<?php 
		if($signup->ifDuplicateEmail($_REQUEST['email']))
		{
			echo "Es gibt bereits einen User unter dieser E-Mail Adresse";
		}
		?>
			<div class="left">
				<div style="font-weight: bold; width: 60%; margin: 5% 30%;">Bugenbook ermöglicht es dir, mit den retardierten Spacken aus deinem Berufsbildungswerk in Verbindung zu treten und Unsinnigkeiten mit diesen zu verbreiten.</div>
				<img src="<?php echo URL_IMAGES; ?>/OBaVg52wtTZ.png" alt="" />
			</div>
			<div class="rigth">
				Registrieren
				<br/>
				Bugenbook ist und bleibt von der Hello24 Group.
				<br/><br/>
				<form action="<?php echo URL_ROOT;?>/signup/" method="post">
					<input 	id="vorname" 
							name="vorname" 
							placeholder="Vorname" 
							<?php 
								echo ($signup->checkVorname($_REQUEST['vorname']) or !isset($_REQUEST['vorname'])) ? '' : 'style="background-color: #FF9595;"';
								echo (isset($_REQUEST['vorname'])) ? ' value="' . $_REQUEST['vorname'] . '" ' : '';
							?>
							onchange="if(this.value != '') this.style.backgroundColor = 'white'"
							onblur="if(this.value == '') document.getElementById('vorname').style.backroundColor = '#FF9595';"
					/>
					<input 	id="nachname" 
							name="nachname" 
							placeholder="Nachname"
							<?php 
								echo ($signup->checkNachname($_REQUEST['nachname']) or !isset($_REQUEST['nachname'])) ? '' : 'style="background-color: #FF9595;"';
								echo (isset($_REQUEST['nachname'])) ? ' value="' . $_REQUEST['nachname'] . '" ' : '';
							?>
							onchange="if(this.value != '') this.style.backgroundColor = 'white'"
							onblur="if(!this.value) this.value = this.defaultValue;"
					/>
					<br/><br/>
					<input 	id="email" 
							name="email" 
							placeholder="E-Mail" 
							<?php 
								echo ($signup->checkEmail(	$_REQUEST['email'], 
															$_REQUEST['email_check']) 
														or 	!isset($_REQUEST['email'])) ? '' : 'style="background-color: #FF9595;"';
								echo (!$signup->ifDuplicateEmail($_REQUEST['email'])) ? '' : 'style="background-color: #FF9595;"';
								echo (isset($_REQUEST['email'])) ? ' value="' . $_REQUEST['email'] . '" ' : '';
							?>
							onchange="if(this.value != '') this.style.backgroundColor = 'white'"
							onblur="if(!this.value) this.value = this.defaultValue;"
					/>
					<br/><br/>
					<input 	id="email_check" 
							name="email_check" 
							placeholder="E-Mail wiederholen" 
							<?php 
								echo ($signup->checkEmail($_REQUEST['email'], $_REQUEST['email_check']) or !isset($_REQUEST['email_check'])) ? '' : 'style="background-color: #FF9595;"';
								echo (!$signup->ifDuplicateEmail($_REQUEST['email'])) ? '' : 'style="background-color: #FF9595;"';
								echo (isset($_REQUEST['email'])) ? ' value="' . $_REQUEST['email'] . '" ' : '';
							?>
							onchange="if(this.value != '') this.style.backgroundColor = 'white'"
							onblur="if(!this.value) this.value = this.defaultValue;"
					/>
					<br/><br/>
					<input 	type ="text"
							id="passwort" 
							name="passwort" 
							placeholder="Passwort" 
							<?php 
								echo ($signup->checkPasswort($_REQUEST['passwort']) or !isset($_REQUEST['passwort'])) ? '' : 'style="background-color: #FF9595;"';
							?>
							onchange="if(this.value != '') this.style.backgroundColor = 'white'"
							onfocus="	if(this.value == this.defaultValue) this.value = ''; 
										this.setAttribute('type','password');"
					/>
					<br/><br/>
					Geburtstag <br/>
					<select 	id="tag" 
								name="tag"
								<?php 
									echo ($signup->checkGeburtstag(Date::toMysqlDate($_REQUEST['tag'], $_REQUEST['monat'], $_REQUEST['jahr'])) or !isset($_REQUEST['tag'])) ? '' : 'style="background-color: #FF9595;"';
								?>
								onchange="if(this.value != 'tag') this.style.backgroundColor = 'white'"
					>
						<option>Tag</option>
						<?php 
						for($i= 1; $i <= 31; $i++)
						{
							?>
							<option <?php echo ($i == $_REQUEST['tag']) ? ' selected="selected" ' : '';?>><?php echo $i;?></option>
							<?php							
						}
						?>
					</select>
					
					<select 	id="monat" 
								name="monat"
								<?php 
									echo ($signup->checkGeburtstag(Date::toMysqlDate($_REQUEST['tag'], $_REQUEST['monat'], $_REQUEST['jahr'])) or !isset($_REQUEST['monat'])) ? '' : 'style="background-color: #FF9595;"';
								?>
								onchange="if(this.value != 'monat') this.style.backgroundColor = 'white'"
					>
						<option>Monat</option>
						<?php 
						for($i= 1; $i <= 12; $i++)
						{
							?>
							<option<?php echo ($i == $_REQUEST['monat']) ? ' selected="selected" ' : '';?>><?php echo $i;?></option>
							<?php							
						}
						?>
					</select>
					<select 	id="jahr" 
								name="jahr"
								<?php 
									echo ($signup->checkGeburtstag(Date::toMysqlDate($_REQUEST['tag'], $_REQUEST['monat'], $_REQUEST['jahr'])) or !isset($_REQUEST['jahr'])) ? '' : 'style="background-color: #FF9595;"';
								?>
								onchange="if(this.value != 'jahr') this.style.backgroundColor = 'white'"
					>
						<option>Jahr</option>
						<?php 
						for($i= 1930; $i <= date('Y') - 14; $i++)
						{
							?>
							<option<?php echo ($i == $_REQUEST['jahr']) ? ' selected="selected" ' : '';?>><?php echo $i;?></option>
							<?php							
						}
						?>
					</select>
					<br/><br/>
					<input 	type="radio" 
							id="weiblich" 
							name="geschlecht" 
							value="2"
							<?php 
								echo ($_REQUEST['geschlecht'] == "2") ? 'checked="checked"' : '';
							?>
							onclick="if(this.value == '1' or this.value == '2') this.style.backgroundColor = 'white'"
							/>
								<label for="weiblich"
								<?php 
									echo 	(	$signup->checkGeschlecht($_REQUEST['geschlecht']) OR
												(!isset($_REQUEST['geschlecht']) AND !isset($_REQUEST['tag']))
											) 
											? '' : 'style="background-color: #FF9595;"';
								?>
								> Weiblich</label> 
					<input 	type="radio" 
							id="maennlich" 
							name="geschlecht" 
							value="1" 
							<?php 
								echo ($_REQUEST['geschlecht'] == "1") ? 'checked="checked"' : '';
							?>
							onclick="if(this.value == '1' or this.value == '2') this.style.backgroundColor = 'white'"
							/>
								<label for="maennlich"
								<?php 
										echo 	(	$signup->checkGeschlecht($_REQUEST['geschlecht']) OR
													(!isset($_REQUEST['geschlecht']) AND !isset($_REQUEST['tag']))
												) 
												? '' : 'style="background-color: #FF9595;"';
								?>
								> Männlich</label>
					<br/><br/>
					<button id="absenden" name="absenden" value="registrierung" style="height: 30px; background-color: #69a74e; color: white;">Registrierung</button>
				</form>
			</div>
		</div>
	</body>
</html>