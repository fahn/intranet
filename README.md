---------
README.sh
---------

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/fc23a5e452e745aab296a6bf9eda2bd0)](https://www.codacy.com?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fahn/intranet&amp;utm_campaign=Badge_Grade)

# Requirements
Folgende Sachen werden benötigt:
- Webserver
Auf diesem Webserver werden folgende Komponentent benötigt:
- apache2/nginx
- php
- composer (inkl. der composer-libaries die in der Datei .composer.json enthalten sind)

## Docker container
Alternativ kann der Docker-container (https://github.com/fahn/rangliste_docker) genutzt werden

# Installation
1. Dateien auf dem Server übertragen
2. sql/mysql_schema.sql importieren
3. User in der DB anlegen ``` INSERT INTO User (email, password, admin) VALUES ('###EMAIL###', '###PASSWORD###', 1) ```  
   Dabei die Platzhalter ###EMAIL### und ###PASSWORD### (das Passwort muss erst mit password_hash('###PASSWORD###', DEFAULT_PASSWORD) verschlüsselt werden) ersetzen
4. config.ini unter inc/config.ini bearbeiten und anpassen

# Verwendete Bibliotheken  
Folgende Bibliotheken wurde hier eingesetzt:  

| Bibliothek         | Version | Lizenz      | Link zum Projekt                          |
|--------------------|---------|-------------|-------------------------------------------|
| parsedown          | 1.7     | MIT         | (https://github.com/erusev/parsedown)     |
| nette/mail         | 2.4     | BSD License | (https://github.com/nette/mail)           |
| box/spout          | 2.7     | apache      | (https://github.com/box/spout             |
| dompdf/dompdf      | 0.8.3   | LGPL-2.1    | (https://github.com/dompdf/dompdf)        |
| eluceo/ical        | 0.15.0  | MIT         | (https://github.com/markuspoerschke/iCal) |
| Gargron/fileupload | 1.4.0   | MIT         | (https://github.com/Gargron/fileupload)   |
| erusev/parsedown   | 1.7.3   | MIT         | (https://github.com/erusev/parsedown)     |


## weitere Hilfsmittel
- https://mapmarker.io/documentation: Erstellung Google Maps Marker

# Docker
## Build des Images
```
  docker build --no-cache --tag rangliste .
```

## Start der Rangliste
```
  docker-compose up -d
```

# Programmierer
- Stefan Metzner
- Philip Fischer
