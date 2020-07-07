# Softwareoversigten

Softwareoversigten er en simpel webfrontend til brug sammen med data fra KITOS.
Projektet består af 3 dele: 
* Softwareoversigten - frontend applikation lavet i PHP og javascript
* MySQL database - backend applikatonslag. Indeholder en forsimplet datamodel med data fra KITOS
* Data importer - en import server, der læser data fra KITOS' REST API og indlæser til MySQL databasen.

Forudsætninger
- 
Applikation forudsætter følgende: 
* At man har fået oprettet en KITOS bruger med adgang til WebAPI'et.
* At man har adgang til en docker server, samt at docker-compose er installeret og kan bruges til at bygge et image.
* At man har installeret git klient til installation af selve projektet

Installation
-
1. Installer Softwareoversigten på din docker server via git:
```shell
git clone https://github.com/os2kitos/Softwareoversigten.git
```

2. Rediger settings.json filen i folderen Softwareoversigten/php/python/settings.json
Her skal der angives brugernavn og adgangskode til den KITOS bruger med WebAPI adgang der skal anvendes til indlæsning af data fra KITOS.
Dette gøres ved at redigere disse to linier i settings.json filen. 
Der skal ikke ændres i andre indstillinger.
```shell
 "KITOS_USER": "ANGIV BRUGER MED API ADGANG",
 "KITOS_PASSWORD": "ANGIV PASSWORD",
```

3. Byg docker images med docker-compose
Der kan nu bygges de nødvendige docker images:
```shell
cd Softwareoversigten
sudo docker-compose build
```

4. Start Softwareoversigten som docker container
Der er nu bygget de nødvendige images og applikation kan startes.
```shell
sudo docker-compose up -d
```

5. Indlæs data fra KITOS vha. importeren
Den medfølgende importer kan bruges til hente data fra KITOS og indlæse dem i den forsimplede MySQL database.
Dette gøres ved at kalde et shell script i den kørende docker container:
```shell
sudo docker exec -it softwareoversigten_webapp_1 /opt/kitos_tools/import_to_mysql.sh
```
Dette kan evt. automatiseres vha. en scheduled task, i cron eller lignende, så data indlæses med faste intervaller, f.eks. hvert døgn.

6. Åbn Softwareoversigtens frontend
Nu skulle alle services gerne køre og data være indlæst til MySQL databasen. 
Selve frontend applikationen er nu tilgængelig på adressen http://<docker-server:8084/

Såfremt frontend applikationen ønskes udstillet på en anden port en 8084, skal docker-compose.yml filen rettes og den viste linie rettes til:
```shell
 ports:
         - "8084:80"
```
Angiv det portnummer der ønskes i stedet for "8084".

Softwareoversigten kan nu genstartes med docker-compose
```shell
sudo docker-compose down 
sudo docker-compose up -d
```
