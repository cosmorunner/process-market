# Konfiguration ➜ Vorlagen

---

- [Einleitung](#introduction)
- [Vorlage anlegen](#create-template)
- [Daten-Mapping](#data-mapping)
- [Eigene Logik](#custom-logic)

<a name="introduction"></a>

## Einleitung

Vorlagen werden für den E-Mail Versand, der PDF-Dokumentenerzeugung und der Aussteuerung eigener Logik genutzt (
Aktions-Prozessor "Eigene Logik ausführen). Beim E-Mail Versand und der PDF-Dokumentenerzeugung werden HTML-Vorlagen
genutzt.

![Vorlagen](/img/docs/de/templates/config-templates-example.png)

Der obenstehende Screenshot zeigt die erzeugte E-Mail bei der Aktion "Montage starten" des "Montage" Case-Study
Prozesses. Vorlagen werden im Konfigurationsbereich unter "Vorlagen" konfiguriert.

![Vorlagen](/img/docs/de/templates/config-templates-overview.png)

Mit dem Button "Vorlage anlegen" **(1)** wird eine neue Vorlage angelegt. Im Kopfbereich der Vorlage wird kenntlich
gemacht, um welchen Typ es sich bei der Vorlage handelt **(2)**. Auf der rechten Seite kann die Vorlage
dupliziert und gelöscht **(3)** werden.

Der untenstehende Screenshot zeit die HTML-Vorlagen-Konfiguration der E-Mail aus der "Montage starten"-Aktion:

![Vorlagen](/img/docs/de/templates/config-templates-config-edit.png)

Ganz oben sind drei Tabs: 
Unter Bearbeiten **(1)** werden der Name der Vorlage **(2)**, sowie 
Hinweise zum HTML & Styling **(3)** angezeigt. Es folgt ein Editor, mit dem die Vorlagenstruktur bearbeitet
wird **(4)**. 
Im zweiten Tag ist das Daten-Mapping **(5)**. Alle Werte, die in der Vorlage genutzt werden sollen,
müssen beim Daten-Mapping definiert sein. 
Zum Schluss kommt der Vorschau Tab **(6)**. Hier kann die Struktur der Vorlage in HTML oder in einem gerenderten PDF 
angezeigt werden.

![Vorlagen](/img/docs/de/templates/config-templates-config-mapping.png)

Unter "Daten-Mapping" können Variablen definiert, und in der Vorlage genutzt werden. Links stehen die Variablen Namen, 
welche über die Suche **(1)** gefunden werden können. Hier befinden sich standardmäßig Variablen, die in der Vorlage 
immer zur Verfügung stehen (s. "Global").
Es folgt eine Beschreibung **(2)** und der Typ der Variablen **(3)**.
In der letzten Spalte wird der Wert der Variablen angezeigt **(4)**. Dieser kann aus dem Syntx-Loader ausgewählt,
oder manuell definiert werden. Mit dem Plus-Button **(5)** werden neue Variablen hinzugefügt.

![Vorlagen](/img/docs/de/templates/config-templates-config-preview.png)

Im letzten Tab "Vorschau" können für die zuvor definierten Variablen Beispielwerte definiert werden. So muss nicht jedes
Mal zum Testen eine Kation gestartet werden. Oben sind wieder drei Tabs: "Werte", "HTML", und "PDF". Unter "Werte" können
die Beispielwerte eingetragen werden. "HTML" und "PDF" liefern eine Vorschau in den jeweiligen Formaten. Links sind Name 
und Typ der Variablen verortet **(2)**. Auf der rechten Seite können dann für jede Variable Beispielwerte erstellt werden **(3)**.

Die HTML-Vorlagen basieren aus dem HTML/CSS-Framework **Bootstrap 4.6**. Entsprechend können die CSS-Klassen des
Frameworks in der HTML-Vorlage genutzt werden. Das im Editor hinterlegte HTML wird beim Rendern als erstes Child-Element
in den **HTML Body-Tag** gegeben. Es ist somit **nicht notwendig** im Editor das HTML-Gerüst
nachzubilden (`<html>...</html>`).

Die Vorlagen werden mithilfe der **"Twig 3.x Templating Engine"** verarbeitet, wodurch es möglich ist, Variablen, Loops
und Logik-Statements zu nutzen: https://twig.symfony.com/doc/3.x/templates.html

<a name="create-template"></a>
## Vorlage anlegen

Mit dem Button "Vorlage anlegen" wird eine neue Vorlage angelegt. Es kann zwischen fünf Vorlagen-Beispiele
gewählt werden:

- HTML - Leere Vorlage
- HMTL - E-Mail/PDF - Nachricht mit Button
- HTML - E-Mail/PDF - Einfache Nachricht
- Eigene Logik (Für "Eigene Logik ausführen"-Prozessor)
- Eigene Logik (Für den Smart-Status vom Typ "Eigene Logik")

![Vorlagen](/img/docs/de/templates/config-templates-templates.png)

<a name="data-mapping"></a>
## Daten-Mapping

Unter "Daten-Mapping" werden Variablen erfasst, die in im Editor genutzt werden können. Dies gilt sowohl für
HTML-Vorlagen als auch für "Eigene Logik"-Vorlagen.

#### Globale Variablen

Es werden automatisch einige globalen Variablen in das Template geladen, welche nicht zusätzlich im Daten-Mapping definiert werden müssen.

- `app_name`: Name der Plattform.
- `app_description`: Beschreibung der Plattform.
- `app_url`: Absolute URL zur Plattform.
- `app_image_url`: Absolute URL zum Plattform-Logo.
- `process_name`: Name der Prozess-Instanz, in der die Vorlage genutzt wird.
- `process_id`: Id der Prozess-Instanz, in der die Vorlage genutzt wird.
- `process_url`: Absolute URL zur der Prozess-Instanz, in der die Vorlage genutzt wird.
- `process_data`: Assoziatives Array mit den Prozess-Datenfeldern als Keys und den Werten. Beispiel: 

```json
{
    "name": "Anna",
    "age": 27,
    "hobbies": ["soccer", "travel"]
}
```

- `process_situation`: Assoziatives Array mit den Status-Referenznamen als Keys und einem weiteren assoziativen Array mit den Status-Informationen als Werten. Beispiel:

```json
{
    "hauptstatus_referenzname": {
        "color": "aeb6bf",
        "image": "block",
        "value": "9.000",
        "state_id": "a2e53f2d-22ba-4f05-926e-3448e5f1847c",
        "text_value": "Abgewiesen"
    },
    "weiterer_status_referenzname": {
        "color": "ffffff",
        "image": "help_outline",
        "value": "999.000",
        "state_id": null,
        "text_value": "Zustand nicht definiert."
  }
}
```

- `action_type_name`: Name der Aktion, in der die Vorlage genutzt. Ist leer sein beim Smart-Status vom Typ "Eigene Logik".
- `time_24`: Uhrzeit im 24-Stunden Format, z.B. "08:45".
- `date_ddmmyyyy`: Datum im "dd.mm.yyyy"-Format, z.B. "03.10.1980".
- `user_full_name`: Vor- und Nachname des Benutzers.
- `user_first_name`: Vorname des Benutzers. Ist leer sein beim Smart-Status vom Typ "Eigene Logik".
- `user_last_name`: Nachname des Benutzers. Ist leer sein beim Smart-Status vom Typ "Eigene Logik".
- `user_email`: E-Mail des Benutzers. Ist leer sein beim Smart-Status vom Typ "Eigene Logik".

Es können fünf Arten von Variablen angelegt werden:

#### Zeichenkette

Eine einfache Zeichenkette, wie zum Beispiel "Hello World" oder "123".

![Vorlagen](/img/docs/de/templates/config-templates-string-value.png)

Die obenstehende Variable würde zum Beispiel folgendermaßen in der Vorlage genutzt werden. Variablen werden mit `{{}}`
ausgegeben.

```html
<p>
    <span>{{ auftrag_name }}</span>
</p>
```

#### JSON Objekt/Array

Ein JSON kodiertes Array oder Objekt wird für Aktions- und Prozesstypen genutzt, die vom Typ "JSON-Array" oder "JSON-Objekt" sind. Beispielsweise
könnte eine Vorlage zwei Variablen vom Typ "JSON-Array" besitzen, die jeweils folgende Werte haben:

item_names

```json
[
    "PC-Monitor",
    "Whiteboard-Marker"
]
```

item_prices

```json
[
    "250",
    "9"
]
```

Wenn man nun die obigen Werte untereinander in einer Liste anzeigen lassen müsste, könnte folgendes HTML genutzt werden:

```html

<ul>
    {% for name in item_names %}
    <li>{{ name }} - €{{ item_prices[loop.index0] }}</li>
    {% endfor %}
</ul>
```

Die obige Syntax erzeugt folgendes HTML:

```html

<ul>
    <li>PC-Monitor - €250</li>
    <li>Whiteboard-Marker - €9</li>
</ul>
```

Mit `{% for name in item_names %}` wird durch jeden Eintrag in "item_names" gelooped. "name" hält den Wert an dem
jeweiligen Index. Mit `item_prices[loop.index0]` wird auf den aktuellen Wert in der "item_prices"-Variable
zugegriffen. `loop.index0` referenziert den aktuellen numerischen Index (0-basiert) in dem Loop-Durchlauf.

Weitere Informationen: https://twig.symfony.com/doc/3.x/tags/for.html

#### Listeninhalt

Der Listeninhalt-Typ ermöglicht es, den Inhalt einer Prozess-Liste auf eine Variable zu schreiben. Es werden maximal 500
Einträge von der Liste berücksichtigt. Der Inhalt einer Liste ist ein Array mit Objekten, bei dem jedes Objekt eine
Listenzeile darstellt. Eine Liste könnte zum Beispiel folgende Werte haben:

actions

```json
[
    {
        "actions_id": "df6f535c-4ea7-4e6d-a27e-d4ae4b809d4e",
        "actions_action_type_name": "Material kommissionieren",
        "actions_created_at": "2020-03-02 13:30:12"
    },
    {
        "actions_id": "5de68eb8-3fd0-4361-82bc-fe75e3fb192c",
        "actions_action_type_name": "Montage starten",
        "actions_created_at": "2020-03-02 13:37:00"
    }
]
```

Wenn man nun die obigen Listenzeilen untereinander in einer Liste anzeigen lassen müsste, könnte folgendes HTML genutzt
werden:

```html

<ul>
    {% for item in actions %}
    <li>{{ item['actions_action_type_name'] }} - {{ item['actions_created_at'] }}</li>
    {% endfor %}
</ul>
```

Die obige Syntax erzeugt folgendes HTML:

```html

<ul>
    <li>Material kommissionieren - 2020-03-02 13:30:12</li>
    <li>Montage starten - 2020-03-02 13:37:00</li>
</ul>
```

Mit `{% for item in actions %}` wird durch jeden Listeneintrag in "actions" gelooped. "item" hält den Eintrag an dem
jeweiligen Index. Mit `item['actions_created_at']` wird der Wert hinter dem Key `actions_created_at` zurückgegeben.

Weitere Informationen: https://twig.symfony.com/doc/3.x/tags/for.html

#### Benutzer

Mit dem Variablen-Typ "Benutzer" können die Benutzer-Daten einer Benutzer Model-Pipe-Notation ausgelesen werden. Wird
beispielsweise ein Prozess-Datenfeld
mit einer Benutzer Model-Pipe-Notation belegt, kann es hilfreich sein, in der Vorlage auf den Namen oder die URL zur
Prozess-Identität zuzugreifen.

![Vorlagen](/img/docs/de/templates/config-templates-type-user.png)

Die "Model-Pipe-Notation" ist ein interner Mechanismus, um Objekte eindeutig in der Plattform zu identifizieren.
Im obenstehenden Screenshot hat das Prozess-Datenfeld "benutzer" den
Wert: ``user|c2d7a48e-37b6-4f00-845f-aa8010392c46[Franziska Frühling]``.

Wird beim Daten-Mapping der Variablen Typ "Benutzer" gewählt und dort der die Variable ``person`` mit dem Wert des
Prozess-Datenfeldes "benutzer" befüllt, kann auf die Daten des Benutzers zugegriffen werden.
Der Wert muss eine gültige Benutzer Model-Pipe-Notation sein, andernfalls ist der Wert ``NULL``.

> {info.fa-info-circle} Der ausführende Benutzer benötigt das System-Rollen Recht in der Allisa Plattform, Benutzer
> einsehen zu dürfen. Andernfalls ist der Wert der Variable ``NULL``

![Vorlagen](/img/docs/de/templates/config-templates-type-user-table.png)

Folgende Daten stehen beim Variablen-Typ "Benutzer" zur Verfügung. Beispiel:

```json
{
  "id": "7434c722-52f4-4750-b31a-8558bc2af4d3",
  "first_name": "Franziska",
  "last_name": "Frühling",
  "full_name": "Franziska Frühling",
  "email": "franziska@example.com",
  "locale": "en",
  "pipe_notation": "user|7434c722-52f4-4750-b31a-8558bc2af4d3[Franziska Frühling]",
  "url": "https://example.com/admin/users/7434c722-52f4-4750-b31a-8558bc2af4d3",
  "identity": {
    "id": "5d3453a6-7c6b-4b06-8ab1-a6c58f2909c0",
    "name": "Franziska Frühling",
    "description": "",
    "reference": "",
    "tags": [
      "tag1",
      "tag2"
    ],
    "url": "https://example.example.com/processes/5d3453a6-7c6b-4b06-8ab1-a6c58f2909c0",
    "pipe_notation": "process|5d3453a6-7c6b-4b06-8ab1-a6c58f2909c0[Franziska Frühling]"
  }
}
```

In der Vorlage kann dann folgendermaßen auf die Daten zugegriffen werden:

```html
<p>
    <span>E-Mail: {{ person.email }}</span>
    <span>URL zur Prozess-Identität: {{ person.identity.url }}</span>
</p>
```

#### Gruppe

Mit dem Variablen-Typ "Gruppe" können die Gruppen-Daten einer Gruppen Model-Pipe-Notation ausgelesen werden. Wird
beispielsweise ein Prozess-Datenfeld
mit einer Gruppen Model-Pipe-Notation belegt, kann in der Vorlage auf den Namen oder die Tags zugegriffen werden.

![Vorlagen](/img/docs/de/templates/config-templates-type-group.png)

Die "Model-Pipe-Notation" ist ein interner Mechanismus, um Objekte in der Plattform eindeutig zu identifizieren.
Im obenstehenden Screenshot hat das Prozess-Datenfeld "abteilung" den
Wert: ``group|a3438e71-b735-4354-b80b-aaed5a3df582[Marketing]``.

Wird beim Daten-Mapping der Variablen Typ "Gruppe" gewählt und dort eine Variable ``abteilung`` mit dem Wert des
Prozess-Datenfeldes "abteilung" befüllt, kann auf die Daten der Gruppe zugegriffen werden.
Der Wert muss eine gültige Gruppen Model-Pipe-Notation sein, andernfalls ist der Wert ``NULL``.

> {info.fa-info-circle} Der ausführende Benutzer benötigt das System-Rollen Recht in der Allisa Plattform, Gruppen
> einsehen zu dürfen. Andernfalls ist der Wert der Variable ``NULL``

![Vorlagen](/img/docs/de/templates/config-templates-type-group-table.png)

Folgende Daten stehen beim Variablen-Typ "Gruppe" zur Verfügung. Beispiel:

```json
{
  "id": "65e9758c-a727-465f-8117-4b0e442a3027",
  "name": "Marketing",
  "description": "",
  "aliases": [
    "marketing"
  ],
  "tags": [
    "mt-1",
    "internal",
    "team"
  ],
  "pipe_notation": "group|65e9758c-a727-465f-8117-4b0e442a3027[Marketing]",
  "url": "https://example.example.com/admin/groups/65e9758c-a727-465f-8117-4b0e442a3027",
  "identity": {
    "id": "65e9758c-a727-465f-8117-4b0e442a3027",
    "name": "Marketing",
    "description": "",
    "reference": "",
    "tags": [
      "tag1",
      "tag2"
    ],
    "url": "https://example.example.com/processes/65e9758c-a727-465f-8117-4b0e442a3027",
    "pipe_notation": "process|65e9758c-a727-465f-8117-4b0e442a3027[Marketing]"
  }
}
```

In der Vorlage kann dann folgendermaßen auf die Daten zugegriffen werden:

```html
<p>
    <span>Gruppe: {{ abteilung.name }}</span>
</p>
```

<a name="custom-logic"></a>
## Eigene Logik

Vorlagen vom Typ "Eigene Logik" werden vom Aktions-Prozessor "Eigene Logik ausführen" genutzt 
und ermöglichen es, Daten nach individuellen Vorgaben zu verändern oder zu setzen. Die Vorlage ermöglicht es,
mittels der Twig-Engine (https://twig.symfony.com) fachliche Logik auszuführen und somit Daten nach
speziellen Vorgaben zu transformieren.

![Vorlagen](/img/docs/de/templates/config-custom-logic-template.png)

Die Vorlage ist in zwei Bereiche unterteilt. Der obere Teil umfasst die Logik-Programmierung **(1)**.
Im unteren Ausgabe-Bereich werden Twig-Variablen auf Aktions-Datenfelder geschrieben **(2)**. 

#### JSON Array/Objekt auf ein Aktions-Datenfeld schreiben

Standardgemäß werden Werte mit folgender Syntax auf ein Aktions-Datenfeld geschrieben:

```html

<output name="city">{{ city }}</output>
```

``name="city"`` entspricht dabei den Namen eines Aktions-Datenfeldes (city) der Aktion, welche die Vorlage mittels 
"Eigene Logik ausführen" Prozessors nutzt. Mit der doppelten ``{}`` Syntax wird der Wert als Zeichenkette
auf das Datenfeld geschrieben.

Falls mit dem JSON Objekt/Array Mapping-Datentyp ein Objekt/Array in die Vorlage gegeben wurde, 
ist es manchmal gewünscht, das JSON Objekt/Array mit der Vorlage 
zu manipulieren (z.B. Eigenschaften zum JSON Objekt hinzuzufügen, siehe https://twig.symfony.com/doc/3.x/filters/merge.html) 
und dann den aktualisierten Wert erneut auf ein Aktions-Datenfeld zu schreiben. 
Dafür kann folgende Syntax genutzt werden:

```html

{# Dies ist der Ausgabe-Bereich für die Aktions-Daten. #}
<outputs>
    <action>
        <output name="person">{{ person|json_encode() }}</output>
    </action>
</outputs>
```

``person`` ist hier ein Aktions-Datenfeld vom Typ "JSON-Objekt". Zuvor wurde der Wert von den Prozess-Daten vorgeladen:

![Vorlagen](/img/docs/de/templates/config-custom-logic-template-json-object.png)

Mit ``person|json_encode()`` wird der ``json_encode()`` 
Twig-Filter (https://twig.symfony.com/doc/3.x/filters/index.html) genutzt.


***

[Nächster Artikel: Konfiguration ➜ Demo-Umgebungen](/{{route}}/{{version}}/environments)
