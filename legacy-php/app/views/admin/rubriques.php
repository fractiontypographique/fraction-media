<h1>Rubriques</h1>
<form method="post" action="/admin/rubriques/enregistrer<?php echo $editer ? '/' . (int)$editer['id'] : ''; ?>">
    <?php echo csrf_field(); ?>
    <div class="champ">
        <label for="nom">Nom *</label>
        <input type="text" id="nom" name="nom" required value="<?php echo e($editer['nom'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="rslug">Slug</label>
        <input type="text" id="rslug" name="slug" placeholder="auto" value="<?php echo e($editer['slug'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="rdesc">Description (affichée en tête de page)</label>
        <textarea id="rdesc" name="description" style="min-height:80px"><?php echo e($editer['description'] ?? ''); ?></textarea>
    </div>
    <div class="champ">
        <label for="rordre">Ordre</label>
        <input type="number" id="rordre" name="ordre" value="<?php echo (int)($editer['ordre'] ?? 0); ?>">
    </div>
    <p>
        <button class="bouton" type="submit"><?php echo $editer ? 'Mettre à jour' : 'Créer'; ?></button>
        <?php if ($editer): ?><a href="/admin/rubriques">nouvelle rubrique</a><?php endif; ?>
    </p>
</form>

<table class="tableau">
    <tr><th>Nom</th><th>Slug</th><th>Ordre</th><th></th></tr>
    <?php foreach ($rubriques as $r): ?>
    <tr>
        <td><strong><?php echo e($r['nom']); ?></strong></td>
        <td><?php echo e($r['slug']); ?></td>
        <td><?php echo (int)$r['ordre']; ?></td>
        <td>
            <a href="/admin/rubriques/editer/<?php echo (int)$r['id']; ?>">modifier</a> ·
            <a href="/rubrique/<?php echo e($r['slug']); ?>/" target="_blank">voir</a> ·
            <form method="post" action="/admin/rubriques/supprimer/<?php echo (int)$r['id']; ?>" style="display:inline" onsubmit="return confirm('Supprimer cette rubrique ?');">
                <?php echo csrf_field(); ?>
                <button type="submit" style="border:none;background:none;color:#c5221f;cursor:pointer;padding:0">supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
