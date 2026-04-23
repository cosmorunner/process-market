# Konfiguration ➜ Aktions-Komponenten

---

- [Einleitung](#introduction)
- [Web-Formular - allisa/form](#allisa-form)
- [-- Feldtypen](#allisa-form-fields)
- [-- Computed Input](#allisa-form-computed-input)
- [-- Mehrfach-Sets](#allisa-form-multi-sets)
- [Liste - allisa/collection](#allisa-collection)
- [Fortschrittsbalken - allisa/progress-bar](#allisa-progress-bar)

<a name="introduction"></a>

## Einleitung

Aktions-Komponenten sind Elemente einer Aktion, die in der Benutzer-Oberfläche einer Aktion angezeigt werden. Die am
häufigsten verwendete Aktions-Komponente ist das Web-Formular, mit der Benutzer Daten bei der Aktionsausführung erfassen
können. Des Weiteren ist eine Liste eine wichtige Aktions-Komponente. Eine Aktion kann beliebig viele Aktions-Komponenten
besitzen.

![Konfiguration](/img/docs/de/config-action-component-example.png)

Der obenstehende Screenshot zeigt die Aktion "Auftrag ändern" des "Montage" Case-Study Prozesses in der Allisa
Plattform. Die Aktion besitzt eine "Web-Formular"-Komponente **(1)** mit einem Hinweis und zwei Feldern "Kunden-Nr."
und "Getriebe-Typ".

Untenstehend die entsprechende Konfiguration in der Prozessfabrik. Oben rechts ist die Aktion "Auftrag ändern" **(1)**
gewählt. Die Aktion hat eine Komponente vom Typ "allisa/form" **(2)**, welche ein Web-Formular ist. Das Web-Formular hat
zwei Sets **(3, 4)**. Das zweite Set hat zwei Felder "Kunden-Nr." und "Getriebe-Typ" **(5, 6)**.

![Konfiguration](/img/docs/de/action-components/config-action-component-config.png)

<a name="allisa-form"></a>

## Web-Formular - allisa/form

Das Web-Formular ist die am häufigsten genutzte Aktions-Komponente. Mit einem Web-Formular können Benutzer Eingaben
tätigen, die mit der Aktionsausführung gespeichert werden.

![Konfiguration](/img/docs/de/action-components/config-allisa-form-example.png)

Im obenstehenden Screenshot wird ein typisches Web-Formular in der Allisa Plattform angezeigt. Ein Web-Formular kann
mehrere Sets **(1, 2)** besitzen, die jeweils Felder besitzen.

Ein neue "Form"-Komponente wird angelegt, indem man bei "Komponente hinzufügen" "Formular - allisa/form" wählt.

![Konfiguration](/img/docs/de/action-components/config-form-create.png)

Eine "Formular"-Komponente besteht aus Sets und Feldern und kann in der Breite angepasst werden.

![Konfiguration](/img/docs/de/action-components/config-form-overview.png)

1. Mit diesem Icon kann eine Komponente, ein Set oder ein Feld kopiert, und in anderen oder der selben Aktion wieder 
   eingefügt werden. Allerdings wird beim Kopieren von Sets und Feldern der Suffix "_kopie" angehängt.  
2. Der orangene Block ist ein Set. Das Set hat das Label "Daten".
3. Der blaue Block im Set ist ein Feld. Das Feld hat den Namen "vorname" und ist ein Text-Feld (siehe Icon links
   daneben).
4. Die Anzeige für das Vorladen bzw. Speichern der Aktions- und Prozessdaten. Wenn aktiviert, sind die Icons grün
   markiert.
    - Das erste Icon zeigt an, ob es ein Aktions-Vorladewert mit dem Namen gibt. Wenn ja, wird das Feld mit dem
      Wert vorbefüllt.
    - Das zweite Icon zeigt an, ob es ein Aktions-Datenfeld mit dem Namen gibt. Wenn ja, wird der Wert des Feldes
      in den Aktions-Daten gespeichert.
    - Das dritte Icon zeigt an, ob es ein Prozess-Datenfeld mit dem Namen gibt. Wenn ja, wird der Wert des Feldes
      in den Prozess-Daten gespeichert.
5. Breite des Feldes.
6. Per "Drag & Drop" kann das Feld an eine andere Position verschoben werden.
7. Anzeige-Regeln des Feldes. Das Feld kann konditionell versteckt, als Pflichtfeld oder als "Nur lesen"-Feld markiert
   werden.
8. Breite des Sets.
9. Per "Drag & Drop" kann das Set an eine andere Position verschoben werden.
10. Anzeige-Regeln des Sets. Die Felder des Sets können konditionell versteckt, als Pflichtfeld oder als "Nur lesen"-Feld
   markiert werden.
11. Anzeige-Regeln der Komponente. Die Komponente kann konditionell versteckt werden.

<a name="allisa-form-fields"></a>

## Feldtypen

Folgende Feldtypen können genutzt werden:

- [Text](#allisa-form-text)
- [Textarea](#allisa-form-textarea)
- [Radio](#allisa-form-radio)
- [Auswahl](#allisa-form-select)
- [Checkbox](#allisa-form-checkbox)
- [Währung](#allisa-form-currency)
- [Datei-Upload](#allisa-form-file-upload)
- [Hinweis](#allisa-form-notice)
- [Autocomplete](#allisa-form-autocomplete)
- [Datepicker](#allisa-form-datepicker)
- [Icon](#allisa-form-icon)
- [Link](#allisa-form-link)
- [Dialog-Liste](#allisa-form-dialog)
- [Paragraph](#allisa-form-paragraph)
- [Zeit-Auswahl](#allisa-form-timepicker)

### Grundlegende Feldoptionen

Jedes Feld in der Web-Formular Komponente muss einen einzigartigen Namen besitzen **(1)**. Sollte die Aktion
**Vorladedaten oder Aktions-Daten** besitzen (siehe Prozessmodellierung - "Regeln & Daten"), müssen die Namen der
Datenfelder mit den Namen der Web-Formular Felder übereinstimmen, sodass die Daten korrekt vorgeladen, bzw. gespeichert
werden können.

Das "Label" ist der Anzeige-Name des Feldes **(2)**. Mit der Hilfe-Option kann der nutzenden Person eine Eingabehilfe
gegeben werden **(3)**. Unter "CSS-Klassen" **(4)**, können mittels Leerzeichen getrennte, benutzerdefinierte CSS-Klassen angegeben 
werden. Die Eigenschaften dieser Klassen werden in der Plattform im Adminbereich unter "Design" festgelegt. Die "Feld hervorheben"-Option
markiert ein Feld mit einer roten Umrandung **(5)**.
Die Optionen für den Namen, das Label, die Hilfe, die CSS-Klassen und Feld hervorheben sind bei jedem Feldtyp vorhanden, mit
Ausnahme des versteckten Feldes, dort gibt es keine dieser Optionen.

![Konfiguration](/img/docs/de/action-components/config-form-basic.png)

Der untenstehende Screenshot zeigt das Feld in der Aktion in der Allisa Plattform. "Daten" stammt aus der
Set-Konfiguration. Die Hilfe wird unterhalb des Feldes angezeigt.

![Konfiguration](/img/docs/de/action-components/config-form-basic-example.png)

<a name="allisa-form-text"></a>

### Text

Ein einfaches Textfeld.

![Konfiguration](/img/docs/de/action-components/config-form-text.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-text-config.png)

<a name="allisa-form-textarea"></a>

### Textarea

Ein einfaches Textarea-Feld.

![Konfiguration](/img/docs/de/action-components/config-form-textarea.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-textarea-config.png)

1. Zeilen - Anzahl der Eingabezeilen.

<a name="allisa-form-radio"></a>

### Radio

Ein einfaches Radio-Feld.

![Konfiguration](/img/docs/de/action-components/config-form-radio.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-radio-config.png)

1. Optionen - Die Radio-Optionen, von denen die nutzende Person eine Option auswählen kann.

<a name="allisa-form-select"></a>

### Auswahl

Ein einfaches Auswahlfeld.

![Konfiguration](/img/docs/de/action-components/config-form-select.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-select-config.png)

1. Optionen - Die Auswahl-Optionen, von denen die nutzende Person eine Option auswählen kann.
2. Listenkonfiguration - Alternativ kann eine Prozess-Liste gewählt werden von dessen Einträgen die Auswahl-Optionen
   erzeugt werden.

<a name="allisa-form-checkbox"></a>

### Checkbox

Eine einfache Checkbox. Markierte Checkboxen haben den Wert "1". Nicht markierte Checkboxen haben den Wert "0".

![Konfiguration](/img/docs/de/action-components/config-form-checkbox.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-checkbox-config.png)

<a name="allisa-form-currency"></a>

### Währung

Mit dem Währungs-Feld lassen sich verschiedene Währungen formatiert in einem Eingabefeld anzeigen und bearbeiten.

![Konfiguration](/img/docs/de/action-components/config-form-currency.png)

Alle Währungsangaben werden als numerische Zeichenkette in den Aktions-Daten gespeichert. Die Werte der obenstehenden
Beispiele sind

- "10000.50"
- "1000"

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-currency-config.png)

1. Währung - Dropdown mit verfügbaren Währungscodes
2. Dezimalstellen - Bei Angabe sind zwei Dezimalstellen möglich, sonst keine. Standardmäßig aktiviert.
3. Währungscode - Bei Angabe wird der Währungscode (z. B. 'EUR') links vom Input-Feld angezeigt

#### Währungsangaben in einem Mehrfach-Set

Ein häufiger Anwendungsfall des Währungsfeldes ist die Angabe von Artikeln. Dies könnte beispielsweise folgendermaßen
aussehen:

![Konfiguration](/img/docs/de/action-components/config-form-currency-multi-set.png)

Folgende Werte sind in den Aktions-Daten (Debug-Anzeige) bei der Befüllung der obenstehenden Mehrfach-Set Zeilen
hinterlegt:

![Konfiguration](/img/docs/de/action-components/config-form-currency-multi-set-values.png)

Die Felder "Preis" und "Brutto" sind Währungsfelder. Das Feld "Mehrwertsteuer" ist ein Auswahlfeld mit den Optionen "
Keine" (Wert: 1),
"7%" (Wert: 1.07) und "19%" (Wert: 1.19). Das "Brutto" Feld hat einen "Computed Input" Wert mit folgendem JavaScript:

```javascript
// Wert des Feldes "preis" aus dem Array an dem jeweiligen Index holen. "0" ist der Standardwert, 
// falls der Wert "preis" noch nicht in den Aktions-Daten gesetzt ist.
let price = action.val('preis', 0, index);

// Dasselbe für das Mehrwertsteuer-Feld
let mwst = action.val('mwst', 0, index);

// Summe berechnen. "mathjs" ist eine automatisch eingebundene Bibliothek, siehe https://mathjs.org.
let sum = mathjs.multiply(price, mwst);

// Wert auf zwei Nachkommastellen runden.
return mathjs.round(sum, 2);
```

> {info.fa-info-circle} Die "math.js" Bibliothek (https://mathjs.org/docs/index.html) ist automatisch eingebunden und
> kann damit in Computed Input und dem
> Aktions- und Prozess JavaScript genutzt werden.

<a name="allisa-form-file-upload"></a>

### Datei-Upload

Mit einem Datei-Upload Feld kann ein Dokument hochgeladen werden.

![Konfiguration](/img/docs/de/action-components/config-form-file-upload.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-file-upload-config.png)

<a name="allisa-form-notice"></a>

### Hinweis

Mit dem "Hinweis"-Feldtyp kann ein einfacher Hinweis im Web-Formular angezeigt werden. Anzeige in der Aktion:

![Konfiguration](/img/docs/de/action-components/config-form-notice.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-notice-config.png)

1. Text - Anzeigetext für den Hinweis. Falls das Feld ein Vorladewert hat, wird dieser für den Hinweis genutzt.
2. Typ - Es kann zwischen "Information", "Erfolgsmeldung", "Warnung" und "Sehr wichtiger Hinweis" gewählt werden.

<a name="allisa-form-autocomplete"></a>

### Autocomplete

Mit dem "Autocomplete"-Feld können Einträge aus einer Liste gewählt werden oder nach Einträgen gesucht werden. Einträge
in der Liste werden anhand der Suche gefiltert.

> {info.fa-info-circle} Der Wert des Autocomplete-Feldes ist ein Array. Bitte stellen Sie sicher, 
> dass das Aktions-Datenfeld vom Typ "JSON-Array" ist.

![Konfiguration](/img/docs/de/action-components/config-form-autocomplete.png)

![Konfiguration](/img/docs/de/action-components/config-form-autocomplete-2.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-autocomplete-config.png)

1. Max. Einträge - Anzahl an Einträgen, die das Feld maximal halten kann.
2. Optionen - Manuelle Eingabe von Optionen von denen Werte ausgewählt werden können.
3. Listenkonfiguration - Die Optionsauswahl basierend auf einer Prozess-Liste generieren.
4. Eigene Werte erlauben? - Bei aktivierter Option, kann der Benutzer einen eigenen Wert mit "Enter" eingeben.

<a name="allisa-form-datepicker"></a>

### Datepicker

Ermöglicht der nutzenden Person ein Datum auszuwählen.

![Konfiguration](/img/docs/de/action-components/config-form-datepicker.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-datepicker-config.png)

1. Format - Das Format für das ausgewählte Datum.

<a name="allisa-form-icon"></a>

### Icon

Ein individuelles Icon anzeigen.

![Konfiguration](/img/docs/de/action-components/config-form-icon.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-icon-config.png)

1. Iconauswahl
2. Feld, dessen Wert für das Icon genutzt werden soll
3. Mapping für welchen Wert des Feldes welches Icon angezeigt werden soll
4. Mapping für welchen Wert des Feldes welche Farbe für das Icon genutzt werden soll

<a name="allisa-form-link"></a>

### Link

Einen Link als normalen Link oder Button anzeigen.

![Konfiguration](/img/docs/de/action-components/config-form-link.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-link-config.png)

1. Link-Anzeigetext - Leer lassen um die URL als Anzeigetext zu nutzen.
2. Icon - Optionales Icon welches for dem Link angezeigt wird.
3. URL - Die URL des Links. Leer lassen um den Vorladewert des Feldes zu nutzen.
4. Style - Einfacher Link, ein Button oder ein Block-Button.
5. Klickverhalten - Den Link im aktuellen Tab oder in einem neuen Tab öffnen.

<a name="allisa-form-dialog"></a>

### Dialog-Liste

Eine Prozess-Liste in einem Dialog-Fenster öffnen.

![Konfiguration](/img/docs/de/action-components/config-form-dialog.png)
![Konfiguration](/img/docs/de/action-components/config-form-dialog-2.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-dialog-config.png)

1. Listenkonfiguration - Auswahl der Prozess-Liste, die im Dialogfenster angezeigt werden soll.
2. Suche - Auswahl des Datenfeldes, welches für die Listen-Suche genutzt wird.
3. Filter - Auswahl des Datenfeldes, nachdem gefiltert werden kann.
4. URL Query-Params - Datenfelder auf URL-Parameter mappen
5. "context"-URL-Parameter übernehmen - Den aktuellen "Context"-Parameter aus der URL übernehmen
6. Button-Label - Label für den Button mit dem das Dialogfenster geöffnet wird.
7. Wert - Wert aus der Liste auf das Dialogfeld übertragen. Die Liste benötigt Spalte mit einem "Mapping"-Button.
8. Weiteres Mapping - Weitere Werte auf andere Felder übertragen. Die Liste benötigt Spalte mit einem "Mapping"-Button.

Der in der Liste ausgewählte Wert (Option: Wert) wird als Button-Label angezeigt. Bei einer Model-Pipe-Notation wird der
Wert
mit Icon angezeigt. Beispiel einer Benutzer Model-Pipe-Notation:

![Konfiguration](/img/docs/de/action-components/config-form-dialog-selected-value.png)

<a name="allisa-form-paragraph"></a>

### Paragraph

Einen Text-Paragraph anzeigen.

![Konfiguration](/img/docs/de/action-components/config-form-paragraph.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-paragraph-config.png)

1. Text - Text für den Paragraph. Leer lassen um den Vorladewert zu nutzen.

<a name="allisa-form-timepicker"></a>

### Zeit-Auswahl

Ermöglicht die Auswahl einer Uhrzeit im 24-Stunden Format.

![Konfiguration](/img/docs/de/action-components/config-form-timepicker.png)

Konfigurations-Optionen:

![Konfiguration](/img/docs/de/action-components/config-form-timepicker-config.png)

<a name="allisa-form-computed-input"></a>

## Computed Input

Viele Feldtypen besitzen eine "Computed Input"-Option. Mit Computed Input kann der Wert des Feldes durch eine eigene
Logik ermittelt werden. Dadurch ist es möglich, den Wert des Feldes anhand anderer Feldwerte zu berechnen.

![Konfiguration](/img/docs/de/action-components/config-form-computed-config.png)

Der obenstehenden Screenshot zeigt die Konfigutation von einem Web-Formular mit drei Feldern: "vorname", "nachname"
und "ganzer_name". Ziel ist es, dass der Wert des Feldes "ganzer_name" sich aus dem Vor- und Nachnamen zusammensetzt.
Dafür hat das Feld "ganzer_name" folgenen Computed Input in Form von JavaScript Code:

![Konfiguration](/img/docs/de/action-components/config-form-computed-code.png)

Es wird der Vor- und Nachname mit einem Leerzeichen konkateniert zurückgegeben **(1)**.

#### Übergebene Variablen in der Computed Input Methode

Der Computed-Input Funktion werden drei Variablen übergeben, die in dem JavaScript Code genutzt werden können:

- value - Dies ist der aktuelle Wert des Feldes zum Zeitpunkt der Computed Input Berechnung.
- action - Ein Objekt mit dem auf alle aktuellen Daten der Aktion zugegriffen werden kann.
- index - Der numerische Index des Feldes in einem Mehrfach-Set, andernfalls nicht relevant.

Mit dem "action" Objekt kann auf die aktuellen Daten der Aktion zugegriffen werden. Die "val"-Methode liefert den Wert
anhand eines Feldnamens (key) zurück:

```javascript

/**
 * Gibt einen Wert aus den aktuellen Aktions-Daten zurück.
 * @param {string|null} key Feldname. Wenn leer werden alle Daten zurückgegeben.
 * @param {string} def Standard-Wert falls "key" nicht in den Aktions-Daten existiert.
 * @param {Number|null} index Optionale Übergabe eines Index-Wertes, falls der Wert ein Array ist.
 */
this.val = function (key = null, def = '', index = null) {
    // ... 
};

```

Durch die Computed Input Option beim "ganzer_name"-Feld, wird dieses Feld nun automatisch befüllt, wenn der Vorname oder
Nachname eingegeben wird, weil der berechnete Wert des Feldes sich aus dem Vor- und Nachnamen zusammensetzt:

```javascript 
return action.val('vorname') + ' ' + action.val('nachname');
```

![Konfiguration](/img/docs/de/action-components/config-form-computed-example.png)

<a name="allisa-form-multi-sets"></a>

## Mehrfach-Sets

Mit einem Mehrfach-Set kann eine dynamische Anzahl an Web-Formular Felder-Zeilen erzeugt werden, sodass die Eingabe von
Mehrfach-Einträgen ermöglicht wird.

![Konfiguration](/img/docs/de/action-components/config-form-multiset-example.png)

Das obenstehende Screenshot zeigt ein Mehrfach-Set für "Ausgaben" mit zwei Feldern "Name" und "Kosten". Untenstehend die
entsprechende Konfiguration.

![Konfiguration](/img/docs/de/action-components/config-form-multiset-overview.png)

Der gestreifte Sethintergrund zeigt an, dass es sich bei dem Set um ein Multiset handelt. Die Optionen des Sets können
konfiguriert werden indem das Label (hier "Ausgaben") gewählt wird.

![Konfiguration](/img/docs/de/action-components/config-form-multiset-config.png)

Mit der "Mehrfach-Set"-Checkbox wird die Multi-Set Funktionalität bei einem Set de-/aktiviert **(1)**. Falls die
Feldwerte in dem Set in den Aktionsdaten gespeichert werden sollen, müssen diese Aktions-Daten vom Typ "JSON-Array"
sein **(2)**. Mit der Min./Max. Anzahl wird festgelegt, wieviele Felder-Zeilen entfernt bzw. hinzugefügt werden
können **(3)**. Die "Nur lesen" Option entfernt die Möglichkeit, Felder-Zeilen zu entfernen oder hinzuzufügen **(4)**.
Mit der Option "Nicht befüllte Set-Zeilen anzeigen" werden die maximale Anzahl an Felder-Zeilen angezeigt, auch wenn
nicht alle Zeilen befüllt sind **(5)**. Bei Aktivierung von "Feld-Labels für jede Zeile", wird jede Zeile des Mehrfachsets
die Label anzeigen, ansonsten stehen sie nur einmal gan oben **(6)**.

#### Computed Input bei einem Multi-Set

Ein Computed Input Wert kann einerseits basierend auf allen Felder-Zeilen berechnet werden oder innerhalb einer
Felder-Zeile. Untenstehend zunächst das oben genannte Beispiel mit Ausgaben und einer "Gesamtkosten" Anzeige. Die
Gesamtkosten summiert alle "Kosten" Felder der Felder-Zeilen.

![Konfiguration](/img/docs/de/action-components/config-form-multiset-computed-example.png)

Das Feld "Gesamtkosten" ist in einem eigenen Set und hat eine Computed Input Funktion, die alle Kosten der Ausgaben
addiert.

![Konfiguration](/img/docs/de/action-components/config-form-multiset-computed-config.png)

Die Feldwerte innerhalb eines Multi-Sets sind Arrays, sodass mit der ersten Zeile zunächst alle Kosten auf eine Variable
geschrieben werden. Anschließend wird mit der JavaScript nativen "reduce" Methode alle Werte zusammenaddiert und
zurückgegeben. Computed Input von "Gesamtkosten":

```javascript
// Leeres Array als Standardwert, falls "cost" noch nicht existiert.
let items = action.val('cost', []);

// Alle Werte summieren. Wert zu Integer casten ("+item").
return items.reduce((carry, item) => carry + +item, 0)
```

Innerhalb eines Multi-Set können ebenfalls Computed Inputs genutzt werden. Im untenstehenden Screenshot ein Beispiel mit
Vor- und Nachnamen von Personen. Der ganze Namen wird aus dem Vor- und Nachnamen der jeweiligen Felder-Zeile
zusammengesetzt.

![Konfiguration](/img/docs/de/action-components/config-form-multiset-computed-example-2.png)

Die Web-Formular Konfiguration hat drei Felder, wobei das Feld "Ganzer Name" eine Computed Input Funktion hat.

![Konfiguration](/img/docs/de/action-components/config-form-multiset-computed-config-2.png)

Die Computed Input Funktion beim Feld "Ganzer Name" ist sehr ähnlich zu einen normalen Set, mit dem Unterschied, dass
bei der "action.val()"-Methode als drittes Argument der "index" mit übergeben werden muss, um den korrekten Wert der
jeweiligen Felder-Zeile zurückzugeben.

```javascript
return action.val('vorname', '', index) + ' ' + action.val('nachname', '', index);
```

<a name="allisa-collection"></a>

## Liste - allisa/collection

Mit der Listen-Komponente kann eine Prozess-Liste in einer Aktion angezeigt werden.

![Konfiguration](/img/docs/de/action-components/config-collection-action.png)

Ein neue Listen-Komponente wird angelegt, indem bei "Komponente hinzufügen" "Liste - allisa/collection" gewählt wird.

![Konfiguration](/img/docs/de/action-components/config-form-create.png)

Nach dem Anlegen der Komponente kann sie in der Übersicht gewählt werden, um die Optionen zu definieren.

![Konfiguration](/img/docs/de/action-components/config-collection-config.png)

In den Optionen der Komponente wird die Prozess-Liste gewählt **(1)**. Mit dem "Daten-Mapping" können Listenwerte auf
Aktions-Daten gemappt werden. Mit "URL Query-Parameter Binding" können Aktions-Daten auf Query-Parameter (z.B. "search"
, "context") für die Listensuche/Listenfilterung gemappt werden **(3)**. Durch ""context"-URL-Parameter übernehemn" kann
der aktuelle URL-Parameter "context" für die Liste übernommen werden **(4)**. 
Zuletzt kann konfiguriert werden, ob die Liste versteckt ist, wenn sie keine Einträge hat **(5)**.

#### Daten-Mapping

Mit Daten-Mapping ist es möglich, Werte aus der Liste auf Aktions-Daten zu schreiben.

![Konfiguration](/img/docs/de/action-components/config-collection-mapping.png)

In dem obenstehenden Screenshot wurde eine Listen-Komponente hinzugefügt. Das Daten-Mapping gibt an, dass die Werte des
Datenfeldes "processes_id" auf das Aktions-Datenfeld "benutzer_ids" gemappt werden sollen und die Werte des
Datenfeldes "processes_name" auf "benutzer_namen" **(1)**.

![Konfiguration](/img/docs/de/action-components/config-collection-mapping-example.png)

In der Aktion auf der Allisa Plattform wird die Liste angezeigt. Unter "Daten & Anzeige", einer Hilfe-Anzeige für die
Prozessmodellierung, wird deutlich, dass die Werte auf die Aktions-Daten gemappt wurden **(1)**. Die Daten werden stets
mit den Listeneinträgen synchronisiert, sodass beispielsweise eine Suche in der Liste, die gemappten Werde auch filtern
würde.

#### URL Query-Parameter Binding

Mit dem URL Query-Parameter Binding können Aktions-Daten auf Query-Parameter der Listensuche gemappt werden. Es können
somit die Query-Parameter "context", "search", "sort", "page", "rows_per_page" mit aktuellen Aktions-Daten befüllt
werden. Dabei wird die Liste stets synchron mit den Aktions-Daten gehalten.

![Konfiguration](/img/docs/de/action-components/config-collection-query-parameter-config.png)

Im obenstehenden Screenshot hat die Aktion eine Formular-Komponente und eine Listen-Komponente. Das Web-Formular hat ein
Textfeld "eigenes_suchfeld". Bei der Listen-Komponente wird der Query-Parameter "search" auf das Aktions-Datenfeld "
eigenes_suchfeld" gemappt.

![Konfiguration](/img/docs/de/action-components/config-collection-query-parameter-example.png)

Da mit dem "search"-Query Paramter eine Liste durchsucht wird, aktualisiert sich automatisch die Liste, wenn man bei
"Eigenes Suchfeld" einen Wert einträgt.

<a name="allisa-progress-bar"></a>

## Fortschrittsbalken - allisa/progress-bar

Mit dem Fortschrittsbalken kann ein Fortschritt als Fortschrittsbalken oder in Form von ausgefüllten Icons angezeigt
werden.

![Konfiguration](/img/docs/de/action-components/config-progress-example.png)
![Konfiguration](/img/docs/de/action-components/config-progress-example-2.png)

Ein neue Fortschrittsbalken-Komponente wird angelegt, indem bei "Komponente hinzufügen" "Fortschrittsbalken -
allisa/progress-bar" gewählt wird.

![Konfiguration](/img/docs/de/action-components/config-progress-create.png)

Als Typ kann zwischen einem Fortschrittsbalken und Icons gewählt werden, die je nach Fortschritt ausgefüllt oder nicht
ausgefüllt sind **(1)**. Das Label wird oberhalb des Fortschrittsbalken angezeigt **(2)**. Der Wert legt den Fortschritt
fest **(3)**. Mit "Min" und "Max" wird die untere und obere Grenze definiert **(4, 5)**. Die Farbe legt die Farbe des
Fortschrittsbalken bzw. der Icons fest **(6)**. Die Hilfe wird unterhalb des Fortschrittsbalken angezeigt **(7)**. Mit
"Wert anzeigen" kann der aktuelle Wert auf dem Fortschrittsbalken ein-/ausgeblendet werden **(8)**.

![Konfiguration](/img/docs/de/action-components/config-progress-config.png)

***

[Nächster Artikel: Konfiguration ➜ Aktions-Prozessoren](/{{route}}/{{version}}/action-processors)
