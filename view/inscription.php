<div id="content-main3">
    <form action="?action=verifInscription" method="post" name="coordonnees">
        <input type="hidden" name="action_suivante" value="verification" />
        <h3>Création de votre compte client:</h3>
        <hr class="separation" />
        <div class="box_form">
            <h4>a) Informations client</h4>
            <p><label for="email">Email :</label><input type="text" placeholder="gillesallain@exemple.com" name="email" value="<?php echo $context['email']; ?>" size="16" maxlength="50" /> </p>
            <p><label for="password">Password :</label><input type="password" name="pass" value="" size="16" maxlength="50" /></p>
            <p><label for="pass_verif">Confirmer :</label><input type="password" name="pass_verif" value="" size="16" maxlength="50" /></p>
            <hr/>
            <p><label for="nom">Nom :</label>
                <input type="text" name="nom" value="<?php echo $context['nom']; ?>" size="16" maxlength="50"  /></p>
            <p><label for="prenom">Pr&eacute;nom :</label>
                <input type="text" name="prenom" value="<?php echo $context['prenom']; ?>" size="16" maxlength="50" /></p>
            <p><label for="tel"><small>T&eacute;l&eacute;phone</small> :</label><input type="text" name="tel" value="<?php echo $context['tel']; ?>" size="16" maxlength="50" /></p>
            <a  href="/clients/recup_password/" accesskey="" title="Fonction non accessible pour le moment." style="display: inline;text-decoration: none; color: #0098AF; background: none; font-size: 0.8em; float: right;">Modifier mot de passe?</a> 

        </div>
        <div class="box_form">
            <h4>b) Adresse principale (Facturation/Livraison) </h4>
            <p><label for="adresse">Adresse&nbsp;:</label><input type="text" name="adresse" value="<?php echo $context['adresse']; ?>" size="16" maxlength="200"  /></p>
            <p><label for="adresses">Suite* :</label>
                <input type="text" name="adresses" value="<?php echo $context['adresses']; ?>" size="16" maxlength="200" /></p>
            <p><label for="cp"><small>Code&nbsp;Postal&nbsp;:</small></label><input type="text" name="cp" value="<?php echo $context['cp']; ?>" size="5" maxlength="5" /></p>
            <p><label for="ville">Ville :</label><input type="text" name="ville" value="<?php echo $context['ville']; ?>" size="16" maxlength="70" /></p>
            <p>
                <label for="id_pays">Pays : </label>&nbsp;
                <select name="id_pays">
                    <?php
                    $lespays = paysTable::getAllPays();
                    $group == 0;
                    foreach ($lespays as $pays) {

                         if ($previous_region != $pays->region && $previous_region) {
                              // Pas de optgroup pour la France puisque $previous_region n'est pas encore définit
                              if ($group > 1)
                                   echo '</optgroup>'; // On ferme les optgroup à partir du
                              echo '<optgroup label="' . strtoupper($pays->region) . '">';
                              $group++;
                         }
                         // if($panier->getFraisPort() ) $panier->getFraisPort() = 1 ; // France par Défaut
                         $selected = ($context['id_pays'] == $pays->id_pays) ? 'selected="selected"' : '';
                         echo '<option value="' . $pays->id_pays . '" ' . $selected . '>' . $pays->nom . '</option>';
                         $previous_region = $pays->region;
                    }
                    echo '</optgroup>';
                    ?>
                </select>  
        </div>
        <p><strong>*</strong> Indique les champs facultatifs</p>
        <p style="font-size:0.8em"><strong class="lettrine">?</strong>Vos informations sont confidentielles, elles permettent de vous identifier en tant qu'acheteur lors du processus de commande et ne peuvent &ecirc;tre  ni utiliser à des fins publicitaires et ni soumises à un tiers.</p>
        <hr class="separation" />

        <ul class="bt_action">
            <li>  <input class="action_importante" type="submit" name="saisie_form" value="S'inscrire maintenant"/>  </li>
        </ul>
    </form>
</div>