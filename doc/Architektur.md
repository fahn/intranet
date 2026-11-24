# Architektur

Code-Überblick für `badtra/intranet` — Web-Intranet für Badminton-Vereine (Turniere, Rangliste, News, User-Verwaltung).

## Stack

- PHP 7.4, PSR-4 Autoloading via Composer
- Smarty 4 (Templating)
- MariaDB (`docker-build/mysql/mysql_schema.sql`)
- Eigener Mini-Router (`src/inc/libs/Router.php`) statt Framework
- Docker-basiertes Deployment (`Dockerfile`, `docker-compose*.yml`, `Makefile`)
- Composer-Libs: Smarty, nette/mail, box/spout (Excel), dompdf (PDF), eluceo/ical, symfony/yaml, symfony/validator

## Einstiegspunkte

| Datei | Zweck |
|---|---|
| `src/index.php` | Haupt-Web-Einstieg. Lädt `routes.yaml`, dispatcht URI über `Router` |
| `src/ajax/index.php` | AJAX-Endpunkte |
| `src/api/index.php` | API / CLI-Aufrufe, z. B. `php api/index.php tournament` (Turnier-Reminder-Cronjob, siehe `docker-compose.yml` Service `reminder`) |
| `src/inc/config.ini` | Instanz-Konfiguration (DB-Zugang, Basis-URL, Feature-Flags). **Nicht versioniert** (siehe `.gitignore`), lokal pro Deployment anzulegen |

## Routing

`src/routes.yaml` bildet Pfad → Controller/Methode ab (Symfony YAML geparst durch `Router.php`):

```yaml
tournament_details:
  path: /tournament/details/{id}
  controller: TournamentController
  method: showTournamentDetails
  requirements:
    id: \d+
```

`Router::dispatch()` matcht die Request-URI per Regex gegen alle Routen, instanziiert `Badtra\Intranet\Controller\{controller}` und ruft die Methode mit den URL-Parametern auf. Kein Auth-Layer im Router selbst — Zugriffsschutz passiert in den Pages/PrgPattern-Klassen.

## Schichten (`src/inc/`, PSR-4 `Badtra\Intranet\*`)

```
Controller  →  Html (Page)  →  Logic (PrgPattern*)  →  DB
                    ↓
                 Widget, Smarty-Template (src/templates/)
```

- **`controller/`** — dünne Controller, ein Controller pro Feature (z. B. `TournamentController`). Instanziiert die zugehörige `Html`-Page-Klasse, ruft View-Methode auf, echot Ergebnis. Keine Business-Logik.
- **`html/`** — Page-Klassen, eine pro Feature (`TournamentPage`, `News.php`, …). Erben von `BrdbHtmlPage` → `HtmlPageProcessor`. Bündeln pro Request: DB-Zugriff (`$this->brdb`), Business-Logik (`$this->prgPattern` / `PrgPatternElement*`), Smarty (`$this->smarty`), Login-Status (`$this->prgPatternElementLogin`). View-Methoden (`listView()`, `detailView()`, `addView()`, …) weisen Smarty-Variablen zu und rendern ein `.tpl` über `smartyFetchWrap()`.
- **`logic/`** — Geschäftslogik, ein `PrgPatternElement*` pro Feature (z. B. `PrgPatternElementTournament`), registriert an einem zentralen `PrgPattern`-Dispatcher. Enthält Formatierung, Validierung, Berechnungen (z. B. Elo-Ranking), Zugriffsprüfungen. `PrgPatternElementLogin` regelt Auth/Session.
- **`db/`** — Datenzugriff, eine `*DB`-Klasse pro Aggregat (`TournamentDB`, `UserDB`, `RankingDB`, …), zentral gebündelt in `BrankDB` (von Page-Klassen als `$this->brdb` genutzt).
- **`model/`** — einfache Datenklassen (`Tournament`, `User`, `Player`, `Game`, …), erben von `BaseModel`/`DefaultModel`.
- **`widget/`** — wiederverwendbare Seitenbausteine (Sidebar-Widgets: `NewsWidget`, `RankingWidget`, `TournamentWidget`, `BdayWidget`, `TeamWidget`).
- **`exception/`** — eigene Exception-Typen.
- **`libs/`** — Querschnitts-Utilities, aktuell nur `Router.php`.

Namenskonvention: Feature-Name zieht sich durch alle Schichten (`Tournament` → `TournamentController`, `TournamentPage`, `PrgPatternElementTournament`, `TournamentDB`, `Tournament` Model, `TournamentWidget`).

## Templates

Smarty-Templates in `src/templates/<feature>/*.tpl`, gemeinsame Bausteine (`page_header.tpl`, `page_footer.tpl`, `navi.tpl`, `default.tpl`) im Wurzelverzeichnis. Compile-Cache unter `src/template_c` bzw. `src/templates_c` (Docker-Volume `data/`).

## Admin-Bereich

Eigene Controller/Pages unter `admin/*` (`AdminUserController`, `AdminTournamentController`, `AdminSettingsController`, …), Routen unter `/admin/...` in `routes.yaml`. Gleiche Schichtenteilung wie Public-Bereich.

## Tests

`src/tests/` — PHPUnit (`phpunit.xml.dist`), unterteilt nach Schicht (`inc/db`, `inc/html`, `inc/logic`, `inc/model`). Kein durchgängiges Coverage-Muster; teils Modell-, teils Logik-Tests (z. B. `eloRanking.php` für Ranking-Berechnung).

## Build / Betrieb

- `make build` — Docker-Image bauen (`Dockerfile`)
- `make run` / `make run_dev` — Compose-Stack starten (App + MariaDB, optional Reminder-Cronjob-Container)
- `make sniff` — PHP_CodeSniffer (`phpcs.xml.dist`)
- `make testreport` — PHPUnit mit Coverage
- `composer.json` — PSR-4-Mapping, Abhängigkeiten, `post-install-cmd` legt `template_c` an

## Konfiguration

`src/inc/config.ini` (INI, nicht versioniert) steuert pro Instanz: DB-Zugang, Basis-URL/Branding, Wartungsmodus, Google-Maps-Key, Mail-Absender, Feature-Flags (`tournamentEnable`, `RankingEnabled`, `faqEnabled`, `notificationEnable`, `newsEnable`, `registerEnabled`). Kein Beispiel-File im Repo vorhanden — bei Neuaufsetzung anhand der Sektionen in dieser Datei anlegen.

## Weitere Docs im Repo

- `doc/Installer.md` — Installationsanleitung
- `doc/Manual.md` — Benutzerhandbuch
- `README.md` — Setup, verwendete Bibliotheken
