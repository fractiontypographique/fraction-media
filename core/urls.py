from django.urls import path

from . import views

app_name = "core"

urlpatterns = [
    path("", views.home, name="home"),
    path("article/<slug:slug>/", views.article_detail, name="article"),
    path("rubrique/<slug:slug>/", views.rubrique_view, name="rubrique"),
    path("photographie/", views.galerie_list, name="galeries"),
    path("photographie/<slug:slug>/", views.galerie_detail, name="galerie"),
    path("art/", views.oeuvres_view, name="oeuvres"),
    path("a-propos/", views.apropos, name="apropos"),
]
