# Konfiguration ➜ Menü

---

- [Einleitung](#introduction)
- [Menüpunkt hinzufügen](#create-item)
- [Menüpunkte berechtigen](#access)

<a name="introduction"></a>

## Einleitung

Jeder Prozess besitzt ein eigenes Menü, welches unter "Menü" im Konfigurationsbereich definiert wird. Das Menü wird
seitlich in einer Prozess-Instanz angezeigt **(1)**.

![Menu](/img/docs/de/config-menu-overview.png)

In der Regel verlinkt ein Menüpunkt auf eine Prozess-Liste, Aktion oder zu einem verknüpften Prozess. Der untenstehende
Screenshot zeigt die Menü-Konfiguration des "Montage" Case-Study Prozesses. Mit dem Button **(1)** kann ein neuer
Menüpunkt hinzugefügt werden. Mit den Pfeilen an den Menüpunkten kann diese sortiert werden. Mit dem "x"-Icon wird ein
Menüpunkt entfernt **(2)**.

<a name="create-item"></a>

## Menüpunkt hinzufügen

Über den "Menüpunkt hinzufügen"-Button gelangt man zum Eingabeformular für einen neuen Menüpunkt. Unter "Label" **(1)**
wird der Anzeigenamen definiert. Die "URL"-Option **(2)** erlaubt sowohl eine statische URL als auch die Nutzung von
Syntax-Werten. Bei "Icon" **(3)** wird das Icon festgelegt. Unter "Konditionen" **(4)** können Anzeigebedingungen
definiert werden. Nur wenn die Bedingungen erfüllt sind, wird der Menüpunkt angezeigt.

Jede Bedingung wird einer Gruppe zugeordnet. Bedingungen innerhalb einer Gruppe werden mit "UND" verknüpft. Gruppen
untereinander werden mit "ODER" verknüpft.

![Menu](/img/docs/de/navigation/config-menu-item.png)

<a name="access"></a>

## Menüpunkte berechtigen

Einzelne Menüpunkte können für unterschiedliche Prozess-Rollen berechtigt werden. Die Einstellungen dafür befinden sich
im Bereich "Regeln & Daten" unter "Rollen".

***

[Nächster Artikel: Konfiguration ➜ Verknüpfungstypen](/{{route}}/{{version}}/relationtypes)
