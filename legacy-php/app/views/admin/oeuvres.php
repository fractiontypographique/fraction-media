<h1>Œuvres d'art</h1>

<form method="post" enctype="multipart/form-data" action="/admin/oeuvres/enregistrer<?php echo $editer ? '/' . (int)$editer['id'] : ''; ?>">
    <?php echo csrf_field(); ?>
    <div class="champ">
        <label for="otitre">Titre *</label>
        <input type="text" id="otitre" name="titre" required value="<?php echo e($editer['titre'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="oartiste">Artiste</label>
        <input type="text" id="oartiste" name="artiste" value="<?php echo e($editer['artiste'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="oannee">Année</label>
        <input type="number" id="oannee" name="annee" min="0" max="3000" value="<?php echo isset($editer['annee']) ? (int)$editer['annee'] : ''; ?>">
    </div>
    <div class="champ">
        <label for="otechnique">Technique</label>
        <input type="text" id="otechnique" name="technique" placeholder="photographie argentique, huile sur toile…" value="<?php echo e($editer['technique'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="odesc">Description</label>
        <textarea id="odesc" name="description" style="min-height:100px"><?php echo e($editer['description'] ?? ''); ?></textarea>
    </div>
    <div class="champ">
        <label for="oimage">Image * <?php echo isset($editer['image']) ? ' (nouveau fichier = remplacement)' : ''; ?></label>
        <input type="file" id="oimage" name="image" accept="image/*" <?php echo $editer ? '' : 'required'; ?>>
        <?php if (isset($editer['image']) && $editer['image']): ?><p class="aide">Actuelle : <a href="/<?php echo e($editer['image']); ?>" target="_blank"><?php echo e($editer['image']); ?></a> — enregistrer sans nouveau fichier met l'ancienne image à zéro après édition, préférez uploader une nouvelle image.</p><?php endif; ?>
    </div>
    <div class="champ">
        <label class="champ-inline"><input type="checkbox" name="publiee" value="1" <?php echo !isset($editer['publiee']) || $editer['publiee'] ? 'checked' : ''; ?>> Publiée</label>
    </div>
    <p>
        <button class="bouton" type="submit"><?php echo $editer ? 'Mettre à jour' : 'Créer'; ?></button>
        <?php if ($editer): ?><a href="/admin/oeuvres">nouvelle œuvre</a><?php endif; ?>
    </p>
</form>

<table class="tableau">
    <tr><th>Aperçu</th><th>Titre</th><th>Artiste</th><th>Publiée</th><th></th></tr>
    <?php foreach ($oeuvres as $o): ?>
    <tr>
        <td><img src="/<?php echo e($o['image']); ?>" alt=""></td>
        <td><strong><?php echo e($o['titre']); ?></strong></td>
        <td><?php echo e($o['artiste'] . ($o['annee'] ? ', ' . $o['annee'] : '')); ?></td>
        <td><?php echo $o['publiee'] ? 'oui' : 'non'; ?></td>
        <td>
            <a href="/admin/oeuvres/editer/<?php echo (int)$o['id']; ?>">modifier</a> ·
            <form method="post" action="/admin/oeuvres/supprimer/<?php echo (int)$o['id']; ?>" style="display:inline" onsubmit="return confirm('Supprimer cette œuvre ?');">
                <?php echo csrf_field(); ?>
                <button type="submit" style="border:none;background:none;color:#c5221f;cursor:pointer;padding:0">supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
