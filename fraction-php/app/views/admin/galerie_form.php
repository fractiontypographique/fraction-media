<h1><?php echo isset($editer['id']) ? 'Gérer la galerie' : 'Nouvelle galerie'; ?></h1>

<form method="post" action="/admin/galeries/enregistrer<?php echo isset($editer['id']) ? '/' . (int)$editer['id'] : ''; ?>">
    <?php echo csrf_field(); ?>
    <div class="champ">
        <label for="gtitre">Titre *</label>
        <input type="text" id="gtitre" name="titre" required value="<?php echo e($editer['titre'] ?? ''); ?>">
    </div>
    <div class="champ">
        <label for="gdesc">Description</label>
        <textarea id="gdesc" name="description" style="min-height:100px"><?php echo e($editer['description'] ?? ''); ?></textarea>
    </div>
    <div class="champ">
        <label for="gphoto">Photographe</label>
        <select id="gphoto" name="photographe_id">
            <option value="">—</option>
            <?php foreach ($photographes as $ph): ?>
                <option value="<?php echo (int)$ph['id']; ?>" <?php echo (isset($editer['photographe_id']) && (int)$editer['photographe_id'] === (int)$ph['id']) ? 'selected' : ''; ?>><?php echo e($ph['nom'] ?: $ph['username']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="champ">
        <label class="champ-inline"><input type="checkbox" name="publiee" value="1" <?php echo !isset($editer['publiee']) || $editer['publiee'] ? 'checked' : ''; ?>> Publiée</label>
    </div>
    <p><button class="bouton" type="submit">Enregistrer</button> — <a href="/admin/galeries">retour</a></p>
</form>

<?php if (isset($editer['id'])): ?>
<h2 class="section-titre">Photographies (<?php echo count($photos); ?>)</h2>

<form method="post" enctype="multipart/form-data" action="/admin/galeries/photo-ajouter/<?php echo (int)$editer['id']; ?>">
    <?php echo csrf_field(); ?>
    <div class="champ">
        <label for="pimage">Ajouter une photographie *</label>
        <input type="file" id="pimage" name="image" accept="image/*" required>
    </div>
    <div class="champ">
        <label for="plegende">Légende</label>
        <input type="text" id="plegende" name="legende">
    </div>
    <div class="champ">
        <label for="pcredit">Crédit (ex. : © Nom)</label>
        <input type="text" id="pcredit" name="credit">
    </div>
    <div class="champ">
        <label class="champ-inline"><input type="checkbox" name="mise_en_avant" value="1"> Mise en avant</label>
    </div>
    <p><button class="bouton" type="submit">+ Ajouter la photographie</button></p>
</form>

<table class="tableau">
    <tr><th>Aperçu</th><th>Légende / crédit</th><th>Ordre</th><th>À la une</th><th></th></tr>
    <?php foreach ($photos as $p): ?>
    <tr>
        <td><img src="/<?php echo e($p['image']); ?>" alt=""></td>
        <td>
            <form method="post" action="/admin/galeries/photo-enregistrer/<?php echo (int)$p['id']; ?>" id="pf-<?php echo (int)$p['id']; ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="galerie_id" value="<?php echo (int)$editer['id']; ?>">
                <input type="text" name="legende" placeholder="légende" value="<?php echo e($p['legende']); ?>" style="width:220px">
                <input type="text" name="credit" placeholder="crédit" value="<?php echo e($p['credit']); ?>" style="width:140px">
            </form>
        </td>
        <td><input form="pf-<?php echo (int)$p['id']; ?>" type="number" name="ordre" value="<?php echo (int)$p['ordre']; ?>" style="width:64px"></td>
        <td><input form="pf-<?php echo (int)$p['id']; ?>" type="checkbox" name="mise_en_avant" value="1" <?php echo $p['mise_en_avant'] ? 'checked' : ''; ?>></td>
        <td>
            <button form="pf-<?php echo (int)$p['id']; ?>" class="bouton" type="submit" style="padding:0.3rem 0.6rem">ok</button>
            <form method="post" action="/admin/galeries/photo-supprimer/<?php echo (int)$p['id']; ?>" style="display:inline" onsubmit="return confirm('Supprimer cette photographie ?');">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="galerie_id" value="<?php echo (int)$editer['id']; ?>">
                <button type="submit" style="border:none;background:none;color:#c5221f;cursor:pointer;padding:0">supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
