# Prozess-Export

---

- [Einleitung](#introduction)
- [Vorraussetzungen](#prerequisites)
- [API Client erstellen](#api-client)
- [Allisa Plattform registrieren](#register-platform)
- [Prozess exportieren](#export)
- [Prozess berechtigen](#group-role-permission)

<a name="introduction"></a>
## Einleitung

Um einen Prozess in einer Allisa Plattform nutzen zu können, muss dieser von der Prozessfabrik in die Allisa Plattform exportiert werden. 
Dafür ist es notwendig, die Allisa Plattform in der Prozessfabrik zu registrieren. Es können beliebig viele Allisa Plattformen für ein Benutzer-Profil
oder für ein Organisations-Profil registriert werden, um somit einen Prozess im mehrere Allisa Plattformen zu verteilen.

<a name="prerequisites"></a>
## Vorraussetzungen

Um Prozesse in eine Allisa Plattform zu exportieren, müssen bestimmte Vorraussetzungen erfüllt sein:
- Administrative Rechte: Auf der Allisa Plattform, zu der man Prozesse exportieren möchte, muss man die administrative Rolle einnehmen.
- API Client: Es wird ein "Client Credentials Grant"-API Client auf der Allisa Plattform benötigt(Beschreibung unten)
- Allisa Plattform registrieren: Es muss die Allisa Plattform, zu der Prozesse exportiert werden sollen, in der Prozessfabrik
  registriert werden (Beschreibung unten).

<a name="api-client"></a>
## API Client erstellen

Der API Client auf Ihrer Allisa Plattform ermöglicht es anderen Systemen mit ihrer Plattform zu kommunizieren. Es ist somit erforderlich, 
dass ein solcher API Client erstellt wird, bevor Prozesse von der Prozessfabrik zu Ihrer Plattform exportiert werden können.

Navigieren Sie im Administrationsbereich Ihrer Allisa Plattform zu "API Clients" und erstellen Sie einen neuen API Client 
indem Sie oben rechts auf "Neuer API Client" klicken.

![API Client erstellen](/img/docs/de/export/api-clients-create.png)

1. Tragen Sie bei Name "Prozessfabrik" ein.
2. Wählen Sie "Client Credentials Grant Tokens" als Typ.

Nach dem Erstellen des API Clients gelangen Sie zur Detailansicht. Der API Client ist nun zur Nutzung bereit.

![API Client Ansicht](/img/docs/de/export/api-clients-detail.png)

Die Client-Id (1) und und das Client-Secret (2) werden für die Erstellung der Authentifizierungstoken benötigt. Im nächsten Schritt 
wird der soeben erzeugte API Client genutzt, um Ihre Allisa Plattform in der Prozessfabrik zu registrieren.

> {info.fa-info-circle} Die Prozessfabrik kann mit dem API Client ausschließlich Prozesse exportieren. Es können keine Daten von der Allisa
> Plattform abgefragt werden.

<a name="register-platform"></a>
## Allisa Plattform registrieren

Wenn Sie dort eine Allisa Plattform registrieren, können Sie Prozesse von der Prozessfabrik zu Ihrer Allisa Plattform exportieren.

![Prozessfabrik Plattform hinzufügen](/img/docs/de/export/marketplace-add-platform.png)

Navigieren Sie zu Ihren Einstellungen und dort zu "Allisa Plattformen" [[zu Allisa Plattformen](/manage/setting/systems)]. 
Klicken Sie dann auf "Allisa Plattform hinzufügen".

![Prozessfabrik Plattform hinzufügen](/img/docs/de/export/marketplace-add-platform-detail.png)

1. Tragen Sie hier den Namen Ihrer Allisa Plattform ein.
2. Die URL zu Ihrer Allisa Plattform. Falls Sie die Allisa Cloud nutzen hat die URL das Format: *https://<kundenidentifikation>.example.com*
3. Die Client-Id eines "Client Credentials Grant" API Client (siehe oben)
4. Das Client-Secret des API Clients (siehe oben).

Nach dem Hinzufügen sehen Sie in der Übersicht der Allisa Platformen den Eintrag. Sie können nun Prozesstypen zu dieser Allisa Platform exportieren.

![Prozessfabrik Plattform Ansicht](/img/docs/de/export/marketplace-platform.png)

> {info.fa-info-circle} Bei einer Organisation gelangen Sie von dem Organisations-Profil mithilfe des "Zahnrad"-Icons zu den Einstellungen. Von dort
> gelangen Sie zu den Allisa Plattformen der Organisation. Sie benötigen administrative Rechte in der Organisation um eine Allisa Plattform hinzuzufügen.

<a name="export"></a>
## Prozess exportieren

Navigieren Sie zu dem Prozess, den Sie zu einer Allisa Plattform exportieren möchten. Wählen Sie bei den erweiterten 
Optionen des Prozesses die Option "Versionen".

![Prozessfabrik Prozess Details](/img/docs/de/export/marketplace-process-detail.png)

Wählen Sie eine Prozess-Version und klicken Sie auf "Exportieren".

![Prozessfabrik Versionen](/img/docs/de/export/marketplace-choose-version.png)

Falls Sie bereits mehrere Allisa Plattformen registriert haben, können Sie die Prozess-Version auch zu mehreren Allisa Plattformen exportieren.

![Prozessfabrik Export](/img/docs/de/export/marketplace-export.png)

<a name="group-role-permission"></a>
## Prozess berechtigen

In Ihrer Allisa Plattform finden Sie eine Übersicht aller Prozesse (Prozesstypen) unter Administration > Prozesstypen.
Dort finden Sie den soeben importierten Prozesstyp. Wenn Sie das erste Mal einen Prozesstyp importieren, müssen Sie den Prozesstyp zunächst für Gruppen-Rollen
freischalten. Andernfalls können Sie keine Prozess-Instanzen von dem Prozesstyp erzeugen. Alle nachfolgenden Prozesstyp-Versionen müssen nicht einzeln berechtigt werden.

Navigieren Sie zu einer Gruppe und wählen Sie eine Gruppen-Rolle aus, für die Sie den Prozesstyp freischalten möchten.

![Prozessfabrik Prozess Optionen](/img/docs/de/export/group-detail.png)

1. Zur Bearbeitung einer Gruppen-Rolle

Im Bearbeitungsbereich einer Gruppen-Rolle scrollen Sie bis nach unten, wo die Rechte für die Prozesstypen gelistet sind.

![Prozessfabrik Prozess Optionen](/img/docs/de/export/group-permissions.png)

1. Das Recht, neue Prozess-Instanzen zu erzeugen.
2. Das Recht, bestehende Prozess-Instanzen zu löschen.

Speichern Sie die Einstellungen ganz unten. Alle nutzenden Personen der Gruppe in der gewählten Gruppen-Rolle haben nun das Recht, Prozess-Instanzen von dem importierten
Prozess zu erstellen. Sie erstellen einen neuen Prozess, indem Sie im Hauptmenü auf "Neuer Prozess" klicken.

***

[Nächster Artikel: Prozess-Digitalisierung - Grundlagen](/{{route}}/{{version}}/rules-basics)
