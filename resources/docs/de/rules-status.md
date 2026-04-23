# Regeln & Daten ➜ Status

---

- [Einleitung](#introduction)
- [Status erstellen](#create-status)
    - [Mehrfach-Anlage Status](#bulk-create-status)
    - [Beispiele Mehrfach-Anlage Status](#bulk-create-status-example) 
- [Zustand erstellen](#create-state)
    - [Mehrfach-Anlage Zustände](#bulk-create-state)
    - [Beispiele Mehrfach-Anlage Zustände](#bulk-create-state-example)
- [Smart-Status](#smart-status)
    - [Typ: Verknüpfungstyp](#smart-status-relationtype)
    - [Typ: Eigene Logik](#smart-status-custom-logic)
    - [Typ: Verknüpfter Status](#smart-status-related-status)

<a name="introduction"></a>

## Einleitung

Die Status (pl.) beschreiben die Situation in der sich der Prozess befindet und geben Aufschluss über den Fortschritt
des Prozesses. Jeder Status besitzt mehrere Zustände, die durch eine Beschreibung und einen numerischen Wertebereich
definiert werden. Aufgrund der fachlichen Nähe zu den Aktionen findet die Statusmodellierung häufig gemeinsam mit der
Aktionsmodellierung statt. Zu jedem Zeitpunkt im Prozessverlauf ist genau ein Zustand von jedem Status aktiv, sodass
sich aus allen aktiven Zuständen der Status der Prozess-Sitaution ergibt.

Zum Beispiel hat der "Montage"-Case-Study Prozess vier Status: Auftrag, Material **(1)**, Montage und
Qualitätssicherung. Der "Material"-Status hat die Zustände "Keine Daten vorhanden", "Nicht vollständig" und
"Vollständig" **(2, 3, 4)**.

![Status](/img/docs/de/rules-status/rules-status-example-1.png)

In der Prozess-Instanz auf der Allisa Plattform wird die aktuelle Prozess-Situation im oberen Bereich angezeigt. Der
untenstehenden Screenshot zeigt die Prozess-Situation einer "Montage"-Prozess-Instanz. Dort sind die oben beschriebenen
Status sichtbar mit ihrem jeweiligen, aktuell aktiven Zustand, zum Beispiel "Vollständig" beim
"Material"-Status **(1)**.

![Status](/img/docs/de/rules-status/process-situation.png)

In der Prozessmodellierung sind alle Status im "Status"-Panel gelistet. Es ist auch möglich einen Status über die
graphische Oberfläche anzuwählen, um die Zustände in der Detailansicht im Panel zu öffnen.

![Status](/img/docs/de/rules-status/rules-status-states.png)

Der obenstehende Screenshot zeigt den "Material"-Status in der Detailansicht **(1)**. Der Initial-Wert ist der
numerische Wert, welchen der Status zu Beginn des Prozess hat **(2)**. Jeder Zustand besitzt eine Beschreibung und ein
Wertebereich (Min./Max) **(3)**. In diesem Fall ist der Min- und Max-Wert identisch, weshalb nur ein Wert angegeben
wird.
Der Wertebereich legt den Gültigkeitsbereich des Zustandes fest und kann auch in den negativen Zahlenbereich reichen.
Der Minimal-Wert und Maximal-Wert können bis zu drei Nachkommastellen besitzen.

> {info.fa-info-circle} Ein Prozess-Status besitzt stets einen aktuellen, numerischen Wert, welcher durch einen Zustand
> beschrieben wird.

Beim obenstehenden "Material"-Status ist der Initial-Wert "0.000". Dieser Wert wird durch den Wertebereich des
Zustandes "Keine Daten vorhanden" abgedeckt, wodurch zu Beginn eines neuen "Montage"-Prozesses der Status "Material" auf
dem "Keine Daten vorhanden"-Zustand gesetzt wird.

<a name="create-status"></a>

## Status erstellen

Sie können entweder über den Button "+Hinzufügen" im "Status"-Panel **(1)** oder mit einem **Rechtsklick auf die
grafische Oberfläche** einen neuen Status erstellen.

![Status](/img/docs/de/rules-status/rules-create-status.png)

Es öffnet sich ein Dialog-Fenster mit einem Web-Formular.

![Status](/img/docs/de/rules-status/rules-create-status-form.png)

Sie können einen Namen, eine Beschreibung und eine Referenz **(1)** vergeben. Ein Smart-Status ist ein besonderer
Status-Typ, bei dem der aktuelle Wert nicht durch Statusregeln gesetzt wird, sondern automatisch ermittelt wird **(2)**.
Der Initial-Wert ist der Anfangswert des Status. Wird eine neue Prozess-Instanz erstellt, wird der Status automatisch
auf diesen Wert gesetzt. Nach dem Anlegen des Status, wird automatisch ein Initial-Zustand entsprechend des Initial-Wertes 
des Status mit der Beschreibung "Start" erstellt und in der grafischen Oberfläche angezeigt.

<a name="bulk-create-status"></a>

## Mehrfach-Anlage Status

Neben dem "+ Hinzufügen"-Knopf befindet sich der Knopf **(1)** für die Mehrfachanlage von Status. Hier können mehrere
Status gleichzeitig angelegt werden.

![Status](/img/docs/de/rules-status/bulk-create-status-button.png)

Über den Knopf öffnet sich ein Text-Editor, in dem die Status mithilfe einer spezifischen Syntax angelegt werden können.
Pro **Zeile** kann hier **ein** Status angelegt werden.

**Format**: `<Status-Name\>;<?Initialwert>`

- Status-Name
- Initialwert (optional)
    - Numerisch (Standard "-1")

<a name="bulk-create-status-example"></a>

## Beispiele für Mehrfach-Anlage von Status

- **Mein Status**  
  Status mit dem Namen `Mein Status` und einem Initial-Zustand `"Start"` mit dem Wert `-1`.

- **Mein Status 2;3**  
  Status mit dem Namen `Mein Status 2` und einem Initial-Zustand `"Start"` mit dem Wert `3`.

<a name="create-state"></a>

## Zustand erstellen

Sie können entweder über den Button "+Zustand hinzufügen" in der Detailansicht des Status oder mit einem
**Rechtklick in den Status** einen neuen Zustand erstellen.

![Status](/img/docs/de/rules-status/rules-create-state.png)

Jeder Zustand hat eine Beschreibung und einen Wert. Dieser wird automatisch bestimmt, kann aber optional manuell erfasst werden.
Dazu kann optional ein Wertebereich angegeben werden.
Es können sowohl negative als auch positive Zahlen für den Wertebereich gewählt werden. Maximal können drei
Nachkommastellen angegeben werden.

![Status](/img/docs/de/rules-status/rules-create-state-form.png)

<a name="bulk-create-state"></a>

## Mehrfach-Anlage Zustände

Neben dem "+ Zustand Hinzufügen"-Knopf befindet sich der Knopf **(1)** für die Mehrfachanlage von Zuständen. Hier können
mehrere
Zuständen gleichzeitig angelegt werden.

![Status](/img/docs/de/rules-status/bulk-create-state-button.png)

Über den Knopf öffnet sich ein Text-Editor, in dem die Zuständen mithilfe einer spezifischen Syntax angelegt werden
können.
Pro **Zeile** kann hier **ein** Zustand angelegt werden.

**Format**: `<Beschreibung\>;<?Min-Wert>;<?Max-Wert>`

- Beschreibung
- Min-Wert (optional)
  - Numerisch
- Max-Wert (optional)
  - Numerisch

<a name="bulk-create-state-example"></a>

## Beispiele für Mehrfach-Anlage von Zuständen

- **Mein Zustand**  
  Zustand `Mein Zustand` mit einem automatisch hochzählenden Wert.

- **Mein Zustand;10**  
  Zustand `Mein Zustand` mit dem Wert `10`.

- **Mein Zustand;10-15**  
  Zustand `Mein Zustand` mit einem Wertebereich von `10-15`.

<a name="smart-status"></a>

## Smart-Status

Ein Smart-Status ist ein Prozess-Status, der den aktuellen Wert automatisch ermittelt und somit
nicht durch Statusregeln verändert werden kann. Gewöhnliche Status werden durch Statusregeln einer Aktion
verändert.

> {info.fa-info-circle} Mit jeder Aktionsausführung, Prozess-Löschung und Aktions-Rollback werden Smart-Status neu
> berechnet.

<a name="smart-status-relationtype"></a>

### Typ - Verknüpfungstyp

Dieser Smart-Status Typ ermittelt den aktuellen Status-Wert anhand der Anzahl von verknüpften Prozessen eines
Verknüpfungstyp.
Prozesse in der Allisa Plattform können miteinander verknüpft werden um auf fachliche Zugehörigkeiten zu repräsentieren
und auf gegenseitige
Daten zuzugreifen. Der Verknüpfungstyp repräsentiert die Verknüpfung zweier Prozesse.

![Status](/img/docs/de/rules-status/smart-status-relationtype.png)

Der obenstehende Screenshot zeigt die Konfigurationsoptionen für einen Smart-Status vom Typ "Verknüpfungstyp" **(1)**.
Mit einem Verknüpfungstyp
wird angegeben, welche Verknüpfungen für die Ermittlung des Status-Wertes genutzt werden sollen **(2)**. Zusätzlich können
optional noch Konditionen angegeben werden **(3)**.
Mit den Konditionen können die verknüpften Prozesse gefiltert werden.

<a name="smart-status-custom-logic"></a>

### Typ - Eigene Logik

Dieser Smart-Status Typ ermittelt den aktuellen Status-Wert anhand einer "Eigene Logik"-[Vorlage](/{{route}}/{{version}}/templates). Bei jeder
Aktionsausführung wird die Vorlage genutzt
um den aktuellen Status-Wert zu ermitteln.

![Status](/img/docs/de/rules-status/smart-status-custom-logic.png)

Bei der Konfiguration des Status, wird lediglich die Vorlage angegeben **(2)**.
Beim Anlegen der Vorlage kann die Vorlage "Eigene Logik - Smart-Status" gewählt werden (siehe unten).

![Status](/img/docs/de/rules-status/smart-status-custom-logic-2.png)

Das untenstehende Beispiel zeigt eine "Eigene-Logik"-Vorlage. Mit `{% set random_number = random(0, 100) %}` wird eine
zufällige Zahl zwischen 0 und 100
ermittelt und auf die Variable `random_number` geschrieben. Im Ausgabebereich wird `random_number` auf den Smart-Status
mit dem Referenznamen `status_reference_1`
geschrieben.

```twig
{# Dokumentation: https://twig.symfony.com/doc/3.x/templates.html #}

{# Hier wird beispielhaft eine zufällige Zahl zwischen 0-100 gewählt. #}
{% set random_number = random(0, 100) %}

{# Dies ist der Ausgabe-Bereich für den Smart-Status Wert. Es muss ein gültiger Zustands-Wert zurückgegeben werden. #}
<outputs>
    <situation>
        <status reference="status_reference_1">{{ random_number }}</status>
    </situation>
</outputs>
```

<a name="smart-status-related-status"></a>

### Typ - Verknüpfter Status

Mit dem Smart-Status "Verknüpfter Status" kann ein Status einer verknüpften Prozess-Instanz referenziert werden. Dies
ermöglicht es, Aktionsregeln basierend
auf einen Status eines verknüpften Prozesses zu erstellen.

![Status](/img/docs/de/rules-status/smart-status-related-status.png)

Der obenstehende Screenshot zeigt die Konfigurationsoptionen für einen Smart-Status vom Typ "Verknüpfter Status" **(1)
**. Unter "Verknüpfter Prozess" **(2)** wird ein interner, zum Prozess gehörender Verknüpfungstyp,
oder ein Verknüpfungstyp eines anderen Prozesses (externer Verknüpfungstyp) gewählt. Wird ein interner Verknüpfungstyp
gewählt, muss dieser entweder den Verbindungstyp "N:1" oder "1:1" haben. Bei einem externen
Verknüpfungstyp muss der Verbindungstyp entweder "1:N" oder "1:1" sein. Über den Verknüpfungstyp wird die
Prozess-Instanz geladen, deren Status über die Option "Status-Referenz" **(3)** referenziert wird.
In dem Fall, dass die Prozess-Instanz zur Laufzeit noch keine verknüpfte Prozess-Instanz von dem angegebenen
Verknüpfungstyp hat, wird als Wert der "Fallback-Wert" als Statuswert gewählt **(4)**.

***

[Nächster Artikel: Regeln & Daten ➜ Aktions- und Statusregeln](/{{route}}/{{version}}/actionrules-statusrules)
