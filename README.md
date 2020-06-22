# Softwareoversigten

Softwareoversigten er en simpel webfrontend til brug sammen med data fra KITOS.


Installation
- 
Se under "installation". 
Projektet kræver en webserver med PHP, samt en MySQL database der indeholder data fra KITOS.
MySQL databasen opdateres med data via export_to_mysql i "kitos_tools" projektet.
 

# Projektstruktur


Det kan også køres med en simpel docker run
```shell

```

eller via

```shell
docker-compose up 
```
efter der er lavet et image med 

```shell
docker build -t softwareoversigten .
```


Der er i skrivende stund ikke et image klar til download, så man er nødt til selv at bygge et image.
