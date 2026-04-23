# Konfiguration ➜ Listen

---

- [Einleitung](#introduction)
- [Liste erstellen](#create-list)
- [Listenkonfiguration](#list-configuration)
- [Optionen](#options)
- [Daten - "Prozesse"-Vorlage](#data-processes)
- [Daten - "Benutzer"-Vorlage](#data-users)
- [Daten - "Gruppen"-Vorlage](#data-groups)
- [Daten - "Gruppen-Mitglieder"-Vorlage](#data-group-members)
- [Daten - "Ausgeführte Aktionen"-Vorlage](#data-actions)
- [Daten - "Verknüpfte Prozesse"-Vorlage](#data-relations)
- [Daten - "Ausgeführte Aktionen"-Vorlage](#data-action)
- [Daten - "Konnektor-Anfrage"-Vorlage](#data-Konnektor)
- [Spalten](#columns)

<a name="introduction"></a>

## Einleitung

Unter "Listen" werden alle Listen des Prozesses verwaltet. Prozess-Listen werden in der Regel über die
Prozess-Navigation erreicht und können zum Beispiel verknüpfte Prozess-Instanzen anzeigen. Innerhalb einer Aktion kann
eine Liste in einem Dialog-Fenster angezeigt werden oder als Datengrundlage für ein Auswahl-Feld oder Autocomplete-Feld
in einem Web-Formular genutzt werden.

<a name="create-list"></a>

## Liste erstellen

Über den Button "Neue Liste" **(1)** kann eine neue Liste angelegt werden.

![Listen](/img/docs/de/config-lists/config-lists-create.png)

Dort wird ein Label **(1)** eine Beschreibung **(2)**, ein Slug **(3)** und eine Vorlage **(4)** gewählt. Der "Slug"
ist die Listenkennung in der URL, z.B. "/lists/*personen*". Die Beschreibung wird bei der Listenansicht unterhalb des
Namens angezeigt.

![Listen](/img/docs/de/config-lists/config-lists-create-form.png)

Es bestehen folgende Vorlagen:

- Prozesse: Listet beliebige Prozess-Instanzen. Es kann nach Prozesstyp gefiltert werden.
- Benutzer: Listet Benutzer. Optional können Benutzer nach Tags gefiltert werden.
- Gruppen: Listet Gruppen. Optional können Gruppen nach Tags gefiltert werden.
- Gruppen-Mitglieder: Listet die Gruppen-Mitglieder von beliebig vielen Gruppen.
- Verknüpfte Prozesse: Listet verknüpfte Prozess-Instanzen. Es kann nach Verknüpfungstyp gefiltert werden.
- Ausgeführte Aktionen: Listet ausgeführte Aktionen. Es kann nach Aktion gefiltert werden.
- Rollen: Listet die Prozess-Rollen.
- Eigenes SQL: Ermöglicht die Erstellung einer eigenen SQL-Abfrage für die Liste.
- Konnektor-Anfrage: Ermöglicht es, das Ergebnis einer Konnektor-Anfrage in einer Liste anzuzeigen.

<a name="list-configuration"></a>

## Listenkonfiguration

In der Übersicht einer Liste befinden sich verschiedene Bereiche, in denen die Liste konfiguriert wird. Je nach Vorlage,
wird ein anderer "Daten"-Bereich angezeigt.

![Listenkonfiguration](/img/docs/de/config-lists/config-lists-overview.png)

1. Optionen: Gundlegende Konfiguration der Liste, wie z.B. Name, Beschreibung, Slug, Anzahl der Einträge pro Seite,
   weitere Anzeigeoptionen und Quick-Filter.
2. Daten: Konfiguration des Listeninhalts und Sortierung.
3. Spalten: Konfiguration der Listenspalten.
4. Kopieren/Löschen: Liste kopieren/löschen.
5. Aktuelle Liste, die bearbeitet wird.

<a name="options"></a>

## Optionen

Im "Optionen"-Bereich werden grundlegende Daten der Liste, wie z.B. Name, Beschreibung, Slug, Anzahl der Einträge pro
Seite, weitere Anzeigeoptionen und Quick-Filter konfiguriert.

![Listenkonfiguration](/img/docs/de/config-lists/config-lists-options.png)

1. Name der Liste.
2. Beschreibung der Liste. Wird unterhalb des Names bei der Listenansicht angezeigt.
3. Slug: Kennung der Liste in der URL, z.B. "/lists/*budget-freigaben*".
4. Suchfeld ein-/ausblenden.
5. Paginiereung ein-/ausblenden.
6. Anzahl der Listeneinträge ein-/ausblenden. Die Liste lädt schneller, wenn die Ergebnisanzahl ausgeblendet ist.
7. Quick-Filter: Ermöglicht die schnelle Filterung der Listeneinträge nach vorgegebenen Werten. Beispielsweise ist bei
   der "Budget-Freigabe"-Liste ein Quick-Filter eingetragen, um nach schnell nach *freigegebenen* "Budget-Freigaben" zu
   filtern.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-quick-filter.png)

8. Listen-Button-Konfiguration: Festlegung einer individuellen URL. In der Regel wird dort zu einer Initial-Aktion
   verlinkt.

<a name="data-processes"></a>

## Daten - "Prozesse"-Vorlage

Der nachfolgende Screenshot zeigt den "Daten"-Bereich der "Prozesse"-Listenvorlage. Die Listenvorlage ist ideal für
Listen, in denen Prozess-Instanzen angezeigt werden sollen. Im Bereich "Prozesstypen" **(1)** können beliebig viele
Prozesstypen gewählt werden, deren Prozess-Instanzen angezeigt werden sollen. Im Bereich "Meta-Daten" **(2)** können
Meta-Daten der Prozesse gewählt werden. In dem Fall, dass nur ein Prozesstyp gewählt wurde, können unter
"Status-Daten" **(3)** und "Prozess-Daten" **(4)** weitere Datenfelder hinzugefügt werden.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-processes.png)

Unter "Konditionen" **(5)** können weitere Filter gesetzt werden, mit denen die Auswahl der Prozess-Instanzen weiter
eingeschränkt wird. Im Bereich "Sortierung" **(6)** kann die Sortierung der Listeneinträge angepasst werden. Zuletzt
besteht die Möglichkeit, die Prozesse nach Beteiligung der nutzenden Person zu filtern. Falls der Schalter **(7)**
aktiviert ist, werden nur Prozess-Instanzen angezeigt, auf die die nutzende Person Zugriff hat.

<a name="data-users"></a>

## Daten - "Benutzer"-Vorlage

Mit der Listenvorlage "Benutzer" können Benutzern der Allisa Plattform geladen werden. Die Liste der Benutzer kann nach
Tags gefiltert werden.
Der nachfolgende Screenshot zeigt den "Daten"-Bereich der "Benutzer"-Listenvorlage.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-users.png)

Die obenstehende Liste lädt alle Benutzer der Allisa Plattform mit dem Tag "extern" und sortiert diese aufsteigende nach
dem Vornamen.

Folgende Datenfelder stehen mit der Liste zur Verfügung:
- Benutzer - Pipe-Notation
- Benutzer - Id
- Benutzer - Vorname
- Benutzer - Nachname
- Benutzer - E-Mail
- Benutzer - Ganzer Name
- Benutzer - Aliases (JSON-Array)
- Benutzer - Tags (JSON-Array)
- Benutzer - Erstelldatum
- Benutzer - Aktualisierungsdatum

> {info.fa-info-circle} Der Benutzer benötigt das Allisa Plattform Recht, die Benutzer der Plattform einsehen zu dürfen.

<a name="data-groups"></a>

## Daten - "Gruppen"-Vorlage

Mit der Listenvorlage "Gruppen" können Gruppen der Allisa Plattform geladen werden. Die Liste der Gruppen kann nach Tags
gefiltert werden.
Der nachfolgende Screenshot zeigt den "Daten"-Bereich der "Gruppen"-Listenvorlage.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-groups.png)

Die obenstehende Liste lädt alle Gruppen der Allisa Plattform mit dem Tag "fachbereich" und sortiert diese aufsteigende
nach dem Namen.

Folgende Datenfelder stehen mit der Liste zur Verfügung:
- Gruppe - Pipe-Notation
- Gruppe - Id
- Gruppe - Name
- Gruppe - Beschreibung
- Gruppe - Aliases (JSON-Array)
- Gruppe - Tags (JSON-Array)
- Gruppe - Erstelldatum
- Gruppe - Aktualisierungsdatum

> {info.fa-info-circle} Der Benutzer benötigt das Allisa Plattform Recht, die Gruppen der Plattform einsehen zu dürfen.

<a name="data-group-members"></a>

## Daten - "Gruppen-Mitglieder"-Vorlage

Der nachfolgende Screenshot zeigt den "Daten"-Bereich der "Gruppen-Mitglieder"-Listenvorlage. Mit dieser Listenvorlage
kann eine Liste von Gruppen-Mitgliedern geladen werden. Es können die Mitglieder beliebig vieler Gruppen geladen werden.
Für jede Gruppe, dessen Mitglieder geladen werden sollen (siehe "Gruppen-Alias **(1)**), können kommasepariert Rollen-Aliase
angegeben werden. Bei Angabe von Rollen-Aliase, werden nur die Mitglieder dieser Gruppen-Rollen geladen **(2)**. Bei fehlender
"Rollen-Aliase" Angabe werden alle Gruppen-Mitglieder geladen.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-group-members.png)

Die obenstehende Liste lädt alle Mitglieder der Gruppe mit dem Alias "standard", welche die Gruppen-Rolle "direktor" einnehmen
und zusätzlich alle Mitglieder der Gruppe "finance". 

Folgende Datenfelder stehen mit der Liste zur Verfügung:

- Benutzer - ID
- Benutzer - Vorname
- Benutzer - Nachname
- Benutzer - Voller Name
- Benutzer - E-Mail
- Benutzer - Model-Pipe-Notation
- Benutzer - Beitrittsdatum
- Gruppe - Name
- Gruppe - Model-Pipe-Notation
- Gruppen-Rolle - Name
- Gruppen-Rolle - Model-Pipe-Notation

> {info.fa-info-circle} Der Benutzer benötigt das Allisa Plattform Recht, die Benutzer der angegebenen Gruppen einzusehen.

<a name="data-relations"></a>

## Daten - "Verknüpfte Prozesse"-Vorlage

Der nachfolgende Screenshot zeigt den "Daten"-Bereich der "Verknüpfte Prozesse"-Listenvorlage. Die Listenvorlage ist
ideal für Listen, in denen verknüpfte Prozess-Instanzen angezeigt werden sollen. Im Bereich
"Verknüpfungstypen" **(1)** können die verknüpften Prozess-Instanzen nach Verknüpfungstyp gefiltert werden. Im Bereich
"Prozesstypen" **(2)** können beliebig viele Prozesstypen gewählt werden, deren Prozess-Instanzen angezeigt werden
sollen. Im Bereich "Meta-Daten" **(2)** können Meta-Daten der Prozesse gewählt werden. In dem Fall, dass nur ein
Prozesstyp gewählt wurde, können unter "Status-Daten" **(4)** und "Prozess-Daten" **(5)** weitere Datenfelder
hinzugefügt werden.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-relations.png)

Im Bereich "Sortierung" **(6)** kann die Sortierung der Listeneinträge angepasst werden. Mit dem Schalter **(7)**
können Prozesse gefiltert werden, in denen die nutzende Person Zugriff hat.

Unter "Kontext der verknüpften Prozesse" **(8)** kann ausgewählt werden, von welcher Prozess-Instanz die verknüpften
Prozesse geladen werden soll. Standardgemäß werden die verknüpften Prozesse des ausführenden Prozesses gewählt. In
manchen Situationen, wie beispielsweise bei einer Initial-Aktion, ist es hilfreich den URL Query "context"-Parameter als
Kontext zu benutzen. Der "context"-Parameter muss eine Prozess Model-Pipe-Notation als Wert haben,
z.B. `.../relations?context=process|e61de66f-9971-40d2-8b2a-85efaa34e18b`

<a name="data-actions"></a>

## Daten - "Ausgeführte Aktionen"-Vorlage

Mit der "Ausgeführte Aktionen"-Vorlage können Aktionen nach einem Aktionstyp gefiltert werden.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-actions.png)

Unter "Aktionstypen" **(1)** können die ausgeführten Aktionen nach Aktionstypen gefiltert werden. Bei "Meta-Daten"
**(2)** können die Meta-Daten einer Aktion gewählt werden.

<a name="data-roles"></a>

## Daten - "Rollen"-Vorlage

Mit der "Rollen"-Vorlage können die Rollen des Prozesstyps in einer Liste angezeigt werden. In der Praxis ist die Liste
für Auswahl-Felder oder Autocomplete-Felder bei Aktionen hilfreich, die Zugriff erteilen. Dort könnte die nutzende
Person eine Rolle aus der Liste auswählen, mit eine Person oder Gruppe Zugriff erteilt bekommt.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-roles.png)

<a name="data-Konnektor"></a>

## Daten - "Konnektor-Anfrage"-Vorlage

Bei der "Konnektor-Anfrage"-Listenvorlage kann der Rückgabewert eines **externen Schnittstellen-Aufrufes** als
Datengrundlage für die Liste genutzt werden. Vorraussetzung dafür ist, dass eine Konnektor-Anfrage genutzt wird, die
eine **Liste von Einträgen** zurückgibt.

![Listenkonfiguration](/img/docs/de/config-lists/list-config-data-Konnektor.png)

Im Bereich "Konnektor-Anfrage" **(1)** wird die Konnektor-Anfrage gewählt, die als Datengrundlage für die Liste genutzt
werden soll. Der "Listen-Root" **(2)** ist der "Pfad" (in Dot-Notation) zu den Einträgen auf der
Schnittstellen-Rückgabe.

```json
{
    "result": {
        "data": [
            {
                "name": "Alice",
                "age": "32"
            },
            {
                "name": "Bob",
                "age": "28"
            }
        ]
    }
}
```

Beim obigen Beispiel würde der Listen-Root "result.data" auf die zwei "data"-Einträge zeigen.

Im Bereich "Select - Datenfelder" werden die **Datenfelder des Schnittstellen-Rückgabewertes auf beliebige Aliases
geschrieben**, wodurch man nicht auf die Namenskonventionen der Schnittstelle angewiesen ist.

Im letzten Bereich "Listensuche Parameter-Mapping" werden die Plattform-nativen Such-Parameter auf die Query-Parameter
der Konnektor-Anfrage gemappt. In dem obigen Screenshot wird beim Konnektor "Allisa Inside REST API" der Parameter für
die "rowsPerPage" (Einträge pro Seite) auf den Plattform Query-Parameter "rows_per_page" gemappt. Das Mapping ermöglicht
es, die Liste der externen Schnittstelle zu durchsuchen.

<a name="columns"></a>

## Spalten

Im "Spalten"-Tab werden die Listenspalten konfiguriert. Jede Spalte hat folgende Optionen:

![Listenkonfiguration](/img/docs/de/config-lists/list-config-columns.png)

- Primäres Datenfeld: Datenfeld aus dem "Daten"-Bereich.
- Sekundäres Datenfeld: Falls das primäre Datenfeld leer ist (leere Zeichenkette oder NULL), wird dieses Datenfeld
  genommen.
- Hintergrundfarbe: Farbe für den Hintergrund der Listenspalte. Es kann entweder ein HEX-Code (z.B. #001337) oder ein
  Datenfeld gewählt werden.

Weitere Optionen sind abhängig von dem Spalten-Typ. Folgende **Spalten-Typen** zur Verfügung:

### Text

Einfache Text-Anzeige eines Datenfeldes. Es kann ein Untertitel und im Falle eines Datums-Wertes eine Formatierung
gewählt werden.

### Währung

Anzeige eines Währungswertes.

### Link

Ein benutzerspezifischer Link, beispielsweise zu der Prozess-Instanz oder zu einer Prozess-Aktion. Es können Werte der
Listenzeile genutzt werden, um die URL zu formatieren.

### Tags

Anzeige von JSON-Array Werten als Tags. Bei einem Klick auf ein Tag wird die Liste danach durchsucht.

### Fortschritt

Fortschritts-Balken oder Fortschritts-Icons. Die Min- und Max-Werte können anhand von Listenzeilen-Werten gesetzt
werden.

### Button

Es kann entweder ein einfacher Link angezeigt werden oder ein REST API Post Request durchgeführt werden. Ein
Post Request ist beispielsweise nützlich, wenn in einer Prozess-Instanz eine Aktion ausgeführt werden soll wollen,
ohne dass die Prozess-Instanz geöffnet werden möchte.

### Icon

Anzeige eines Icons. Je nach Datenfeld-Wert können unterschiedliche Icons angezeigt werden.

### Input

Anzeige eines HTML-Eingabefeldes. In Kombination mit einem POST-Request bei einer "Button"-Spalte können so individuelle
Aktions-Ausführungen aus einer Liste getriggert werden.

### Auswahl

Anzeige eines HTML-Eingabefeldes. In Kombination mit einem POST-Request bei einer "Button"-Spalte können so individuelle
Aktions-Ausführungen aus einer Liste getriggert werden.

### JSON-Liste

Einfache Liste von Werten in einem JSON-Array.

***

[Nächster Artikel: Konfiguration ➜ Menü](/{{route}}/{{version}}/navigation)
