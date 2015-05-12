<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h3 style="margin-top: 0">Nouveau mot de passe :</h3>
        <div class="alert alert-info"><i class="glyphicon glyphicon-info-sign"></i> Le mot de passe doit être composé de 6 caractères alphanumériques.</div>
        <form action="index.php?action=modifPassword" method="post" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-4">   
                    <label for="old_password" class="control-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" name="old_password" id="old_password"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4">
                    <label for="new_password" class="control-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" name="new_password" id="new_password"/>
                </div>
                <div class="col-sm-4">
                    <label for="verif_password" class="control-label">Vérification nouveau mot de passe</label>
                    <input type="password" name="verif_password" id="verif_password" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8">
                    <button style=" width: 49.5%;" type="submit" class="btn btn-success">Enregistrer</button>
                    <a style="width: 49.5%;display: inline-block;" class="btn btn-danger" href="index.php">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>