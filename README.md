# UKMstatistikk_arr_sys
UKM Statistikk på arrangørsystemet


# Endpoints

## Arrangement

Alle deltakere på arrangement er kun hentet fra gyldige innslag (fullførte innslag)
OBS: Arrangement tilgang sjekkes gjennom StatistikkManager

### Aldersfordeling

- **URL:** `arrangement/aldersfordeling`
- **Method:** `POST`
- **Description:** Aldersfordeling er designet for å hente ut alle aldersdataene for personer som er meldt på i aktive innslag i et arrangement. Den opererer typisk på en samling av participant hvor hver person har en spesifisert alder predefinert. Alder begregnes på tidspunktet arrangementet ble holdt.

#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `plId`   | `number` | Arrangement ID |

#### Response Example
```json
[
  {
    "age":"16",
    "antall":"1"
  },
  {
    "age":"17",
    "antall":"1"
  },
  {
    "age":"21",
    "antall":"1"
  },
]
```


### Antall Deltakere

- **URL:** `arrangement/antallDeltakere`
- **Method:** `POST`
- **Description:** Returnerer antall deltakere i et arrangement fra aktive (fullførte) innslag. Det er mulig å hente unike og ikke unike deltakere ved å sende `unike` parameter
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `plId`   | `number` | Arrangement ID |
| `unike`   | `boolean` | Unike eller ikke unike deltakere |

#### Response Example
```json
{
  "erUnike":true,
  "antall":15
}
```



### Kjønnsfordeling

- **URL:** `arrangement/kjonnsfordeling`
- **Method:** `POST`
- **Description:** Returnerer antall personer fordelt på kjønn. Kjønn er identifisert basert på fornavn og noen ganger kan det bli udefinert
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `plId`   | `number` | Arrangement ID |

#### Response Example
```json
{
  "unknown":2,
  "male":5,
  "female":5
}
```


### Sjangerfordeling

- **URL:** `arrangement/sjangerfordeling`
- **Method:** `POST`
- **Description:** Returnerer antall personer fordelt på sjanger eller innslag type. Typene som ikke kan genereres, blir representert som udefinert
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `plId`   | `number` | Arrangement ID |

#### Response Example
```json
{
  "ukjent": {
    "antall": 4,
    "type_navn": "Ukjent"
  },
  "utstilling": {
    "antall": 20,
    "type_navn": "Utstilling"
  },
  "video": {
    "antall": 10,
    "type_navn": "Film"
  },
  "konferansier": {
    "antall": 3,
    "type_navn": "Konferansier"
  },
  "scene": {
    "antall": 2,
    "type_navn": "Noe annet på scene"
  },
  "musikk": {
    "antall": 6,
    "type_navn": "Musikk"
  },
  "dans": {
    "antall": 5,
    "type_navn": "Dans"
  },
  "nettredaksjon": {
    "antall": 1,
    "type_navn": "Media"
  }
}
```


## Kommune

Alle deltakere på kommune er kun hentet fra gyldige innslag (fullførte innslag)

### Aldersfordeling

- **URL:** `arrangement/aldersfordeling`
- **Method:** `POST`
- **Description:** Antall deltakere i gyldige innslag i en kommune. Det er mulig å hente unike og ikke unike deltakere ved å sende `unike` parameter

#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `kommuneId` | `number` | Kommune ID |
| `season`    | `number` | Sesong |
| `unike`     | `boolean` | Unike eller ikke unike deltakere |

#### Response Example
```json
{
  "erUnike":false,
  "antall":26
}
```


## Fylke

OBS: Fylke statistikk henter data fra kommuner i fylke og ikke arrangementer i fylke. Dette gjøres fordi innslag videresendes fra kommune til fylke, derfor arrangementer i kommuner representerer best statistikken i fylke 


### Aldersfordeling

