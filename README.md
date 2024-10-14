# UKMstatistikk_arr_sys
UKM Statistikk på arrangørsystemet


# Endpoints

## Arrangement

Alle deltakere på arrangement er kun hentet fra gyldige innslag (fullførte innslag)
OBS: Arrangement tilgang sjekkes gjennom StatistikkManager

### Aldersfordeling

- **URL:** `arrangement/aldersfordeling`
- **Access:** Administrator i arrangementet eller administrator i fylket/kommunen som arrangementet tilhører
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
- **Access:** Administrator i arrangementet eller administrator i fylket/kommunen som arrangementet tilhører
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
- **Access:** Administrator i arrangementet eller administrator i fylket/kommunen som arrangementet tilhører
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
- **Access:** Administrator i arrangementet eller administrator i fylket/kommunen som arrangementet tilhører
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


### Alle kommuner i fylke

- **URL:** `kommune/getAlleKommunerIFylke`
- **Access:** Tilgang til kommune (kommuneId)
- **Method:** `POST`
- **Description:** Alle kommuner i et fylke

#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `fylkeId` | `number` | Kommune ID |

#### Response Example
```json
[
  {
    "id":4203,
    "navn":"Arendal"
  },
  {
    "id":4216,
    "navn":"Birkenes"
  },
  {
    "id":4220,
    "navn":"Bygland"
  }
]
```


### Antall Deltakere i kommune

- **URL:** `kommune/antallDeltakere`
- **Access:** Tilgang til kommune (kommuneId)
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

### Aldersfordeling i kommune

- **URL:** `kommune/aldersfordeling`
- **Access:** Tilgang til kommune (kommuneId)
- **Method:** `POST`
- **Description:** Aldersfordeling av deltakere i en kommune.

#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `kommuneId` | `number` | Kommune ID |
| `season`    | `number` | Sesong |

#### Response Example
```json
[
  {"age":"11","antall":"2"},
  {"age":"12","antall":"1"},
  {"age":"13","antall":"5"},
  {"age":"14","antall":"2"},
  {"age":"15","antall":"2"},
  {"age":"16","antall":"1"},
  {"age":"17","antall":"1"},
  {"age":"19","antall":"5"},
  {"age":"20","antall":"1"},
  {"age":"21","antall":"1"},
  {"age":"22","antall":"1"},
  {"age":"23","antall":"2"},
  {"age":"50","antall":"2"}
]

```


### Aldersfordeling fra SSB

- **URL:** `kommune/aldersfordelingSSB`
- **Access:** Alle
- **Method:** `POST`
- **Description:** Antall innbyggere i en kommune for et gitt år. Resultet inneholder antall innbyggere fra 10 til 25 år.

#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `kommuneId` | `number` | Kommune ID |
| `year`    | `number` | Sesong |

#### Response Example
```json
{
  "10": {"age": 10, "antall": 190},
  "11": {"age": 11, "antall": 224},
  "12": {"age": 12, "antall": 195},
  "13": {"age": 13, "antall": 209},
  "14": {"age": 14, "antall": 218},
  "15": {"age": 15, "antall": 205},
  "16": {"age": 16, "antall": 220},
  "17": {"age": 17, "antall": 220},
  "18": {"age": 18, "antall": 230},
  "19": {"age": 19, "antall": 195},
  "20": {"age": 20, "antall": 198},
  "21": {"age": 21, "antall": 196},
  "22": {"age": 22, "antall": 165},
  "23": {"age": 23, "antall": 168},
  "24": {"age": 24, "antall": 182},
  "25": {"age": 25, "antall": 167}
}
```

### Kjønnsfordeling i kommune

- **URL:** `kommune/kjonnsfordeling`
- **Access:** Tilgang til kommune (kommuneId)
- **Method:** `POST`
- **Description:** Kjønnsfordeling i kommune. Kjønn defineres av fornavn fra en database og det kan returnere unknown noen ganger

#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `kommuneId` | `number` | Kommune ID |
| `season`    | `number` | Sesong |

#### Response Example
```json
{
  "unknown":13,
  "male":11,
  "female":2
}
```


### Sjangerfordeling i kommune

- **URL:** `kommune/sjangerfordeling`
- **Access:** Tilgang til kommune (kommuneId)
- **Method:** `POST`
- **Description:** Returnerer antall personer fordelt på sjanger eller innslag type i kommune. Typene som ikke kan genereres, blir representert som udefinert.

#### URL Parameters

| Parameter | Type   | Description                |
|-----------|--------|----------------------------|
| `kommuneId` | `number` | Kommune ID |
| `season`    | `number` | Sesong |

#### Response Example
```json
{
  "musikk":
    {
      "antall":3,
      "type_navn":"Musikk"
    },
  "enkeltperson":
    {
      "antall":4,
      "type_navn":"Person"
    },
  "utstilling":
    {
      "antall":2,
      "type_navn":"Utstilling"
    },
  "video":
    {
      "antall":3,
      "type_navn":"Film"
    }
}
```


