# Mein erster Prozess

---

- [Einleitung](#welcome)
- [1. Prozess anlegen](#create-process)
- [2. Aktionen anlegen](#create-actions)
- [3. Status anlegen](#create-status)
- [4. Aktions- & Statusregeln anlegen](#create-rules)
- [5. Prozess simulieren](#simulation)
- [6. "Anlegen"-Formular](#webform)
- [7. Demo starten](#start-demo)
- [8. Initial-Aktion "Anlegen"](#initial-action)
- [9. Prozess-Metadaten ändern](#update-process-meta)
- [10. Fertigstellen und exportieren](#complete-export)

<a name="welcome"></a>
## Willkommen

In diesem Tutorial lernen Sie die Grundlagen der Prozess-Modellierung an einem kleinen Beispiel-Prozess kennen.
Der Prozess ermöglicht es uns Aufgaben zu erfassen, diese zu priorisieren und als "Erledigt" zu vermerken. 
Die Modellierung umfasst das Erstellen der Aktionen, Status und Regeln. Mithilfe der integrierten Simulations- 
und Demofunktion können wir den Ablauf testen und so sicherzustellen, dass der Prozess ordnungsgemäß funktioniert.

> {info.fa-info-circle} Das Tutorial nimmt ca. 20-30 Minuten in Anspruch. 

<a name="create-process"></a>
## 1. Prozess anlegen

Wir starten damit einen neuen Prozess anzulegen indem wir in der Hauptmenü-Leiste auf "Neu" klicken [[zu "Neu"](/processes/create)].

![Prozess anlegen](/img/docs/de/my-first-process/create-process-tutorial.png)

Beim "Titel" geben wir "Aufgabe" an und beim Namespace den Namespace von unserem Profil (1). Falls man einer Organisation angehört, könnten wir
den Prozess auch der Organisation zugehörig machen, indem wir den Namespace einer Organisation wählen. Bei "Identifikation" (2) tragen wir
ebenfalls "aufgabe" ein. Der Namespace und die Identifikation eines Prozesses sind einzigartig und können nachträglich **nicht mehr verändert
werden**. Nach dem Erstellen, gelangen wir automatisch zu **Regeln & Daten**.

![Regeln und Daten](/img/docs/de/my-first-process/rules-and-data.png)

Im Bereich **Regeln & Daten** wird das Regelwerk modelliert und der **Datenfluss** der Aktionen konfiguriert.

<a name="create-actions"></a>
## 2. Aktionen anlegen

Unser Aufgaben-Prozess besitzt drei Aktionen:
1. **Anlegen**: In dieser Aktion wird der Aufaben-Name und die Priorität erfasst. Später legen wir fest, dass dies die "Initial-Aktion" ist, mit der
jede neue Aufgaben Prozess-Instanz startet.
2. **Als erledigt markieren**: Mit dieser Aktion markieren wir die Aufgabe als erledigt. Die Aufgabe ist dann abgeschlossen.
3. **Stornieren**: Diese Aktion gibt uns die Möglichkeit, eine Aktion abzubrechen, falls sie beispielsweise nicht mehr relevant ist.

Mit einem **Rechtsklick** auf die freie Fläche legen wir die drei Aktionen an.

![Aktion anlegen](/img/docs/de/my-first-process/create-action-rightclick.png)

Alternativ kann der Button "+Hinzufügen" im Tab "Aktionen" links gewählt werden.

![Aktion anlegen](/img/docs/de/my-first-process/create-action-detail.png)

Der Aufaben-Prozess hat nun drei Aktionen. Per **Drag and Drop** können wir die lilafarbenen Aktions-Kreise auf der grafischen Oberfläsche bewegen,
sodass sie geordneter platziert sind.

![Alle drei Aktionen](/img/docs/de/my-first-process/todo-three-actions.png)

<a name="create-status"></a>
## 3. Status anlegen

Die Situation eines Aufgaben-Prozesses setzt sich aus zwei Status zusammen: Ergebnis und Priorität. Die beiden Status haben folgende
Zustände mit ihren Wertebereichen (Min./Max.) in den Klammern:
- **Fortschritt**
  - Nicht definiert (0-0) 
  - Offen (1-1)
  - Erledigt (2-2)
  - Storniert (3-3)
- **Priorität**
  - Offen (0-0)
  - Niedrig (1-1)
  - Mittel (2-2)
  - Hoch (3-3)

Sind Min. & Max. gleich wird im Zustand der entsprechende Wert angezeigt, ansonsten der Wertebereich.
Wir legen einen Status an, indem wir mit **Rechtsklick** auf die grafische Fläche klicken. 

![Status erstellen](/img/docs/de/my-first-process/create-status.png)

Alternativ kann der "+Hinzufügen"-Button im Tab "Status" gewählt werden.

![Status erstellen](/img/docs/de/my-first-process/create-status-detail.png)

Wir tragen jeweils den Namen der Status ein. **Beide Status haben den Initialwert 0**.
Hier wird nun automatisch ein `Start`-Zustand mit dem Initialwert erstellt.
Anschließend legen wir für beide Status die jeweiligen fehlenden Zustände an, indem
mit **Rechtsklick** auf einen Status geklickt wird und dann "Neuer Zustand" gewählt wird.
Die Werte für diese Zustände werden automatisch bestimmt.

![Zustand erstellen](/img/docs/de/my-first-process/create-states.png)

![Zustand erstellen](/img/docs/de/my-first-process/create-state-prio.png)

Nachdem wir alle acht Zustände angelegt haben, sollten wir folgende Übersicht erhalten:

![Alle Status](/img/docs/de/my-first-process/all-status.png)

Die grünen Zustände "Nicht definiert" und "Offen" geben an, dass diese Zustände die 
Initial-Zustände (Startzustand) der jeweiligen Status sind.

<a name="create-rules"></a>
## 4. Aktions- & Statusregeln anlegen

Es sind nun alle Aktionen und Status angelegt. Die drei Aktionen sind aktuell "freie Aktionen" weil sie keinerlei Regeln haben. Sie
sind an keine "Bedingungen" geknüpft und verändern auch nicht die Prozess-Situation wenn sie ausgeführt werden. Wir nennen die "Bedingungen", die
an Aktionen geknüpft werden **Aktionsregeln** weil sie festlegen, unter welchen Situations-Bedingungen die Aktion ausgeführt werden kann.
Wir nennen die Regeln, die die Situation des Prozesses verändern **Statusregeln**, weil sie die Status der Prozess-Instanz verändern.

Beispielsweise wollen wir festlegen, dass die Aktion "Anlegen" nur möglich ist, wenn der Status "Ergebnis" auf "Nicht definiert" steht und
auch die "Priorität" noch "Offen" ist.
Wie auch die Aktionen und Status, sind auch die Aktions- und Statusregeln sehr von der Fachlichkeit des Prozesses abhängig.

Folgende Aktionsregeln fügen wir für die Aktionen hinzu:
- **Anlegen**
  - Wenn "Ergebnis" gleich "Nicht definiert".
  - Wenn "Priorität" gleich "Offen".
- **Als erledigt markieren**
  - Wenn "Ergebnis" gleich "Offen".
- **Stornieren**
  - Wenn "Ergebnis" gleich "Offen".

Eine Aktionsregel gehört stets zu einer Aktion und ist an einen Status gebunden. Mit einem **Rechtsklick** auf die weiße Fläche
vom Status "Ergebnis" legen wir eine Aktionsregeln an.

![Aktionsregel anlegen](/img/docs/de/my-first-process/create-actionrule.png)

Im Dialogfenster wählen wir die Aktion "Anlegen" mit der Regel "ist gleich" und dem Zustand "Nicht definiert".

![Aktionsregel anlegen](/img/docs/de/my-first-process/create-actionrule-dialog.png)

Es bildet sich ein orangener Pfeil vom Zustand "Nicht definiert" zur Aktion "Anlegen". Die Aktion "Anlegen" wird zur 
"Ergebnis"-Statusfläche hinzugefügt.

![Aktionsregel anlegen](/img/docs/de/my-first-process/actionrule.png)

Wir fügen die weiteren drei Aktionsregeln hinzu (siehe oben) und erhalten folgende grafische Übersicht:

![Aktionsregel anlegen](/img/docs/de/my-first-process/actionrule-all.png)

Also fügen wir die **Statusregeln** hinzu. Wenn eine Aktion ausgeführt wird, verändern Statusregeln die Prozess-Situation, wodurch
manche Aktionen freigeschaltet und andere Aktionen blockiert werden. Auf diesem Grund nennen wir Allisa Prozesse auch
**statusorientierte Prozesse** weil sich die Aktionen an Status orientieren.

Folgende Statusregeln besitzen die Aktionen:
- **Anlegen**
    - Setzt "Ergebnis" auf "Offen".
    - Setzt "Priorität" auf den Wert des Feldes "prioritaet".
- **Als erledigt markieren**
    - Setzt "Ergebnis" auf "Erledigt".
- **Stornieren**
    - Setzt "Ergebnis" auf "Storniert".

In der Regel setzen Statusregeln einen Status direkt auf einen bestimmten Zustand (Setzt "Ergebnis" auf "Offen"). Manchmal
wollen wir jedoch einen Eingabewert der nutzenden Person verwenden, um einen Status zu setzen 
(Setzt "Priorität" auf den Wert des Feldes "prioritaet".). Legen wir zunächst die "statischen" Statusregeln fest, die keine 
Benutzereingabe benötigen, indem mit einem **Rechtsklick** auf den jeweiligen Status geklickt wird.

![Statusregel anlegen](/img/docs/de/my-first-process/create-statusrule.png)

Wir wählen die enstprechende Aktion aus und die Regel mit dem Zustand.

![Statusregel anlegen](/img/docs/de/my-first-process/create-statusrule-detail.png)

Nach dem Anlegen bildet sich ein blauer Pfeil von der "Anlegen" Aktion zum "Offen" Zustand. Dies sagt aus, dass durch das Ausführen
der "Anlegen" Aktion der Status "Ergebnis" auf den Zustand "Offen" gesetzt wird. 

![Statusregel Pfeil](/img/docs/de/my-first-process/statusrule-arrow.png)

Wir fügen die weiteren zwei Statusregeln hinzu und erhalten folgende grafische Übersicht (mit etwas Restrukturierung).

![Statusregeln](/img/docs/de/my-first-process/statusrule-all.png)

Es fehlt nun noch folgende Statusregel für die "Anlegen" Aktion: Setzt "Priorität" auf den Wert des Feldes "prioritaet".
Da der Wert, auf dem der Status "Priorität" nach der Aktionsausführung ein Eingabewert ist, müssen wir bei der "Anlegen" Aktion
zunächst ein **Aktions-Datenfeld** anlegen. Aktions-Datenfelder geben uns eine strukturierte Übersicht aller Daten, die eine Aktion
"erzeugen" kann. Diese Daten können wir dann im Prozess speichern und nutzen.

![Aktionsdaten anlegen](/img/docs/de/my-first-process/create-action-data.png)

Klicken Sie mit **Linksklick** auf die "Anlegen" Aktion in der grafischen Übersicht oder wählen Sie sie links aus dem "Aktionen" Tab.
Klicken Sie dort dann auf das "+"-Icon bei "Daten".

![Aktionsdaten anlegen](/img/docs/de/my-first-process/action-data-field.png)

Tragen Sie bei Name "prioritaet" ein und aktivieren Sie unten bei "Validatoren" die "Pflichtfeld" Checkbox. Bei "Beliebige Zeichen"
tragen Sie "1,2,3" ein, weil dies den möglichen Prioritäts-Werten entspricht. Nun können wir beim **Priorität**-Status die letzte
Statusregel hinzufügen. Anstelle eines Zustandes wählen wir **Aktions-Datenfeld** und wählen dort das "prioritaet"-Feld.

![Aktionsdaten anlegen](/img/docs/de/my-first-process/dynamic-statusrule.png)

Zuletzt erhalten wir folgende Übersicht:

![Alle Regeln](/img/docs/de/my-first-process/all-rules.png)

Der Pfeil von "Anlegen" zu den drei Prioritäts-Zuständen wurde gesetzt, weil aufgrund der Validierungsregel beim "prioritaet"-Datenfeld
ausschließlich die Werte "1", "2" und "3" erlaubt sind. Diese Werte entsprechen den Zustandsbereichen (Min./Max) von "Niedrig", "Mittel"
und "Hoch". Zusätzlich wurden die drei Zustände für eine bessere Übersicht in eine blaue Box gruppiert. 
Dies bedeutet, dass der Status "Priorität" auf einen der drei Zustände gesetzt wird, je nach Benutzereingabe.

<a name="simulation"></a>
## 5. Prozess testen

Das Regelwerk unseres Aufgaben-Prozesses ist nun fertiggestellt. Um sicherzustellen, dass alle Regeln korrekt sind, 
können wir das Regelwerk testen. Dafür starten wir eine neue Demo mit dem Button "Demo starten" 
in der Standard-Rolle "Maintainer". 

![Start simulation](/img/docs/de/my-first-process/start-simulation.png)

Die Ansicht wechselt in den "Simulation"-Modus und sowohl die Tabs links als auch die grafische Darstellung ändert sich. 

![Simulation](/img/docs/de/my-first-process/simulation.png)

1. Die aktuelle Situation des Prozesses. "Priorität" und "Ergebnis" haben ihre Initial-Zustände.
2. Der aktuell aktive Zustand des Status, in diesem Fall "Nicht definiert" vom Status "Ergebnis".
3. Eine freigeschaltete Aktion.
4. Eine aufgrund der Aktionsregeln blockierte Aktion (benötigt den Zustand "Offen").
5. Der Prozessverlauf. Dort können Aktionen rückgängig gemacht werden.

Mit einem **Linksklick** auf eine grüne Aktion führen wir die Aktion "Anlegen" aus. Es öffnet sich ein Dialogfenster, weil wir
die Priorität angeben müssen.

![Simulation](/img/docs/de/my-first-process/simulation-execute-action.png)

Wir führen die Aktion aus und sehen anschließend, dass sich die Prozess-Situation verändert hat.

![Simulation](/img/docs/de/my-first-process/simulation-execute-action-2.png)

Im Tab "Verlauf" können Sie Aktionen rückgängig machen, um andere Eingabewerte oder "Prozess-Pfade" zu testen.

![Simulation](/img/docs/de/my-first-process/simulation-history.png)

<a name="webform"></a>
## 6. "Anlegen"-Webformular

Da das Regelwerk unseres Aufgaben-Prozesses nun fertiggestellt ist, schauen wir uns im nächsten Schritt das
Webformular der "Anlegen" Aktion an. Der Aufgaben-Prozess sieht vor, dass die nutzende Person die "Anlegen" 
Aktion als Initial-Aktion öffnet, dort den Namen der Aufabe und die Priorität festlegt, und dann die neue Aufabe 
mit diesen Daten erstellt wird. Da auch der **Name der Aufabe** eine Benutzereingabe ist, die wir nutzen möchten,
müssen wir auch dafür ein **Aktions-Datenfeld** bei der "Anlegen"-Aktion hinzufügen. Wir klicken auf 
das "+"-Icon im Bereich "Daten", tragen im Dialogfenster bei Name "name" ein und machen dies ebenfalls zu ein Pflichtfeld
im Bereich "Validierung".

![Name Aktionsdatenfeld](/img/docs/de/my-first-process/action-data-field-2.png)

Nun erstellen wir das **Web-Formular für die "Anlegen" Aktion**. Wir wechseln in den "Konfiguration"-Bereich, indem wir
links oben auf "Konfiguration" klicken.

![Wechsel zum Konfigurationsbereich](/img/docs/de/my-first-process/switch-to-config.png)

Der "Konfiguration"-Bereich umfasst alles, was nicht Teil des Prozess-Regelwerkes und Daten ist. Dort werden
Aktions-Webformulare, Prozess-Listen, Prozess-Menü-Einträge, Verknüpfungstypen und Demo-Umgebungen erstellt.

![Konfiguration - Aktionen](/img/docs/de/my-first-process/config-actions.png)

Wir bleiben im Bereich "Aktionen" (1) und wählen rechts im Auswahlfeld (2) "Anlegen"

![Konfiguration - Neue Komponente](/img/docs/de/my-first-process/config-new-component.png)

Unter "Komponente hinzufügen" wählen wir "Formular - allisa/form". Das Label ist optional. Im Anschluss fügen wir mithilfe 
des "+ Feld"-Buttons folgende zwei Felder hinzu:

![Konfiguration - Name Feld](/img/docs/de/my-first-process/config-name-field.png)

Ein **Text-Feld** für den Namen der Aufgabe. Das Feld heißt **"name""** weil das Aktions-Datenfeld ebenfalls "name" ist. Damit wird
der Wert des Feldes auf das Aktions-Datenfeld geschrieben.

![Konfiguration - Priorität Feld](/img/docs/de/my-first-process/config-prio-field.png)

Zusätzlich ein **Auswahl-Feld** mit dem Namen **"prioritaet"** und vier Optionen. Die Werte der Optionen entsprechen den
Zustandsbereichen des Priorität-Status (1 = Niedrig, 2 = Mittel, 3 = Hoch).

![Konfiguration - Form](/img/docs/de/my-first-process/config-simple-form.png)

Unser fertiges Formular hat nun zwei Felder,
"name" und "prioritaet" (1). Mit dem grünen Blitz-Icon (2) auf der rechten Seite erkennen wir, dass die Eingabewerte 
der beiden Felder in den passenden Aktions-Datenfeldern (ebenfalls "name" und "prioritaet") gespeichert werden.

<a name="start-demo"></a>  


Im Bereich "Status" können die Icons der Status sowie die Icons und Farbe deren Zustände eingestellt werden.

![Status - Konfig](/img/docs/de/my-first-process/state-styling.png)
![Status - Konfig](/img/docs/de/my-first-process/state-styling-detail.png)

## 7. Demo starten

Analog zu der vorherigen Simulation, können wir mithilfe der **integrierten Demo-Funktionalität** unseren Aufgaben-Prozess
in einer Demo Allisa Plattform testen. Dafür wählen wir **im Konfigurationsbereich oben rechts "Demo"**.

![Konfiguration - Demo](/img/docs/de/my-first-process/start-demo-1.png)

Wir wählen die Standard "Maintainer"-Rolle und starten die Demo.


![Konfiguration - Demo](/img/docs/de/my-first-process/start-demo.png)

Ausschließlich für diese Demo wird eine **komplette Allisa Plattform Instanz gestartet** und in die Prozessfabrik
temporär eingebungen. Der Aufgaben-Prozess wird dorthin exportiert und eine Demo-Prozess-Instanz angelegt. 
Wir testen in einer **echten, produktiven Umgebung** und können somit sicherstellen, dass der Prozess nach unserer
Vorstellung funktioniert.

![Konfiguration - Demo](/img/docs/de/my-first-process/demo.png)

Wir wählen im mittleren Bereich die Aktion "Anlegen", wodurch sie geöffnet wird. Dort erscheint unser soeben erstelltes
Web-Formular mit den zwei Feldern.

![Konfiguration - Demo](/img/docs/de/my-first-process/demo-form.png)

Wir tragen einen Namen ein und wählen eine Priorität. Anschließend führen wir die Aktion aus.

![Konfiguration - Demo](/img/docs/de/my-first-process/demo-executed.png)

Wir gelangen zurück zur Prozess-Instanz Übersicht und sehen, dass sich die Prozess-Situation verändert hat (1), neue 
Aktionen freigeschaltet sind (2) und dass es einen Eintrag im Prozess Verlauf gibt (3).

> {info.fa-info-circle} Es kann zwischen der "Simulation" und "Demo" direkt gewechselt werden, indem oben der Link
> "Im Regelwerk fortsetzen" bzw. "In Demo fortsetzen" gewählt wird.

**Wir beenden die Demo** mit dem Button "Demo beenden".

Im folgenden Abschnitt konfigurieren wir eine einfache **Demo-Umgebung** mit der wir festlegen, dass wir zu Beginn des 
Prozesses mit der "Anlegen" Aktion starten wollen.

<a name="initial-action"></a>
## 8. Initial-Aktion "Anlegen"

Beim Starten einer Demo oder Simulation fällt auf, dass die Prozess-Instanz direkt mit dem Namen "Demo-Instanz"angelegt wird.
Wir möchten allerdings eine neue Aufgabe **mit der "Anlegen"-Aktion starten**, in der wir den Namen der Aufgabe 
und die Priorität festlegen. Dafür müssen wir eine Demo-Umgebung anlegen. **Demo-Umgebungen** ermöglichen es uns, 
den Prozess in verschiedenen Szenarien zu testen. In diesem Tutorial belassen wir es bei einer einfachen Demo-Umgebung, 
bei der die Initial-Aktion die "Anlegen"-Aktion ist.

Im Konfigurations-Bereich navigieren wir zu "Demo-Umgebungen" und wählen dort "Umgebung anlegen".

![Konfiguration - Umgebung anlegen](/img/docs/de/my-first-process/config-create-env.png)

Bei Name tragen wir "Standard" ein und wählen bei "Initiale Aktion" die "Anlegen"-Aktion.

![Konfiguration - Umgebung anlegen](/img/docs/de/my-first-process/config-env-default.png)

Nach dem Speichern der Demo-Umgebung erhalten wir folgende Übersicht.

![Konfiguration - Umgebungen](/img/docs/de/my-first-process/config-env-overview.png)

Wenn wir nun eine neue Demo starten, können wir die Standard-Umgebung auswählen. Wir gelangen direkt zur "Anlegen"-Aktion,
noch bevor ein neue Aufgaben-Prozess-Instanz erzeugt wurde. Wir befüllen das Formular und wählen "Ausführen"

![Konfiguration - Demo](/img/docs/de/my-first-process/demo-executed.png)

Wir haben nun eine Aufgaben-Prozess-Instanz mit einer Initial-Aktion erstellt. Auch hier sehen wir die akuelle 
Prozess-Situation (1), die freigeschalteten Aktionen (2) und den Prozess-Verlauf (3).

Allerdings fällt uns auf, dass der Name der Prozess-Instanz noch "Demo-Instanz" ist, und nicht der eingetragene
Name des Formulares in der "Anlegen"-Aktion. Dies ist unser letzter Konfigurations-Schritt für diesen Prozess.

<a name="update-process-meta"></a>
## 9. Prozess-Metadaten ändern

Wir wollen definieren, dass der Wert aus dem "name"-Feld der "Anlegen"-Aktion als Prozess-Instanz Name genutzt 
wird (anstelle Demo-Instanz). Dafür nutzen wir einen **Aktions-Prozessor**. Aktions-Prozessoren sind Teil einer
Aktion und können viele verschiedene Aufgaben erledigen, wie beispielsweise E-Mails versenden, Dokumente erzeugen
oder Prozess-Metadaten ändern. 

Wir navigieren im Konfigurations-Bereich zu den "Aktionen", wählen dort die "Anlegen"-Aktion und öffnen den Tab
"Prozessoren".

![Konfiguration - Prozessor](/img/docs/de/my-first-process/config-create-processor.png)

Dort wählen wir den Prozessor "Prozess-Metadaten ändern" und wählen bei "Name" das Aktions-Datenfeld "name".

![Konfiguration - Prozessor](/img/docs/de/my-first-process/config-update-metadata.png)

Wenn Sie nun eine neue Demo starten, wird der Eingabewert aus dem "name"-Feld genutzt um den Prozess-Instanz Name zu setzen.

![Konfiguration - Prozess-Demo](/img/docs/de/my-first-process/demo-todo.png)


<a name="complete-export"></a>
## 10. Fertigstellen und exportieren

Herzlichen Glückwunsch, Sie haben Ihren ersten Prozess modelliert. Der Entwicklungsstand des Prozesses markiert
einen Meilenstein, weshalb wir nun den Prozess in der ersten Version fertigstellen. Dafür wählen wir oben rechts
"Fertigstellen".

![Konfiguration - Fertigstellen](/img/docs/de/my-first-process/config-complete.png)

Prozesse in der Prozessfabrik werden versioniert. Dadurch können wir den Entwicklungsfortschritt jedes Prozesses
nachvollziehen und nur qualitätsgesicherte Versionen in unsere Allisa Plattform exportieren.

![Fertigstellen - Version](/img/docs/de/my-first-process/complete-version.png)

Wir wählen die Versionsnummer 1.0.0, weil für uns dieser Prozess vollständig ist. Später könnten wir den Prozess um 
Aktionen und/oder Status erweitern und dann beispielsweise die Version 2.0.0 fertigstellen. Nach dem Fertigstellen 
einer Version wird automatisch eine neue "In der Entwicklung" Version angelegt.

![Fertigstellen - Version](/img/docs/de/my-first-process/versions.png)

Im nächsten Artikel erfahren Sie, wie Sie die Prozess-Version in eine Allisa Plattform exportieren können.

***

[Nächster Artikel: Prozess-Export](/{{route}}/{{version}}/export)
