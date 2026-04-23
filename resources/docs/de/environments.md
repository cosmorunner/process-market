# Konfiguration ➜ Demo-Umgebungen

---

- [Einleitung](#introduction)
- [Nutzung](#usage)
- [Beispielprozess mit einer Demo-Umgebung Konfiguration](#example)
- [Prozess-Abhängigkeiten & Vorraussetzungen an eine Allisa Plattform](#platform-requirements)
- [Demo-Umgebung anlegen](#create-environment)

<a name="introduction"></a>

## Einleitung

Mit Demo-Umgebungen können unterschiedliche Szenarien, Prozess-Abläufe und Anwendungsfälle des Prozesses
qualitätsgesichert getestet werden. Demo-Umgebungen werden beim Starten einer Prozes-Demo gewählt und
definieren **die Vorraussetzungen an die Allisa Plattform, die für die Prozess-Demo initialisiert wird**. Eine
Demo-Umgebung simuliert andere Benutzer der Allisa Plattform, abhängige Prozess-Instanzen, Gruppen, Konnektor-Requests
und vieles mehr. Ziel einer Demo-Umgebung ist es, für den Prozess eine Umgebung zu schaffen, in der der Prozess
bestmöglich **getestet und simuliert** werden kann.

<a name="usage"></a>

## Nutzung

Eine Demo-Umgebung kann **beim Starten einer Prozess-Demo** gewählt werden. Die Simulation oder Demo wird
dann mit dieser Demo-Umgebung initialisiert. Wenn die Demo-Umgebung zum Beispiel weitere Benutzer definiert hat, werden
diese Benutzer in der Allisa Plattform erstellt. Hat die Demo-Umgebung eine Initial-Aktion definiert, startet die
Prozess-Demo mit dieser Initial-Aktion.

In der Praxis ist eine Demo-Umgebung notwendig, **um bestimmte Prozessfunktionen testen zu können**. Wenn man
beispielsweise bei einer Aktion einer nutzenden Person Zugriff erteilen möchte, ist es notwendig, dass es in der Allisa
Plattform weitere nutzenden Personen gibt. Diese Personen werden bei der Demo-Umgebung definiert.

Ein weiteres Beispiel sind Prozess-Abhängigkeiten. Im Kontext eines Jobportals könnte man einen "Bewerbung"-Prozess
modellieren. Eine Bewerbung ist Teil eines "Stellenausschreibung"-Prozesses. Wenn nun der Bewerbungsprozess in einer
Demo getestet werden soll, muss es eine "Stellenausschreibung" Prozess-Instanz geben, auf der die Bewerbung basiert,
weil andernfalls der Bewerbungsprozess nicht ordnungsgemäß funktionieren würde. Die "Stellenausschreibung"
-Prozess-Instanz kann bei einer Demo-Umgebung definiert werden.

<a name="example"></a>

## Beispielprozess mit einer Demo-Umgebung Konfiguration

Als vereinfachtes Praxis-Beispiel wird ein Auftragsprozess genommen. Beim Auftragsprozess ist fachlich festgelegt, dass
dieser mit einer "Erfassen"-Aktion startet. Dort werden die auftragsspezifischen Daten erfasst. Nachdem der Auftrag
erfasst wurde, kann man die "Beteiligte hinzufügen" ausführen, mit der andere nutzende Personen der Plattform Zugriff
auf den erstellen Auftrag erhalten.

![Demo-Umgebungen](/img/docs/de/environments/environments-example.png)

Der obenstehende Screenshot zeigt das Regelwerk des vereinfachten Auftragsprozesses. Mit der "Erfassen"-Aktion wird der
Hauptstatus auf "Erfasst" gesetzt, sodass anschließend die "Beteiligte hinzufügen"-Aktion ausgeführt werden kann.

#### Aktionskonfiguration

Die Aktion "Erfassen" ist ein einfaches Web-Formular mit einem Feld "name" in dem der Auftragsname erfasst wird. Die
"Erfassen"-Aktion hat ebenfalls ein Aktions-Datenfeld "name".

![Demo-Umgebungen](/img/docs/de/environments/environments-erfassen-form.png)

Die Aktion "Beteiligte hinzufügen" hat ebenfalls ein Web-Formular mit einem Autocomplete-Feld "benutzer".

![Demo-Umgebungen](/img/docs/de/environments/environments-beteiligte-form.png)

Bei den Optionen des Autocomplete-Feldes (untenstehend) ist eine Prozess-Liste gewählt **(1)**. Für jede
Autocomplete-Option wird der Listen-Datensatz "processes_name" als Label **(2)** und "processes_pipe_notation" **(3)**.

![Demo-Umgebungen](/img/docs/de/environments/environments-beteiligte-autocomplete.png)

Die Aktion "Beteiligte hinzufügen" hat noch einen "Zugriff erteilen"-Prozessor, der die beim Autocomplete-Feld
(benutzer) nutzende Person in der Rolle "Maintainer" hinzufügt.

![Demo-Umgebungen](/img/docs/de/environments/environments-beteiligte-processor.png)

#### Demo-Umgebung Konfiguration

Im Konfigurationsbereich unter "Demo-Umgebungen" wird nun eine Demo-Umgebung angelegt, welches die Aktion "Erfassen"
**(1)** als Initial-Aktion definiert hat und drei weitere Benutzer mit ihrer Prozess-Identität anlegt **(2)**.

![Demo-Umgebungen](/img/docs/de/environments/environments-example-config.png)

Nun kann eine Demo mit dem "Demo"-Button **(3)** gestartet werden. Beim Starten einer Demo wird die soeben erfasste
Demo-Umgebung gewählt.

![Demo-Umgebungen](/img/docs/de/environments/environments-initial-action.png)

Der Prozess startet nun mit der "Erfassen"-Aktion **(1)**. Unter "Name" wird ein Auftragsname vergeben **(2)**.

![Demo-Umgebungen](/img/docs/de/environments/environments-demo-instance.png)

Nach der Aktionsausführung wird man zur Prozess-Instanz weitergeleitet. Von dort kann nun die Aktion
"Beteiligte hinzufügen" **(1)** gewählt werden. **In der Aktion beim Autocomplete-Feld kann z.B. nach "Franziska"
gesucht werden, weil bei der Demo-Umgebung weitere Benutzer hinzugefügt wurden.**

![Demo-Umgebungen](/img/docs/de/environments/environments-autocomplete-example.png)

Das obige Beispiel verdeutlicht, dass die erstellte Demo-Umgebung für den Test des Prozesses hilfreich ist, weil dort
der Prozessverlauf mit den erforderlichen Ressourcen (Benutzer, Prozesse, Initial-Aktion,...) bereits bei der
Prozessmodellierung simuliert werden kann.

<a name="platform-requirements"></a>

## Prozess-Abhängigkeiten & Vorraussetzungen an eine Allisa Plattform

Neben der Funktion der Demo-Umgebungen, den Prozessverlauf testen zu können, haben Demo-Umgebungen noch eine andere
Funktion: **Eine Demo-Umgebung definiert Vorraussetzungen an die Allisa Plattform**, in der der Prozess zukünftig
genutzt wird.

Zum Beispiel können auf einer Allisa Plattform "Variablen" erfasst werden. Eine Variable repräsentiert einen Wert, der
plattformweit von allen Prozess-Instanzen ausgelesen werden kann. Die Prozessmodellierung in der Prozessfabrik **ist
unabhängig von der Allisa Plattform, auf der der Prozess zukünftig genutzt wird**. Daher benötigt es einen Mechnismus um
bereits bei der Prozessmodellierung festzulegen, dass eine bestimmte Variable auf der Allisa Plattform vorhanden sein
muss, in der der Prozess zukünftig genutzt wird. Andernfalls würde der Prozess auf der Allisa Plattform nicht
ordnungsgemäß funktionieren, weil die Variable dort möglicherweise fehlt.

Bei einer Demo-Umgebung können Variablen, Benutzer, Gruppen, Bots und vieles mehr angelegt werden. Diese werden dann
beim Starten einer Prozess-Demo auf der Allisa Plattform erstellt. Wird eine Resource (Variable, Benuter,...)
bei der Demo-Umgebung erfasst, wird diese standardgemäß als eine Prozess-Abhängigkeit definiert.

![Demo-Umgebungen](/img/docs/de/environments/environments-dependencies.png)

Der obenstehende Screenshot zeigt die Abhängigkeiten eines Prozesses. **Prozess-Abhängigkeiten können im Tab
"Versionen" in den Prozesseinstellungen eingesehen werden ("Zahnrad-Icon" im Konfigurationsbereich).** Aufgrund dessen,
dass bei einer Demo-Umgebung des Prozesses eine Gruppe mit dem Identifier "finanzen" angelegt wurde, wurde diese Gruppe
als Abhängigkeit hinterlegt. Zusätzlich benötigt die Allisa Plattform, in der der Prozess zukünftig genutzt wird einen
Bot "gast" und einen Konnektor "wetter_api" mit einem Request "get_weather".

> {info.fa-info-circle} Ressourcen, dessen Alias oder Identifier mit "demo" beginnen, werden nicht als Prozess-Abhängigkeit definiert.

<a name="create-environment"></a>

## Demo-Umgebung anlegen

Eine neue Demo-Umgebung wird im Konfigurationsbereich unter "Demo-Umgebungen" **(1)** mit dem "Umgebung anlegen"
-Button **(2)** angelegt.

![Demo-Umgebungen](/img/docs/de/environments/environments-create.png)

Im Dialogfenster können die verschiedenen Bereiche der Demo-Umgebung konfiguriert werden.

#### Information

Die "Standard"-Option **(1)** legt fest, ob die Demo-Umgebung die Standard-Umgebung ist. Die Standard-Umgebung wird beim
Starten einer Prozess-Demo vorausgewählt und repräsentiert einen üblichen Prozessverlauf. Mit dem Namen **(2)**
und Beschreibung **(3)** kann die Demo-Umgebung beschrieben werden. Unter "Initiale Aktion" kann eine Aktion festgelegt
werden, mit der der Prozess gestartet werden soll. Falls angegeben, kann zusätzlich noch ein URL "context" Parameter
angegeben werden. Falls der Wert des URL "context" Parameter eine Prozess Model-Pipe-Notation
(z.B. process|de4ae73b-ac4d-4eac-bd64-5dbb5c0f471d) ist, können mithilfe von Aktions-Vorladedaten Werte aus der
Prozess-Instanz vorgeladen werden.

![Demo-Umgebungen](/img/docs/de/environments/environments-information.png)

#### Prozesse

Unter "Prozesse" können Prozess-Instanzen angegeben werden, die beim Starten einer Prozess-Demo in der Allisa
Plattform erzeugt werden. Die dort angelegten Prozesse können unter "Verknüpfungen" genutzt werden um Prozess-Prozess
Verknüpfungen anzulegen.

![Demo-Umgebungen](/img/docs/de/environments/environments-process.png)

Für jede Prozess-Instanz kann der Prozesstyp, Name, Situation, Prozess-Daten und Zugriff definiert werden.

#### Gruppen

Unter "Gruppen" können Gruppen definiert werden, die im der Allisa Plattform für eine Prozess-Demo vorhanden
sein sollen.

![Demo-Umgebungen](/img/docs/de/environments/environments-groups.png)

Mit dem Alias wird der Gruppe eindeutig auf der Allisa Plattform identifiziert, auf der der Prozess zukünftig genutzt
wird. Beginnt der Alias mit "demo" wird die Gruppe nicht als Prozess-Abhängigkeit definiert.

#### Benutzer

Unter "Benutzer" können Benutzer definiert werden, die in der Allisa Plattform für eine Prozess-Demo vorhanden
sein sollen. Standardgemäß hat die Allisa Plattform bei der Demo lediglich den Demo-Benutzer angelegt.

![Demo-Umgebungen](/img/docs/de/environments/environments-users.png)

Mit dem Alias wird die nutzende Person eindeutig auf der Allisa Plattform identifiziert, auf der der Prozess zukünftig
genutzt wird. Beginnt der Alias mit "demo" wird die nutzende Person nicht als Prozess-Abhängigkeit definiert.

**Prozess-Identität**

Die Prozess-Identität ist eine Prozess-Instanz, die eine nutzende Person repräsentiert und fest mit dem Konto der nutzenden Person
verknüpft ist. Jede nutzende Person auf der Allisa Plattform besitzt eine Prozess-Identität. Die Prozess-Identität hat den Zweck,
weitere Daten und Funktionen zu einem Benutzerprofil hinzuzufügen. Standardgemäß ist die Prozess-Identität eine Prozess-Instanz vom Typ "allisa/person".
Dies kann jedoch auch individuell auf die fachlichen Anforderungen angepasst werden.

> {info.fa-info-circle} Der Prozess einer Prozess-Identität muss entweder Datenfelder "first_name", "last_name" und "user" oder 
> die Datenfelder "vorname", "nachname" und "benutzer" besitzen.

#### Bots

Unter "Bots" können Bots definiert werden, die in der Allisa Plattform für eine Prozess-Demo vorhanden sein
sollen. Ein Bot ist ein Systembenutzer und kann für die Authentifizierung einer öffentlichen API auf der Allisa
Plattform genutzt werden.

![Demo-Umgebungen](/img/docs/de/environments/environments-bots.png)

Mit dem Alias wird der Bot eindeutig auf der Allisa Plattform identifiziert, auf der der Prozess zukünftig genutzt wird.
Beginnt der Alias mit "demo" wird der Bot nicht als Prozess-Abhängigkeit definiert.

#### Verknüpfungen

Unter "Verknüpfungen" werden Prozess-Prozess Verknüpfungen definiert. Es können Verknüpfungen zwischen den unter
"Prozesse" angegebenen Prozessen hinzugefügt werden.

![Demo-Umgebungen](/img/docs/de/environments/environments-relations.png)

Falls der gewählte Verknüpfungstyp Verknüpfungsdaten besitzt, können dort auch die Werte dafür angegeben werden.

#### Konnektoren

Unter "Konnektoren" werden Test-Konnektoren und dazugehörige Requests angelegt. Bei den Konnektor-Requests können
Demo-Rückgabewerte angegeben werden, um bei einer Prozess-Demo die Schnittstellen-Antwort zu simulieren.

![Demo-Umgebungen](/img/docs/de/environments/environments-Konnektors.png)

Nachdem ein Konnektor angelegt wurde, kann dieser bearbeitet werden. Dort können Requests angelegt werden.

![Demo-Umgebungen](/img/docs/de/environments/environments-request.png)

Bindings **(1)** sind Variablen, auf die bei der Aktionsausführung durch den Aktions-Prozessor "Konnektor-Request
ausführen" Werte geschrieben werden können. Binding-Variablen werden auf der Allisa Plattform beim Konfigurieren des
Konnektor-Requests für die URL oder den Request-Body genutzt. In dieser Stelle werden Binding-Variablen lediglich für
die Dokumentation und dem Mapping beim Aktions-Prozessor genutzt.

#### Debug Optionen

Wird ein Konnektor bei einer Prozess-Demo genutzt, werden die Debug Optionen genutzt **(2)**, um eine Rückgabe
des Konnektors zu simulieren.

```json
{
    "type": "json",
    "body": {
        "example": "data"
    }
}
```

Beim `"type" : "json"` wird das unter `body` angegebene Objekt zurückgegeben `{"example": "data"}` zurückgegeben.

Möchte man einen Dokument-Download simulieren, kann folgende Konfiguration genutzt werden:

```json
{
    "type": "file",
    "body": "[[faker.file.pdf.raw]]"
}
```

Es wird ein Demo-Dokument erzeugt. Optional kann auch `[[faker.file.jpg.raw]]` oder `[[faker.file.xslx.raw]]` für `body`
angegeben werden.

#### Öffentliche APIs

Unter "Öffentliche APIs" werden die in der Allisa Plattform existierenden öffentlichen APIs definiert. Dies ist zum
Beispiel für HTML-Vorlagen hilfreich, um in der E-Mail eine URL zu einer öffentlichen API zu generieren.

![Demo-Umgebungen](/img/docs/de/environments/environments-public-apis.png)

Mit dem Slug wird die öffentliche API eindeutig auf der Allisa Plattform identifiziert, auf der der Prozess zukünftig genutzt wird.
Beginnt der Slug mit "demo" wird die öffentlich Api nicht als Prozess-Abhängigkeit definiert.

#### Variablen

Unter "Variablen" können Variablen definiert werden, die in der Allisa Plattform für eine Prozess-Demo vorhanden sein
sollen. Der Wert der Variable kann eine Zeichenkette, JSON oder ein Dokument sein.

![Demo-Umgebungen](/img/docs/de/environments/environments-variables.png)

Mit dem Identifier wird die Variable eindeutig auf der Allisa Plattform identifiziert, auf der der Prozess zukünftig genutzt wird.
Beginnt der Identifier mit "demo" wird die Variable nicht als Prozess-Abhängigkeit definiert.

***

[Nächster Artikel: Konfiguration ➜ Events](/{{route}}/{{version}}/events)
