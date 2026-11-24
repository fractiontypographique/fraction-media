<h1><?php echo $article ? 'Modifier l’article' : 'Nouvel article'; ?></h1>
<form method="post" enctype="multipart/form-data" action="/admin/articles/<?php echo $article ? 'enregistrer/' . (int)$article['id'] : 'nouveau'; ?>">
    <?php echo csrf_field(); ?>

    <div class="champ">
        <label for="titre">Titre *</label>
        <input type="text" id="titre" name="titre" required value="<?php echo e($article['titre'] ?? ''); ?>">
    </div>

    <div class="champ">
        <label for="slug">Slug (URL)</label>
        <input type="text" id="slug" name="slug" placeholder="automatique à partir du titre" value="<?php echo e($article['slug'] ?? ''); ?>">
    </div>

    <div class="champ">
        <label for="chapo">Chapô</label>
        <textarea id="chapo" name="chapo" style="min-height:80px"><?php echo e($article['chapo'] ?? ''); ?></textarea>
    </div>

    <div class="champ">
        <label for="corps">Corps (Markdown : ## titre, > citation, **gras**, *italique*, [lien](https://…), listes avec — )</label>
        <textarea id="corps" name="corps" style="min-height:340px" required><?php echo e($article['corps'] ?? ''); ?></textarea>
    </div>

    <div class="champ">
        <label for="image">Image de couverture <?php echo isset($article['image']) && $article['image'] ? ' (laisser vide pour conserver)' : ''; ?></label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($article['image'])): ?><p class="aide">Actuelle : <a href="/<?php echo e($article['image']); ?>" target="_blank"><?php echo e($article['image']); ?></a></p><?php endif; ?>
    </div>

    <div class="champ">
        <label for="legende">Légende de couverture (avec crédit)</label>
        <input type="text" id="legende" name="legende" value="<?php echo e($article['legende'] ?? ''); ?>">
    </div>

    <div class="champ">
        <label for="rubrique_id">Rubrique *</label>
        <select id="rubrique_id" name="rubrique_id" required>
            <option value="">— choisir —</option>
            <?php foreach ($rubriques as $r): ?>
                <option value="<?php echo (int)$r['id']; ?>" <?php echo (isset($article['rubrique_id']) && (int)$article['rubrique_id'] === (int)$r['id']) ? 'selected' : ''; ?>><?php echo e($r['nom']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="champ">
        <label>Auteur·rices</label>
        <div class="checks">
            <?php foreach ($tousAuteurs as $au): ?>
                <label><input type="checkbox" name="auteurs[]" value="<?php echo (int)$au['id']; ?>" <?php echo in_array((int)$au['id'], $auteursChoisis, true) ? 'checked' : ''; ?>> <?php echo e($au['nom'] ?: $au['username']); ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="champ">
        <label>Tags</label>
        <div class="checks">
            <?php foreach ($tousTags as $t): ?>
                <label><input type="checkbox" name="tags[]" value="<?php echo (int)$t['id']; ?>" <?php echo in_array((int)$t['id'], $tagsChoisis, true) ? 'checked' : ''; ?>> #<?php echo e($t['nom']); ?></label>
            <?php endforeach; ?>
        </div>
        <p class="aide">Créer des tags dans la section <a href="/admin/tags">Tags</a>.</p>
    </div>

    <div class="champ">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
            <option value="brouillon" <?php echo (isset($article['statut']) && $article['statut'] === 'brouillon') ? 'selected' : ''; ?>>Brouillon</option>
            <option value="public" <?php echo (isset($article['statut']) && $article['statut'] === 'public') ? 'selected' : ''; ?>>Publication programmée</option>
            <option value="publie" <?php echo (isset($article['statut']) && $article['statut'] === 'publie') ? 'selected' : (!isset($article['statut']) ? 'selected' : ''); ?>>Publié</option>
        </select>
    </div>

    <div class="champ">
        <label for="date_publication">Date de publication (dates passées : déjà parue ; futures : publication programmée)</label>
        <input type="datetime-local" id="date_publication" name="date_publication" value="<?php echo isset($article['date_publication']) ? e(str_replace(' ', 'T', substr($article['date_publication'], 0, 16))) : ''; ?>">
    </div>

    <div class="champ">
        <label class="champ-inline"><input type="checkbox" name="a_la_une" value="1" <?php echo !empty($article['a_la_une']) ? 'checked' : ''; ?>> Mettre à la une (affiché en tête de l'accueil)</label>
    </div>

    <p><button class="bouton" type="submit">Enregistrer</button> — <a href="/admin/articles">retour</a></p>
</form>
