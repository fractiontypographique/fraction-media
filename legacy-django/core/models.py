from django.conf import settings
from django.db import models
from django.urls import reverse
from django.utils import timezone
from django.utils.text import slugify


class Rubrique(models.Model):
    nom = models.CharField(max_length=120, unique=True, verbose_name="Nom")
    slug = models.SlugField(max_length=140, unique=True, verbose_name="Slug")
    description = models.TextField(blank=True, verbose_name="Description")
    ordre = models.PositiveIntegerField(default=0, verbose_name="Ordre d'affichage")

    class Meta:
        verbose_name = "Rubrique"
        verbose_name_plural = "Rubriques"
        ordering = ["ordre", "nom"]

    def __str__(self):
        return self.nom

    def save(self, *args, **kwargs):
        if not self.slug:
            self.slug = slugify(self.nom)
        super().save(*args, **kwargs)

    def get_absolute_url(self):
        return reverse("core:rubrique", kwargs={"slug": self.slug})


class Profil(models.Model):
    ROLES = [
        ("redacteur", "Rédacteur·rice"),
        ("photographe", "Photographe"),
        ("artiste", "Artiste"),
        ("contributeur", "Contributeur·rice"),
    ]

    user = models.OneToOneField(
        settings.AUTH_USER_MODEL, on_delete=models.CASCADE, related_name="profil"
    )
    role = models.CharField(max_length=20, choices=ROLES, default="redacteur")
    bio = models.TextField(blank=True, verbose_name="Bio")
    avatar = models.ImageField(upload_to="avatars/", blank=True, verbose_name="Avatar")

    class Meta:
        verbose_name = "Profil d'auteur·rice"
        verbose_name_plural = "Profils d'auteur·rices"

    def __str__(self):
        return self.user.get_full_name() or self.user.username

    @property
    def nom_complet(self):
        return self.user.get_full_name() or self.user.username


class Tag(models.Model):
    nom = models.CharField(max_length=80, unique=True, verbose_name="Nom")
    slug = models.SlugField(max_length=100, unique=True, verbose_name="Slug")

    class Meta:
        verbose_name = "Tag"
        verbose_name_plural = "Tags"
        ordering = ["nom"]

    def __str__(self):
        return self.nom

    def save(self, *args, **kwargs):
        if not self.slug:
            self.slug = slugify(self.nom)
        super().save(*args, **kwargs)


class PubliableQuerySet(models.QuerySet):
    def publies(self):
        return self.filter(statut="publie", date_publication__lte=timezone.now())


class Article(models.Model):
    STATUTS = [
        ("brouillon", "Brouillon"),
        ("public", "Publication programmée"),
        ("publie", "Publié"),
    ]

    titre = models.CharField(max_length=250, verbose_name="Titre")
    slug = models.SlugField(max_length=280, unique=True, verbose_name="Slug")
    chapo = models.TextField(blank=True, verbose_name="Chapô", help_text="Accroche affichée sous le titre.")
    corps = models.TextField(
        verbose_name="Corps de l'article",
        help_text="Markdown autorisé (titres, citations, gras, italique, listes).",
    )
    image_couverture = models.ImageField(
        upload_to="articles/%Y/%m/", blank=True, verbose_name="Image de couverture"
    )
    legende_couverture = models.CharField(
        max_length=250, blank=True, verbose_name="Légende de couverture", help_text="Inclut le crédit photo."
    )
    rubrique = models.ForeignKey(
        Rubrique, on_delete=models.PROTECT, related_name="articles", verbose_name="Rubrique"
    )
    auteurs = models.ManyToManyField(Profil, related_name="articles", blank=True, verbose_name="Auteur·rices")
    tags = models.ManyToManyField(Tag, blank=True, related_name="articles")
    a_la_une = models.BooleanField(default=False, verbose_name="À la une")
    statut = models.CharField(max_length=12, choices=STATUTS, default="brouillon")
    date_publication = models.DateTimeField(
        default=timezone.now, null=True, blank=True, verbose_name="Date de publication"
    )
    cree_le = models.DateTimeField(auto_now_add=True)
    modifie_le = models.DateTimeField(auto_now=True)

    objects = PubliableQuerySet.as_manager()

    class Meta:
        verbose_name = "Article"
        verbose_name_plural = "Articles"
        ordering = ["-date_publication"]

    def __str__(self):
        return self.titre

    def save(self, *args, **kwargs):
        if not self.slug:
            self.slug = slugify(self.titre)
        super().save(*args, **kwargs)

    def get_absolute_url(self):
        return reverse("core:article", kwargs={"slug": self.slug})

    @property
    def est_publie(self):
        return self.statut == "publie" and self.date_publication and self.date_publication <= timezone.now()

    @property
    def temps_lecture(self):
        mots = len(self.corps.split())
        return max(1, round(mots / 200))


