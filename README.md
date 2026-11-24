# Fraction Média

> Dépôt du média **Fraction Média** — art, photographie et articles universitaires (politique, sociologie, histoire).

## Contenu du dépôt

| Dossier | Rôle |
|---|---|
| [`fraction-php/`](fraction-php/) | **CMS PHP en production** — zéro dépendance, compatible hébergeur mutualisé accessible par FTP seul. Tout le guide de déploiement est dans son [README](fraction-php/README.md). |
| [`legacy-django/`](legacy-django/) | Première version Django (conservée pour référence ; nécessite un hébergeur avec Python). |

## Démarrage rapide (version PHP)

1. Télécharger le ZIP du dépôt, extraire le contenu de `fraction-php/`.
2. L'envoyer par FTP à la racine du site (ex. `public_html/`).
3. Droits 775 sur `data/` et `uploads/` (FileZilla → clic droit → Attributs du fichier).
4. Ouvrir `/check.php` puis `/install.php` (choisir identifiant + mot de passe admin).
5. Supprimer `install.php` et `check.php` du serveur.
6. Back-office : `/admin/` — site public : `/`.
