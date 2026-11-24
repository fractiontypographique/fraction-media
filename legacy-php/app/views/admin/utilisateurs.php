<h1>Utilisateurs</h1>

<form method="post" action="/admin/utilisateurs/enregistrer<?php echo $editer ? '/' . (int)$editer['id'] : ''; ?>">
    <?php echo csrf_field(); ?>
    <div class="champ">
        <label for="uusername">Identifiant *</label>
        <input type="text" id="uusername" name="username" required value="<?php echo e($editer['username'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="unom">Nom affiché (byline)</label>
        <input type="text" id="unom" name="nom" value="<?php echo e($editer['nom'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="upassword">Mot de passe <?php echo $editer ? '(laisser vide pour conserver)' : '* (8 caractères minimum)'; ?></label>
        <input type="password" id="upassword" name="password" <?php echo $editer ? '' : 'required'; ?> minlength="8">
    </div>
    <div class="champ">
        <label for="urole">Rôle</label>
        <select id="urole" name="role">
            <option value="redacteur" <?php echo (isset($editer['role']) && $editer['role'] !== 'admin') ? 'selected' : (!isset($editer['role']) ? 'selected' : ''); ?>>Rédacteur·rice</option>
            <option value="admin" <?php echo (isset($editer['role']) && $editer['role'] === 'admin') ? 'selected' : ''; ?>>Administrateur·rice</option>
        </select>
    </div>
    <div class="champ">
        <label for="ubio">Bio</label>
        <textarea id="ubio" name="bio" style="min-height:80px"><?php echo e($editer['bio'] ?? ''); ?></textarea>
    </div>
    <p>
        <button class="bouton" type="submit"><?php echo $editer ? 'Mettre à jour' : 'Créer le compte'; ?></button>
        <?php if ($editer): ?><a href="/admin/utilisateurs">nouveau compte</a><?php endif; ?>
    </p>
</form>

<table class="tableau">
    <tr><th>Identifiant</th><th>Nom</th><th>Rôle</th><th></th></tr>
    <?php foreach ($personnes as $pers): ?>
    <tr>
        <td><strong><?php echo e($pers['username']); ?></strong></td>
        <td><?php echo e($pers['nom']); ?></td>
        <td><span class="badge <?php echo $pers['role'] === 'admin' ? 'badge-publie' : 'badge-public'; ?>"><?php echo e($pers['role']); ?></span></td>
        <td>
            <a href="/admin/utilisateurs/editer/<?php echo (int)$pers['id']; ?>">modifier</a>
            <?php if ((int)$pers['id'] !== (int)current_user()['id']): ?> ·
            <form method="post" action="/admin/utilisateurs/supprimer/<?php echo (int)$pers['id']; ?>" style="display:inline" onsubmit="return confirm('Supprimer ce compte ?');">
                <?php echo csrf_field(); ?>
                <button type="submit" style="border:none;background:none;color:#c5221f;cursor:pointer;padding:0">supprimer</button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
