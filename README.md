# Fraction Média

> Dépôt du média **Fraction Média** — art, photographie et articles universitaires (politique, sociologie, histoire).

## Contenu du dépôt

| Dossier | Rôle |
|---|---|
| [`wordpress-theme/`](wordpress-theme/) | **Version actuelle — thème WordPress** « Fraction Média », inspiré de StreetPress et du CNAP. Aucun plugin requis. Installation FTP : [guide complet](wordpress-theme/README.md). |
| [`legacy-php/`](legacy-php/) | CMS PHP natif autonome (conservé pour référence ; remplacé par le thème WordPress). |
| [`legacy-django/`](legacy-django/) | Première version Django (conservée pour référence ; nécessite un hébergeur avec Python). |

## Démarrage rapide (thème WordPress)

1. Installer WordPress sur l'hébergement (PHP mutualisé OK ; la plupart des hébergeurs proposent un installeur 1 clic).
2. Télécharger le ZIP du dépôt, extraire **`wordpress-theme/fraction-media/`**.
3. Envoyer ce dossier par FTP dans **`wp-content/themes/`** (FileZilla).
4. Admin WordPress → **Apparence → Thèmes → Fraction Média → Activer**.
5. **Réglages → Permaliens → Enregistrer** pour activer les URL propres.

Guide détaillé + prise en main du contenu : [`wordpress-theme/README.md`](wordpress-theme/README.md).
