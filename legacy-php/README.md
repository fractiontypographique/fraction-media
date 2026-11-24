# Fraction Média — CMS PHP

CMS autonome **sans dépendance** (uniquement PHP + l'extension PDO), conçu pour les hébergeurs mutualisés accessibles **par FTP seul**. Il reprend le design éditorial (une en grand format, grilles d'articles, section photographie sombre, portfolio) et les fonctionnalités :

- **Articles** : chapô, corps en Markdown (titres, citations, listes, gras, italique, liens), image de couverture + légende/crédit, rubrique, auteur·rices, tags, statuts **brouillon / publication programmée / publié**, « à la une », temps de lecture auto.
- **Rubriques** avec description et ordre d'affichage.
- **Galeries photo** : photos multiples avec légende, crédit, ordre, mise en avant, photographe.
- **Œuvres d'art** : titre, artiste, année, technique, description, image.
- **Utilisateurs** : rôles administrateur / rédacteur·rice, mots de passe hachés (`password_hash`).
- **Flux RSS** (`/rss.xml`), page 404, navigation responsive.
- **Sécurité** : requêtes préparées (PDO), protection CSRF sur chaque formulaire, échappement systématique des sorties, exécution PHP interdite dans `uploads/`, `app/` et `data/` interdits d'accès web.

## Déploiement par FTP (guide pas à pas)

### 1. Récupérer les fichiers
Sur la page du dépôt GitHub : bouton vert **Code → Download ZIP**, puis décompressez. Tout ce qui doit être envoyé est le **contenu du dossier `fraction-php/`**.

### 2. Envoyer par FTP
Avec FileZilla (ou équivalent) : connectez-vous à votre hébergeur et envoyez **le contenu de `fraction-php/`** (et non le dossier lui-même) dans la racine de votre site — souvent `public_html/`, `www/` ou `htdocs/`.

Vous devez obtenir ceci à la racine :

```
public_html/
├── index.php
├── .htaccess
├── check.php
├── install.php
├── assets/
├── app/
├── data/
└── uploads/
```

### 3. Régler les droits des dossiers
Dans FileZilla : clic droit sur `data` → **Attributs du fichier** → cochez « Écrire » dans toutes les colonnes (ou tapez **775**). Faites de même pour `uploads/`. C'est indispensable pour la base SQLite et l'envoi d'images.

### 4. Vérifier puis installer
1. Ouvrez `http://votre-domaine.tld/check.php` : tous les tests doivent être verts (sinon corrigez les droits indiqués).
2. Ouvrez `http://votre-domaine.tld/install.php` : choisissez identifiant, nom affiché et mot de passe admin ; cochez le contenu de démonstration si souhaité.

### 5. Sécurité immédiate
**Supprimez `install.php` et `check.php` du serveur via FTP.**

### 6. Travailler au quotidien
- Back-office : `http://votre-domaine.tld/admin/`
- Le site est immédiatement à jour après chaque enregistrement (pas de publication en deux temps).

## Base de données

Par défaut : **SQLite** (fichier `data/site.sqlite`), aucun réglage. Si votre hébergeur fournit MySQL, créez la base dans son panneau, puis dans `app/config.php` :

```php
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'nom_de_la_base');
define('DB_USER', 'utilisateur');
define('DB_PASS', 'mot_de_passe');
```
puis relancez `install.php` et supprimez-le ensuite.

## Dépannage

| Symptôme | Cause probable | Solution |
|---|---|---|
| Liens `/article/…` renvoient 404 chez l'hébergeur | mod_rewrite absent/désactivé | Accès via `/index.php?route=article/<slug>` ; ou demandez l'activation de mod_rewrite |
| Page blanche | Erreur PHP masquée | Dans `app/config.php`, mettez `define('APP_DEBUG', true);`, rechargez, corrigez, puis repassez à `false` |
| Impossible d'enregistrer une image | `uploads/` non inscriptible | Droits 775 sur `uploads/` via FTP |
| « Erreur : data/ non inscriptible » | `data/` non inscriptible | Droits 775 sur `data/` via FTP |

## Sauvegarde

Sauvegardez régulièrement via FTP : le dossier `data/` (base) et `uploads/` (images). C'est tout — le reste est voisin d'un code source que vous pouvez rétélécharger.

## Structure du code

```
index.php            Routeur public (accueil, article, rubrique, photographie, art, à propos, RSS)
install.php          Installateur (à supprimer après usage)
check.php            Vérification d'environnement (à supprimer après usage)
app/config.php       Configuration (base de données, nom du site, debug)
app/bootstrap.php    Noyau : session, DB, CSRF, slug, markdown, envois, schéma
app/models.php       Requêtes de lecture du site public
app/admin/admin.php  Back-office : articles, galeries, œuvres, rubriques, tags, utilisateurs
app/views/           Vues publiques + views/admin/
assets/              CSS (design éditorial) et JS (menu mobile)
data/                Base SQLite (protégé d'accès web)
uploads/             Images envoyées (PHP interdit ici)
```
