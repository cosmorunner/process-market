# Konfiguration ➜ Status

---

- [Einleitung](#introduction)

<a name="introduction"></a>

## Einleitung

Unter "Status" im Konfigurationsbereich wird die Anzeige der Prozess-Situation konfiguriert. Die Prozess-Situation wird
im oberen Bereich einer Prozess-Situation auf der Allisa Plattform angezeigt. Die Konfiguration des Status und der
Status-Zustände findet im Bereich "Regeln & Daten" statt.

![Konfiguration](/img/docs/de/config-status/config-status-example.png)

Der obenstehende Screenshot zeigt die Prozess-Situation einer Prozess-Instanz vom "Montage" Case-Study Prozess.
Untenstehend ist ein Screenshot der Anzeigenkonfiguration.

![Konfiguration](/img/docs/de/config-status/config-status-config.png)

Die Darstellung der Status repräsentiert die Prozess-Situation in einer Prozess-Instanz. Durch das Auswählen eines
Status **(1)** können die Darstellungsoptionen des Status verändert werden. Über die Breitenanzeige links **(2)** kann
die Breite des Status angepasst werden. Mit den Pfeilen rechts **(3)** wird die Reihenfolge verändert.

Untenstehend die Darstellungsoptionen eines einzelnen Status.

![Konfiguration](/img/docs/de/config-status/config-status-single.png)

Für die allgemeine Darstellung kann zwischen zwei Typen gewählt werden **(1)**: "allisa/simple" und "allisa/progress". Mit
"**allisa/simple**" wird der Status wie auf dem obenstehenden Screenshot angezeigt.

![Konfiguration](/img/docs/de/config-status/config-status-example-simple.png)

Der Name des Status wird oben angezeigt und das Icon mit der aktuellen Zustandsbeschreibung unterhalb des Namens. 

Mit dem Typ "**allisa/progress**" kann der Status in Form eines Fortschrittsbalkens angezeigt werden.

![Konfiguration](/img/docs/de/config-status/config-status-example-progress.png)

Beim Fortschrittsbalken wird der Statusname ebenfalls oberhalb angezeigt. Der Fortschritt ist relativ zum unteren
Wertebereich des niedrigsten Zustands und obereren Wertebereich des höchsten Zustandes. Die Hintergrundfarbe stammt aus
dem aktuell aktiven Zustand. 

Das Icon **(2)** definiert ein Standard-Icon für den Status. 
Mit der "Anzeigen"-Option **(3)** kann der Status in der Web-Oberfläche in der Allisa Plattform angezeigt/versteckt werden.

Im nachfolgenden Bereich werden alle Zustände des Status angezeigt. Hier können Icon und Farbe für jeden Zustand gewählt werden **(4)**.
Außerdem werden hier die Zustandsbeschreibung und der Wertebereich der Zustände angezeigt **(5)**.
Alternativ zum Status, können auch einzelne Zustände angezeigt/versteckt werden **(6)**.

***

[Nächster Artikel: Konfiguration ➜ Listen](/{{route}}/{{version}}/config-lists)
