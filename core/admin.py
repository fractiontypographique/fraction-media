from django.contrib import admin
from django.contrib.auth.admin import UserAdmin

from .models import Article, Galerie, Oeuvre, Photo, Profil, Rubrique, Tag


@admin.register(Rubrique)
class RubriqueAdmin(admin.ModelAdmin):
    list_display = ("nom", "ordre", "nb_articles")
    prepopulated_fields = {"slug": ("nom",)}
    search_fields = ("nom", "description")
    list_editable = ("ordre",)

    @admin.display(description="Articles publiés")
    def nb_articles(self, obj):
        return obj.articles.publies().count()


@admin.register(Profil)
class ProfilAdmin(admin.ModelAdmin):
    list_display = ("nom_complet", "role", "user")
    list_filter = ("role",)
    search_fields = ("user__first_name", "user__last_name", "user__username", "bio")


@admin.register(Tag)
class TagAdmin(admin.ModelAdmin):
    list_display = ("nom", "slug")
    prepopulated_fields = {"slug": ("nom",)}
    search_fields = ("nom",)


class PhotoInline(admin.StackedInline):
    model = Photo
    extra = 1
    fields = ("image", "legende", "credit", "ordre", "mise_en_avant")


@admin.register(Article)
class ArticleAdmin(admin.ModelAdmin):
    list_display = ("titre", "rubrique", "statut", "a_la_une", "date_publication", "temps_lecture_display")
    list_filter = ("statut", "rubrique", "a_la_une", "date_publication")
    list_editable = ("a_la_une",)
    search_fields = ("titre", "chapo", "corps")
    prepopulated_fields = {"slug": ("titre",)}
    filter_horizontal = ("auteurs", "tags")
    date_hierarchy = "date_publication"
    autocomplete_fields = ("rubrique",)
    fieldsets = (
        ("Contenu", {"fields": ("titre", "slug", "chapo", "corps")}),
        (
            "Média",
            {"fields": ("image_couverture", "legende_couverture"), "classes": ("collapse",)},
        ),
        (
            "Publication",
            {"fields": ("rubrique", "auteurs", "tags", "statut", "a_la_une", "date_publication")},
        ),
    )

    @admin.display(description="Lecture")
    def temps_lecture_display(self, obj):
        return f"{obj.temps_lecture} min"


@admin.register(Galerie)
class GalerieAdmin(admin.ModelAdmin):
    list_display = ("titre", "photographe", "publiee", "date_publication", "nb_photos")
    list_filter = ("publiee", "photographe", "date_publication")
    search_fields = ("titre", "description")
    prepopulated_fields = {"slug": ("titre",)}
    date_hierarchy = "date_publication"
    inlines = [PhotoInline]

    @admin.display(description="Photos")
    def nb_photos(self, obj):
        return obj.photos.count()


@admin.register(Oeuvre)
class OeuvreAdmin(admin.ModelAdmin):
    list_display = ("titre", "artiste", "annee", "technique", "publiee", "date_publication")
    list_filter = ("publiee", "rubrique", "date_publication")
    search_fields = ("titre", "artiste", "description")
    date_hierarchy = "date_publication"
