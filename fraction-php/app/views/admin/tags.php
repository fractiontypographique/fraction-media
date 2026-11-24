<h1>Tags</h1>
<form method="post" action="/admin/tags/enregistrer" style="margin-bottom:1rem">
    <?php echo csrf_field(); ?>
    <label for="tnew" style="font-weight:700;font-size:0.85rem">Nouveau tag</label>
    <input type="text" id="tnew" name="nom" required placeholder="ex. : démocratie" style="padding:0.4rem;border:1px solid #c9c6c1;border-radius:3px">
    <button class="bouton" type="submit">Créer</button>
</form>
<table class="tableau">
    <tr><th>Nom</th><th>Slug</th><th></th></tr>
    <?php foreach ($tags as $t): ?>
    <tr>
        <td><strong>#<?php echo e($t['nom']); ?></strong></td>
        <td><?php echo e($t['slug']); ?></td>
        <td>
            <form method="post" action="/admin/tags/supprimer/<?php echo (int)$t['id']; ?>" style="display:inline" onsubmit="return confirm('Supprimer ce tag ?');">
                <?php echo csrf_field(); ?>
                <button type="submit" style="border:none;background:none;color:#c5221f;cursor:pointer;padding:0">supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