class Galerie(models.Model):
    titre = models.CharField(max_length=200, verbose_name="Titre")
    slug = models.SlugField(max_length=220, unique=True, verbose_name="Slug")
    description = models.TextField(blank=True, verbose_name="Description")
    photographe = models.ForeignKey(
        Profil,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="galeries",
        verbose_name="Photographe",
    )
    date_publication = models.DateTimeField(default=timezone.now, verbose_name="Date de publication")
    publiee = models.BooleanField(default=True, verbose_name="Publiée")
    cree_le = models.DateTimeField(auto_now_add=True)

    class Meta:
        verbose_name = "Galerie photo"
        verbose_name_plural = "Galeries photo"
        ordering = ["-date_publication"]

    def __str__(self):
        return self.titre

    def save(self, *args, **kwargs):
        if not self.slug:
            self.slug = slugify(self.titre)
        super().save(*args, **kwargs)

    def get_absolute_url(self):
        return reverse("core:galerie", kwargs={"slug": self.slug})

    @property
    def couverture(self):
        for photo in self.photos.all():
            if photo.mise_en_avant:
                return photo
        return self.photos.first()


class Photo(models.Model):
    galerie = models.ForeignKey(Galerie, on_delete=models.CASCADE, related_name="photos")
    image = models.ImageField(upload_to="galeries/%Y/%m/", verbose_name="Image")
    legende = models.CharField(max_length=250, blank=True, verbose_name="Légende")
    credit = models.CharField(max_length=120, blank=True, verbose_name="Crédit")
    ordre = models.PositiveIntegerField(default=0, verbose_name="Ordre")
    mise_en_avant = models.BooleanField(default=False, verbose_name="Mise en avant")

    class Meta:
        verbose_name = "Photo"
        verbose_name_plural = "Photos"
        ordering = ["ordre", "id"]

    def __str__(self):
        return self.legende or f"Photo #{self.pk}"


class Oeuvre(models.Model):
    titre = models.CharField(max_length=200, verbose_name="Titre")
    artiste = models.CharField(max_length=120, verbose_name="Artiste")
    annee = models.PositiveIntegerField(null=True, blank=True, verbose_name="Année")
    technique = models.CharField(
        max_length=160,
        blank=True,
        verbose_name="Technique",
        help_text="Ex. : photographie argentique, huile sur toile…",
    )
    description = models.TextField(blank=True, verbose_name="Description")
    image = models.ImageField(upload_to="oeuvres/%Y/%m/", verbose_name="Image")
    rubrique = models.ForeignKey(
        Rubrique,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="oeuvres",
        verbose_name="Rubrique",
    )
    date_publication = models.DateTimeField(default=timezone.now, verbose_name="Date de publication")
    publiee = models.BooleanField(default=True, verbose_name="Publiée")

    class Meta:
        verbose_name = "Œuvre d'art"
        verbose_name_plural = "Œuvres d'art"
        ordering = ["-date_publication"]

    def __str__(self):
        return f"{self.titre} — {self.artiste}"

    def get_absolute_url(self):
        return reverse("core:oeuvres")
