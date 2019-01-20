---------
README.sh
---------

# Requirements
Folgende Sachen werden benötigt:
- Webserver
- composer

## Docker container
Alternativ kann der Docker-container (https://github.com/fahn/rangliste_docker) genutzt werden

# Installation
1. Dateien auf dern Server übertragen
2. sql/mysql_schema.sql importieren
3. User in der DB anlegen ``` INSERT INTO User (email, password, admin) VALUES ('###EMAIL###', PASSWORD('###PASSWORD###'), 1) ```  
   Dabei die Platzhalter ###EMAIL### und ###PASSWORD### ersetzen
4. config.ini unter inc/config.ini bearbeiten und anpassen

# Programmierer
- Stefan Metzner
- Philip Fischer
