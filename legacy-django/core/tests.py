from datetime import datetime

from django.contrib.auth.models import User
from django.test import Client, TestCase
from django.utils import timezone

from .models import Article, Galerie, Photo, Profil, Rubrique, Tag
from .templatetags.fraction_tags import md_to_html


class DonneesTests(TestCase):
    def setUp(self):
        self.user = User.objects.create_user(username="camille", first_name="Camille", last_name="Delaunay")
        self.profil = Profil.objects.create(user=self.user)
        self.sociologie = Rubrique.objects.create(nom="Sociologie")
        self.tag = Tag.objects.create(nom="Travail")
        self.article = Article.objects.create(
            titre="Le travail invisible des soignantes",
            corps="# Analyse\n\nUn article de fond.",
            rubrique=self.sociologie,
            statut="publie",
            date_publication=timezone.make_aware(datetime(2025, 1, 15, 10, 0)),
        )
        self.article.auteurs.add(self.profil)
        self.article.tags.add(self.tag)

    def test_rubrique_str(self):
        self.assertEqual(str(self.sociologie), "Sociologie")

    def test_slug_automatique(self):
        self.assertEqual(self.article.slug, "le-travail-invisible-des-soignantes")
        self.assertEqual(self.tag.slug, "travail")

    def test_est_publie(self):
        self.assertTrue(self.article.est_publie)
        brouillon = Article.objects.create(titre="Brouillon test", corps="...", rubrique=self.sociologie)
        self.assertFalse(brouillon.est_publie)

    def test_temps_lecture_minimum(self):
        self.assertGreaterEqual(self.article.temps_lecture, 1)

    def test_publies_exclut_brouillons(self):
        Article.objects.create(titre="À venir", corps="...", rubrique=self.sociologie, statut="brouillon")
        self.assertIn(self.article, Article.objects.publies())
        self.assertEqual(Article.objects.publies().count(), 1)

    def test_galerie_couverture(self):
        galerie = Galerie.objects.create(titre="Mirages urbains")
        self.assertIsNone(galerie.couverture)
        photo = Photo.objects.create(galerie=galerie, image="galeries/test.jpg", legende="Vue de nuit")
        self.assertEqual(galerie.couverture, photo)

    def test_markdown_filter(self):
        rendered = md_to_html("# Titre\n\n**gras**")
        self.assertIn("<h1>", rendered)
        self.assertIn("<strong>gras</strong>", rendered)
        self.assertEqual(md_to_html(""), "")


class PagesTests(TestCase):
    def setUp(self):
        self.client = Client()
        self.user = User.objects.create_user(username="camille", first_name="Camille", last_name="Delaunay")
        self.profil = Profil.objects.create(user=self.user)
        self.sociologie = Rubrique.objects.create(nom="Sociologie")
        self.article = Article.objects.create(
            titre="Résister par l'image",
            chapo="Comment la photographie documente la contestation.",
            corps="## Introduction\n\nUn long développement.",
            rubrique=self.sociologie,
            statut="publie",
            date_publication=timezone.make_aware(datetime(2025, 2, 1, 9, 0)),
        )
        self.article.auteurs.add(self.profil)
        self.brouillon = Article.objects.create(
            titre="En chantier", corps="...", rubrique=self.sociologie, statut="brouillon"
        )

    def test_accueil_200(self):
        response = self.client.get("/")
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Résister par l")

    def test_accueil_masque_brouillon(self):
        response = self.client.get("/")
        self.assertNotContains(response, "En chantier")

    def test_article_detail_200(self):
        response = self.client.get(self.article.get_absolute_url())
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Camille Delaunay")
        self.assertContains(response, "Introduction")

    def test_brouillon_cache_anonyme(self):
        response = self.client.get(self.brouillon.get_absolute_url())
        self.assertEqual(response.status_code, 404)

    def test_brouillon_visible_staff(self):
        user = User.objects.create_user(username="redac", password="motdepasse-long", is_staff=True)
        self.client.force_login(user)
        response = self.client.get(self.brouillon.get_absolute_url())
        self.assertEqual(response.status_code, 200)

    def test_rubrique_200(self):
        response = self.client.get(self.sociologie.get_absolute_url())
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Résister par l")

    def test_galeries_et_cap_404(self):
        self.assertEqual(self.client.get("/photographie/").status_code, 200)
        self.assertEqual(self.client.get("/photographie/inconnue/").status_code, 404)

    def test_pages_art_et_apropos(self):
        self.assertEqual(self.client.get("/art/").status_code, 200)
        self.assertEqual(self.client.get("/a-propos/").status_code, 200)

    def test_admin_acces_avec_compte(self):
        response = self.client.get("/admin/", follow=True)
        self.assertEqual(response.status_code, 200)