- **URL:** `fylke/aldersfordeling`
- **Method:** `POST`
- **Description:** Aldersfordeling er henter ut alle aldersdataene for personer som er meldt på i aktive innslag i et fylke. Alder begregnes på tidspunktet arrangementet ble holdt
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId`   | `number` | Fylke ID |
| `season`   | `number` | Sesong |

#### Response Example
```json
[
  {
    "age": "16",
    "antall": "1"
  },
  {
    "age": "17",
    "antall": "4"
  },
  {
    "age": "18",
    "antall": "2"
  },
  {
    "age": "19",
    "antall": "4"
  },
  {
    "age": "20",
    "antall": "1"
  },
  {
    "age": "21",
    "antall": "4"
  },
  {
    "age": "22",
    "antall": "6"
  },
  {
    "age": "23",
    "antall": "9"
  },
  {
    "age": "24",
    "antall": "3"
  },
  {
    "age": "25",
    "antall": "1"
  },
  {
    "age": "54",
    "antall": "3"
  }
]
```


### Antall deltakere

- **URL:** `fylke/antallDeltakere`
- **Method:** `POST`
- **Description:** Antall deltakere i kommuner som tilhører et fylke. Deltakere hentes kun fra gyldige (fullførte) innslag. 
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId`   | `number` | Fylke ID |
| `season`    | `number` | Sesong |
| `unike`     | `boolean` | Unike eller ikke unike deltakere |


#### Response Example
```json
{
  "erUnike":true,
  "antall":40
}
```


### Gjennomsnitt deltakere på fylke

- **URL:** `fylke/gjennomsnittDeltakere`
- **Method:** `POST`
- **Description:** Returnerer gjennomsnitt deltakere på arrangementer i alle kommuner i et fylke i en sesong. Det tas ikke i begregning arrangementer som har 0 deltakere. Arrangementer i fylke blir ikke begregnet
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId`   | `number` | Fylke ID |
| `season`    | `number` | Sesong |


#### Response Example
```json
{
  "gjennomsnittDeltakere":2,
  "season":"2024"
}
```


### Kjønnsfordeling på fylke

- **URL:** `fylke/kjonnsfordeling`
- **Method:** `POST`
- **Description:** Returnerer antall deltakere fordelt på kjønn. Kjønn er identifisert basert på fornavn og noen ganger kan det bli udefinert
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId`   | `number` | Fylke ID |
| `season`    | `number` | Sesong |


#### Response Example
```json
{
  "unknown":2,
  "male":14,
  "female":12
}
```


### Sjangerfordeling på fylke

- **URL:** `fylke/sjangerfordeling`
- **Method:** `POST`
- **Description:** Returnerer antall personer fordelt på sjanger eller innslag type i kommuner i et fylke. Typene som ikke kan genereres, blir representert som udefinert. Kan sendes arrangement som skal ekskluderes. Dette kan brukes når det kalles fra et arrangement i fylke som skal ikke bli med i begregning
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `fylkeId`      | `number` | Fylke ID | required |
| `season`       | `number` | Sesong | required |
| `excludePlId`  | `number` | Ekskluder et arrangement i begregning | optional | 



#### Response Example
```json
{
  "utstilling": {
    "antall": 34,
    "type_navn": "Utstilling"
  },
  "musikk": {
    "antall": 226,
    "type_navn": "Musikk"
  },
  "arrangor": {
    "antall": 20,
    "type_navn": "Arrangør"
  },
  "konferansier": {
    "antall": 15,
    "type_navn": "Konferansier"
  },
  "scene": {
    "antall": 9,
    "type_navn": "Noe annet på scene"
  },
  "video": {
    "antall": 17,
    "type_navn": "Film"
  },
  "dans": {
    "antall": 35,
    "type_navn": "Dans"
  },
  "nettredaksjon": {
    "antall": 2,
    "type_navn": "Media"
  },
  "matkultur": {
    "antall": 2,
    "type_navn": "Matkultur"
  },
  "ukjent": {
    "antall": 2,
    "type_navn": "Ukjent"
  }
}
```


### Kommuner i fylket som har UKM-aktivtet

- **URL:** `fylke/kommunerAktivitet`
- **Method:** `POST`
- **Description:** Returnerer kommuner som har arrangementer i en sesong.
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId`   | `number` | Fylke ID |
| `season`    | `number` | Sesong |


#### Response Example
```json
{
  "season":2024,
  "kommuner_aktivitet":
    {
      "5001":"Trondheim - Tr\u00e5ante",
      "5007":"Namsos","5021":"Oppdal",
      "5028":"Melhus",
      "5035":"Stj\u00f8rdal",
      "5052":"Leka",
      "5055":"Heim",
      "5037":"Levanger"
    }
}
```

