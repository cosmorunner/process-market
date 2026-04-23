# Regeln & Daten ➜ Demo

---

- [Einleitung](#introduction)
- [Demo starten](#start-simulation)
- [Aktion ausführen](#execute-action)
- [Benutzer wechseln](#change-user)

<a name="introduction"></a>

## Einleitung

Mit einer Prozess-Demo in der "Regeln & Daten"-Ansicht kann der Prozessablauf getestet und sichergestellt werden, dass das Regelwerk und der Datenfluss
zwischen den Aktionen ordnungsgemäß funktioniert. Des Weiteren kann der Prozessverlauf mit verschiedenen Rollen
simuliert werden um die Zugriffsrechte zu prüfen.

Für jede Prozess-Demo wird eine eigene Allisa Plattform mit eigener Datenbank initialisiert, die nur für den Zeitraum der
Demo existiert. Während der Demo sind Sie auf dieser Allisa Plattform als "Demo Benutzer" angemeldet. Mit
dem Beenden der Demo wird die Allisa Plattform entfernt.

<a name="start-simulation"></a>

## Demo starten

Mit dem grünen Button "Demo starten" wird eine neue Prozess-Demo in der "Regeln & Daten"-Ansicht gestartet.

![Simulation](/img/docs/de/simulation/rules-simulation.png)

Es öffnet sich ein Dialogfenster, bei dem eine Demo-Umgebung **(1)** und eine Rolle **(2)** für die Prozess-Demo gewählt
werden kann.

![Simulation](/img/docs/de/simulation/rules-start-simulation.png)

Im Konfigurationsbereich der Prozessmodellierung können Demo-Umgebungen erstellt werden. Eine Demo-Umgebung enthält
Anweisungen über die Beschaffenheit der Allisa Plattform für die Prozess-Demo. **Mit einer Demo-Umgebung wird die Allisa
Plattform der Prozess-Demo mit Daten befüllt, sodass Ihr Prozess bestmöglich getestet werden kann**. Mit unterschiedlichen
Demo-Umgebungen können Sie den Prozess in unterschiedlichen Szenarien testen.

Wenn eine Aktion zum Beispiel eine Aktion enthält, in der einem Benutzer Zugriff zur Prozess-Instanz erteilt wird, so
ist es in der Prozess-Demo erforderlich, dass ein solcher Testbenutzer in der Prozess-Demo existiert. Ein anderes Beispiel
ist das Zusammenspiel Ihres Prozesses mit anderen Prozessen. Häufig werden sogenannte Prozess-Verknüpfunen durch
Aktionen erstellt. Um auch in der Prozess-Demo solche Verknüpfungen erstellen zu können, müssen andere Prozess-Instanzen
in der Prozess-Demo existieren. [Hier erfahren Sie mehr über Demo-Umgebungen.](/{{route}}/{{version}}/environments)

![Simulation](/img/docs/de/simulation/rules-running-simulation.png)

Wird eine Prozess-Demo gestartet, verändert sich das Panel und die graphische Übersicht. Im Panel wird nun die aktuelle
Situation der Prozess-Instanz angezeigt **(1)**. Neben der Situation werden die aktiven Aktionen gelistet **(2)**, die
aktuellen Prozess-Daten **(3)** sowie die erteilten Prozess-Zugriffe **(4)** und der Aktionsverlauf **(5)**.

Die graphische Übersicht zeigt den jeweiligen aktiven Zustand eines Status mit einer gelben Umrandung **(6)**.
Aktive, ausführbare Aktionen haben ein grünen Kreis mit einem "play"-Icon im Titel **(7)**.

<a name="execute-action"></a>

## Aktion ausführen

Um eine Aktion auszuführen kann entweder im "Aktionen" Tab eine Aktion gewählt werden oder direkt das Element in der
graphischen Übersicht. Wenn die Aktion Aktions-Daten besitzt, öffnet sich ein Dialog-Fenster in dem die Datenfelder mit
Werten befüllt werden können **(1)**. Bei vielen Aktions-Datenfeldern ist es hilfreich, die eingegebenen Daten als
Demo-Datensatz für die Aktion abzuspeichern **(2)**, sodass diese nicht für jeden Prozess-Demo-Durchlauf neu eingegeben
werden müssen.

![Simulation](/img/docs/de/simulation/rules-simulation-execute-action.png)

Nach der Aktionsausführung, wird die ausgeführte Aktion im Tab "Verlauf" angezeigt, wie im untenstehenden Screenshot
gezeigt. In der Detailansicht der ausgeführten Aktion **(2)** werden die Statusveränderungen und die gespeicherten
Aktionsdaten angezeigt **(3)**. Die Aktion kann mit dem grünen Button **(4)** rückgängig gemacht werden. In der
graphischen Übersicht ist nun die Aktion "Antrag erfassen" deaktiviert **(5)** und der Zustand "Daten erfasst"
aktiv **(6)**.

![Simulation](/img/docs/de/simulation/rules-simulation-executed-action.png)

<a name="change-user"></a>

## Benutzer wechseln

Im Tab "Zugriffe" werden die aktuell erteilten Zugriffe auf die Prozess-Instanz angezeigt. Dort kann zu einem anderen
Benutzer gewechselt werden, um die Prozess-Demo in dessen Rolle fortzusetzen **(1)**. Im Dropdown-Menü **(2)** werden alle
Benutzer der Prozess-Demo gelistet, auch jene, die eventuell keinen Zugriff auf die Prozess-Instanz haben.
Dort kann auch ein Benutzer gewählt werden, mit dem die Prozess-Demo fortgesetzt wird.

> {info.fa-info-circle} Die Prozess-Demo läuft in einer eigens für die Demo erzeugten Allisa Plattform. 

![Simulation](/img/docs/de/simulation/rules-simulation-change-user.png)

***

[Nächster Artikel: Bereich: Konfiguration](/{{route}}/{{version}}/config)
