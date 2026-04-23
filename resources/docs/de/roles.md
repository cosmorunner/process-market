# Regeln & Daten ➜ Rollen

---

- [Einleitung](#introduction)
- [Funktionsweise](#function)
- [Rolle erstellen](#create-role)

<a name="introduction"></a>

## Einleitung

Rollen in einem Prozess regeln den Zugriff auf Aktionen, Daten, Menü-Punkte, Listen und vieles mehr. Jede nutzende
Person, die Zugriff auf eine Prozess-Instanz hat, hat die Zugriffsrechte von einer oder mehreren Prozess-Rolle(n).

**Prozess-Rollen werden basierend auf der Fachlichkeit des Prozesses modelliert** und unterscheiden sich somit von
Prozess zu Prozess. Zum Beispiel könnte es bei einem Antrags-Prozess die Rollen "Antragsteller/in" und
"Antragsverarbeiter/in" geben, bei dem die Person, welche den Antrag gestellt hat, die Rolle "Antragsteller/in" in der
Prozess-Instanz einnimmt. Die verantworliche Person für die Antragsverarbeitung würde dann die Rolle "Antragsverarbeiter/in" einnehmen. 
Je nach Rolle können die Personen unterschiedliche Aktionen in dem Antrags-Prozess ausführen.

Rollen werden im Panel unter "Rollen" bearbeitet.

![Rollen](/img/docs/de/roles/rules-roles.png)

Eine Prozess-Rolle kann folgende, allgemeine Rechte besitzen:

- Alle Aktionen ausführen
- Alle Aktionen einsehen
- Alle Prozess-Daten einsehen
- Alle Status einsehen
- Alle Listen einsehen
- Alle Menü-Einträge einsehen
- Alle Aktions-Ausführungen einsehen (Alle Details)
  - Alle Situationsveränderungen einsehen
  - Alle Aktions-Daten einsehen
  - Alle Prozessor-Ausführungen einsehen
  - Alle Aktions-Artefakte einsehen 
      - Alle Dokumente einsehen
      - Alle E-Mails einsehen
- Alle Aktionen exportieren
- Alle Status einsehen
- Alle Listen einsehen
- Alle Artefakte einsehen
- Alle Menüeinträge einsehen
- Aktionen rückgängig machen
- Aktions-Verlauf einsehen

Für einzelne Bereiche eines Prozess können individuell Rechte vergeben werden. Allgemeine Rechte überschreiben die Rechte der Unterbereiche.

Rechte für jede Aktion:

- Aktion ausführen
- Aktion einsehen (Nur lesen)
- Aktion exportieren
- Ausführungen einsehen (Alle Details)
  - Situationsveränderungen einsehen
  - Aktions-Daten einsehen
  - Prozessorausführungen einsehen
  - Alle Aktions-Artefakte einsehen
      - Alle Dokumente einsehen
      - Alle E-Mails einsehen

Rechte für jeden Daten/Status/Liste/Menü-Eintrag:

- Element einsehen

> {info.fa-info-circle} Bei neuen Prozessen wird automatisch die "Maintainer"-Rolle mit allen Rechten angelegt.

<a name="function"></a>

## Funktionsweise

Das Berechtigungssystem der Allisa Plattform sieht vor, dass nutzende Personen immer eine Rolle in einer Prozess-Instanz
einnehmen müssen, um Zugriff auf diese zu erhalten. Alle erteilten Zugriffe werden in der Prozess-Instanz oben rechts
angezeigt.

![Rollen](/img/docs/de/roles/rules-process-access.png)

In dem obenstehenden Screenshot ist eine Demo Prozess-Instanz abgebildet bei der der Benutzer "Philip Gold" in der Rolle
"Maintainer" Zugriff auf die Prozess-Instanz hat. Alle anderen nutzenden Personen und Gruppen haben keinen Zugriff auf
die Prozess-Instanz.

#### Öffentliche Rolle

Eine Ausnahme zu dieser Regel ist die öffentliche Rolle. Die öffentliche Rolle ist eine Prozess-Rolle, die alle nutzenden Personen einnehmen, 
denen nicht explitit Zugriff erteilt wurde. Wird eine Prozess-Instanz angelegt, welche eine öffentliche Rolle hat, 
nehmen automatisch alle nutzende Personen der Allisa Plattform diese Rolle in der Prozess-Instanz ein.

![Rollen](/img/docs/de/roles/rules-process-access-2.png)

Eine aktivierte, öffentliche Rolle wird als Icon oben rechts in der Prozess-Instanz angezeigt.

<a name="create-role"></a>

## Rolle erstellen

Mit dem "Hinzufügen"-Button unter "Rollen" im Panel wird eine neue Rolle angelegt.

![Rollen](/img/docs/de/roles/rules-create-role.png)

Unter "Allgemeine Rechte" **(1)** kann der Rolle vollständige Rechte für bestimmte Bereiche gegeben werden. Bei den
darauf folgenden Optionen können bestimmte Rechte in den Bereichen vergeben werden **(2)**. Ist ein allgemeines Recht
vergeben, haben die bestimmt vergebenen Rechte gegebenenfalls keine Wirkung mehr. Wenn die Rolle beispielsweise "Alle
Aktionen ausführen" als Recht aktiviert hat, haben die Einstellungen unter "Aktionen" keine Auswirkung.
Standardmäßig sind alle Bereiche mt Ausume von "Allgemeine Rechte" eingeklappt. Oben rechts können alle Bereiche auf einmal ein-
und ausgeklappt werden **(3)**.

***

[Nächster Artikel: Regeln & Daten ➜ Optionen](/{{route}}/{{version}}/rules-options)
