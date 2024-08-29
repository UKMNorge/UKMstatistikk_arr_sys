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
- **Description:** Returnerer kommuner som har aktivitet i et fylke i en seson. Hvis kommune har minst 1 arrangement i en sesong, regnes det som aktivitet.
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId`   | `number` | Fylke ID |
| `season`    | `number` | Sesong |


#### Response Example
```json
{
  "season": 2024,
  "kommuner": {
    "5001": {
      "navn": "Trondheim - Tråante",
      "aktivitet": true
    },
    "5006": {
      "navn": "Steinkjer",
      "aktivitet": false
    },
    "5007": {
      "navn": "Namsos",
      "aktivitet": true
    },
    "5014": {
      "navn": "Frøya",
      "aktivitet": false
    },

    ...

    "5059": {
      "navn": "Orkland",
      "aktivitet": false
    },
    "5060": {
      "navn": "Nærøysund",
      "aktivitet": false
    },
    "5061": {
      "navn": "Rindal",
      "aktivitet": false
    }
  }
}

```


### Antall arrangementer i alle kommuner i fylke

- **URL:** `fylke/antallArrangementerIFylke`
- **Method:** `POST`
- **Description:** Returnerer antall arrangementer arrangert kun på kommuner i 1 fylke
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId`   | `number` | Fylke ID |
| `season`    | `number` | Sesong |


#### Response Example
```json
{
  "antall":11
}

```


### Antall arrangementer fordelt på fylke

- **URL:** `fylke/antallArrangementerPerFylke`
- **Method:** `POST`
- **Description:** Returnerer antall arrangementer på kommuner fordelt i fylke
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `season`    | `number` | Sesong |


#### Response Example
```json
{
  "01": {
    "navn": "Østfold",
    "antall": 7
  },
  "02": {
    "navn": "Akershus",
    "antall": 23
  },
  "03": {
    "navn": "Oslo",
    "antall": 17
  },

 ...

  "20": {
    "navn": "Finnmark - Finnmárku",
    "antall": 20
  },
  "99": {
    "navn": "Uoppgitt",
    "antall": 0
  }
}
```



## Land

OBS: Lands statistikk henter data fra arrangementer, kommuner og fylker i hele landet. Videresending som standart tas ikke med. Sesong brukes for å hente data i en spesifikk sesong.
OBS: Noen ganger tall stemmer ikke helt. Det er pga noen deltakere er lagt til manuelt, noen har ikke gyldig navn eller gyldig fødselsdato osv.

### Antall deltakere i hele landet

- **URL:** `land/antallDeltakere`
- **Method:** `POST`
- **Description:** Henter alle deltakere i alle arrangementer i alle kommuner i alle fylker. Fylke arrangerte arrangementer blir ikke med i beregning.
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `season`   | `number` | Sesong |

#### Response Example
```json
{
  "antall":24053
}
```


### Aldersfordeling i hele landet

- **URL:** `land/aldersfordeling`
- **Method:** `POST`
- **Description:** Henter deltakere fra alle arrangementer i hele landet fordelt på alder.
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `season`   | `number` | Sesong |

#### Response Example
```json
{
  "": 105,
  "7": 7,
  "8": 15,
  "9": 92,
  "10": 817,
  "11": 1270,
  "12": 1939,
  "13": 2562,
  "14": 2520,
  "15": 3082,
  "16": 2608,
  "17": 1927,
  "18": 1271,
  "19": 533,
  "20": 168,
  "21": 51,
  "22": 22,
  "23": 19,
  "24": 18,
  "25": 5,
  "26": 1,
  "27": 2,
  ...
}
```


### Kjønnsfordeling i hele landet

- **URL:** `land/kjonnsfordeling`
- **Method:** `POST`
- **Description:** Henter deltakere fra alle arrangementer i hele landet fordelt på kjønn.
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `season`   | `number` | Sesong |

#### Response Example
```json
{
  "female":9795,
  "male":7099,
  "unknown":2464
}
```


### Sjangerfordeling i hele landet

- **URL:** `land/sjangerfordeling`
- **Method:** `POST`
- **Description:** Sjanger på aktive innslag fra alle arrangementer i hele landet.
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `season`   | `number` | Sesong |

#### Response Example
```json
{
  "Utstilling":3868,
  "Musikk":6311,
  "Arrang\u00f8r":572,
  "Konferansier":298,
  "Dans":974,
  "Film":396,
  "Litteratur":83,
  "Noe annet p\u00e5 scene":203,
  "Media":135,
  "Teater":112,
  "Ressurs":1,
  "Ukjent":35,
  "Matkultur":8
}
```

### Gjennomsnitt deltakere på landsnivå

- **URL:** `land/gjennomsnittDeltakere`
- **Method:** `POST`
- **Description:** Returnerer gjennomsnitt deltakere på arrangementer på landsnivå i en sesong
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `season`   | `number` | Sesong |

#### Response Example
```json
{
  "gjennomsnitt":58
}
```


### Antall innslag

- **URL:** `land/getAllInnslag`
- **Method:** `POST`
- **Description:** Returnerer antall innslag på alle kommuner i alle fylker i en sesong. Det hentes ikke arrangementer arrangert i fylke
#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `season`   | `number` | Sesong |

#### Response Example
```json
{
  "01": {
    "fylke_navn": "Østfold",
    "antall_innslag": 486
  },
  "02": {
    "fylke_navn": "Akershus",
    "antall_innslag": 1578
  },
  "03": {
    "fylke_navn": "Oslo",
    "antall_innslag": 443
  },

  ...

  "99": {
    "fylke_navn": "Uoppgitt",
    "antall_innslag": 0
  }
}

```
