from django.shortcuts import get_object_or_404, render

from .models import Article, Galerie, Oeuvre, Rubrique


def home(request):
    a_la_une = (
        Article.objects.publies().filter(a_la_une=True).select_related("rubrique").first()
    )
    articles = Article.objects.publies().select_related("rubrique")
    if a_la_une:
        articles = articles.exclude(pk=a_la_une.pk)
    galeries = Galerie.objects.filter(publiee=True)[:4]
    oeuvres = Oeuvre.objects.filter(publiee=True)[:6]
    return render(
        request,
        "core/home.html",
        {
            "a_la_une": a_la_une,
            "articles": articles[:9],
            "galeries": galeries,
            "oeuvres": oeuvres,
        },
    )


def article_detail(request, slug):
    article = get_object_or_404(Article, slug=slug)
    if not article.est_publie and not request.user.is_staff:
        return render(request, "core/article_brouillon.html", {"article": article}, status=404)
    articles_lies = (
        Article.objects.publies()
        .filter(rubrique=article.rubrique)
        .exclude(pk=article.pk)[:3]
    )
    return render(
        request,
        "core/article_detail.html",
        {"article": article, "articles_lies": articles_lies},
    )


def rubrique_view(request, slug):
    rubrique = get_object_or_404(Rubrique, slug=slug)
    articles = rubrique.articles.publies().select_related("rubrique")
    return render(
        request,
        "core/rubrique.html",
        {"rubrique": rubrique, "articles": articles, "rubriques": Rubrique.objects.all()},
    )


def galerie_list(request):
    galeries = Galerie.objects.filter(publiee=True).prefetch_related("photos")
    return render(request, "core/galerie_list.html", {"galeries": galeries})


def galerie_detail(request, slug):
    galerie = get_object_or_404(
        Galerie.objects.filter(publiee=True).prefetch_related("photos"), slug=slug
    )
    return render(request, "core/galerie_detail.html", {"galerie": galerie})


def oeuvres_view(request):
    oeuvres = Oeuvre.objects.filter(publiee=True).select_related("rubrique")
    return render(request, "core/oeuvres.html", {"oeuvres": oeuvres})


def apropos(request):
    return render(request, "core/apropos.html")
