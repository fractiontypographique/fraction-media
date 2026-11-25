# Thème WordPress « Fraction Média »

Thème éditorial custom pour **Fraction Média** — art, photographie et articles de fond
(politique, sociologie, histoire).

Inspirations :
- **StreetPress** : bandeau noir, liseré rouge, headlines massives en serif, grille dense ;
- **CNAP** : présentation sobre et muséale des photographies et des œuvres, grandes respirations, légendes raffinées.

Aucun plugin requis, aucune dépendance : 100 % WordPress standard.

## Ce que le thème apporte

| Fonction | Détail |
|---|---|
| Article avec chapô | Métabox **« Infos éditoriales — Fraction Média »** sur chaque article (chapô + crédit de l'image de couverture) |
| Rubriques | Les **catégories** WordPress standard (créez Politique, Sociologie, Histoire, Culture…) |
| Étiquettes | Les **mots-clés** WordPress standard |
| Photographies | Type de contenu **« Galeries photo »** — créez une galerie, puis « Ajouter un média » pour y joindre les photos (légende + crédit sur chaque média) |
| Œuvres d'art | Type de contenu **« Œuvres d'art »** avec notice : artiste, année, technique + description dans le contenu |
| À la une | Rendez un article **épinglé** (visible dans « Réglages → Visibilité » de l'éditeur) : il devient la une de la page d'accueil |
| Menus | **Apparence → Menus** : « Menu principal » et « Menu pied de page » |
| Logo / marque | **Apparence → Personnaliser → Identité du site** (logo optionnel, sinon titre du site + SVG rouge de la marque) ; favicon : il suffit alors de définir l'icône du site (16×16..512×512), sinon la marque sert de favicon de secours. |
| Temps de lecture | Calculé automatiquement (200 mots/min) |

## Installation (hébergement FTP, sans SSH)

1. Téléchargez le ZIP de ce dépôt (bouton vert **Code → Download ZIP**) et décompressez-le.
2. Avec FileZilla, envoyez **le dossier `wordpress-theme/fraction-media/`** (le dossier entier, avec ses fichiers PHP, `assets/`, etc.) dans le répertoire **`wp-content/themes/`** de votre hébergement.
   - Résultat attendu : `wp-content/themes/fraction-media/style.css`.
3. Installez le cœur WordPress si ce n'est pas encore fait (votre hébergeur PHP mutualisé le permet — la plupart proposent aussi un installeur en 1 clic : recherchez « WordPress » dans votre panel).
4. Connectez-vous à l'admin WordPress → **Apparence → Thèmes → Fraction Média → Activer**.
5. **Réglages → Permaliens** : choisissez « Nom de l'article » puis Enregistrer (une fois — cela active les URL propres des galeries et des œuvres).

## Mise en route du contenu

1. **Articles → Catégories** : créez Politique, Sociologie, Histoire, Culture.
2. **Apparence → Menus** : créez un menu avec ces catégories + « Photographies » + « Œuvres », affectez-le à « Menu principal ».
3. Écrivez un premier article : remplissez le **chapô** dans la métabox, définissez une **image mise en avant**, épinglez-le pour la une.

## Réglages recommandés

- **Réglages → Général** : titre « Fraction Média », slogan e.g. « Art, photographie et articles de fond ».
- **Réglages → Lecture** : optionnel, définissez une page statique d'accueil si vous préférez ;
  sinon la page d'accueil éditoriale du thème s'affiche automatiquement.

## Dépannage

| Problème | Solution |
|---|---|
| Liens Photographies / Œuvres en 404 | Réenregistrez les permaliens (Réglages → Permaliens → Enregistrer) |
| Pages d'accueil vides | Publiez au moins un article (il sera automatiquement à la une), la page d'accueil masque les sections vides |
| Le menu mobile ne s'ouvre pas | Videz le cache (plugin de cache ou cache de l'hébergeur) |
| Traduction manquante | Le texte est en dur en français dans le thème, domaine `fraction-media` |

## Fichiers

```
wordpress-theme/fraction-media/
├── style.css           ← entêtes du thème + tout le design
├── functions.php       ← supports, menus, CPT galeries + œuvres, métaboxes, helpers
├── header.php          ← bandeau noir + nav
├── footer.php          ← pied de page
├── front-page.php      ← accueil : une + derniers articles + photographies + œuvres
├── index.php           ← liste de tous les articles
├── single.php          ← article (kicker, chapô, byline, couverture, tags, à lire aussi)
├── single-galerie.php  ← galerie photo (grille des médias attachés)
├── single-oeuvre.php   ← notice d'œuvre façon musée
├── archive.php         ← rubriques, étiquettes, archives galeries/œuvres
├── search.php / searchform.php ← recherche
├── page.php / 404.php
├── template-parts/carte.php ← carte d'article réutilisable
└── assets/ (nav.js, editor.css)
```
