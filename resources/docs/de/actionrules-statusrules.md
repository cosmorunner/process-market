# Regeln & Daten ➜ Aktionsregeln & Statusregeln

---

- [Einleitung](#introduction)
- [Aktionsregeln](#actionrules)
- [Aktionsregeln anlegen](#create-actionrule)
- [Statusregeln](#statusrules)
- [Statusregel anlegen](#create-statusrule)

<a name="introduction"></a>

## Einleitung

Aktions- und Statusregeln sind die **Regeln des Prozesses**, weil sie die Bedingungen für eine Aktion festlegen und die
Situationsveränderungen im Prozessverlauf definieren. Aktionsregeln legen fest, in welcher Situation sich der Prozess
befinden muss, sodass eine Aktion freigeschaltet und ausführbar ist. Statusregeln werden bei der Aktionsausführung
angewandt und verändern die Situation des Prozesses.

<a name="actionrules"></a>

## Aktionsregeln

Aktionsregeln sind die Bedingungen für eine Aktionsausführung und fester Bestandteil einer Aktion. Eine
**Aktionsregel ist eine Bedingung, die mit einem Status verknüpft ist.** Diese muss erfüllt sein, damit die Aktion
freigeschaltet ist. Nur freigeschaltete Aktionen können im Prozess ausgeführt werden.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-actionrules-statusrules.png)

Im obenstehenden Screenshot ist die Detailansicht der Aktion "Auftrag starten" des "Montage"-Case-Study Prozesses
abgebildet. Die Aktion hat eine Aktionsregeln **(1)**:

- Status "Auftrag" ist gleich "Daten liegen vor".

Wenn diese Bedingung erfüllt ist, ist die Aktion freigeschaltet und ausführbar. Eine Aktionsregel wird in der
graphischen Übersicht durch einen orangenen Pfeil vom Zustand "Daten liegen vor" zu der Aktion repräsentiert **(2)**.

Eine Aktionsregel setzt sich aus drei Teilen zusammen:

- Status: Eine Aktionsregel bezieht sich auf einen Status.
- Regel: Eine Vergleichsregel - Gleich, nicht gleich, kleiner als, kleiner oder gleich, größer und größer oder gleich.
- Wert: Ein Vergleichswert - Entweder ein Zustand des Status oder ein manuel eingetragener Wert.

Bei der oben geschriebenen Aktionsregel ist "Auftrag" der Status, "ist gleich" die Regel und "Daten liegen vor" der
Vergleichswert.

<a name="create-actionrule"></a>

## Aktionsregel anlegen

Bei einem **Rechtsklick** auf einem Status kann mit "Neue Aktionsregel" eine neue Aktionsregel angelegt werden.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-create-actionrule.png)

Es öffnet sich ein Dialogfenster, in der die Aktion gewählt wird, für die eine Aktionsregel angelegt werden
soll **(1)**, die Regel **(2)** und den Vergleichswert **(3)**. Zusätzlich kann die Regel noch in eine Gruppe **(4)** 
platziert werden.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-create-actionrule-1.png)

Aktionsregel-Gruppen haben den Zweck, die Verknüpfungen der Aktionsregeln zu bestimmen.
Aktionsregeln in der selben Gruppe werden mit dem "UND" verknüpft. Gruppen untereinander werden mit "ODER" verknüpft.
Dadurch lassen sich komplexe Strukturen abbilden, wie z.B. eine UND/ODER Bedingung mit 3 Aktionsregeln:

- "Status 1 ist gleich X." (Gruppe 1)
- UND
- "Status 2 ist gleich Y."(Gruppe 1)
- ODER
- "Status 3 ist gleich Z." (Gruppe 2)

Standardgemäß werden Aktionsregeln in Gruppe 1 platziert, wodurch alle Regeln mit "UND" verknüpft werden.

Nach dem Hinzufügen der Aktion, wird automatisch die graphische Übersicht neu geladen und die entsprechenden
Verknüpfungen (Pfeile) generiert. Der Aktionsregel-Pfeil kann gewählt werden, um die Details der Aktionsregel im Panel
anzuzeigen.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-actionrules-example.png)

<a name="statusrules"></a>

## Statusregeln

Statusregeln legen fest, wie sich die Prozess-Situation nach einer Aktionsausführung verändert. Eine Statusregel kann
einen Status auf einen fest definierten Wert setzen, die Benutzereingabe aus einem Web-Formular nutzen oder konditionell
einen Zustand setzen.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-actionrules-statusrules.png)

Der obenstehenden Screenshot zeigt die Detailansicht der "Auftrag starten" Aktion aus dem "Montage"-Case-Study Prozess.
Die
Aktion hat eine Statusregel **(3)**:

- Setzt "Auftrag" auf "In Bearbeitung".

Wenn die Aktion ausgeführt wird, wird der Status "Auftrag" auf den Zustand "In Bearbeitung" gesetzt. Dies wird durch den
hellblauen Pfeil von der Aktion zum Zustand in der graphischen Übersicht gezeigt **(4)**.

Eine Statusregel bezieht sich stets auf einen Status und setzt sich aus drei Teilen zusammen:

- Status: Der Status dessen Wert verändert wird.
- Regel: Ein Wert kann konkret auf einen Wert gesetzt werden oder um einen numerischen Wert addiert/subtrahiert werden.
- Wert: Der Wert auf den der Status gesetzt wird oder um den der Status addiert/subtrahiert wird.

Bei der oben beschriebenen Statusregel ist "Auftrag" der Status, "setzt" die Regel und "In Bearbeitung" der Wert.

<a name="create-statusrule"></a>

## Statusregel anlegen

Bei einem **Rechtsklick** auf einem Status kann mit "Neue Statusregel" eine neue Statusregel angelegt werden.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-create-statusrule-1.png)

Es öffnet sich ein Dialogfenster in dem zunächst die Aktion ausgewühlt wird, für die die Statusregel angelegt wird **(
1)**.
Als Regel kann entweder "setzen auf", "addieren" oder "subtrahieren" gewählt werden **(2)**. Als Wert kann entweder
direkt ein Zustand, eine Benutzereingabe von einem Web-Formular (Aktions-Datenfeld) oder ein manueller
Wert gewählt werden. Mit "Konditionen" kann konditionell ein Wert ermittelt werden. Die erste gültige Kondition wird bei
der
Statusregel-Auswertung gewählt.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-create-statusrule.png)

Nach dem Hinzufügen der Regel wird diese als hellblauer Pfeil in der graphischen Übersicht angezeigt. Der
Statusregel-Pfeil kann gewählt werden, um die Details der Statusregel im Panel anzuzeigen.

![Aktionsregeln-Statusregeln](/img/docs/de/actionrules-statusrules/rules-statusrule-arrow.png)
***

[Nächster Artikel: Regeln & Daten ➜ Daten](/{{route}}/{{version}}/process-data)
