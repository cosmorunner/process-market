# Regeln & Daten ➜ Optionen

---

- [Einleitung](#introduction)
- [Standard-Rollenzuordnung](#default-role)
- [Öffentlichen Rolle](#public-role)
- [Liste für Prozessverlauf](#history-list-config)
- [Icon](#icon)
- [Automatische Referenzerzeugung](#reference)

<a name="introduction"></a>

## Einleitung

In den Optionen unter Regeln & Daten können Einstellungen zur Standardrolle, öffentlichen Rolle, Icon und automatischen
Referenzerzeugung getätigt werden.

![Rollen](/img/docs/de/rules-options/rules-options.png)

Über das Icon im Panel **(1)** werden weitere Optionen für den Prozess eingeblendet.

<a name="default-role"></a>

#### Standard-Rollenzuordnung (2)

Diese Rolle nimmt die nutzende Person in einer neu erzeugten Prozess-Instanz ein, wenn ihr nicht explizit Zugriff
erteilt wurde. Einer nutzenden Person wird mit dem Aktions-Prozessor "Zugriff erteilen" explizit Zugriff auf eine
Prozess-Instanz erteilt. **Hat die nutzende Person nach der Prozess-Instanz Erstellung keinen Zugriff erteilt bekommen,
wird die Standard-Rolle für den Zugriff verwendet.** Die öffentliche Rolle wird an dieser Stelle nicht gewertet, sodass
die nutzende Person möglicherweise nach der Prozess-Instanz Erstellung sowohl Zugriff mittels der Standard-Rolle als
auch mittels der öffentlichen Rolle erteilt bekommen hat.

![Rollen](/img/docs/de/rules-options/rules-options-default-role.png)

Mit einer gesetzten Standard- und öffentlichen Rolle erhält die nutzende Person einmal expliziten Zugriff **(1)**
aufgrund der Standard-Rolle und impliziten Zugriff aufgrund der öffentlichen Rolle **(2)**.

<a name="public-role"></a>

#### Öffentlichen Rolle (3)

Wenn gesetzt, erhält jede nutzende Person Zugriff auf die Prozess-Instanz in der gesetzten Rolle. Dieser sogenannte
"implizierter" Zugriff unterscheidet sich vom "expliziten Zugriff" in der Hinsicht, dass die nutzenden Personen nicht
durch eine Aktionsausführung Zugriff erteilt bekommen haben, sondern aufgrund der Prozessmodellierung.

![Rollen](/img/docs/de/rules-options/rules-options-public-role.png)

Ein Zugriff mittels einer öffentlichen Rolle wird in der Prozess-Instanz durch das "Globus"-Icon repräsentiert.

<a name="history-list-config"></a>

#### Liste für Prozessverlauf (4)

Der Verlauf einer Prozess-Instanz wird in der Allisa Plattform unterhalb der Aktionsübersicht angezeigt.

![Rollen](/img/docs/de/rules-options/rules-options-history-list-config.png)

Standardgemäß zeigt die Liste die ausgeführten Aktionen mit dem Aktionsnamen, dem Benutzer und dem Zeitpunkt. Mit der
Option
"Liste für Prozessverlauf" kann diese Liste mit einer Prozessliste der Vorlage "Ausgeführte Aktionen" ausgetauscht
werden.

<a name="icon"></a>

#### Icon (4)

Das Icon wird für alle neuen Prozess-Instanzen genutzt und wird vor dem Namen in der Prozess-Instanz Ansicht angezeigt.
Der Aktions-Prozessor "Prozess-Metadaten ändern" kann dieses Icon aktualisieren.

<a name="reference"></a>

#### Automatische Referenzerzeugung (5)

Die automatische Referenzerzeugung hilft dabei, Prozess-Referenzen (Aktenzeichen) zu generieren. Die Prozess-Referenz
wird bei der Prozess-Instanz Erzeugung generiert. Über das Dropdown-Menü beim "+"-Icon können individuelle Formate
erstellt werden. Dabei ist zu beachten, dass die Referenz **pro Prozesstyp** einzigartig sein muss. Verschiedene
Prozesstypen
können auch gleiche Referenzen haben.

![Rollen](/img/docs/de/rules-options/rules-options-reference.png)

In dem obenstehenden Beispiel wird die vierstellige Jahreszahl und der aktuelle UNIX Zeitstempel mit einem "-"
verknüpft.
Die Prozess-Referenz wird unterhalb des Prozess-Namens angezeigt.

![Rollen](/img/docs/de/rules-options/rules-options-reference-example.png)

***

[Nächster Artikel: Regeln & Daten ➜ Simulation](/{{route}}/{{version}}/simulation)
