# Konfiguration ➜ Aktions-Prozessoren

---

- [Einleitung](#introduction)
- [Verfügbare Prozessoren](#available-processors)
- [Pflichtausführung](#required-execution)
- [Ausführungskonditionen](#execution-conditions)
- [Zweck der Demo-Umgebungen für Prozessoren](#environments)

<a name="introduction"></a>

## Einleitung

Aktions-Prozessoren sind Zusatzfunktionen von Aktionen. Ein Prozessor ermöglicht einer Aktion, neben dem Speichern von
Daten zum Beispiel eine E-Mail zu versenden, ein PDF-Dokument zu generieren oder Zugriff auf die Prozess-Instanz zu
erteilen.

Prozessoren sind Teil einer Aktion und werden bei der Aktionsausführung in einer fest definierten Reihenfolge
verarbeitet. Dies hat den Vorteil, dass beispielsweise ein Dokument, welches mit dem "Dokument erzeugen"-Prozessor
erstellt wurde, beim "E-Mail versenden"-Prozessor als E-Mail Anhang genutzt werden kann, weil dieser Prozessor nach
dem "Dokument erzeugen"-Prozessor verarbeitet wird.

Der untenstehende Screenshot zeigt die Prozessoren der Aktion "Auftrag erfassen" des "Montage" Case-Study Prozesses. Mit
dem Prozessor "Prozess-Metadaten ändern" kann der Prozess-Instanz Namen geändert werden.

![Konfiguration](/img/docs/de/action-processors/config-processors-overview.png)

Ein neuer Prozessor wird hinzugefügt, indem unter "Prozessoren" der Button "Prozessor anlegen" gewählt wird.

<a name="available-processors"></a>

## Verfügbare Prozessoren

Folgende Prozessoren können genutzt werden:

- [Prozess-Metadaten aktualisieren](#update-process-meta)
- [Prozess-Instanz erstellen](#create-process)
- [Prozess-Instanz löschen](#delete-process)
- [Zugriff erteilen](#create-access)
- [Zugriff entziehen](#delete-access)
- [Verknüpfung erstellen/aktualisieren](#create-relation)
- [Verknüpfung löschen](#delete-relation)
- [Aktion ausführen](#execute-action)
- [Eigene Logik ausführen](#execute-custom-logic)
- [Dokument erzeugen](#create-document)
- [Artefakt kopieren](#copy-artifact)
- [Benachrichtigung versenden](#send-push-message)
- [E-Mail versenden](#send-email)
- [Flash-Nachricht anzeigen](#display-flash-message)
- [Connector-Anfrage ausführen](#trigger-connector)
- [Event auslösen](#trigger-event)
- [Aufgabe ausführen](#trigger-task)
- [Aktion markieren](#tag-action)
- [Weiterleitung](#redirect)
- [E-Rechnungserstellung](#einvoice)

<a name="update-process-meta"></a>

## Prozess-Metadaten aktualisieren

Mit dem "Prozess-Metadaten aktualisieren"-Prozessor können die Prozess-Metadaten Name, Beschreibung, Referenz, Tags und
Icon aktualisiert werden. Der Prozessor ist in der Praxis besonders hilfreich, wenn Aktions-Daten für die
Prozess-Metadaten genutzt werden sollen.

![Konfiguration](/img/docs/de/action-processors/config-processor-update-process-meta-config.png)

Für Name, Beschreibung und Referenz, können zwei Werte mit einem Trennzeichen konkateniert werden **(1, 2, 3)**. In dem
obenstehenden Screenshot werden für den Prozess-Namen die Aktions-Datensätze "kunden_nr" und "getriebe_typ" mit einem
Bindestrich konkateniert. Unter Referenz **(3)**, Icon **(4)** und Tags **(5)** kann ebenfalls entweder ein statischer
Wert gewählt werden oder ein Aktions/Prozess-Datensatz.

> {info.fa-info-circle} Referenznamen von Prozess-Instanzen müssen plattformweit einzigartig sein.

<a name="create-process"></a>

## Prozess-Instanz erstellen

Der Prozessor ermöglicht es, mit der Aktionsausführung eine neue Prozess-Instanz zu erzeugen. Beispielsweise können die
Aktions-Daten genutzt werden, um eine neue Prozess-Instanz zu erzeugen.

![Konfiguration](/img/docs/de/action-processors/config-processor-create-process-config.png)

In der Prozessor-Konfiguration muss zunächst der Prozesstyp und die Version gewählt werden, von dem eine neue
Prozess-Instanz erstellt werden soll **(1)**. In den folgenden Optionen werden
die Prozess-Metadaten definiert. Dabei kann stets entweder ein Syntax-Wert oder direkt ein Wert gewählt werden
(siehe "+"-Icon). Anschließend kann ein Icon für die neue Prozessinstanz gewählt werden **(2)**.
Unter "Ausgabe" **(3)** kann ein Datenfeld gewählt werden, auf das die Model-Pipe-Notationen der neu erstellen
Prozess-Instanzen geschrieben wird.

#### Mehrfachausführung

Der Prozessor ermöglicht es, eine dynamische Anzahl an Prozess-Instanzen zu erzeugen. In der Praxis wird dies häufig in
Kombination mit einem Multi-Set bei einem Web-Formular genutzt. Dort möchte man zum Beispiel, dass für jede Felder-Zeile
eine Instanz mit den jeweiligen Zeilen-Werten erzeugt wird.

Bei der Option "Mehrfachausführung" muss ein **Aktions-Datenfeld vom Typ "JSON Array"** gewählt werden. Die Anzahl der
Werte in dem Aktions-Datenfeld bei der Aktionsausführung legt fest, wie viele Prozess-Instanzen erzeugt werden.

Für die Options-Werte (Name, Beschreibung, Daten-Mapping,...) müssen ebenfalls Aktions-Daten angegeben werden, die vom
Typ "JSON Array" sind. Der Prozessor nimmt dann für jede Prozess-Instanz den jeweiligen Wert aus den JSON Arrays.

<a name="delete-process"></a>

## Prozess-Instanz löschen

Mit dem Prozessor "Prozess-Instanz löschen" können sowohl andere Prozess-Instanzen als auch die ausführende
Prozess-Instanz gelöscht werden. Die ausführende Prozess-Instanz ist die Prozess-Instanz in der die Aktion ausgeführt
wird, welche den Prozessor hat.

![Konfiguration](/img/docs/de/action-processors/config-processor-delete-process-config.png)

Der Prozessor besitzt zwei Optionen: "Prozess" und "Verknüpfte Prozesse löschen". Unter "Prozess" wird der Prozess
gewählt, der gelöscht werden soll. Folgende Werte sind möglich:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Prozess Model-Pipe-Notation entspricht, z.B.
  "process|23ad6afb-f4a0-43f8-b411-5c89da502793".
- Ein Verknüpfungstyp. Es werden dann alle verknüpfte Prozesse-Instanzen von dem Typ gelöscht.

Mit der Option "Verknüpfte Prozesse löschen" können zusätzlich noch die Prozess-Instanzen gelöscht werden, die mit dem
unter "Prozess" angegebenen Prozess-Instanzen mit dem Verknüpfungstyp verknüpft sind.

> {info.fa-info-circle} Die Person benötigt das Recht, Prozess-Instanzen löschen zu dürfen.

<a name="create-access"></a>

## Zugriff erteilen

![Konfiguration](/img/docs/de/action-processors/config-processor-create-access-config.png)

Mit dem "Zugriff erteilen"-Prozessor werden Personen und Gruppen Zugriff auf die ausführende Prozess-Instanz
erteilt. Personen und Gruppen benötigen Zugriff auf eine Prozess-Instanz um auf Aktionen und
Daten zugreifen zu können.

Statt bestimmten Personen oder Gruppen Zugriff zu erteilen, kann auch eine öffentliche Rolle hinzugefügt werden.
Alle Benutzer der Plattform erhalten dann Zugriff auf die Prozess-Instanz in dieser Rolle **(1)**.

Unter Benutzer/Gruppen **(2)** werden die Personen und/oder Gruppen gewählt, denen Zugriff erteilt werden
soll. Foldende Werte sind möglich:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Prozess Model-Pipe-Notation entspricht, z.B.
  "process|23ad6afb-f4a0-43f8-b411-5c89da502793". Die Prozess-Instanz muss die Prozess-Identität einer Person
  sein.
- E-Mail Adresse einer Person.
- Gruppe Model-Pipe-Notation, z.B. "group|finance" bei dem "finance" die Identifikation oder Alias einer Gruppe ist.

Unter Rolle **(3)** wird die Prozess-Rolle gewählt, mit der die Personen und Gruppen Zugriff auf die
Prozess-Instanz erhalten.

<a name="delete-access"></a>

## Zugriff entziehen

![Konfiguration](/img/docs/de/action-processors/config-processor-delete-access-config.png)

Mit dem "Zugriff entziehen"-Prozessor wird Personen und Gruppen der Zugriff auf die ausführende
Prozess-Instanz entzogen.

Statt bestimmten Personen oder Gruppen den Zugriff zu entziehen, kann auch eine öffentliche Rolle entfernt werden **(1)
**.

Unter "Benutzer/Gruppen/Rollen" **(2)** werden die Personen, Gruppen und/oder Rollen gewählt, denen der Zugriff entzogen
werden soll. Wird eine Rolle gewählt, werden alle Personen und Gruppen identifiziert, welche diese Rolle zum Zeitpunkt
der Aktionsausführung einnehmen.

Foldende Werte sind möglich sind für:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Prozess Model-Pipe-Notation entspricht, z.B.
  "process|23ad6afb-f4a0-43f8-b411-5c89da502793". Die Prozess-Instanz muss die Prozess-Identität einer Person
  sein.
- E-Mail Adresse einer Person.
- Gruppe Model-Pipe-Notation, z.B. "group|finance" bei dem "finance" der Alias einer Gruppe ist.
- Prozess-Rolle Model-Pipe-Notation.

Unter "Rollen-Filterung" **(3)** kann eine Prozess-Rolle angegeben werden. Dadurch kann ein bestimmter Rollen-Zugriff
einer Person
entfernt werden. Wird keine Rollen-Filterung angegeben, werden die Zugriffe aller Rollen der Person oder Gruppe
entfernt.

> {info.fa-info-circle} Es können nur öffentliche Rollen entfernt werden, die mit dem "Zugriff erteilen"-Prozessor
> hinzugefügt worden sind.
> Die optionale, öffentliche Rolle des Prozesses kann nicht entfernt werden.

<a name="create-relation"></a>

## Verknüpfung erstellen/aktualisieren

Mit dem "Verknüpfung erstellen/aktualisieren"-Prozessor werden Prozess-Prozess Verknüpfungen erstellt. Eine
Prozess-Instanz kann beliebig viele verknüpfte Prozesse besitzen. Prozess-Verknüpfungen untersützen dabei, Prozesse
fachlich zu organisieren und auf die Daten von verknüpften Prozessen zuzugreifen.

![Konfiguration](/img/docs/de/action-processors/config-processor-create-relation-config.png)

Bei "Prozess" **(1)** wird die Prozess-Instanz gewählt, die verknüpft werden soll. Folgende Werte sind möglich:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Prozess Model-Pipe-Notation entspricht, z.B.
  "process|23ad6afb-f4a0-43f8-b411-5c89da502793".
- E-Mail Adresse einer Person. Es wird die Prozess-Identität der Person verknüpft.
- Das Ergebnis eines "Prozess-Instanz erstellen"-Prozessors. Es wird die erzeugte Prozess-Instanz verknüpft.

Unter "Verknüpfungstyp" **(3)** wird der Verknüpfungstyp gewählt, mit der die Prozess-Instanz verknüpft werden soll. Mit
der Option "Verknüpfungstyp vom Ziel-Prozess wählen" **(2)** wird definiert, ob der Verknüpfungstyp vom Prozesstyp der
ausführenden Prozess-Instanz stammt oder vom Prozesstyp der zu verknüpftenden Prozess-Instanz. In der Praxis ist dies
hilfreich, um die Arten der Verknüpfungen (1-n, 1-1, n-1) einzuhalten (siehe Artikel Verknüpfungstypen).

Unter "Verknüpfungsdaten" **(4)** können Daten zu der Verknüpfung gespeichert werden. Vorraussetzung ist, dass der
Verknüpfungstyp Datenfelder konfiguriert hat.

Falls "Prozess" leer gelassen wird, werden bestehende Prozess-Verknüpfungen aktualisiert. In der Praxis wird dies
genutzt, wenn lediglich die Verknüpfungsdaten aktualisiert werden sollen.

#### Mehrfachausführung

Der Prozessor ermöglicht es, eine dynamische Anzahl an Verknüpfungen zu erzeugen. In der Praxis wird dies häufig in
Kombination mit einer Listen-Komponente genutzt. Die Prozess Model-Pipe-Notationen der Listen-Komponente
(z.B. "process|dfe9f7ca-c511-43b0-aff2-81e96892d05e") werden auf ein Aktions-Datenfeld gemappt. Dieses Aktions-Datenfeld
wird bei "Prozess"-Option angegeben, sodass alle Prozess-Instanzen der Liste verknüpft werden.

Bei der Option "Mehrfachausführung" muss ein **Aktions-Datenfeld vom Typ "JSON Array"** gewählt werden. Die Anzahl der
Werte in dem Aktions-Datenfeld bei der Aktionsausführung legt fest, wie viele Verknüpfungen erzeugt werden.

Für die Verknüpfungsdaten-Werte müssen ebenfalls Aktions-Daten angegeben werden, die vom Typ "JSON Array" sind. Der
Prozessor nimmt dann für jede Verknüpfung den jeweiligen Wert aus den JSON Arrays.

<a name="delete-relation"></a>

## Verknüpfung löschen

Der "Verknüpfung löschen" ermöglicht es, Verknüpfungen zu anderen Prozess-Instanzen zu entfernen.

![Konfiguration](/img/docs/de/action-processors/config-processor-delete-relation-config.png)

Unter "Prozess" **(1)** werden die Prozess-Instanzen angegeben, dessen Verknüpfungen entfernt werden sollen. Folgende
Werte sind möglich:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Prozess Model-Pipe-Notation entspricht, z.B.
  "process|23ad6afb-f4a0-43f8-b411-5c89da502793".
- E-Mail Adresse einer Person. Die Prozess-Identität der Person wird gewählt.
  wird gewählt.

Wird die Option nicht befüllt, werden alle Verknüpfungen zu Prozess-Instanzen des ausgewählten Verknüpfungstyps
entfernt.

Bei der "Verknüpfungstyp"-Option **(2)** kann die obige Prozess-Auswahl nach einem Verknüpfungstyp gefiltert werden.
Wird die Option leer gelassen, werden alle Verknüpfungen der Prozess-Auswahl entfernt.

<a name="execute-action"></a>

## Aktion ausführen

Mit dem "Aktion ausführen"-Prozessor kann eine Aktion in einer anderen Prozess-Instanz ausgeführt werden.

![Konfiguration](/img/docs/de/action-processors/config-processor-execute-action-config.png)

Bei der "Prozess"-Option **(1)** wird die Prozess-Instanz gewählt, in der eine Aktion ausgeführt werden soll. Folgende
Werte sind möglich:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Prozess Model-Pipe-Notation entspricht, z.B.
  "process|23ad6afb-f4a0-43f8-b411-5c89da502793".
- E-Mail Adresse einer Person. Die Prozess-Identität der Person wird gewählt.
  wird gewählt.

Die "Prozesstyp"-Option **(2)** wird der Prozesstyp und die Version der oben gewählten Prozess-Instanz gewählt. Unter
"Aktionstyp" **(3)** wird die Aktion gewählt, die in der Prozess-Instanz ausgeführt werden soll. Beim
"Daten-Mapping" **(4)** können Werte hinzugefügt werden, mit denen die Aktion ausgeführt werden soll. Wird eine Datei im
Daten-Mapping übergeben, wird diese in den neuen Prozess kopiert.

#### Mehrfachausführung

Der Prozessor ermöglicht es, für eine dynamische Anzahl an Prozess-Instanzen eine Aktion auszuführen. In der Praxis wird
dies häufig in Kombination mit einer Listen-Komponente genutzt. Die Prozess Model-Pipe-Notationen der Listen-Komponente
(z.B. "process|dfe9f7ca-c511-43b0-aff2-81e96892d05e") werden auf ein Aktions-Datenfeld gemappt. Dieses Aktions-Datenfeld
wird bei "Prozess"-Option angegeben, sodass für alle Prozess-Instanzen die definierte Aktion ausgeführt wird.

Bei der Option "Mehrfachausführung" muss ein **Aktions-Datenfeld vom Typ "JSON Array"** gewählt werden. Die Anzahl der
Werte in dem Aktions-Datenfeld bei der Aktionsausführung legt fest, wie viele Aktionen ausgeführt werden.

Für die Daten-Mapping-Werte müssen ebenfalls Aktions-Daten angegeben werden, die vom Typ "JSON Array" sind. Der
Prozessor nimmt dann für jede Aktionsausführung den jeweiligen Wert aus den JSON Arrays.

<a name="execute-custom-logic"></a>

## Eigene Logik ausführen

Mit dem "Eigene Logik ausführen"-Prozessor können **komplexe Berechnungen mit Aktions- und Prozess-Daten durchgeführt
werden** und das Ergebnis in Aktions- und Prozess-Daten gespeichert werden. Dadurch ist es möglich, individuelle Logik
auszusteuern. In der Praxis ist der Prozessor hilfreich, wenn sensible Datenberechnungen durchgeführt werden sollen und
berechnete Werte clientseitig nicht einsehbar sein dürfen. Des Weiteren ist der Prozessor hilfreich, wenn
Datenberechnungen sowohl mittels einer clientseitigen Web-Formular Dateneingabe als auch mittels einer REST-API Anfrage
durchgeführt werden sollen.

![Konfiguration](/img/docs/de/action-processors/config-processor-execute-custom-logic-config.png)

Der Prozessor benötigt lediglich eine "Eigene Logik"-Vorlage. Informationen zur Nutzung einer "Eigene Logik"-Vorlage
befinden sind im "Vorlagen"-Artikel.

> {info.fa-info-circle} Der Prozessor wird vor der Aktions- und Prozess-Daten Speicherung und der Anwendung der
> Statusregeln durchgeführt,
> sodass nachfolgende Prozessoren die bereits aktualisierten Aktions- und Prozess-Daten nutzen.


<a name="create-document"></a>

## Dokument erzeugen

Mit dem "Dokument erzeugen" können PDF-Dokumente anhand von HTML-Vorlagen erzeugt werden. Erzeugte PDF-Dokumente können
dann zum Beispiel bei einem E-Mail Versand als Anhang genutzt werden.

![Konfiguration](/img/docs/de/action-processors/config-processor-create-document-config.png)

Mit der "Dokument-Name"-Option **(1)** wird der Ausgabename des Dokuments definiert. Dort kann sowohl direkt eine
Eingabe getätigt werden als auch Syntax-Werte mit dem "+"-Icon genutzt werden. Mit der "HTML-Vorlage"-Option **(2)**
wird die Vorlage gewählt, die für die Dokument-Erzeugung genutzt werden soll. Die "Ausgabe"-Option **(3)** kann optional
genutzt werden, um die Model-Pipe-Notation (z.B. "artifact|0afd4df4-a026-42ea-883b-8325a7c261d2") auf ein
Aktions-Datenfeld zu schreiben.

Informationen zur Nutzung von HTML-Vorlagen befinden sich im "Vorlagen"-Artikel.

<a name="copy-artifact"></a>

## Artefakt kopieren

Der "Artefakt kopieren"-Prozessor kann ein Prozess-Artefakt anhand einer Artefakt Model-Pipe-Notation (z.B.
"artifact|05eae49b-a25f-42b3-9cac-925c34425afd") kopiert werden. Ein Artefakt ist ein Dokument, welches im
Prozess-Verlauf erzeugt wurde, zum Beispiel ein hochgeladenes oder erzeugtes Dokument. In der Praxis ist der Prozessor
hilfreich um ein Dokument aus einer anderen Prozess-Instanz in die ausführende Prozess-Instanz zu kopieren.

![Konfiguration](/img/docs/de/action-processors/config-processor-copy-artifact-config.png)

Unter "Artefakt" **(1)** wird der Aktions- oder Prozess-Datensatz gewählt, welcher als Wert eine Artefakt
Model-Pipe-Notation hat. Bei der "Ausgabe"-Option wird ein Aktions-Datenfeld angegeben, auf das die Artefakt
Model-Pipe-Notation von dem kopierten Artefakt geschrieben wird.

<a name="send-push-message"></a>

## Benachrichtigung versenden

Der "Benachrichtigung versenden"-Prozessor kann genutzt werden, um Benachrichtigungen an Personen, Gruppen oder
Prozess-Rollen zu versenden. In der Allisa Plattform werden Benachrichtigungen beim Glocken-Icon angezeigt.

![Konfiguration](/img/docs/de/action-processors/config-processor-send-push-message-example-1.png)

Eine Benachrichtigung hat einen Typ, eine Nachricht und einen Link. Es bestehen die Typen "Information",
"Erfolgsmeldung", "Warnung" und "Wichtiger Hinweis". Je nach Typ wird ein anderes Icon angezeigt.

![Konfiguration](/img/docs/de/action-processors/config-processor-send-push-message-example.png)

Der obenstehende Screenshot zeigt zwei Benachrichtigungen vom Typ "Information" und eine Benachrichtigung vom Typ
"Erfolgsmeldung".

![Konfiguration](/img/docs/de/action-processors/config-processor-send-push-message-config.png)

Mit der "Typ"-Option wird der Typ festgelegt **(1)**. Als Empfänger **(2)** können Personen, Gruppen oder
Prozess-Rollen gewählt werden. Bei einer Gruppe wird die Benachrichtigung an alle Gruppenmitglieder versendete. Bei
einer Rolle wird die Benachrichtigung an alle Personen oder Gruppen versendet, welche diese Rolle in der
Prozess-Instanz einnehmen. Bei der "Nachricht"-Option **(3)** kann sowohl ein statischer Text als auch Syntax-Werte
genutzt werden. Mit "Button-Label" **(4)** wird der Text auf dem Link festgelegt. Für "Button-Url" **(5)** kann zwischen
dem Link zur ausgeführten Aktion und dem Link zum ausführenden Prozess gewählt werden.

<a name="send-email"></a>

## E-Mail versenden

Der Prozessor ermöglicht es, E-Mails an Personen, Gruppen oder Prozess-Rollen zu versenden. Bei einer Gruppe
wird die E-Mail an alle Gruppen-Mitglieder versendet. Bei einer Rolle wird die E-Mail an alle Personen oder
Gruppen versendet, welche diese Rolle in der Prozess-Instanz einnehmen.

![Konfiguration](/img/docs/de/action-processors/config-processor-send-email-config.png)

Unter "Empfänger" **(1)** können sowohl die standardmäßigen Empfänger als auch CC und BCC angegeben werden. Folgende
Werte sind erlaubt:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Prozess-Identität Model-Pipe-Notation entspricht, z.B.
  "process|23ad6afb-f4a0-43f8-b411-5c89da502793". Die E-Mail wird an die Person der Prozess-Identität
  versendet.
- Benutzer Model-Pipe-Notation, z.B. "user|8b842284-677d-4e21-ae8c-bec868412022" oder "user|teamlead", wobei "teamlead"
  der Alias einer Person ist.
- Gruppen Model-Pipe-Notation, z.B "group|8eaf958b-56e2-4f92-ba9e-5436dfad7090" oder "group|finance", wobei "finance"
  der Alias einer Gruppe ist.
- Prozess-Rolle

Der Betreff **(2)** erlaubt sowohl normalen Text als auch die Nutzung von Syntax-Variablen ("+"-Dropdown) und der
Model-Pipe-Notation. Die HTML-Vorlage **(3)** legt die Vorlage für die E-Mail fest. Informationen zur Nutzung einer
HTML-Vorlage befinden sind im "Vorlagen"-Artikel. Bei der "Anhänge"-Option **(4)** können Anhange zur E-Mail hinzugefügt
werden. Folgende Werte sind möglich:

- Aktions-Datenfeld/Prozess-Datenfeld bei dem der Wert einer Artefakt Model-Pipe-Notation entspricht, z.B.
  "artifact|23ad6afb-f4a0-43f8-b411-5c89da502793".
- Aktions-Datenfeld welches für einen Datei-Upload genutzt wird.
- Prozessor-Ergebnis von dem "Dokument erstellen"-Prozessor.
- Ergebnis des "Konnektor-Anfrage ausführen"-Prozessors, sofern dieses ein Dokument ist.
- Variable Model-Pipe-Notation, z.B. "variable|privacy_policy", wobei "privacy_policy" der Name einer Variable ist.

<a name="display-flash-message"></a>

## Flash-Nachricht anzeigen

Mit dem "Flash-Nachricht anzeigen" können kurzzeitige Nachrichten am oberen Rand der Allisa Plattform angezeigt werden.
Diese Flash-Nachricht verschwindet automatisch, wenn die Person die Seite wechselt. In der Praxis ist der
Prozessor hilfreich, um der Person eine individuelle Rückmeldung zur ausgeführten Aktion zu geben.

![Konfiguration](/img/docs/de/action-processors/config-processor-display-flash-message-example.png)

Der obenstehende Screenshot zeigt den "Budget-Freigabe" Case-Study Prozess. Die Initial-Aktion "Anlegen" zeigt eine
Flash-Nachricht an, wenn das angefragte Budget niedriger als €1.000 ist.

![Konfiguration](/img/docs/de/action-processors/config-processor-display-flash-message-config.png)

Es kann zwischen vier Anzeigetypen gewählt werden: "Information", "Erfolgsmeldung", "Warnung" und "Wichtiger
Hinweis" **(1)**. Die Nachricht **(2)** erlaubt sowohl normalen Text als auch die Nutzung von Syntax-Variablen ("+"
-Dropdown). Optional kann die Flash-Nachricht einen Button mit einem Link besitzen **(3, 4)**.

> {info.fa-info-circle} Eine Flash-Nachricht vom Prozessor ersetzt die standardmäßige "Die Aktion...wurde ausgeführt"
> -Flash-Nachricht einer Aktion.

<a name="trigger-connector"></a>

## Connector-Anfrage ausführen

Mit dem "Connector-Anfrage ausführen"-Prozessor können Anfragen an externe Schnittstellen bei der Aktionsausführung
durchgeführt werden. Dadurch können die auf der Allisa Plattform hinterlegten Connectoren genutzt werden.

![Konfiguration](/img/docs/de/action-processors/config-processor-trigger-connector-request-config.png)

Im obenstehenden Screenshot wird eine Connector-Anfrage genutzt um das Wetter für eine Stadt abzufragen und die
Temperatur in den Aktions-Daten zu speichern. Dafür wurde ein "rest_api"-Connector angelegt und für den Connector eine
"get_weather"-Anfrage. Der Connector und die dazugehörige Anfrage wird bei "Connector" **(1)** und "Request" **(2)**
gewählt. Beim "Hilfe-Text" kann ein Dokumentationshinweis eingegeben werden. Beim "Anfrage-Mapping" **(4)** können Werte
aus der Aktion auf Anfrage-Parameter gemappt werden. In diesem Beispiel wird der Stadtname übergeben, von dem das Wetter
abgefragt werden soll.

Beim HTTP-Connector kann beim "Antwort-Mapping" **(5)** können Werte aus der Anfrage-Antwort auf Aktions-Daten
geschrieben werden. Mit
der `[[...]]`-Syntax wird auf die "connector_response" zugegriffen, wobei `[[connector_response.body]]` die gesamte
Anfrage-Antwort-Body als JSON-Objekt repräsentiert. Mit `[[connector_response.headers]]` kann auf Header-Werte der
Anfrage-Antwort zugegriffen werden,
so gibt beispielsweise `[[connector_response.headers.Content-Type]]` den Wert des Content-Type Headers zurück.

```json
{
  "city": "Berlin",
  "temperature": 28,
  "temperature_format": "Celcius"
}
```

Die Eingabe `[[connector_response.body.temperature]]` gibt im obigen Beispiel "28" zurück.

<a name="trigger-event"></a>

## Event auslösen

Der "Event auslösen"-Prozessor ermöglicht es, ein Prozess-Event plattform-weit auszulösen. Prozess-Events können genutzt
werden um anderen Prozess-Instanzen über erreichte Meilensteine im Prozessverlauf zu informieren. Andere
Prozess-Instanzen können mithilfe von "Listenern" auf diese Events reagieren.

![Konfiguration](/img/docs/de/action-processors/config-processor-trigger-event-config.png)

Unter "Event" wird das Event gewählt, welches die Prozess-Instanz an die Allisa Plattform sendet.

<a name="trigger-task"></a>

## Aufgabe ausführen

Der "Aufgabe ausführen"-Prozessor ermöglicht es, eine Aufgabe manuell über eine Aktion zu starten. Mit Aufgaben können
automatisierte und terminierte
Massenverarbeitungen implementiert werden. Aufgaben werden Bots zugewiesen und können beispielsweise Prozess-Aktionen
ausführen oder
Prozess-Instanzen löschen. Die Prozessor-Ausführung startet die Aufgabe unabhängig vom eventuell konfigurierten
Aufgaben-Zeitplan.
Aufgaben müssen zunächst in einer Demo-Umgebung hinzugefügt werden um diese beim Prozessor nutzen zu können.

![Konfiguration](/img/docs/de/action-processors/config-processor-trigger-task-config.png)

Unter "Aufabe" wird die auszuführende Aufgabe gewählt. Mit der Checkbox "Aufgabe mit angemeldeten Benutzer ausführen?"
wird festgelegt,
ob der angemeldete Benutzer (also die Person, welche die Aktion ausführt) für die Aufgaben-Ausführung genutzt werden
soll oder der Bot, dem
die Aufgabe gehört. Bei Parameter können Daten definiert werden, die an die Aufgaben-Ausführung übergeben werden. Werden
beispielsweise Aktionen
ausgeführt, können somit Daten an die Aktionen übergeben werden.

> {info.fa-info-circle} Stellen Sie sicher, dass die ausführende Person/Bot der Aufgabe alle notwendigen Rechte besitzt.

<a name="tag-action"></a>

## Aktion markieren

Der "Aktion markieren"-Prozessor ermöglicht es, auf die Daten einer zuvor ausgeführten Aktion zuzugreifen.

![Konfiguration](/img/docs/de/action-processors/config-processor-tag-action-config.png)

Unter "Tag" wird ein belieber Tagname gewählt, welcher die ausführende Aktion markiert. In nachfolgenden Aktionen kann
dann auf diese Aktion
mittels eines Syntax-Wertes zugegriffen werden. Wiederholende Ausführungen des Prozessors mit demselbem Tagnamen
aktualisieren die markierte Aktion.
Die verfügbaren Aktions-Daten der markierten Aktion werden im Syntax-Wert Menü unter "Prozess-Aktionen" aufgeführt.

![Konfiguration](/img/docs/de/action-processors/config-processor-tag-action-config-2.png)

Beispielsweise kann das Ausführungsdatum mit dem Syntax-Wert "Prozess-Aktion - Erstelldatum" bei einem Vorlade-Datenfeld
geladen werden.
Mit dem "Bearbeiten"-Icon (Am Ende der Zeile) kann der Wert formatiert werden. Beim Erstelldatum kann das
Datums-Format angepasst werden.

![Konfiguration](/img/docs/de/action-processors/config-processor-tag-action-config-3.png)

> {info.fa-info-circle} Verfügbare Datumsformat-Optionen finden
> Sie [hier](https://www.php.net/manual/en/datetime.format.php).
> Standardgemäß wird folgendes Format gewählt: 'd.m.Y H:i:s', zum Beispiel: 31.01.2020 13:37:20.

Aktions-Daten der markierten Aktion können mit dem Syntax-Wert "Prozess-Aktion - Datenfeld - DATENFELD_NAME" geladen
werden. Dort muss das "DATENFELD_NAME" mit dem tatsächlichen Aktions-Datenfeld ersetzt werden.

![Konfiguration](/img/docs/de/action-processors/config-processor-tag-action-config-4.png)

Bei einem JSON-Objekt oder JSON-Array kann ein Wert extrahiert werden. Dafür muss das "extract" Argument beim
Syntax-Wert mit einem Wert der "Dot-Notation" angegeben werden.

Beispiel Aktions-DatenWert "data_field_1":

``` json
    {
      "id": "123",
      "persons": [
          {
            "name": "Thomas",
            "age": 32
          },
          {
            "name": "Julia",
            "age": 35
          }
        ]
    }
```

Mit dem Syntax-Wert ``[[process.actions.tag_1.outputs.data_field_1(extract=persons.1.name)]]`` wird ``Julia`` geladen.

<a name="redirect"></a>

## Weiterleitung

Der "Weiterleitung"-Prozessor kann genutzt werden, um nach der Aktionsausführung zu einer indivuellen URL weitergeleitet
zu werden.

![Konfiguration](/img/docs/de/action-processors/config-processor-redirect-config.png)

Unter "URL" wird die URL erfasst, zu der nach der Aktionsausführung weitergeleitet werden soll.

***

<a name="required-execution"></a>

## Pflichtausführung

Bei den Prozessor-Optionen kann im oberen Bereich die Pflichtausführung de-/aktiviert werden.

![Konfiguration](/img/docs/de/action-processors/config-processor-required-execution.png)

Wenn aktiviert wird die Aktionsausführung abgebrochen, wenn die erforderlichen Daten für die Prozessorausführung fehlen.
Wenn deaktiviert, wird die Prozessorausführung lediglich übersprungen. Dadurch ist es möglich Prozessoren optional
ausführen zu lassen.

<a name="execution-conditions"></a>

## Ausführungskonditionen

Für jeden Prozessor können Ausführungskonditionen definiert werden. Wenn definiert wird der Prozessor nur ausgeführt,
wenn die Konditionen erfüllt sind.

![Konfiguration](/img/docs/de/action-processors/config-processor-execution-conditions.png)

Über das "Blitz"-Icon **(1)** unten links wird das Dialogfenster für die Ausführungskonditionen geöffnet.

![Konfiguration](/img/docs/de/action-processors/config-processor-execution-conditions-modal.png)

Auf der linken Seite wird für jede Regel eine Gruppe definiert. Es gilt: Alle Regeln innerhalb einer Gruppe werden mit
UND verknüpft. Alle Gruppen untereinander werden mit ODER verknüpft. Mit den Gruppen können komplexe UND/ODER
Bedingungen erstellt werden.

[Nächster Artikel: Aktions-Kategorien](/{{route}}/{{version}}/action-categories)

<a name="environments"></a>

## Zweck der Demo-Umgebungen für Prozessoren

Für die Prozessmodellierung sind Demo-Umgebungen von großer Bedeutung. Mit Demo-Umbebungen können unterschiedliche
Szenarien für die Prozess-Demo erstellt werden, sodass der Prozess ausführlich getestet werden kann.

Für die Prozessorkonfiguration sind die Demo-Umgebungen ebenfalls wichtig. Zum Beispiel muss beim "Connector-Anfrage
ausführen"-Prozessor ein Connector und Request gewählt werden. **Connectoren sind Teil einer Allisa Plattform** und
werden auf einer Allisa Plattform erstellt und konfiguriert. Es muss daher eine Möglichkeit geben, bereits bei der
Prozessmodellierung festzulegen, dass es auf der Allisa Plattform, auf der der Prozess in Zukunft ausgeführt wird,
bestimmte Connectoren/Requests geben muss. Es werden Abhängigkeiten an Connectoren auf der Allisa Plattform
definiert. Dies wird mit den Connectoren und Requests bei einer Demo-Umgebung gemacht. Die dort erstellen Connectoren
mit ihren "Identifiern" sagen aus, dass es auf der Allisa Plattform, in der dieser Prozess zukünftig ausgeführt wird,
einen
Connector und Request mit diesem Namen gegen muss, damit der Prozess genutzt werden kann.

Demo-Umgebungen legen somit teilweise die Abhängigkeiten fest, die an die Allisa Plattform gestellt werden, auf der der
Prozess zukünftig ausgeführt wird.

<a name="einvoice"></a>
## E-Rechnungserstellung

Mit dem "E-Rechnung erstellen"-Prozessor kann eine Digitale (E) Rechnung erstellt werden. 

![Konfiguration](/img/docs/de/action-processors/config-processor-einvoice-no-selection.png)

Für E-Rechnungen gibt es verschiedene Standards. Im Prozessor stehen diese über die "Profile" Option zur Verfügung.

![Konfiguration](/img/docs/de/action-processors/config-processor-einvoice-profiles.png)

Je Profile gibt es unterschiedliche Optionen welche angegeben werden können. Jeder dieser Option bezieht sich auf ein Datenfeld innerhalb eder Rechnung.
Der Konnektor erlaubt die Eingabe naherzu aller Daten, die für das ausgewählte Format angegeben werden können.
Dies betrifft Pflichtfelder als auch Optionale Felder.

![Konfiguration](/img/docs/de/action-processors/config-processor-einvoice-xrechnung-example.png)
***

[Nächster Artikel: Konfiguration ➜ Aktions-Kategorien](/{{route}}/{{version}}/action-categories)
