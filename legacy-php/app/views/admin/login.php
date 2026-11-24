<article class="connexion-card">
    <h1 class="section-titre">Connexion</h1>
    <?php foreach (take_flashes() as $f): ?>
        <p class="flash flash-<?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></p>
    <?php endforeach; ?>
    <form method="post" action="/admin/connexion">
        <?php echo csrf_field(); ?>
        <div class="champ">
            <label for="username">Identifiant</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>
        <div class="champ">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button class="bouton" type="submit">Se connecter</button>
    </form>
</article>
