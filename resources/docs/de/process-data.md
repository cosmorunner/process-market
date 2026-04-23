# Regeln & Daten ➜ Daten

---

- [Einleitung](#introduction)
- [Prozess-Daten ändern](#update-process-data)
- [Datenfeld anlegen](#create-data)

<a name="introduction"></a>

## Einleitung

Eine Prozess-Instanz kann Daten besitzen, sogenannte **Prozess-Daten**. Diese Daten können durch Aktionen genutzt und
verändert werden. Aktionen können keine Daten in eine Prozess-Instanz speichern, die nicht unter "Daten" bei der
Prozessmodellierung angegeben wurden.

Prozess-Daten werden im Panel unter "Daten" bearbeitet. Der Wert eines Prozess-Datenfeldes kann entweder eine
Zeichenkette, NULL, ein JSON-Array oder ein JSON-Objekt sein.

![Daten](/img/docs/de/process-data/rules-process-data-overview.png)

Innerhalb einer Prozess-Instanz auf der Allisa Plattform werden die Prozess-Daten unter dem Menüpunkt "Daten" angezeigt.

![Daten](/img/docs/de/process-data/process-data.png)

> {info.fa-info-circle} Möglicherweise ist der Menüpunkt nicht vorhanden, weil das Menü des Prozesstyps bei der
> "Konfiguration" angepasst wurde.

<a name="update-process-data"></a>

## Prozess-Daten ändern

Prozess-Daten können ausschließlich von Aktionen verändert werden. Manuell kann ein Prozess-Datenfeld nicht
aktualisiert werden. Um einen Wert zu aktualisieren, muss eine Aktion ausgeführt werden, welche ein **Aktions-Datenfeld
mit einem identischen Namen** besitzt, wie das Prozess-Datenfeld. Der Wert des Aktions-Datenfeldes wird dann für das
Prozess-Datenfeld übernommen.

<a name="create-data"></a>

## Datenfeld anlegen

Mit dem "+"-Icon im "Daten"-Bereich öffnet sich ein Dialog-Fenster, wo ein neues Datenfeld angelegt werden kann.

![Daten](/img/docs/de/process-data/rules-create-data.png)

Der Name **(1)** ist die technische Kennung des Datenfeldes. Die Beschreibung **(2)** wird als Zusatzinformation in
einer Prozess-Instanz auf der Allisa Plattform angezeigt (siehe Screenshot in der Einleitung). Als Typ **(3)**
kann entweder eine Zeichenkette (Einfach), ein JSON-Array oder ein JSON-Objekt gewählt werden.

Der Standardwert **(4)** definiert den **Initialwert beim Erstellen einer neuen Prozess-Instanz** und **den
Rückfallwert** wenn eine Aktion dieses Feld setzt, doch der Wert leer ist (leere Zeichenkette/leeres JSON-Array/ leeres
JSON-Objekt). Wenn "
Kein Standard-Wert" gewählt wird, wird das Prozess-Datenfeld nicht beim Erstellen der Prozess-Instanz angelegt, sondern
erst, wenn es durch eine Aktion gesetzt wird.

<a name="bulk-create-data"></a>

## Mehrfach-Anlage Datenfelder

Neben dem "+" Knopf befindet sich der Knopf **(1)** für die Mehrfachanlage von Proozess-Datenfeldern. Hier
können mehrere
Datenfelder gleichzeitig angelegt werden.

![Daten](/img/docs/bulk-create-data-button.png)

Über den Knopf öffnet sich ein Text-Editor, in dem die Datenfelder mithilfe einer spezifischen Syntax angelegt werden
können.
Pro Zeile kann hier ein Datenfeld angelegt werden.

**Format**: <Typ(|~|=)><Name\>;<?Standard-Wert>

- Typ (optional)
    - Der Type des Datenfeldes wird mit Klammern um das Namens-Attribut angegeben.
        - Keine Angabe → Einfach
        - ~ → JSON-Array
        - = → JSON-Objekt
- Name des Datenfeldes
    - Kleingeschrieben, nur "a-z", "0-9" und "_".
- Standard-Wert (optional)
    - Bei einem anderen Typen als "Einfach", kann kein Standard-Wert gesetzt werden.

<a name="bulk-create-data-example"></a>

## Beispiele für Mehrfachanlage von Datenfeldern

- **text_1**  
  Einfaches Datenfeld `text_1` mit einer leeren Zeichenkette als Standard-Wert.

- **text_2;null**  
  Einfaches Datenfeld `text_2` ohne Standard-Wert.

- **text_3;Hello World**  
  Einfaches Datenfeld `text_3` mit `"Hello World"` als Standard-Wert.

- **=liste_1**  
  JSON-Array Datenfeld `liste_1` mit einem leeren JSON-Array als Standard-Wert.

- **~objekt_1**  
  JSON-Objekt Datenfeld `objekt_1` mit einem leeren JSON-Objekt als Standard-Wert.

***

[Nächster Artikel: Regeln & Daten ➜ Rollen](/{{route}}/{{version}}/roles)
