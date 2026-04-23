# Prozess-Digitalisierung - Grundlagen

---

- [Einleitung](#introduction)
- [Statusorientierte Prozesse](#statusoriented-processes)
- [Regeln & Daten](#rules-and-data)
- [Konfiguration](#config)
- [Versionierung](#versions)

<a name="introduction"></a>

## Einleitung

Mit dem integrierten Prozess-Baukasten digitalisieren Sie in der Prozessfabrik sowohl einfache Prozesse, als auch
komplexe Fachverfahren. Ziel dieses Artikels ist, die wichtigsten Bereiche des Prozess-Baukasten vorzustellen und
grundlegende Konzepte der Prozess-Modellierung in der Prozessfabrik aufzuzeigen.

<a name="statusoriented-processes"></a>

## Statusorientierte Prozesse

Prozesse in der Prozessfabrik sind **statusorientierte Prozesse**. Dies bedeutet, dass sich der **mögliche
Prozessablauf an Status (pl.) orientiert**. Je nach aktueller Prozess-Situation sind Aktionen entweder freigeschaltet
oder blockiert. Wird eine Aktion ausgeführt **verändert sich die Prozess-Situation** und Aktionen werden freigeschaltet
oder blockiert. Dieses gundlegende Verhalten von statusorientierten Prozessen hat zur Folge, dass man bei der
Prozess-Modellierung sehr flexibel ist und alles, was einen Zustand besitzt, in Form eines statusorientieren Prozesses
modelliert werden kann.

Sie erstellen Regelwerke, die sich wie herkömmliche Prozesse, Abläufe oder Anträge verhalten.
**Regelwerke mit zahlreichen Zusatzfunktionen** wie beispielsweise einer einfachen Webformular-Konfiguration,
E-Mail-Versand, Dokumentenerzeugung, Schnittstellen-Aufrufe, Datenpersistenz, Reporting, Statistik-Funktionen und vieles
mehr.

Im Bezug auf den Prozess-Baukasten unterscheiden wir in zwei Haupt-Bereiche: **Regeln & Daten** und
**Konfiguration**. Im Bereich "Regeln & Daten" modellieren Sie das Regelwerk des Prozesses und definieren, welche
Datenfelder im Verlauf des Prozesses erzeugt werden und welche Aktionen diese Daten nutzen oder verändern. Im
Konfigurations-Bereich konfigurieren Sie die Webformulare der Aktionen, Listen, Aktions-Zusatzfunktionen
(sogenannte Prozessoren), das Prozess-Menü und vieles mehr.

<a name="rules-and-data"></a>

## Regeln & Daten

Der Bereich **Regeln & Daten** des Prozess-Baukastens ist das Herzstück bei der Prozess-Modellierung, weil Sie dort das
Regelwerk des Prozesses modellieren. Das Regelwerk definiert den Prozess-Ablauf, indem Sie **Bedingungen an Aktionen
knüpfen** (sogenannte Aktionsregeln) und **festlegen wie sich die Prozess-Situation verändert**, wenn eine Aktion
ausgeführt wurde (sogenannte Statusregeln).

![Regeln & Daten](/img/docs/de/rules-example-2.png)

Sie erhalten eine Übersicht über das Regelwerk des Prozesses und bearbeiten dieses in einer grafischen Oberfläche. Des
Weiteren können Sie das Regelwerk jederzeit simulieren um sicherzustellen, dass der Prozess-Ablauf gemäß der fachlichen
Vorgabe korrekt ist.

<a name="config"></a>

## Konfiguration

Im Konfigurations-Bereich konfigurieren Sie Webformulare, Listen, das Prozess-Menü, HTML-Vorlagen und vieles mehr. Sie
befüllen das Prozess-Regelwerk mit weiterem Inhalt, indem Sie beispielsweise Webformulare erstellen und HTML-Vorlagen
definieren, die Sie für den E-Mail Versand nutzen.

![Konfiguration](/img/docs/de/rules-basics/config-example.png)

Der untenstehenden Screenshot zeigt eine Prozess-Instanz aus der Case-Study "Montage"-Prozess. Dort sind beispielhaft
drei Bereiche markiert, die Sie im obigen Konfigurationsbereich definieren (siehe "Menü", "Status" und "
Aktions-Kategorien").

![Demo](/img/docs/de/rules-basics/demo-example-with-numbers.png)

1. Die Menü-Einträge in der Prozess-Instanz werden im Konfigurationsbereich unter "Menü" konfiguriert.
2. Der Typ und die Breite der Status (Die Zustände werden unter "Regeln & Daten" bearbeitet) werden unter "Status" verändert.
3. Die Gruppierung der Aktionen in Kategorien wird unter "Aktions-Kategorien" definiert.

<a name="versions"></a>

## Versionierung

Analog zur Software-Entwicklung werden auch in der Prozessfabrik die **modellierten Prozesse versioniert**. Eine
fertiggestellte Version markiert einen Meilenstein in der Entwicklung des Prozesses und beinhaltet ein sogenannten
"Changelog", welches die Änderungen zur Vorgängerversion aufzeigt. Die fertiggestellte Version kann nachträglich nicht
mehr bearbeitet werden.

![Versionen](/img/docs/de/rules-basics/versions-example.png)

Bei jeder fertiggestellten Version, wird eine neue "In der Entwicklung"-Version angelegt. Diese Version wird genutzt, um
weiter an dem Prozess zu entwickeln. Wird nun ein Prozess in eine Allisa Plattform exportiert, wird stets nur eine
bestimmte Version des Prozesses exportiert.

Jede Prozess-Instanz in einer Allisa Plattform basiert auf einer bestimmten Version eines Prozesses und wird
nachträglich, **auch nach Import einer aktuelleren Version, nicht auf eine neuere Version aktualisiert**.

Eine Versions-Nummer besteht aus drei Zahlen, getrennt mit einem Punkt, z.B. 1.0.0, und folgt, analog zur
Software-Entwicklung, einer semantischen Struktur:

- MAJOR.MINOR.PATCH
    - MAJOR wird erhöht, wenn es Änderungen am Prozess gab, die NICHT mit Vorgänger-Versionen kompatibel sind, z.B.
      Änderungen am Regelwerk oder neue Aktionen bzw. Status.
    - MINOR wird erhöht, wenn es neue Änderungen am Prozess gab, die kompatibel mit Vorgänger-Versionen sind, z.B. ein
      neuer Menü-Punkt, ein gelöschter Prozess-Datensatz. Kann diese Prozess-Instanz zu der neuen Version migriert
      werden, ohne dass die Prozess-Instanz "unbrauchbar/korrupt" wird?
    - PATCH wird erhöht, wenn es kleinere Änderungen oder Fehler gab, z.B. Tippfehler, Änderungen an einer Icon-Farbe,
      Listenspalten-Anpassungen oder Änderungen eines Aktions-Namens. Änderungen, die nicht den Ablauf des Prozesses
      beeinflussen.

***

[Nächster Artikel: Bereich: Regeln & Daten](/{{route}}/{{version}}/rules)
