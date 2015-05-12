<?php
$message.="<h3>Le client a demandé une mise à jour de la commande <strong>".$_POST['idcommande']."</strong>.</h3>";
$message.="<ul>";
 foreach ($_POST as $name => $value) {

 //$message.= $name.' : '. $value.'<br />';

$search = array ('@<script[^>]*?>.*?</script>@si', // Supprime le javascript
                 '@maj@i');

$replace = array ('','');

$name = preg_replace($search, $replace, $name);
if(is_numeric($name)) {
	//echo $name.' =>'.$value.'<br />';
	$res=mysql_query("SELECT * from article WHERE reference=".$name);
	$donnees = mysql_fetch_assoc($res);
	$message.='<li><img src="http://www.top-fishing.fr/images_produits/p_'.$donnees['photo1'].'" /><a href="http://www.top-fishing.fr/catalogue/article/'.$donnees['url_article'].'.html">'.$donnees['nom'].'</a> - '.$value.'</li>';
	}
}
$message.="</ul>";
$message.="<p>Remarques clients : ".utf8_decode($_POST['note-maj'])."</p>";

  $message=utf8_decode($message);
	$headers = "Reply-to: \"Top Fishing | Disponibilité \" <info@top-fishing.fr>\n"; 
	$headers.= "From: \"Top Fishing | Disponibilité\"<info@top-fishing.fr>\n"; 
	$headers.= "MIME-version: 1.0\n"; 
	$headers .='Content-Type: text/html; charset="iso-8859-15"'."\n"; 

	$sujet = utf8_decode("Réponse client Maj n° ".$_POST['idcommande']);
	$commercant= 'info@top-fishing.fr' . ', '; // notez la virgule
     $commercant.= 'jeanloup@kocliko.fr';
		  // Envoi du mail de confirmation au client et au commercant  
	
			mail($commercant, $sujet, $message, $headers);// Mail client------------       
	  
 ?>