## Fylke

OBS: Fylke statistikk henter data fra kommuner i fylke og ikke arrangementer i fylke. Dette gjøres fordi innslag videresendes fra kommune til fylke, derfor arrangementer i kommuner representerer best statistikken i fylke 


### Aldersfordeling

- **URL:** `fylke/aldersfordeling`
- **Access:** Administrator i en kommune som tilhører fylke med fylkeId eller administrator i fylke med fylkeId
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
- **Access:** Administrator i en kommune som tilhører fylke med fylkeId eller administrator i fylke med fylkeId
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
- **Access:** Administrator i en kommune som tilhører fylke med fylkeId eller administrator i fylke med fylkeId
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
- **Access:** Administrator i en kommune som tilhører fylke med fylkeId eller administrator i fylke med fylkeId
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
- **Access:** Administrator i en kommune som tilhører fylke med fylkeId eller administrator i fylke med fylkeId
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
- **Access:** Administrator i en kommune som tilhører fylke med fylkeId eller administrator i fylke med fylkeId
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
- **Access:** Alle som er administratorer i fylke med fylkeId
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
- **Access:** Alle som er administrator i minst 1 fylke
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
- **Access:** Alle som er administrator i minst 1 fylke
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
- **Access:** Alle som er administrator i minst 1 fylke
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

### Gjennomsnitt aldersfordeling på fylkesarrangementer (fylkesnivå)

- **URL:** `land/aldersfordelingGjennomsnittFylkesniva`
- **Access:** Alle som er admin i minst 1 fylke
- **Method:** `POST`
- **Description:** Returnerer gjennomsnitt aldersfordeling på arrangementer på fylkesnivå. Det tas kun arrangementer som er arrangert i fylke.
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required
| `excludePlId`   | `number` | Arrangement ID som skal ekskluderes | Optional |

#### Response Example
```json
{
  "11": 4.47,
  "12": 11.88,
  "13": 23.53,
  "14": 27.71,
  "15": 35.24,
  "16": 44.53,
  "17": 35.82,

  ...

  "25": 0.06,
  "29": 0.06,
  "2": 0.06,
  "22": 0.24,
  "6": 0.06
}
```



### Kjønnsfordeling i hele landet

- **URL:** `land/kjonnsfordeling`
- **Access:** Alle som er administrator i minst 1 fylke
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


### Gjennomsnitt kjønnsfordeling på alle arrangemeter på fylkesnivå

- **URL:** `land/kjonnsfordelingGjennomsnittFylkesniva`
- **Access:** Alle som er administrator i minst 1 fylke
- **Method:** `POST`
- **Description:** Henter kjønnsfordeling gjennomsnitt på alle arrangemeter på fylkesnivå (arrangementer i fylke)
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required
| `excludePlId`   | `number` | Arrangement ID som skal ekskluderes | Optional |

#### Response Example
```json
{
  "male":106.94,
  "female":108.35,
  "unknown":32.47
}
```

### Sjangerfordeling gjennomsnitt på alle arrangementer på fylkesnivå

- **URL:** `land/sjangerfordeling`
- **Access:** Alle som er administrator i minst 1 fylke
- **Method:** `POST`
- **Description:** Sjangerfordeling på alle arrangementer fra alle kommuner. HUSK: at det ikke blir med fylkesarrangementer
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required

#### Response Example
```json
{
  "Utstilling": 3868,
  "Musikk": 6311,
  "Arrangør": 572,
  "Konferansier": 298,
  "Dans": 974,
  "Film": 396,
  "Litteratur": 83,
  "Noe annet på scene": 203,
  "Media": 135,
  "Teater": 112,
  "Ressurs": 1,
  "Ukjent": 35,
  "Matkultur": 8
}
```


### Sjangerfordeling gjennomsnitt på alle arrangementer på fylkesnivå

- **URL:** `land/sjangerfordelingGjennomsnittFylkesniva`
- **Access:** Alle som er administrator i minst 1 fylke
- **Method:** `POST`
- **Description:** Sjangerfordeling gjennomsnitt på alle arrangementer arrangert på fylkesnivå i en sesong
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required
| `excludePlId`   | `number` | Arrangement ID som skal ekskluderes | Optional |

#### Response Example
```json
{
  "Musikk": 63.89,
  "Film": 3.84,
  "Dans": 8.58,
  "Utstilling": 51.47,
  "Noe annet på scene": 5.84,
  "Media": 7.37,
  "Konferansier": 5.37,
  "Arrangør": 11.05,
  "Litteratur": 1.26,
  "Teater": 1,
  "Ukjent": 0.05,
  "Matkultur": 0.05
}
```


### Gjennomsnitt deltakere på landsnivå

