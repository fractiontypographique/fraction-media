<h1>Galeries photo</h1>
<p><a class="bouton" href="/admin/galeries/editer/nouveau">+ Nouvelle galerie</a></p>
<table class="tableau">
    <tr><th>Titre</th><th>Photos</th><th>Publiée</th><th>Publié le</th><th></th></tr>
    <?php foreach ($galeries as $g): ?>
    <tr>
        <td><strong><?php echo e($g['titre']); ?></strong></td>
        <td><?php echo (int)$g['nb_photos']; ?></td>
        <td><?php echo $g['publiee'] ? 'oui' : 'non'; ?></td>
        <td><?php echo e($g['date_publication']); ?></td>
        <td>
            <a href="/admin/galeries/editer/<?php echo (int)$g['id']; ?>">gérer (photos)</a> ·
            <a href="/photographie/<?php echo e($g['slug']); ?>/" target="_blank">voir</a> ·
            <form method="post" action="/admin/galeries/supprimer/<?php echo (int)$g['id']; ?>" style="display:inline" onsubmit="return confirm('Supprimer cette galerie et ses photos ?');">
                <?php echo csrf_field(); ?>
                <button type="submit" style="border:none;background:none;color:#c5221f;cursor:pointer;padding:0">supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
