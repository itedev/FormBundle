### Debug

If you want to dump huge objects in twig, and you cannot do it with default `dump()` function, you can use next way. Start listen for xDebug connections and add next construction to your twig template:

```twig
{% do ite_debug() %} {# for all variables in context #}
```

or

```twig
{% do ite_debug(form, entity) %} {# for specific variables #}
```

xDebug will automatically emits a breakpoint to the debug client on the specific line, and you can check your variable values in `$variables` var.