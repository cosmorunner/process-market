# Konfiguration ➜ JavaScript

---

- [Einleitung](#introduction)
- [Funktion anlegen](#create-function)

<a name="introduction"></a>
## Einleitung

Die "JavaScript" Funktion innerhalb von **Prozessen** ähnelt dem [Aktions-JavaScript](/{{route}}/{{version}}/action-javascript). 
Allerdings kann es global, im gesamten Prozess, also für **alle** Aktionen genutzt werden.

<a name="create-function"></a>
## Funktion anlegen

Eine globale JavaScript-Funktion wird im Konfigurationsbereich unter "JavaScript" erstellt.

![JavaScript](/img/docs/de/config-javascript/config-javascript-create-function.png)

Im JavaScript Editor muss immer das Objekt "methods" **(1)** vorhanden sein. Hierin werden die Funktionen als Attribute
definiert. Damit der berechnete Wert der Funktion genutzt werden kann, muss das Ergebnis immer mittels "return" zurückgegeben werden **(2)**.
Links unter dem Editor **(3)** wird angezeigt, ob der geschriebene Code gültiges JavaScript-Format hat. Mit dem Button unten rechts **(4)**
wird das JavaScript gespeichert.

In diesem Scope, ist im Gegensatz zu Aktions-JavaScript **nicht** das "action"-Objekt verfügbar.
Das Ergebnis der "val"-Methode kann aber als Parameter an die globale Funktion übergeben werden.

Um die Funktionen im Computed Input zu nutzen, muss folgende Syntax verwendet werden:

```javascript
return action.process.methods.sum(1, action.val('anzahl'));
```

***

[Nächster Artikel: Konfiguration ➜ Demo](/{{route}}/{{version}}/demo)
