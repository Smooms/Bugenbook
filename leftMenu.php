<?php
	$Freunde = new Freunde();
	
	$freude_array = $Freunde->getFreunde(5);
	
?>
<link rel="stylesheet" href="<?php echo URL_CSS ?>/nachrichten.css" type="text/css" title="Nachrichten Style" />
<div id="menu">
	<?php 
		Common::dump($freude_array);
		foreach ($freude_array as $key => $value)
		{
			?>
				<a class="link" href="<?php echo URL_ROOT?>/pm/<?php echo $value['freund_id'] ?>/">
					<li class="listen_eintrag" >
						<img class="profilbild" id="<?php echo $key?>" alt="" src="<?php echo URL_MEDIA . '/user/' . $value['freund_id'] . '/' . $value['profilbild'] ?>" />
						<?php echo $value['vorname'] . ' ' . $value['nachname'] ?>
					</li>
				</a>
			<?php
		}
	?>
</div>