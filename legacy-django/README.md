# Fraction Média — CMS

CMS Django pour **Fraction Média**, un média indépendant mêlant **articles universitaires** (politique, sociologie, histoire), **photographie** (galeries) et **art** (portfolio d'œuvres).

## Fonctionnalités

- **Rédaction d'articles** : chapô, corps en Markdown, image de couverture avec légende/crédit, rubrique, auteur·rices, tags, brouillon / publication programmée / publié, « à la une », temps de lecture calculé.
- **Rubriques** : navigation par section (politique, sociologie, histoire, culture…), ordre d'affichage, description éditoriale.
- **Galeries photographiques** : séries de photos avec légendes, crédits, mise en avant, photographe associé.
- **Portfolio d'œuvres** : titre, artiste, année, technique, description, image.
- **Profils d'auteur·rices** : rôle (rédacteur·rice, photographe, artiste, contributeur·rice), bio, avatar, rattachés aux comptes utilisateurs Django.
- **Back-office Django admin** préconfiguré : filtres, recherche, slugs préremplis, filtres par date, gestion des photos en série (inlines).
- **Site public** inspiré de la presse en ligne indépendante (Blast, StreetPress) : une en grand format, grilles d'articles, section photographie sombre, portfolio.

## Démarrage rapide

```bash
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt

python manage.py migrate
python manage.py createsuperuser
python manage.py runserver
```

- Site public : http://127.0.0.1:8000/
- Back-office : http://127.0.0.1:8000/admin/

### Données de démonstration

```bash
python manage.py seed_demo
```

Crée 4 rubriques, 4 articles publiés (dont un « à la une »), des tags, une galerie photo et une œuvre d'exemple.

## Tests

```bash
python manage.py test
```

## Structure

```
config/          Configuration Django (settings, urls)
core/
  models.py      Rubrique, Profil, Tag, Article, Galerie, Photo, Oeuvre
  admin.py       Back-office personnalisé
  views.py       Pages publiques (accueil, article, rubrique, galeries, art, à propos)
  templates/     Templates du site public
  static/        CSS et JS du site
  management/    Commande seed_demo
```

## Variables d'environnement (production)

- `DJANGO_SECRET_KEY` : clé secrète Django (obligatoire en production).
- `DJANGO_DEBUG` : mettre à `False` en production.
- `DJANGO_ALLOWED_HOSTS` : liste des domaines, séparés par des virgules.

L'administration Django n'est pas exposée publiquement par défaut dans une mise en production classique : pensez à la protéger (HTTPS, éventuellement restriction réseau ou SSO).
