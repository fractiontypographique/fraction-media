from datetime import timedelta

from django.contrib.auth.models import User
from django.core.management.base import BaseCommand
from django.utils import timezone

from core.models import Article, Galerie, Oeuvre, Profil, Rubrique, Tag

RUBRIQUES = [
    ("Politique", "Pouvoirs, gouvernements, luttes et mécaniques de la décision publique."),
    ("Sociologie", "Corps sociaux, familles, école, urbain : ce qui relie le collectif et l'intime."),
    ("Histoire", "Retours sur le passé pour éclairer le présent."),
    ("Culture", "Cinéma, littérature, musique et pratiques culturelles."),
]

ARTICLES = [
    {
        "rubrique": "Politique",
        "titre": "La fabrique du désaccord démocratique",
        "chapo": "Comment les assemblées citoyennes réinventent la délibération — et ce qu'elles disent de nos institutions.",
        "corps": (
            "## Un moment délibératif\n\n"
            "Depuis une décennie, les mini-publics se multiplient : conventions citoyennes sur le climat, jurys délibératifs, budgets participatifs. Que produisent-ils réellement ?\n\n"
            "### De la protestation à la délibération\n\n"
            "Loin de substituer au suffrage représentatif, ces dispositifs testent une autre grammaire du commun : parler au lieu de s'affronter, peser sans voter pour soi.\n\n"
            "> La délibération n'est pas la fin du conflit, c'est une autre manière de le tenir.\n\n"
            "### Ce que disent les travaux empiriques\n\n"
            "Les études de cas montrent des effets ambivalents : formulations plus précises des problèmes publics, mais aussi frustrations quand les recommandations restent lettres mortes.\n\n"
            "Pour aller plus loin, la lecture croisée des travaux sur la légitimité procédurale est indispensable."
        ),
        "une": True,
    },
    {
        "rubrique": "Sociologie",
        "titre": "Le travail invisible des soignantes",
        "chapo": "Soigner ne se réduit pas aux actes techniques. Enquête sur tout ce qui ne se compte pas — et que l'épidémie a rendu visible.",
        "corps": (
            "## Tout ce qui ne se compte pas\n\n"
            "Écouter les familles, apaiser la peur, recorriger un dossier à 23 heures : ce travail-là n'apparaît dans aucune nomenclature d'actes.\n\n"
            "### Une division genrée du soin\n\n"
            "Les enquêtes convergent : plus l'activité est invisible, plus elle est assignée aux femmes. L'invisibilité n'est pas un accident de mesure, elle est structurelle.\n\n"
            "- Tenir l'auvent au chevet et à l'accueil\n- Le poids du « care » organisationnel\n- Lemps morcelé, la fatigue cumulée\n\n"
            "Rendre ce travail comptable, c'est déjà changer sa valeur."
        ),
        "une": False,
    },
    {
        "rubrique": "Histoire",
        "titre": "1968, la mémoire en rayons",
        "chapo": "Entre commémoration officielle et souvenirs militants, l'année 1968 reste un champ de bataille historiographique.",
        "corps": (
            "## Une décennie happée par les commémorations\n\n"
            "Chaque anniversaire resserre la mémoire autour de quelques images standardisées. Or les archives locales racontent une autre histoire : celle des comités d'action, des lycées, des usines.\n\n"
            "### Ce que l'archive locale déplace\n\n"
            "En feuilletant les tracts conservés en archives départementales, on découvre un après-68 qui n'était pas la fin mais peut-être le début de quelque chose."
        ),
        "une": False,
    },
    {
        "rubrique": "Culture",
        "titre": "Musées : à qui appartiennent les images ?",
        "chapo": "Open data et reproductions gratuites : les entrepôts d'images bouleversent l'économie du patrimoine.",
        "corps": (
            "## La révolution de l'image ouverte\n\n"
            "Des galeries nationales libèrent leurs collections : millions d'images numériques, libres de droits. Conséquence immédiate : l'image patrimoniale devient un matériau commun.\n\n"
            "### De nouveaux intermédiaires\n\n"
            "Datasets d'entraînement, applications de reconnaissance, clôtures d'artistes : la chaîne de valeur se redistribue. À suivre de près."
        ),
        "une": False,
    },
]


class Command(BaseCommand):
    help = "Crée un jeu de données de démonstration pour Fraction Média."

    def handle(self, *args, **options):
        now = timezone.now()
        profils = []
        for nom, role in [("Camille Delaunay", "redacteur"), ("Ines Ferreira", "photographe")]:
            prenom, nom_famille = nom.split(" ", 1)
            username = prenom.lower().replace(" ", "")
            user = User.objects.filter(username=username).first()
            if user is None:
                user = User.objects.create_user(username=username, first_name=prenom, last_name=nom_famille)
            profil, _ = Profil.objects.get_or_create(user=user, defaults={"role": role})
            profils.append(profil)

        rubriques = {}
        for ordre, (nom, description) in enumerate(RUBRIQUES):
            rubrique, created = Rubrique.objects.get_or_create(
                nom=nom, defaults={"description": description, "ordre": ordre}
            )
            rubriques[nom] = rubrique
            if created:
                self.stdout.write(f"Rubrique créée : {nom}")

        tag_noms = ["démocratie", "travail", "mémoire", "patrimoine", "image"]
        for nom in tag_noms:
            Tag.objects.get_or_create(nom=nom, defaults={"slug": nom})

        articles_crees = 0
        for i, donnees in enumerate(ARTICLES):
            article, created = Article.objects.get_or_create(
                titre=donnees["titre"],
                defaults={
                    "chapo": donnees["chapo"],
                    "corps": donnees["corps"],
                    "rubrique": rubriques[donnees["rubrique"]],
                    "statut": "publie",
                    "a_la_une": donnees["une"],
                    "date_publication": now - timedelta(days=3 * i),
                },
            )
            if created:
                article.auteurs.add(profils[0])
                article.tags.add(*Tag.objects.filter(nom__in=[tag_noms[i % len(tag_noms)]]))
                articles_crees += 1

        galerie, created = Galerie.objects.get_or_create(
            titre="Rues en travaux, vitrines de rupture",
            defaults={
                "description": "Série photographique sur les paysages urbains en mutation, entre échafaudages et patrimoine.",
                "photographe": profils[1],
            },
        )
        if created:
            self.stdout.write(f"Galerie créée : {galerie.titre}")

        oeuvre, created = Oeuvre.objects.get_or_create(
            titre="Sans titre (fragment I)",
            artiste="Inès Ferreira",
            defaults={
                "annee": 2024,
                "technique": "photographie argentique",
                "description": "Premier volet d'une série sur les fractures urbaines.",
            },
        )
        if created:
            self.stdout.write(f"Œuvre créée : {oeuvre.titre}")

        self.stdout.write(
            self.style.SUCCESS(
                f"Démo prête : {articles_crees} article(s), {len(rubriques)} rubriques, 1 galerie, 1 œuvre.\n"
                "Créez un compte admin avec : python manage.py createsuperuser"
            )
        )
