# Konfiguration ➜ Verknüpfungstypen

---

- [Einleitung](#introduction)
- [Verknüpfungstyp erstellen](#create-relationtype)
- [Verbindungstypen](#connection-types)
- [1:1 Verbindungstyp](#1-1-connection-type)
- [1:N Verbindungstyp](#1-n-connection-type)
- [N:1 Verbindungstyp](#n-1-connection-type)
- [N:M Verbindungstyp](#n-m-connection-type)
- [Verknüpfungsdaten](#data)
- [Auf verknüpfte Prozesse zugreifen](#access-related-processes)

<a name="introduction"></a>

## Einleitung

Prozess-Instanzen auf der Allisa Plattform können miteinander verknüpft werden. Eine Verknüpfung dient dazu,
**fachliche Zugehörigkeiten zu repräsentieren** und auf **Daten von verknüpften Prozessen zuzugreifen**.

Zum Beispiel kann es bei einer Job-Portal-Lösung sinnvoll sein, alle Bewerbungen einer Stellenausschreibung mit der
Stellenausschreibung zu verknüpfen, sodass in der Prozess-Instanz der Stellenausschreibung, die jeweiligen Bewerbungen
in einer Liste angezeigt werden können.

Ein weiterer Nutzen von Verknüpfungen ist, auf Daten eines verknüpften Prozesses zuzugreifen. Dies
verhindert, dass Daten redundant gespeichert werden müssen und ermöglicht eine flexiblere Datenstruktur.

Verknüpfungstypen werden im Konfigurationsbereich unter "Verknüpfungstypen" konfiguriert.

![Verknüpfungstypen](/img/docs/de/relationtypes/config-relationtypes-overview.png)

Der obenstehende Screenshot zeigt die Verknüpfungstypen eines "Issue"-Prozesses. Der "Issue" Prozess wird im Kontext der
Software-Entwicklung als ein "Anliegen" bezeichnet und bezieht sich in diesem Beispiel auf einen Software-Fehler oder
eine Anforderung and die Software.
Hier sieht man den Namen des Verknüpfungstypes, sowie den Verbindungstyp und die Referenz **(1)**. Hier wurden beispielhaft 
verschiedene Verbindungstypen ausgewählt. Darunter wird die Beschreibung angezeigt **(2)**. 

Im Verlauf des "Issue"-Prozesses werden Personen mit dem Issue verknüpft. Die Person, die das Issue erfasst
hat, wird mit dem "Initiator" Verknüpfungstyp verknüpft. Auf gleiche Art und Weise werden die Personen, welche die
Umsetzung des Issues bearbeiten oder testen, entsprechend mit dem "Bearbeiter" oder "Tester" Verknüpfungstyp verknüpft.

<a name="create-relationtype"></a>

## Verknüpfungstyp erstellen

Unter "Verknüpfungstypen" im Konfigurationsbereich kann mit dem Button "Verknüpfungstyp anlegen" ein neuer
Verknüpfungstyp erstellt werden.

![Verknüpfungstypen](/img/docs/de/relationtypes/config-relationtypes-form.png)

Jeder Verknüpfungstyp besitzt einen Namen und eine Beschreibung **(1, 2)**. Der Verbindungstyp legt die **(3)**
Beziehung zwischen verknüpften Prozessen fest fest. Der Referenz-Name **(4)** dient dazu, den Verknüpfungstyp systemweit eindeutig zu identifizieren.
Unter Verknüpfungsdaten **(4)** werden die möglichen Datensätze definiert, die eine Verknüpfung von diesem Typ speichern kann.

<a name="connection-types"></a>

## Verbindungstypen

Jeder Verknüpfungstyp besitzt einen Verbindungstyp. Der Verbindungstyp gibt das Verhalten des Verknüpfungstyps vor. Der Verbindungstyp orientiert 
sich am ["Entity-Relationship-Modell"](https://de.wikipedia.org/wiki/Entity-Relationship-Modell)-Konzept, welches vier grundlegende Beziehungstypen definiert:
- 1:1 Beziehung
- 1:N Beziehung
- N:1 Beziehung
- N:M Beziehung

Je nach Verbindungstyp können unterschiedlich viele Prozesse mit diesem Verknüpfungstyp verknüpft werden. Besitzt ein Prozess beispielsweise einen
Verknüpfungstyp mit dem Verbindungstyp "1:1", kann nur ein einziger Prozess mit diesem Verknüpfungstyp verknüpft werden. Wiederholende Verknüpfungsversuche würden 
die bestehende Verknüpfung lediglich aktualisieren oder ersetzen.

Der Aktionsprozessor "Verknüpfung erstellen / aktualisieren" nutzt die Prozess-Zugehörigkeit des Verknüpfungstyps und den Verbindungstyp um zu ermitteln, 
welche Verknüpfungen erstellt, aktualisiert oder ersetzt werden müssen. Entscheidend für diese Ermittlung ist, zu welchem Prozess der Verknüpfungstyp gehört, 
da dies vorgibt, wie viele Prozesse auf "jeder Seite der Verknüpfung" existieren dürfen.

> {info.fa-info-circle} Das Verhalten des Verknüpfungstyp orientiert sich an dem Prozess, zu dem der Verknüpfungstyp gehört. Beim Ausführen des 
> "Verknüpfung erstellen / aktualisieren"-Prozessors muss der Verknüpfungstyp entweder dem ausführenden oder dem zu verknüpftenden Prozess zugehörig sein.

<a name="1-1-connection-type"></a>

## 1:1 Verbindungstyp

Der 1:1 Verbindungstyp ist der restriktivste Verbindungstyp. Dieser Verbindungstyp legt fest, dass der Prozess, zu dem der Verknüpfungstyp gehört,
nur einen Prozess mit diesem Verknüpfungstyp verknüpfen kann. Die beidseitige 1:1 Beziehung legt ebenso fest, dass ein anderer Prozess, der diesen Verknüpfungstyp
für die Aktionsausführung nutzt, nur einen Prozess mit dem Verknüpfungstyp verknüpfen kann.

![Verknüpfungstypen](/img/docs/de/relationtypes/1-1-connection-type.png)

Der obenstehende Screenshot zeigt ein Beispiel des 1:1 Verbindungstyps. Es gibt einen Prozess "Firmenwagen" (allisa/firmenwagen) und einen Prozess "Person" (allisa/person).
Der Verknüpfungstyp gehört zu "Firmenwagen". Der Referenzname des Verknüpfungstyps (eindeutige Identifikation) ist "arbeitnehmer".
Fachlich wird hier festgelegt, dass jeder Firmenwagen nur einer Person (Arbeitnehmer) zugewiesen werden kann. Ebenso kann jeder Person nur einen Firmenwagen zugewiesen werden.

Verhalten bei Aktionsausführung mit "Verknüpfung erstellen / aktualisieren" Prozessor:
- Führt man bei einem Firmenwagen ohne zugewiesener Person die Aktion "Person zuweisen" aus, wird eine Verknüpfung zur Person erstellt. 
In dem Fall, dass die Person bereits zu einem anderen Firmenwagen zugewiesen ist, wird der Firmenwagen (die Verknüpfung) ersetzt.
- Führt man bei einem Firmenwagen mit bereits zugewiesener Person die Aktion "Person zuweisen" aus, wird:
  - Die Verknüpfung ersetzt, wenn die bereits zugewiesene Person nicht gleich die neue Person ist.
  - Die Verknüpfung aktualisiert, wenn die bereits zugewiesene Person gleich die neue Person ist.
- Führt man bei einer Person ohne zugewiesenen Firmenwagen die Aktion "Firmenwagen zuweisen" (Aktion nutzt Verknüpfungstyp "allisa/firmenwagen - arbeitnehmer") aus,
wird eine neue Verknüpfung zu dem Firmenwagen erzeugt. In dem Fall, dass der zugewiesene Firmenwagen bereits einer anderen Person zugewiesen ist, 
wird diese Verknüpfung durch die neue Verknüpfung zur neuen Person ersetzt.

> {info.fa-info-circle} Jeder Firmenwagen kann nur einer Person zugewiesen sein. Jede Person kann nur einen Firmenwagen besitzen.

<a name="1-n-connection-type"></a>

## 1:N Verbindungstyp

Der 1:N Verbindungstyp legt fest, dass der Prozess, zu dem der Verknüpfungstyp gehört, beliebig viele Prozesse mit diesem Verknüpfungstyp verknüpfen kann. 
Die einseitige 1:N Beziehung legt auch fest, dass ein anderer Prozess, der diesen Verknüpfungstyp
für die Aktionsausführung nutzt, nur einen Prozess verknüpfen kann.

![Verknüpfungstypen](/img/docs/de/relationtypes/1-n-connection-type.png)

Der obenstehende Screenshot zeigt ein Beispiel des 1:N Verbindungstyps. Es gibt einen Prozess "Firma" (allisa/firma) und einen Prozess "Person" (allisa/person).
Der Verknüpfungstyp gehört zu "Firma". Der Referenzname des Verknüpfungstyps (eindeutige Identifikation) ist "arbeitnehmer".
Fachlich wird hier festgelegt, dass jeder Firma beliebig viele Personen (Arbeitnehmer) zugewiesen werden können. Jedoch kann jeder Person nur einer Firma zugewiesen werden.

Verhalten bei Aktionsausführung mit "Verknüpfung erstellen / aktualisieren" Prozessor:
- Führt man bei einer Firma die Aktion "Arbeitnehmer zuweisen" aus, wird eine Verknüpfung zur Person erstellt. In dem Fall, dass die Person bereits einer anderen
Firma zugewiesen ist, wird die Firma (die Verknüpfung zur Person) ersetzt.
- Führt man bei einer Firma mit bereits zugewiesenen Arbeitnehmern die Aktion "Arbeitnehmer zuweisen" aus, wird:
    - Die Verknüpfung zur Person erstellt, wenn die Person noch nicht zu dieser Firma zugewiesen wurde.
    - Die Verknüpfung zur Person aktualisiert, wenn die Person bereits vorher zu dieser Firma zugewiesen wurde.
- Führt man bei einer Person ohne zugewiesener Firma die Aktion "Firma zuweisen" (Aktion nutzt Verknüpfungstyp "allisa/firma - arbeitnehmer") aus,
  wird eine neue Verknüpfung zu der Firma erzeugt.
- Führt man bei einer Person mit bereits zugewiesener Firma die Aktion "Firma zuweisen" (Aktion nutzt Verknüpfungstyp "allisa/firma - arbeitnehmer") aus,
  wird:
  - Die Verknüpfung zur Firma ersetzt, wenn die Firma eine andere Firma ist als die bisher zugewiesene Firma.
  - Die Verknüpfung zur Firma aktualisiert, wenn die Firma dieselbe Firma ist, die zuvor bereits zugewiesen wurde.

> {info.fa-info-circle} Jede Firma kann beliebig viele Personen (Arbeitnehmer) haben. Jede Person kann nur einer Firma zugewiesen sein.

<a name="n-1-connection-type"></a>

## N:1 Verbindungstyp

Der N:1 Verbindungstyp legt fest, dass der Prozess, zu dem der Verknüpfungstyp gehört, nur einen Prozess mit diesem Verknüpfungstyp verknüpfen kann.
Die einseitige N:1 Beziehung legt außerdem fest, dass ein anderer Prozess, der diesen Verknüpfungstyp
für die Aktionsausführung nutzt, beliebig viele Prozesse mit dem Verknüpfungstyp verknüpfen kann.

![Verknüpfungstypen](/img/docs/de/relationtypes/n-1-connection-type.png)

Der obenstehende Screenshot zeigt ein Beispiel des N:1 Verbindungstyps. Es gibt einen Prozess "Artikel" (allisa/artikel) und einen Prozess "Auftrag" (allisa/auftrag).
Der Verknüpfungstyp gehört zu "Artikel". Der Referenzname des Verknüpfungstyps (eindeutige Identifikation) ist "auftrag".
Fachlich wird hier festgelegt, dass jeder Artikel nur einem Auftrag zugewiesen werden kann. Jedoch kann jeder Auftrag beliebig viele Artikel haben.

Verhalten bei Aktionsausführung mit "Verknüpfung erstellen / aktualisieren" Prozessor:
- Führt man bei einem Artikel die Aktion "Auftrag zuweisen" aus, wird eine Verknüpfung zum Auftrag erstellt. In dem Fall, dass der Artikel bereits einem anderen
  Auftrag zugewiesen ist, wird der Auftrag (die Verknüpfung zum Auftrag) ersetzt.
- Führt man bei einem Artikel mit bereits zugewiesenem Auftrag die Aktion "Auftrag zuweisen" aus, wird:
    - Die Verknüpfung ersetzt, wenn der bereits zugewiesene Auftrag nicht gleich der neue Auftrag ist.
    - Die Verknüpfung aktualisiert, wenn der bereits zugewiesene Auftrag gleich der neue Auftrag ist.
- Führt man bei einem Auftrag ohne zugewiesene Artikel die Aktion "Artikel zuweisen" (Aktion nutzt Verknüpfungstyp "allisa/artikel - auftrag") aus,
  wird eine neue Verknüpfung zu dem Artikel erzeugt.
- Führt man bei einem Auftrag mit bereits zugewiesenen Artikeln die Aktion "Artikel zuweisen" (Aktion nutzt Verknüpfungstyp "allisa/artikel - auftrag") aus,
  wird:
    - Die Verknüpfung zum Artikel erstellt, wenn dieser Artikel noch nicht mit dem Auftrag verknüpft wurde.
    - Die Verknüpfung zum Artikel aktualisiert, wenn dieser Artikel bereits zuvor mit dem Auftrag verknüpft wurde.

> {info.fa-info-circle} Jeder Artikel kann nur einem Auftrag zugewiesen sein. Jeder Auftrag kann beliebig viele Artikel haben.

<a name="n-m-connection-type"></a>

## N:M Verbindungstyp

Der N:M Verbindungstyp ist der offenste Verbindungstyp. Dieser legt fest, dass der Prozess, zu dem der Verknüpfungstyp gehört, beliebig viele Prozesse
mit diesem Verknüpfungstyp verknüpfen kann. Die beidseitige N:M Beziehung legt außerdem fest, dass ein anderer Prozess, der diesen Verknüpfungstyp
für die Aktionsausführung nutzt, ebenso beliebig viele Prozesse mit dem Verknüpfungstyp verknüpfen kann.

![Verknüpfungstypen](/img/docs/de/relationtypes/n-m-connection-type.png)

Der obenstehende Screenshot zeigt ein Beispiel des N:M Verbindungstyps. Es gibt einen Prozess "Anforderung" (allisa/anforderung) und einen Prozess "Person" (allisa/person).
Der Verknüpfungstyp gehört zu "Anforderung". Der Referenzname des Verknüpfungstyps (eindeutige Identifikation) ist "testers".
Fachlich wird hier festgelegt, dass einer Anforderung beliebig viele Tester zugewiesen werden können. Ebenso kann jeder Person, beliebig viele Anforderungen zugewiesen werden.

Verhalten bei Aktionsausführung mit "Verknüpfung erstellen / aktualisieren" Prozessor:
- Führt man bei einer Anforderung ohne zugewiesenen Personen (Tester) die Aktion "Tester zuweisen" aus, wird eine Verknüpfung zur Person erstellt.
- Führt man bei einer Anforderung mit bereits zugewiesenen Personen (Tester) die Aktion "Tester zuweisen" aus, wird:
    - Die Verknüpfung zur Person erstellt, wenn die Person noch nicht mit dieser Anforderung verknüpft wurde.
    - Die Verknüpfung zur Person aktualisiert, wenn die Person bereits vorher mit dieser Anforderung verknüpft wurde.
- Führt man bei einer Person ohne zugewiesenen Anforderungen die Aktion "Anforderung zuweisen" (Aktion nutzt Verknüpfungstyp "allisa/anforderung - testers") aus,
  wird eine neue Verknüpfung zu der Anforderung erzeugt.
- Führt man bei einer Person mit bereits zugewiesenen Anforderungen die Aktion "Anforderung zuweisen" (Aktion nutzt Verknüpfungstyp "allisa/anforderung - testers") aus,
  wird:
    - Die Verknüpfung zur Anforderung erstellt, wenn diese Anforderung noch nicht mit der Person verknüpft wurde.
    - Die Verknüpfung zur Anforderung aktualisiert, wenn diese Anforderung bereits zuvor mit der Person verknüpft wurde.

> {info.fa-info-circle} Jeder Anforderung können beliebig viele Personen (Tester) zugewiesen werden. Jede Person kann beliebig viele Anforderungen haben.

![Verknüpfungstypen](/img/docs/de/relationtypes/config-relationtypes-reference-data.png)

<a name="data"></a>

## Verknüpfungsdaten

Eine Prozess-Prozess Verknüpfung kann Daten besitzen. In der Regel werden Verknüpfungsdaten genutzt, um Daten zu speichern, die aus fachlicher Sicht keinen der beiden Prozessen
zugeordnet werden können. Die möglichen Datensätze werden beim jeweiligen Verknüpfungstyp definiert.

Als Beispiel dient ein "Seminar"-Prozess und ein "Person"-Prozess. Wenn sich eine Person zum Seminar anmeldet, wird die Person mit dem Verknüpfungstyp "Anmeldung" 
mit der "Seminar"-Prozess-Instanz verknüpft. Der Anmeldezeitpunkt, die individuellen Gebühren (Normaler Preis, Reduzierter Preis) und weitere
Daten werden dann in der Verknüpfung gespeichert werden, weil sie weder zum "Seminar"-Prozess noch zum "Person"-Prozess passen. Die Verknüpfung von einem Seminar und einer Person
repräsentiert eine Anmeldung und diese Anmeldung besitzt Daten (daher heißt der Verknüpfungstyp "Anmeldung").

- Seminar 1 &rarr; "Anmeldungen"-Verknüpfungstyp (Daten: datum = 04.03.2022, gebuehren = €120) &larr; Person 1
- Seminar 1 &rarr; "Anmeldungen"-Verknüpfungstyp (Daten: datum = 08.03.2022, gebuehren = €80) &larr; Person 2
- Seminar 1 &rarr; "Anmeldungen"-Verknüpfungstyp (Daten: datum = 10.03.2022, gebuehren = €120) &larr; Person 3

Verknüpfungsdaten können mit dem Aktions-Prozessor "Verknüpfung erstellen / aktualisieren" aktualisert werden. Bei einer bereits existierenden Verknüpfung, 
werden die Verknüpfungsdaten aktualisiert.

> {info.fa-info-circle} Der Verbindungstyp des Verknüpfungstyp "Anmeldung" ist N:M, weil ein Seminar beliebig viele Personen (Anmeldungen) haben kann 
> und sich eine Person zu beliebig vielen Seminaren anmelden kann.

<a name="access-related-processes"></a>

## Auf verknüpfte Prozesse zugreifen

Es bestehen zwei grundlegende Funktionen, um auf verknüpfte Prozesse zuzugreifen.

### Verknüpfte Prozesse in Listen anzeigen

Verknüpfte Prozesse können in Prozess-Listen angezeigt werden. Die Vorlage "Verknüpfte Prozesse" ist dafür ausgelegt, alle oder nur bestimmte verknüpfte Prozesse anzuzeigen.
![Verknüpfungstypen](/img/docs/de/relationtypes/config-lists-create-relation-type.png)

Im Bereich "Daten" können sowohl die prozessinternen als auch Verknüpfungstypen anderer Prozesse gewählt werden. Des Weiteren können dort Verknüpfungstdaten gewählt werden 
oder nach bestimmten Prozessen gefiltert werden. 

![Verknüpfungstypen](/img/docs/de/relationtypes/config-lists-relation-type-data.png)

### Direkter Datenzugriff

Syntax-Werte ermöglichen es, direkt auf Daten von verknüpften Prozessen zuzugreifen. Es können auf Prozess-Instanzen zugegriffen werden, 
die mittels einer 1:1, 1:N oder N:1-Verknüpfung verknüpft wurden. Dabei ist es nur möglich auf eine verknüpfte Prozess-Instanz zuzugreifen, dessen Verknüpfung aufgrund ihres Verbindungstyps
nur einmal existieren kann. 

- Bei einer 1:1-Verknüpfung können beide Prozesse jeweils auf die Daten des anderen Prozessen zugreifen.
- Bei einer 1:N-Verknüpfung können die "N"-Prozesse jeweils auf den "1"-Prozess zugreifen. In Bezug auf das obenstehende Beispiel aus dem Abschnitt ["1:N Verbindungstyp"](#1-n-connection-type)
können alle Personen auf den "Firma"-Prozess zugreifen.
- Bei einer N:1-Verknüpfung können die "N"-Prozesse jeweils auf den "1"-Prozess zugreifen. In Bezug auf das obenstehende Beispiel aus dem Abschnitt ["N:1 Verbindungstyp"](#n-1-connection-type)
  können alle Artikel auf den Auftrag zugreifen.

![Verknüpfungstypen](/img/docs/de/relationtypes/rules-preload-references.png)

Der obenstehende Screenshot zeigt die Auswahl der Syntax-Werte. In den Kategorien "Referenz-Metadaten", "Referenz-Verknüpfungsdaten", "Referenz-Prozess-Daten", "Referenz-Status-Daten"
uns "Referenz-URLs" können Werte von verknüpften Prozessen geladen werden **(1)**. Der Referenzname der Verknüpfung wird in den "[]"-Klammern angezeigt **(2)**. Verknüpfungstypen von externen
Prozessen haben ihren Namespace vor dem Referenznamen **(3)**. Es werden ausschließlich Verknüpfungstypen bei den Syntax-Werten angezeigt, die den obenstehenden Einschränkungen entsprechen.

***

[Nächster Artikel: Konfiguration ➜ Vorlagen](/{{route}}/{{version}}/templates)