- **URL:** `land/gjennomsnittDeltakere`
- **Access:** Alle som er administrator i minst 1 fylke
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
- **Access:** Superadmin tilgang
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


### Gjennomsnitt deltakere på fylkesarrangementer

- **URL:** `land/gjennomsnittDeltakereFylkesniva`
- **Access:** Alle som er administrator i minst 1 fylke
- **Method:** `POST`
- **Description:** Returnerer antatall (gjennomsnitt) deltaker på alle arrangementer på fylkesnivå på alle fylker i en sesong
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required
| `excludePlId`   | `number` | Arrangement ID som skal ekskluderes | Optional |

#### Response Example
```json
{"antall":259}

```


### Aktivitet på alle kommuner

- **URL:** `land/alleKommunerAktivitet`
- **Access:** Superadmin tilgang
- **Method:** `POST`
- **Description:** Returnerer alle kommuner i en sesong og indikasjon om de har aktivitet i sesongen. Som aktivitet, regnes det minst 1 arrangement
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required

#### Response Example
```json
{
   "season":"2020",
   "kommuner":{
      "0":{
         "navn":"ukjent",
         "aktivitet":false
      },
      "1101":{
         "navn":"Eigersund",
         "aktivitet":false
      },
      "1103":{
         "navn":"Stavanger",
         "aktivitet":false
      },

      ...
  }
}
```



### Antall arrangementer fordelt på type

- **URL:** `land/antallArrangemenTyperLandsniva`
- **Access:** Superadmin tilgang
- **Method:** `POST`
- **Description:** Returnerer antall arrangementtyper på landsnivå lokalt (uten fylkesarrangementer)
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|


#### Response Example
```json
{
  "monstring":2463,
  "arrangement":11
}
```


### Antall arrangementer

- **URL:** `land/antallArrangementerLandsniva`
- **Access:** Superadmin tilgang
- **Method:** `POST`
- **Description:** Returnerer antall arrangementer i hele landet
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|


#### Response Example
```json
{
  "antall":2476
}
```



## Landsfestivalen (UKMFestivalen)
Statistikk om landsfestivalen


### Antall deltakere på landsfestivalen

- **URL:** `land/landsfestivalen/antallDeltakere`
- **Access:** Alle som har tigang til arrangementet
- **Method:** `POST`
- **Description:** Returnerer antall deltakere med mulighet for å hente unike eller ikke unike
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required |
| `unike`   | `boolean` | Unike eller ikke unike deltakere | Required |

#### Response Example
```json
{
  "erUnike":true,
  "antall":0
}
```


### Aldersfordeling på landsfestivalen

- **URL:** `land/landsfestivalen/aldersfordeling`
- **Access:** Alle som har tigang til arrangementet
- **Method:** `POST`
- **Description:** Antall deltakere fordelt på alder på landsfestivalen definert av sesong.
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required |

#### Response Example
```json
[
  {
    "age":"11",
    "antall":"4"
  },
  {
    "age":"12",
    "antall":"6"
  },

  ...

]
```


### Kjønnsfordeling på landsfestivalen

- **URL:** `land/landsfestivalen/kjonnsfordeling`
- **Access:** Alle som har tigang til arrangementet
- **Method:** `POST`
- **Description:** Antall deltakere fordelt på kjønn på landsfestivalen definert av sesong.
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required |

#### Response Example
```json
{
  "male":254,
  "female":151,
  "unknown":66
}
```



### Sjangerfordeling på landsfestivalen

- **URL:** `land/landsfestivalen/sjangerfordeling`
- **Access:** Alle som har tigang til arrangementet
- **Method:** `POST`
- **Description:** Antall deltakere fordelt på sjanger på landsfestivalen definert av sesong.
#### URL Parameters

| Parameter | Type   | Description                | Info |
|-----------|--------|----------------------------|------|
| `season`   | `number` | Sesong | Required |

#### Response Example
```json
{
  "video": {
    "antall": 17,
    "type_navn": "Film"
  },
  "musikk": {
    "antall": 59,
    "type_navn": "Musikk"
  },
  "matkultur": {
    "antall": 6,
    "type_navn": "Matkultur"
  },
  "dans": {
    "antall": 19,
    "type_navn": "Dans"
  },
  "nettredaksjon": {
    "antall": 16,
    "type_navn": "Media"
  },
  "utstilling": {
    "antall": 54,
    "type_navn": "Utstilling"
  },
  "konferansier": {
    "antall": 10,
    "type_navn": "Konferansier"
  },
  "teater": {
    "antall": 2,
    "type_navn": "Teater"
  },
  "scene": {
    "antall": 7,
    "type_navn": "Noe annet på scene"
  },
  "ressurs": {
    "antall": 7,
    "type_navn": "Ressurs"
  },
  "arrangor": {
    "antall": 2,
    "type_navn": "Arrangør"
  },
  "ukjent": {
    "antall": 1,
    "type_navn": "Ukjent"
  }
}
```
