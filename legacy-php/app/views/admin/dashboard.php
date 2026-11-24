<h1>Tableau de bord</h1>
<div class="grille-stats">
    <div class="stat"><strong><?php echo $nb_articles; ?></strong><span>articles</span></div>
    <div class="stat"><strong><?php echo $nb_publies; ?></strong><span>publiés</span></div>
    <div class="stat"><strong><?php echo $nb_galeries; ?></strong><span>galeries</span></div>
    <div class="stat"><strong><?php echo $nb_oeuvres; ?></strong><span>œuvres</span></div>
</div>

<p style="margin-top:1.5rem"><a class="bouton" href="/admin/articles/nouveau">+ Nouvel article</a></p>

<h2 class="section-titre">Derniers ajouts</h2>
<table class="tableau">
    <tr><th>Titre</th><th>Rubrique</th><th>Statut</th><th></th></tr>
    <?php foreach ($recents as $a): ?>
    <tr>
        <td><?php echo e($a['titre']); ?></td>
        <td><?php echo e($a['rubrique_nom']); ?></td>
        <td><span class="badge badge-<?php echo e($a['statut']); ?>"><?php echo e($a['statut']); ?></span></td>
        <td><a href="/admin/articles/editer/<?php echo (int)$a['id']; ?>">modifier</a></td>
    </tr>
    <?php endforeach; ?>
</table>
