# Projecte Final - Apache + Docker + Serveis addicionals

Aplicació realitzada a l'institut Sapalomera (NF2) | Inicicació a Docker.
En aquesta part es detalla tot el desenvolupament d'una aplicació completa que integra els serveis d'Apache, MySQL, Redis i phpMyAdmin, gestionats mitjançant Docker Compose.

---

## Índex
- [Arquitectura del sistema](#arquitectura-del-sistema)
- [Instruccions de desplegament](#instruccions-de-desplegament)
- [URLs d'accés](#urls-daccés)
- [Funcionalitats implementades](#funcionalitats-implementades)
- [Estructura del projecte](#estructura-del-projecte)
- [Requeriments i configuracions](#requeriments-i-configuracions)
- [Diagrama d'Arquitectura](#diagrama-darquitectura)

---

## Arquitectura del sistema

El sistema s'ha dissenyat amb una arquitectura per capes que inclou els següents serveis:

1. **Frontend** (Apache):
   - Serveix el lloc web principal i l'API REST.
   - Configura Virtual Hosts diferenciats (frontend.local i api.local).
   - Implementa seguretat amb HTTPS, capçaleres de seguretat i redireccions HTTP → HTTPS.
   - Genera logs en format JSON per facilitar el monitoratge.
   
2. **Base de dades** (MySQL):
   - Gestiona dades persistents (usuaris i articles).
   - Proporciona inicialització amb dades de prova.

3. **Redis**:
   - Servei de memòria cau.
   - Utilitzat per gestionar estadístiques (nombre de visites al lloc web).

4. **phpMyAdmin**:
   - Interfície gràfica per gestionar la base de dades MySQL.

5. Xarxes:
   - **frontend-network**: Utilitzada exclusivament pel servei Apache.
   - **backend-network**: Compartida entre Apache, MySQL i Redis.

---

## Instruccions de desplegament

Segueix aquests passos per desplegar l'aplicació:

### Prerequisits
- Docker i Docker Compose instal·lats.
- Afegir les següents línies al fitxer `/etc/hosts`:
  ```
  127.0.0.1 frontend.local
  127.0.0.1 api.local
  ```

### Comandes de desplegament
1. Cloneu el repositori:
   ```
   git clone https://github.com/lsanchez69420/projecte-docker-apache.git
   cd projecte-docker-apache
   ```

2. Definiu les variables d'entorn al fitxer `.env`:
   ```dotenv
   MYSQL_ROOT_PASSWORD=supersecurepassword
   MYSQL_USER=usuari
   MYSQL_PASSWORD=contrasenya
   MYSQL_DATABASE=nom_base_dades
   ```

3. Inicieu els serveis:
   ```
   docker compose up -d
   ```

4. Verificar que tots els serveis estan corrent:
   ```
   docker compose ps
   ```

### Aturada dels serveis
Per aturar els serveis, executeu:
```
docker compose down
```

---

## URLs d'accés

- **Frontend**: [https://frontend.local](https://frontend.local)
- **API REST**: [https://api.local](https://api.local)
  - `GET /api/articles`: Retorna articles en format JSON.
  - `POST /api/articles`: Crea un article nou.
  - `GET /api/stats`: Retorna estadístiques.
- **phpMyAdmin**: [http://localhost:8080](http://localhost:8080)
- **Logs d’Apache**: Montats localment al directori `logs/`.

---

## Funcionalitats implementades

### Frontend
- **Pàgina web principal**:
  - Mostra estadístiques de visitants (gestió amb Redis).
  - Consulta i mostra els 5 últims articles de la base de dades MySQL.
- **Formularis**:
  - Formulari per a la creació d’articles nous.

### Backend
- **Base de dades MySQL**:
  - Inici amb dades predeterminades gràcies a l’script d’inicialització.
  - Taules disponibles:
    - `users` (id, username, email, created_at)
    - `articles` (id, user_id, title, content, published_at)

### Servei Redis
- Guarda informació de mètriques i estadístiques.

### Interfície phpMyAdmin
- Permet la gestió gràfica de la base de dades.

---

## Estructura del projecte

```
projecte-final/
├── docker-compose.yml
├── .env
├── README.md
├── apache/
│   ├── Dockerfile
│   ├── conf/
│   │   └── vhosts/
│   │       ├── frontend.conf
│   │       └── api.conf
│   ├── certs/
│   ├── sites/
│       ├── frontend/
│       │   ├── index.php
│       │   └── .htaccess
│       └── api/
│            ├── index.php
│            └── .htaccess
│   
├── mysql/
│   └── init/
│       └── 01-schema.sql
└── logs/
```

---

## Requeriments i configuracions

1. **HTTPS**:
   - Certificats auto-signats generats durant el build.
   - Redirecció HTTP → HTTPS mitjançant `mod_rewrite`.

2. **Seguretat**:
   - Capçaleres segures (HSTS, X-Frame-Options, CSP).
   - Credencials gestionades amb `.env` per seguretat.
   - Restriccions d'accés a rutes concretes amb `.htaccess`.

3. **Volums**:
   - Volum persistent per MySQL i Redis.
   - Montatge de configuració i logs d’Apache.

4. **Xarxes**:
   - Arquitectura de seguretat en capes:
     - **frontend-network**: Exclusiu per a Apache.
     - **backend-network**: MySQL, Redis i Apache.

---

## Diagrama d'Arquitectura

```
[Clients]
    |
    v
[Apache - Frontend] --> frontend-network --> [MySQL] (backend)
               |                           ↳ [Redis] (backend)
               |--> backend-network
               ↳ [phpMyAdmin]
```

---

## Bonus (Implementacions Addicionals)

Es poden considerar els següents punts opcionals per millorar el projecte:
- **Healthcheck personalitzat** per als serveis.
- **Rate limiting** amb `mod_evasive`.
- Configuració de **Prometheus/Grafana** per a monitorització.
- Servei de **backup automàtic** per a MySQL.

---

Lluc S. - ASIX 2 Sapalomera
