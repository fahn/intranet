---------
README.sh
---------

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
3. User in der DB anlegen ``` INSERT INTO User (email, password, admin) VALUES ('###EMAIL###', PASSWORD('###PASSWORD###'), 1) ```  
   Dabei die Platzhalter ###EMAIL### und ###PASSWORD### ersetzen
4. config.ini unter inc/config.ini bearbeiten und anpassen

# Verwendete Bibliotheken  
Folgende Bibliotheken wurde hier eingesetzt:  

| Bibliothek      | Version | Lizenz      | Link zum Projekt                       |
|-----------------|---------|-------------|----------------------------------------|
| parsedown       | 1.7     | MIT         | (https://github.com/erusev/parsedown)  |
| nette/mail      | 2.4     | BSD License | (https://github.com/nette/mail)        |
| box/spout       | 2.7     | apache      | (https://github.com/box/spout          |
| dompdf/dompdf   | 0.8.3   | LGPL-2.1    | (https://github.com/dompdf/dompdf)     |

# Programmierer
- Stefan Metzner
- Philip Fischer
