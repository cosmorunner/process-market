# Bereich: Regeln & Daten

---

- [Einleitung](#introduction)
- [Graph-Elemente](#graph-elements)
- [Panel](#panel)

<a name="introduction"></a>

## Einleitung

Der Bereich **Regeln & Daten** des Prozess-Baukastens ist das Herzstück bei der Prozess-Modellierung. Dort modellieren
Sie das Regelwerk des Prozesses und legen somit den Prozess-Ablauf fest, indem Sie **Bedingungen an Aktionen knüpfen**
(sogenannte Aktionsregeln) und **festlegen wie sich die Prozess-Situation verändert**, wenn eine Aktion
ausgeführt wurde (sogenannte Statusregeln).

![Regeln & Daten](/img/docs/de/rules-example-2.png)

Auf der linken Seite ist eine Übersicht der wichtigsten Regelwerk-Bereiche: Aktionen, Status, Daten und Rollen. Alle 
Bereiche mit ausnahme von Rollen haben oben rechts eine Sucheleiste. Darüber lassen sich Aktionen, Status (pl.) und 
Prozessdatenfelder durchsuchen. Außerdem sind innerhalb von Aktionen die Vorlade- und Aktionsdaten durchsuchbar. 
Im Hintergrund befindet sich die graphische Darstellung des Regelwerkes. Mittels "**Drag and Drop**"können Sie den Graphen 
und dessen Elemente verschieben. Sie können einzelne **Elemente auswählen** um detaillierte Informationen im linken Bereich 
anzuzeigen und mit **Rechtsklick auf ein Graph-Element** öffnet sich bei Status und Regeln (Pfeile) ein Kontext-Menü mit 
Element-spezifischen Bearbeitungsoptionen. Bei Aktionen und Zuständen wird dies über einen **Linksklick**  auf das 
**Hamburger-Menü** (oben rechts) geöffnet.

> {info.fa-info-circle} Klicken Sie auf ein Graph-Element für weitere Informationen.

<a name="graph-elements"></a>

## Graph-Elemente

Die grafische Übersicht des Regelwerkes setzt sich aus verschiedenen Elementen zusammen. Im untenstehenden Screenshot
wird ein Ausschnitt des Regelwerkes vom "allisa/montage"-Prozess gezeigt.

![Regeln & Daten](/img/docs/de/rules/rules-graph-1.png)

Die hellblauen Rechtecke repräsentieren einen **Status(1)**. Die lilafarbenen "Cards" sind **Aktionen (2)** und die
blauen "Cards" innerhalb der Status sind die jeweiligen **Zustände (3)** des Status, wobei der grüne Zustand den Initial-Zustand
und die dunkelblauen Zustände End-Zustände representieren. 
Manche Elemente sind mit Pfeilen miteinander verknüpft. Ein **orangener Pfeil (4) repräsentiert eine Bedingung (Aktionsregel)**. 
Die dortige Aktionsregel definiert, dass die Aktion "Montage pausieren" nur freigeschaltet ist, wenn der Status "Auftrag" 
auf dem Zustand"In Bearbeitung" steht. **Ein blauer Pfeil ist eine Statusregel (5)**. Eine Statusregel verändert den Zustand 
eines Status. Im obigen Beispiel **(5)** setzt die Aktion "Auftrag abschließen" den Status "Auftrag" auf den Zustand
"Abgeschlossen".

Der Graph ist darauf optimiert, die Anzahl an Pfeilen so gering wie möglich zu halten. Deshalb werden manche Zustände in
ein weiteres, hellblaues Rechteck gebündelt, wie es beim "Auftrag" Status mit den Zuständen "Daten liegen vor" und
"In Bearbeitung" der Fall ist. Der orangene Pfeil zu "Auftrag stornieren" startet an diesem hellblauen Rechteckt, was bedeutet,
dass die Aktion "Auftrag stornieren" freigeschaltet ist, wenn der Status "Auftrag" entweder den Zustand "Daten liegen vor"
oder "In Bearbeitung" hat.

<a name="panel"></a>

## Panel

Auf der linken Seite befindet sich ein Panel mit verschiedenen Tabs für die wichtigsten Bereiche des Regelwerkes.

![Regeln & Daten](/img/docs/de/rules/rules-panel.png)

Über den Link **"Konfiguration" (1)** gelangen Sie zum Konfigurationsbereich des Prozess-Baukastens. Mit
**"Fertigstellen" (2)** halten Sie den aktuellen Entwicklungsstand in einer neuen Prozess-Version fest. Unter
**"Demo starten" (3)** können Sie eine Demo mit dem aktuellen Entwicklungsstand unter des Prozesses in der
"Regeln & Daten"-Ansicht starten. Mit dem Icon daneben **(4)**, kann diese Demo direkt in der Allisa Plattform getestet werden.
Die Allisa Plattform wird ausschließlich und nur für den Zeitraum der Demo instanziiert.

Im Tab **"Aktionen" (5)** sehen Sie eine Übersicht aller Aktionen mit ihren Aktions- und Statusregeln. Im Tab
**"Status" (6)** befindet sich eine Übersicht aller Status mit ihren Zuständen. Im **"Daten"-Bereich (7)** bearbeiten
Sie die Prozess-Daten. Im "Rollen"-Bereich definieren Sie die Prozess-Rollen. Standardgemäß hat jeder Prozess eine
"Maintainer"-Rolle, welche alle Rechte im Prozess hat. Üder das **Icon (9)** können Sie weitere Optionen des Prozesses
bearbeiten.

In den folgenden Artikeln werden Ihnen die einzelnen Elemente des Regelwerkes im Detail vorgestellt.

***

[Nächster Artikel: Regeln & Daten ➜ Aktionen](/{{route}}/{{version}}/rules-actions)
