from django import template
from django.utils.safestring import mark_safe

import markdown as md

register = template.Library()


@register.filter
def md_to_html(value):
    if not value:
        return ""
    return mark_safe(md.markdown(value, extensions=["extra", "sane_lists"]))
