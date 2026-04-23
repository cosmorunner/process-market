# Regeln & Daten ➜ Aktionen

---

- [Einleitung](#introduction)
- [Aktion erstellen](#create-action)
    - [Mehrfach-Anlage Aktionen](#bulk-create-action)
    - [Beispiel Mehrfach-Anlage Aktionen](#bulk-create-action-example)
- [Vorlade-Daten](#preload-data)
    - [Mehrfach-Anlage Vorlade-Daten](#bulk-create-preload-data)
    - [Beispiele Mehrfach-Anlage Vorlade-Daten](#bulk-create-preload-data-example)
- [Aktions-Daten](#action-data)
    - [Mehrfach-Anlage Aktions-Daten](#bulk-create-action-data)
    - [Beispiele Mehrfach-Anlage Aktions-Daten](#bulk-create-action-data-example)

<a name="introduction"></a>

## Einleitung

Die Aktionen sind die wichtigsten Elemente eines Prozesses und häufig auch der Startpunkt in der Prozessmodellierung.
Die Granularität der Aktionen gibt häufig Aufschluss über den möglichen Prozess-Ablauf, weshalb diesem Bereich viel
Aufmerksamkeit geschenkt wird. Aktionen sind die Dinge, die im Prozess ausgeführt werden können, und können sehr
unterschiedliche Formen annehmen. Vom einfachen Speichern von Eingabedaten mithilfe eines Web-Formulares, über einen
E-Mailversand bis hin zur externen Schnittstellen-Anfrage mit vorheriger PDF-Dokumentenerzeugung.

Dieser Artikel befasst sich mit den **Grundlagen einer Aktion im Kontext des Prozess-Regelwerkes**. Weiterführende
Funktionen einer Aktion, wie beispielsweise die Web-Formular Erstellung, werden
im [Konfigurationsbereich](/{{route}}/{{version}}/config-actions)
des Prozess-Baukastens vorgestellt.

![Aktionen](/img/docs/de/rules-actions/rules-actions.png)

Aktionen werden einerseits im Tab "Aktionen" **(1)** angezeigt und in der graphischen Übersicht in Form der
**lilafarbenen "Cards" (2, 3)**.

![Aktionen](/img/docs/de/rules-actions/rules-action-detail.png)

Eine Aktion kann im Graph **ausgewählt werden(1)**, um die Detailansicht im Panel anzuzeigen. Im oberen Bereich der
Detailanzeige werden die **Aktionsregeln (2)** angezeigt, unterhalb von den Aktionsregeln die **Statusregeln (3)**.

**Vorlade-Daten (4)** sind Daten, welche beim Öffnen der Aktion in der Prozess-Instanz vorgeladen werden. Diese
vorgeladenen Werten können dann zum Beispiel in einem Web-Formular anzeigt werden.

**Daten (5)** (auch Aktions-Daten genannt) sind Datenfelder, welche die Aktion speichert, wenn sie ausgeführt wird. Hat
die Aktion beispielsweise ein Web-Formular Feld mit einem identischen Namen wir ein Aktions-Daten-Feld, wird der
eingegebene Wert gespeichert. Aktions-Daten agieren somit wie eine Art "Daten-Filter", weil sie genau spezifizieren,
welche Daten maximal aus der Aktion gespeichert werden können.

> {info.fa-info-circle} Web-Formular Felder, dessen Namen nicht in den Aktions-Daten angegeben sind, werden nicht
> gespeichert.

Ganz unten werden die **Prozessoren (6)** der Aktion gelistet. Prozessoren sind Zusatzfunktionen einer Aktion, wie
beispielsweise ein E-Mail-Versand oder eine PDF-Dokumentenerzeugung.

<a name="create-action"></a>

## Aktion erstellen

Sie können entweder über den Button "+Hinzufügen" im "Aktionen"-Panel **(1)** oder mit einem Rechtklick auf
die freie Fläche **(2)** eine neue Aktion erstellen.

![Aktionen](/img/docs/de/rules-actions/rules-create-action.png)

Es öffnet sich ein Dialog-Fenster mit einem Web-Formular.

![Aktionen](/img/docs/de/rules-actions/rules-create-action-form.png)

Vergeben Sie der Aktionen einen Namen und optional eine Beschreibung. Sie können angeben das ein Referenz-Wert
automatisch erzeugt wird **(1)**. Dieser wird in anderen Bereichen verwendet um auf die Aktion zu verweisen.
Dazu können Aktionen in Kategorien **(2)** organisiert werden. Diese werden in der Prozess-Instanz auf der Allisa Plattform angezeigt.
Das Icon **(3)** wird angezeigt, wenn man die Aktion in der Prozess-Instanz öffnet. Die Option "Ausführen-Button verstecken" **(4)**
ist nützlich für Aktionen, die lediglich Informationen anzeigen sollen. Bei aktivierter Option wird der grüne "Ausführen-Button" 
in der geöffneten Aktion nicht angezeigt. Das Ausführen-Label **(5)** ist das Label auf dem "Speichern"-Button in der 
geöffneten Aktion. Mit aktivierter "Instant-Aktion"-Option wird die Aktion direkt ausgeführt, wenn man sie in der Prozess-Instanz
Übersicht auswählt **(6)**. Dies ist besonders für Aktionen nützlich, bei denen die nutzende Person keine Eingaben
tätigen muss.

Im untenstehenden Screenshot ist die Aktion "Material kommissionieren" einer "Montage" Case-Study Prozess-Instanz
geöffnet. Das Icon wird links vom Aktions-Namen angezeigt und das Ausführen-Label ist unten rechts das Label "Weiter"
auf dem grünen Button.

![Aktionen](/img/docs/de/rules-actions/action-example-1.png)

Nach dem Anlegen der Aktion wird diese auf der freien Fläche angezeigt. Die Aktion ist eine "freie Aktion", weil
sie (noch) keine Aktions- oder Statusregeln hat und daher zu jedem Zeitpunkt im Prozess ausgeführt werden kann.
Im Artikel [Aktionsregeln & Statusregeln](/{{route}}/{{version}}/actionrules-statusrules) erfahren Sie, wie Sie
Bedingungen an Aktionen knüpfen und die Prozess-Situation mit Statusregeln verändern.

![Aktionen](/img/docs/de/rules-actions/rules-free-action.png)

<a name="bulk-create-action"></a>

## Mehrfach-Anlage Aktionen

Neben dem "+ Hinzufügen"-Knopf befindet sich der Knopf **(1)** für die Mehrfachanlage von Aktionen. Hier können mehrere
Aktionen gleichzeitig angelegt werden.

![Aktionen](/img/docs/de/rules-actions/bulk-create-action-button.png)

Über den Knopf öffnet sich ein Text-Editor, in dem die Aktionen mithilfe einer spezifischen Syntax angelegt werden
können.
Pro **Zeile** kann hier **eine** Aktion angelegt werden.

**Format**: `<Aktions-Name\>`

- Name der Aktion

<a name="bulk-create-action-example"></a>

## Beispiel für Mehrfach-Anlage von Aktionen

- **Meine Aktion**  
  Aktion mit dem Namen `Meine Aktion`.

<a name="preload-data"></a>

## Vorlade-Daten

Mit Vorlade-Daten können Sie Werte für eine Aktion vorladen. Diese können dann zum Beispiel in einem Web-Formular
angezeigt werden. In der Praxis ist diese Funktion besonders für Aktionen hilfreich, bei denen zuvor eingegebene Daten
bearbeitet werden sollen. Zum Beispiel könnte es bei einem Antrags-Prozess die Aktion "Antragsdaten ändern" geben. Wenn
nun diese Aktion in der Antrags-Prozess-Instanz geöffnet wird, soll das Web-Formular mit den initialen Antragsdaten
vorbelegt werden, die dann geändert werden können. Diese Vorbelegung wird mithilfe der Vorlade-Daten erreicht.

In der Detailansicht einer Aktion können Sie über das "+"-Icon im Vorlade-Daten Bereich ein neues Datenfeld anlegen.

![Aktionen](/img/docs/de/rules-actions/rules-create-preload.png)

Ein Vorlade-Wert kann eine einfache Zeichenkette, ein JSON-Array, ein JSON-Objekt oder der Inhalt einer Prozess-Liste
sein. Bei einer Prozess-Liste ist der Wert ein JSON-Array mit Objekten. Jedes Objekt repräsentiert eine Listenzeile.
Wird beim Listen-Inhalt eine Spalte gewählt, ist der Wert ein JSON-Array mit Zeichenketten. Eine Zeichenkette pro
Listenzeilen-Spaltenwert.

![Aktionen](/img/docs/de/rules-actions/rules-create-preload-form.png)

Der Name des Feldes darf nur "a-z", "0-9" und Unterstriche enthalten. Im Werte-Feld können Sie entweder manuell
einen Wert eintragen oder über das "+"-Dropdown einen Klammer-Syntaxwert nutzen.

Beim **"Auto"-Typ** formatiert die Allisa Plattform den Wert automatisch zu einem JSON-Array bzw. JSON-Objekt, falls
möglich
(z.B. wenn ein Klammer-Syntaxwert JSON kodiert ist).

Beim **"Zeichenkette"-Typ** wird der Wert immer zu einer Zeichenkette kodiert, auch wenn zum Beispiel ein
Klammer-Syntaxwert JSON kodiert ist.

![Aktionen](/img/docs/de/rules-actions/rules-preload-example-1.png)

Der obenstenden Screenshot zeigt die Vorlade-Daten der Aktion "Auftrag ändern" aus dem "Montage"-Case-Study Prozess.
Dort werden zwei Prozess-Datensätze vorgeladen: "getriebe_typ" und "kunden_nr".

![Aktionen](/img/docs/de/rules-actions/rules-preload-example-2.png)

Wird nun die Aktion "Auftrag ändern" in einer Montage Prozess-Instanz auf der Allisa Plattform geöffnet, werden die
Werte im Web-Formular vorgeladen, weil die Feldnamen die entsprechenden Namen "kunden_nr" (Kunden-Nr.) und
"getriebe_typ" (Getriebe-Typ) haben.

<a name="bulk-create-preload-data"></a>

## Mehrfach-Anlage Vorlade-Daten

Neben dem "+" Knopf befindet sich der Knopf **(1)** für die Mehrfachanlage von Vorlade-Daten. Hier
können mehrere Vorlade-Daten gleichzeitig angelegt werden.

![Aktionen](/img/docs/de/rules-actions/bulk-create-preload-data-button.png)

Über den Knopf öffnet sich ein Text-Editor, in dem die Vorlade-Daten mithilfe einer spezifischen Syntax angelegt
werden können.
Pro **Zeile** kann **ein** Vorlade-Datenfeld angelegt werden. Attribute werden durch ein ";" getrennt.

Bei der Mehrfach-Anlage von Vorlade-Daten hat man derzeit folgende Möglichkeiten:

**Format**: `<Typ(|~|=)><Name\>;<?Wert>`

- Typ (optional)
    - Der Typ des Datenfeldes wird mit einem Prefix angegeben:
        - Keine Angabe → Einfach
        - = → JSON-Array
        - ~ → JSON-Objekt
- Name des Vorlade-Datenfeldes
    - Kleingeschrieben, nur "a-z", "0-9" und "_".
- Wert (optional)
    - **Nur** bei dem Typ "Einfach" kann ein Standard-Wert gesetzt werden.

<a name="bulk-create-preload-data-example"></a>

## Beispiele für Mehrfach-Anlage von Vorlade-Datenfeldern

- **text_1**  
  Datenfeld `text_1` vom Typ "Automatisch" mit einer leeren Zeichenkette als Wert.

- **text_2;Hello World**  
  Datenfeld `text_2` vom Typ "Automatisch" mit `"Hello World"` als Wert.

- **text_3;#**  
  Datenfeld `text_3` vom Typ "Automatisch" mit dem gleichnamigen Prozess-Datenfeld als Wert.

- **=liste_1**  
  JSON-Array Datenfeld `liste_1` mit einer leeren Liste als Wert.

- **~objekt_1**  
  JSON-Objekt Datenfeld `objekt_1` mit einer leeren Liste als Wert.

<a name="action-data"></a>

## Aktions-Daten

Aktions-Daten legen fest, welche Daten eine Aktion speichern kann. In der Praxis ist eine Aktion häufig ein Web-Formular
mit Feldern, wie zum Beispiel ein Textfeld oder eine Checkbox. **Stimmen die Web-Formular Feldnamen mit den
Aktions-Daten
Namen überein, wird der eingegebene Wert in dem Aktions-Datensatz gespeichert**. Die Aktions-Daten agieren somit wie
ein Filter, weil sie genau definieren, welche Datensätze gespeichert werden können. Prozess-Datenfelder werden
durch Aktions-Datenfelder mit identischen Namen überschrieben.

![Aktionen](/img/docs/de/rules-actions/rules-create-action-data.png)

Wählen Sie das "+"-Icon im Bereich "Daten" um ein neues Aktions-Datenfeld anzulegen.
Analog zu einem Vorlade-Datensatz darf der Name des Datenfeldes nur "a-z", "0-9" und Unterstriche enthalten.

![Aktionen](/img/docs/de/rules-actions/rules-create-action-data-form.png)

1. Typ: Ein Aktions-Datensatz-Wert kann eine einfache Zeichenkette (Typ "Einfach"), ein JSON-Array oder ein JSON-Objekt sein
2. Standard-Wert: Der Standard-Wert des Datenfeldes wird genutzt, wenn bei der Aktionsausführung kein Wert (oder NULL) für das Datenfeld übermittelt wird.
3. Validatoren: Mit Validatoren können Eingabe-Prüfungen durchgeführt werden. Der Validator "Model-Pipe-Notation" legt fest, 
dass der Wert für das Aktions-Datenfeld dem Format "model|identifier" entsprechen muss, zum Beispiel `process|86043d4a-a768-416f-8df7-4de19b7ce5cb`
oder `bot|guest_1`. Die Model-Pipe-Notation ist eine auf der Allisa Plattform weitverbreitete Syntax zur Identifizierung von Resourcen.
Der Wert vor dem "|" Pipe-Symbol ist die Kennung der Ressource, in diesem Fall "Prozess" oder "Bot". Der Wert hinter dem Pipe-Symbol ist die Id 
oder Identifikation der Ressource. Mit dieser Syntax kann die Ressource eindeutig auf der Allisa Plattform identifiziert werden. Inbesondere die
Aktions-Prozessoren machen viel von der Model-Pipe-Notation Gebrauch.
4. Verknüpfungsprüfung: Mit dem Validator "Verknüpfungsprüfung" kann auf das Vorhandensein oder die Abwesenheit eines verknüpften Prozesses geprüft werden.
5. Wertevergleich: Ermöglicht es, den Wert des Aktionsdatenfeldes mit anderen Werten zu vergleichen. Beispielsweise könnte der Wert mit 
Prozess-Statuswerten oder Prozess-Daten von verknüpften Prozess-Instanzen verglichen werden.  
6. Hiermit wird ein Feld in den Prozess-Daten mit gleichem Namen angelegt. Hierbei werden alle Referenzen aufeinander gesetzt.
7. Hiermit wird ein Feld in den Vorlade-Daten mit gleichem Namen angelegt. Hierbei werden alle Referenzen aufeinander gesetzt.
8. Hiermit wird ein Formularfeld in der Aktion für dieses Feld erstellt.

![Aktionen](/img/docs/de/rules-actions/rules-action-data.png)

Der obenstehende Screenshot zeigt die Daten der "Auftrag erfassen"-Aktion des "Montage"-Case-Study Prozesses. Dort sind
zwei Datenfelder definiert: "getriebe_typ" und "kunden_nr". Das "karierte" Icon hinter dem Datenfeldname besagt, dass
dieses Datenfeld zusätzlich auch in den Prozess-Daten existiert, wodurch es durch andere Aktionen genutzt werden kann.
Wie oben unter "Vorlade-Daten" beschrieben, greift die Aktion "Auftrag erfassen" auf genau diese Datenfelder zu.

#### "Sollten alle Aktions-Daten zusätzlich auch in den Prozess-Daten gespeichert werden?"

Nein, grundsätzlich müssen nicht alle Aktions-Daten auch in den Prozess-Daten gespeichert werden. Wenn Sie jedoch die
Daten einer Aktion auch in anderen Aktionen nutzen möchten, ist die Speicherung in den Prozess-Daten notwendig. Zum
Bespiel möchten Sie die eingegebene Kundennummer beim "Montage"-Prozess in der Aktion "Auftrag erfassen"
in der späteren Aktion "Auftrag ändern" vorladen, weshalb dieser Datensatz in den Prozess-Daten erforderlich ist.

In dem Fall, dass die Aktions-Daten jedoch ausschließlich für die Verarbeitung der Aktion genutzt werden, z.B. bei einem
E-Mail Versand, ist die zusätzliche Speicherung in den Prozess-Daten nicht erforderlich.

<a name="bulk-create-action-data"></a>

## Mehrfach-Anlage Aktions-Daten

Neben dem "+" Knopf befindet sich der Knopf **(1)** für die Mehrfachanlage von Aktions-Daten. Hier können mehrere
Aktions-Daten gleichzeitig angelegt werden.

![Aktionen](/img/docs/de/rules-actions/bulk-create-action-data-button.png)

Über den Knopf öffnet sich ein Text-Editor, in dem die Aktions-Daten mithilfe einer spezifischen Syntax angelegt werden können.
Pro **Zeile** kann hier **ein** Aktions-Datenfeld angelegt werden. 

**Format**: `<Typ(|~|=)><Name><Pflichtfeld(|!)>;<?Standard-Wert>;<?In Prozess-Daten aufnehmen(|0|1)>;<?In Vorlade-Daten 
aufnehmen(|0|1)>;<?Prozess-Datenfeld vorladen(|0|1)>;<?Formular-Feld erzeugen(|0|1)>`  
Werte mit führendem Fragezeichen sind optional. Möchte man einen Wert weglassen, wird nur ein **;** Semicolon angegeben.

- Typ (optional)
    - Der Typ des Datenfeldes wird mit einem Prefix angegeben.
        - Keine Angabe → Einfach
        - = → JSON-Array
        - ~ → JSON-Objekt
- Name des Datenfeldes
    - Kleingeschrieben, nur "a-z", "0-9" und "_".
- Pflichtfeld (Optional)
    - Keine Angabe → Kein Pflichfeld
    - ! → Pflichfeld
- Standard-Wert (optional)
    - Derzeit kann **nur** beim Typ "Einfach" ein Standard-Wert gesetzt werden.
- In Prozess-Daten aufnehmen
    - Boolean (optional)
        - keine Angabe → Nein
        - 0 → Nein
        - 1 → Ja
- In Vorlade-Daten aufnehmen
    - Boolean (optional)
        - keine Angabe → Nein
        - 0 → Nein
        - 1 → Ja
- Prozess-Datenfeld vorladen
    - Boolean (optional)
        - keine Angabe → Nein
        - 0 → Nein
        - 1 → Ja
- Formular-Feld erzeugen
    - Boolean (optional)
        - keine Angabe → Nein
        - 0 → Nein
        - 1 → Ja

<a name="bulk-create-action-data-example"></a>

## Beispiele für Mehrfach-Anlage von Aktions-Datenfeldern

- **text_1**  
  Datenfeld `text_1` mit einer leeren Zeichenkette als Standard-Wert.

- **text_1!**  
  Pflichtfeld `text_1` mit einer leeren Zeichenkette als Standard-Wert.

- **text_2;null**  
  Datenfeld `text_2` ohne Standard-Wert.

- **text_3;Hello World**  
  Datenfeld `text_3` mit `"Hello World"` als Standard-Wert.

- **text_4!;Hello World**  
  Pflichtfeld `text_4` mit `"Hello World"` als Standard-Wert.

- **text_1;;1**  
  Datenfeld `text_1` mit gleichnamigem Prozess-Datenfeld.

- **text_1;;;1**  
  Datenfeld `text_1` mit gleichnamigem Aktions-Vorlade-Datenfeld.

- **text_1;;;1;1**  
  Datenfeld `text_1` mit gleichnamigem Aktions-Vorlade-Datenfeld und Prozess-Datenfeld als Wert.

- **text_1;;;;;1**  
  Datenfeld `text_1` mit gleichnamigem Formular-Feld.

- **=liste_1**  
  JSON-Array Datenfeld `liste_1` mit einer leeren JSON-Array als Standard-Wert.

- **~objekt_1**  
  JSON-Objekt Datenfeld `objekt_1` mit einem leeren JSON-Objekt als Standard-Wert.

***

[Nächster Artikel: Regeln & Daten ➜ Status](/{{route}}/{{version}}/rules-status)
