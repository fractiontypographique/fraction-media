from .models import Rubrique


def site(request):
    return {"rubriques_nav": Rubrique.objects.all(), "site_nom": "Fraction Média"}
