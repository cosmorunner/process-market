# Bereich: Konfiguration

---

- [Einleitung](#introduction)
- [Aktions-Komponenten](#action-components)
- [Aktions-Prozessoren](#actions-processors)
- [Aktions-Kategorien](#action-categories)
- [Status](#status)
- [Listen](#lists)
- [Menü](#menu)
- [Verknüpfungstypen](#relationtypes)
- [Vorlagen](#templates)
- [Demo-Umgebungen](#environments)
- [Events](#events)
- [Listeners](#listeners)
- [JavaScript](#javascript)
- [Demo](#demo)

<a name="introduction"></a>

## Einleitung

Im **Konfigurations-Bereich** des Prozess-Baukastens werden Web-Formulare, Aktions-Prozessoren, Listen, E-Mail Vorlagen,
Demo-Umgebungen und vieles mehr konfiguriert. Der Bereich ergänzt den Bereich "Regeln & Daten", weil dort alles rund um
das Regelwerk konfiguriert wird.

In der Praxis ist es hilfreich zunächst das Regelwerk, inklusive des Datenflusses mittels der Aktions- und
Prozess-Daten, im Bereich "Regeln & Daten" zu modellieren, um anschließend die einzelnen Aktionen mit weiterem Inhalt zu
füllen. Im Konfigurations-Bereich wird an vielen Stellen auf die Aktions- und Prozess-Daten zugegriffen, weshalb eine
vorherige Konzeption des Datenflusses hilfreich ist.

![Regeln & Daten](/img/docs/de/config/config-overview.png)

Der obenstehenden Screenshot zeigt den Konfigurationsbereich des "Industrie 4.0 Montage" Case-Study Prozesses **(1)**. 
In der oberen Leiste **(2)** sind die verschiedenen Bereiche gelistet, welche in den folgenden Artikeln einzeln vorgestellt werden.
Mit den Buttons oben rechts **(3)** kann eine Änderung rückgängig gemacht, oder wiederhergestellt werden. Mit dem Button 
"Regeln & Daten" **(4)** kann direkt in die Detailansicht der Aktion im Bereich "Regeln & Daten" gewechselt werden. 
Der Button "Demo" **(5)** startet eine Simulation des Prozesses. Mit dem Button "Fertigstellen" **(6)** 
wird der aktuelle Entwicklungsstand des Prozesses in einer neuen Version festgehalten. Mit dem
Icon oben rechts **(7)** wird zu den Einstellungen des Prozesses gewechselt.
Unter "Demo-Umgebungen" **(8)** können verschiedene Umgebungen erstellt und mit Beispieldaten befüllt werden, 
welche in der Simulation genutzt werden. Über das Aktionsdropdown **(9)** kann die aktuelle Aktion
zum Bearbeiten ausgewählt werden. Mit dem Icon links neben dem Aktionsdropdown **(10)** kann festgelegt werden, ob das
Formular der Aktion in voller Breite dargestellt werden soll. 

In den folgenden Absätzen werden die Unterbereiche kurz vorgestellt. Weitere Informationen befinden sich in den
einzelnen Artikeln.

<a name="action-components"></a>

## Aktions-Komponenten

Unter "Aktionen" werden die Komponenten der Aktionen konfiguriert. Eine Aktions-Komponente ist zum Beispiel ein
Web-Formular oder eine Liste.

![Konfiguration](/img/docs/de/config-action-component-example.png)

Der obenstehende Screenshot zeigt die Aktion "Auftrag ändern" des "Montage" Case-Study Prozesses in der Allisa
Plattform. Die Aktion besitzt eine "Web-Formular"-Komponente **(1)** mit einem Hinweis und zwei Feldern "Kunden-Nr."
und "Getriebe-Typ".

[Zum Artikel](/{{route}}/{{version}}/action-components)

<a name="action-processors"></a>

## Aktions-Prozessoren

Unter "Aktionen" werden die Komponenten und Prozessoren der Aktionen konfiguriert. Eine Aktions-Komponente ist zum
Beispiel ein Web-Formular oder eine Liste. Aktions-Prozessoren sind Zusatzfunktionen, wie zum Beispiel das Versenden
einer E-Mail oder das Erteilen von Zugriffen auf die Prozess-Instanz.

[Zum Artikel](/{{route}}/{{version}}/action-processors)

<a name="action-categories"></a>

## Aktions-Kategorien

Aktionen können in Kategorien gruppiert werden. Diese werden dann auf der Allisa Plattform in der Aktionsübersicht
**(1)** angezeigt.

![Regeln & Daten](/img/docs/de/config-action-categories.png)

[Zum Artikel](/{{route}}/{{version}}/action-categories)

<a name="status"></a>

## Status

Unter "Status" werden bestimmte Anzeige-Optionen, wie zum Beispiel die Breite, der Status konfiguriert. Das Anlegen und
Definieren der Status-Zustände findet unter "Regeln & Daten" statt.

![Konfiguration](/img/docs/de/config/config-status-overview.png)

[Zum Artikel](/{{route}}/{{version}}/status)

<a name="lists"></a>

## Listen

Im Bereich "Listen" werden alle Prozess-Listen konfiguriert. Eine Prozess-Liste kann zum Beispiel in einer Aktion
angezeigt werden oder durch ein Menü-Punkt in der Prozess-Instanz verlinkt werden.

[Zum Artikel](/{{route}}/{{version}}/lists)

<a name="menu"></a>

## Menü

Jede Prozess-Instanz in der Allisa Plattform kann eigene Menü-Einträge besitzen, welche im Bereich "Menü" konfiguriert
werden. Einzelne Menü-Einträge können konditionell ausgeblendet werden und einzeln für Prozessbeteiligte berechtigt
werden.

![Konfiguration](/img/docs/de/config-menu-overview.png)

Der obenstehende Screenshot zeigt das Menü **(1)** des "Montage" Case-Study Prozesses mit den zwei Einträgen "Übersicht"
und "Daten". Unter "Daten" werden die Prozess-Daten angezeigt.

<a name="relationtypes"></a>

## Verknüpfungstypen

Prozess-Instanzen können miteinander verknüpft werden um Zugehörigkeiten zu repräsentieren. Ein Verknüpfungstyp ist
die "Art" dieser Prozess-Prozess Verknüpfung. In der Praxis ist dies hilfreich, um eine Vielzahl von verknüpften
Prozessen besser unterscheiden zu können und auch, um auf Daten eines verknüpften Prozesses zugreifen zu können.

[Zum Artikel](/{{route}}/{{version}}/relationtypes)

<a name="templates"></a>

## Vorlagen

Mit dem integrierten HTML-Editor werden die Vorlagen für den E-Mail Versand und die PDF-Dokumentenerzeugung erzeugt.
Dabei kann zwischen mehreren, bereits fertig formatierten Vorlagen gewählt werden.

[Zum Artikel](/{{route}}/{{version}}/templates)

<a name="environments"></a>

## Demo-Umgebungen

Demo-Umgebungen werden für die **Prozess-Demo** im genutzt und definieren zugleich, welche Abhängigkeiten der Prozess besitzt. Eine Demo-Umgebung
definiert spezielle Vorgaben an eine Allisa Plattform, welche für Zeitraum die Prozess-Demo erfüllt werden, um
den Prozess-Ablauf in unterschiedlichen Bedingungen zu testen.

Des Weiteren werden durch die erstellen Resourcen in der Demo-Umgebung die **Prozess-Abhängigkeiten an die Allisa
Plattform** definiert.

[Zum Artikel](/{{route}}/{{version}}/environments)

<a name="events"></a>

## Events

Eine Prozess kann "Events" an die Allisa Plattform aussenden, auf die andere Prozess-Instanzen mit "Listenern" reagieren
können. Dadurch wird eine Art automatisierte, "eventgetriebene" Prozessausführung ermöglicht.

[Zum Artikel](/{{route}}/{{version}}/events)

<a name="listeners"></a>

## Listeners

Mit "Listeners" kann auf Events reagiert werden, die durch die Allisa Plattform oder einzelne Prozess-Instanzen
versendet wurden.

[Zum Artikel](/{{route}}/{{version}}/listeners)

<a name="javascript"></a>

## JavaScript

Unter "JavaScript" kann prozessübergreifendes JavaScript definiert werden, welches in allen Aktionen des Prozesses 
verwendet werden kann.

[Zum Artikel](/{{route}}/{{version}}/javascript)

<a name="demo"></a>

## Demo

Mit einer Demo kann der aktuelle Entwicklungsstand des Prozesses in einer echten, produktiven Allisa Plattform
Installation getestet werden. Diese Allisa Plattform wird eigens für die Demo initialisiert und existiert nur für den
Zeitraum der Demo. Mit einer Demo und den Demo-Umgebungen kann der Prozess in unterschiedlichen Szenarien getestet
werden.

[Zum Artikel](/{{route}}/{{version}}/demo)



***

[Nächster Artikel: Konfiguration ➜ Aktions-Komponenten](/{{route}}/{{version}}/action-components)
