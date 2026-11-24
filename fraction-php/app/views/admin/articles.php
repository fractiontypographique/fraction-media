<h1>Articles</h1>
<p>
    <a class="bouton" href="/admin/articles/nouveau">+ Nouvel article</a>
    — Filtre :
    <a href="/admin/articles">tous</a> · <a href="/admin/articles?statut=publie"<?php echo $filtre === 'publie' ? ' style="font-weight:700"' : ''; ?>>publiés</a> ·
    <a href="/admin/articles?statut=brouillon"<?php echo $filtre === 'brouillon' ? ' style="font-weight:700"' : ''; ?>>brouillons</a> ·
    <a href="/admin/articles?statut=public"<?php echo $filtre === 'public' ? ' style="font-weight:700"' : ''; ?>>programmés</a>
</p>
<table class="tableau">
    <tr><th>Titre</th><th>Rubrique</th><th>Statut</th><th>Publié le</th><th>À la une</th><th></th></tr>
    <?php foreach ($articles as $a): ?>
    <tr>
        <td><strong><?php echo e($a['titre']); ?></strong></td>
        <td><?php echo e($a['rubrique_nom']); ?></td>
        <td><span class="badge badge-<?php echo e($a['statut']); ?>"><?php echo e($a['statut']); ?></span></td>
        <td><?php echo e($a['date_publication']); ?></td>
        <td><?php echo $a['a_la_une'] ? '★' : ''; ?></td>
        <td>
            <a href="/admin/articles/editer/<?php echo (int)$a['id']; ?>">modifier</a> ·
            <a href="/article/<?php echo e($a['slug']); ?>/" target="_blank">voir</a> ·
            <form method="post" action="/admin/articles/supprimer/<?php echo (int)$a['id']; ?>" style="display:inline" onsubmit="return confirm('Supprimer cet article ?');">
                <?php echo csrf_field(); ?>
                <button type="submit" style="border:none;background:none;color:#c5221f;cursor:pointer;padding:0">supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
