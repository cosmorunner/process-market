# Konfiguration ➜ Events

---

- [Einleitung](#introduction)
- [Event anlegen](#create-event)

<a name="introduction"></a>

## Einleitung

Eine Prozess-Instanz kann die Allisa Plattform, in der der Prozess ausgeführt wird, über einen bestimmtes Ereignis in
der Prozess-Instanz informieren. Dies wird als ein Event beschrieben. Events sind fachspezifisch und häufig an besondere
Ereignisse im Prozessverlauf geknüpft. Zum Beispiel könnte ein Prozess ein Event triggern, wenn es einen bestimmten
Statuszustand erreicht, der wichtig für andere Prozess-Instanzen auf der Allisa Plattform ist. Andere Prozess-Instanzen
können auf Events mit "Listenern" reagieren.

![Events](/img/docs/de/events/events-overview.png)

<a name="create-event"></a>

## Event anlegen

Ein Event wird im Konfigurationsbereich unter "Events" mit dem Button "Event anlegen" erstellt.

![Events](/img/docs/de/events/events-create-form.png)

Jedem Event kann eine Name und Beschreibgung gegeben werden **(1, 2)**. Unter "Daten" **(3)** können Datenfelder erfasst werden,
die der Aktions-Prozessor "Event auslösen" befüllen kann.

***

[Nächster Artikel: Konfiguration ➜ Listeners](/{{route}}/{{version}}/listeners)
