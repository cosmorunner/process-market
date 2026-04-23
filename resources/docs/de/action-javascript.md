# Konfiguration ➜ Aktions-JavaScript

---

- [Einleitung](#introduction)
- [Funktion anlegen](#create-function)

<a name="introduction"></a>
## Einleitung

Die "JavaScript" Funktion innerhalb von **Aktionen** erweitert die ["Computed Input"-Option](/{{route}}/{{version}}/action-components#allisa-form-computed-input)
von Allisa. Es kann vorkommen, dass in einer Aktion immer wieder die gleiche Logik verwendet wird.
Um Redundanzen zu vermeiden kann solche Logik zentral unter "JavaScript" verwaltet werden.

<a name="create-function"></a>
## Funktion anlegen

Eine JavaScript-Funktion wird in einer Aktion unter "JavaScript" erstellt.

![JavaScript](/img/docs/de/action-javascript/action-javascript-create-function.png)

Im JavaScript Editor muss immer das Objekt "methods" **(1)** vorhanden sein. Hierin werden die Funktionen als Attribute 
definiert. Damit der berechnete Wert der Funktion genutzt werden kann, muss das Ergebnis immer mittels "return" zurückgegeben 
werden **(2)**. Möchte man in diesem Scope auf das "action"-Objekt zugreifen, muss das Keyword "this" genutzt werden **(3)**.
Links unter dem Editor **(4)** wird angezeigt, ob der geschriebene Code gültiges JavaScript-Format hat. Mit dem Button 
unten rechts **(5)** wird das JavaScript gespeichert.

Der Editor ändert den Funktionsnamen (s. Screenshot) bei Wechsel in einen anderen Bereich automatisch in folgendes Format:

```javascript
{
    methods: {
        sum: function(a, b) {
            return a + b;
        } 
    }
}
```

Beide Schreibweisen können im Editor je nach Präferenz genutzt werden.<br>
Möchte man mehrere Funktionen definieren, muss hinter die schließende Klammer jeder Funktion ein Komma gesetzt werden:

```javascript
{
    methods: {
        add: function(a, b) {
            return a + b;
        },
        sub: function(a, b){
            return a - b;
        }
    }
}
```

Um die Funktionen dann im Computed Input zu nutzen, muss folgende Syntax verwendet werden:

```javascript
return action.methods.sum(1, 2);
// Ergebnis: 3
```

***

[Nächster Artikel: Konfiguration ➜ Status](/{{route}}/{{version}}/config-status)