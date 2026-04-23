# Marktplatz für statusorientierte Prozesse

Laravel-basierte Webanwendung für die Entwicklung und Bereitstellung 
von statusorientierten Prozessmodellen gemäß dem SONAL®-Prozessmodell. 

### Kernfunktionen

- Grafischer Prozessmodellierer für statusorientierte Prozessmodellierung.
- Versionierung und Export von Prozessmodellen.
- Kollaborative Prozessentwicklung in Organisationen.
- Veröffentlichung der Prozessmodelle auf dem integrierten Marktplatz.
- Durchlaufsimulation der Prozessmodelle (nur mit verknüpfter "Allisa Plattform" Engine verfügbar)

### Demo Installation mit Docker Compose
Folgende Schritte starten einen Docker Compose Stack mit fünf Containern:

- market_app - PHP 8.1 FPM mit Anwendungscode
- market_postgres - PostgreSQL-Datenbank
- market_nginx - NGINX Webserver
- market_redis – Redis Cache
- market_horizon - Queue Worker

1. Environment kopieren: `cp .env.example-development-docker .env`
2. Docker Compose Datei kopieren: `cp docker-compose-example-development.yml docker-compose.yml`
3. Docker Compose Stack starten: `docker compose up -d`
4. Mit Container "market_app" verbinden: `docker exec -it market_app bash`
   1. Anwendungsschlüssel generieren: `php artisan key:generate`
   2. Datenbank mit Testbenutzern erstellen: `php artisan app:blueprint_run develop`
   3. OAuth Server für REST-API installieren: `php artisan passport:install`
5. http://localhost:8080 öffnen
6. Auf "Anmelden" klicken und mit Testbenutzer anmelden.
   1. E-Mail Adresse: test@example.com
   2. Passwort: password

*Hinweis*: Die Echtzeit-Simulation ist nur in Kombination mit einer verknüpften Prozess-Ausführungsplattform "Allisa Plattform" verfügbar. 
Die oben beschriebenen Schritte repräsentieren keine Produktivumgebung.

### Dokumentation

Die Dokumentation befindet sich bei laufender Demo-Anwendung unter http://localhost:8080/docs/de/overview.
Struktur:

- Einleitung
- Mein erster Prozess
- Prozess-Export
- Prozess-Digitalisierung - Grundlagen
- Bereich: Regeln & Daten
- Bereich: Konfiguration

### Tests
Die PHPUnit-Tests liegen im Verzeichnis `tests`. Für Tests muss die Test-Umgebung genutzt werden (`.env.testing`),
welche bei Nutzung von `docker-compose-example-development.yml` bereits automatisch in den "market_app" Container eingebunden wird.

1. Mit dem "market_app" Container verbinden: `docker exec -it market_app bash`
2. Alle Tests ausführen: `php artisan test`
3. Die "Feature"-Suite ausführen: `php artisan test --testsuite=Feature`
4. Einen einzelnen Test ausführen: `php artisan test tests/Unit/Models/UserTest.php`

### Technologien & Frameworks
- Laravel 10 (PHP 8.1)
- Vue.js 2.6 (JavaScript)
- PostgreSQL 14
- Redis

### Lizenz

Dieses Projekt steht unter der [PolyForm Noncommercial License 1.0.0](./LICENSE).
Kostenlos für private, wissenschaftliche und andere nicht-kommerzielle Nutzung.

