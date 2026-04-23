# Konfiguration ➜ Listeners

---

- [Einleitung](#introduction)
- [Listener anlegen](#create-listener)

<a name="introduction"></a>

## Einleitung

Ein Listener kann auf Prozess-Events reagieren, die durch andere Prozess-Instanzen auf der Allisa Plattform ausgelöst
wurden. Mit einem Event kann eine Prozess-Instanz die Allisa Plattform, in der der Prozess ausgeführt wird, über einen
bestimmtes Ereignis in der Prozess-Instanz informieren. Ein Listener reagiert auf ein Event indem eine Aktion ausgeführt
wird.

![Listeners](/img/docs/de/listeners/listeners-overview.png)

<a name="create-listener"></a>

## Listener anlegen

Ein Listener wird im Konfigurationsbereich unter "Listeners" mit dem Button "Listener anlegen" erstellt.

![Listeners](/img/docs/de/listeners/listener-create-form.png)

Jedem Event kann eine Beschreibgung gegeben werden **(1)**. Unter "Events" **(2)** können Events angegeben werden, auf die
der Listener "hören" soll. Mit einer "Verknüpfungstyp-Kondition" können die Prozesse gefiltert werden, auf dessen Events
der Listener reagiert soll. Wird ein Verknüpfungstyp angegeben, wird nur auf die Events von Prozess-Instanzen reagiert,
die mit der ausführenden Prozess-Instanz mit dem Verknüpfungstyp verknüpft sind.

Es können zusätzlich noch Konditionen angegeben werden, welche die ausführende Prozess-Instanz erfüllen muss, um auf die
Events reagieren zu dürfen **(4)**. Unter "Aktion" wird die Aktion angegeben, die der Listener ausführen soll, wenn auf
ein Event reagiert wird **(5)**.

***

[Nächster Artikel: Konfiguration ➜ JavaScript](/{{route}}/{{version}}/config-javascript)
