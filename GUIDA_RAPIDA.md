# 🚀 Guida Rapida - Event Interactive Map

Una guida passo-passo completa per usare il plugin WordPress **Event Interactive Map**.

---

## 📥 STEP 1: Installazione

### Metodo Semplice (Consigliato)

1. **Scarica** il file `event-interactive-map.zip`
2. Vai su **WordPress Admin** → **Plugin** → **Aggiungi Nuovo**
3. Clicca **Carica Plugin** (in alto)
4. Clicca **Scegli File** e seleziona lo .zip
5. Clicca **Installa Ora**
6. Clicca **Attiva Plugin**

✅ **Fatto!** Ora vedrai una nuova voce "**Event POIs**" nel menu laterale di WordPress.

---

## ➕ STEP 2: Creare il Primo Evento

### 2.1 Accedi alla Pagina Eventi

1. Nel menu admin WordPress, clicca su **Event POIs**
2. Clicca **Aggiungi Nuovo** (in alto)

### 2.2 Compila i Dati Base

Vedrai l'editor WordPress standard:

**Campi principali:**
- **Titolo**: Nome dell'evento (es. "Concerto Rock 2025")
- **Contenuto**: Descrizione completa dell'evento (usa l'editor visuale)
- **Estratto** (facoltativo): Breve descrizione per la mappa

### 2.3 Imposta Tipo e Data (Meta Box in basso)

Scorri in basso fino alla sezione **"Event Details & Location"**:

1. **Event Type** (Tipo Evento):
   - Seleziona dal menu a tendina:
     - 🎵 Concert (Concerto)
     - 🎨 Exhibition (Mostra/Esposizione)
     - 👥 Conference (Conferenza)
     - 📚 Workshop (Laboratorio)
     - 🎫 Festival
     - 🏆 Sports (Sport)
     - 📍 Other (Altro)

2. **Event Date**: Clicca sul calendario e seleziona la data

3. **Event Time**: Seleziona l'ora (formato 24h)

### 2.4 Imposta la Posizione sulla Mappa 🗺️

**Questa è la parte più importante!**

Hai **3 modi** per impostare la posizione:

#### Metodo A: Ricerca Indirizzo (PIÙ FACILE)

1. Nel campo **Address**, scrivi l'indirizzo completo:
   ```
   Esempio: Piazza del Duomo, Milano, Italia
   ```

2. Clicca il pulsante **Search Address**

3. La mappa si sposterà automaticamente e il marker si posizionerà

4. I campi **Latitude** e **Longitude** si compileranno automaticamente ✅

#### Metodo B: Click sulla Mappa

1. Clicca direttamente sulla mappa nel punto desiderato

2. Il marker si sposterà dove hai cliccato

3. Le coordinate si aggiorneranno automaticamente

#### Metodo C: Trascinamento Marker

1. Clicca sul marker (il simbolo rosso sulla mappa)

2. Trascinalo nel punto esatto

3. Rilascia il mouse

4. Le coordinate si aggiorneranno

#### Metodo D: Coordinate Manuali (AVANZATO)

Se hai già le coordinate GPS:

1. Inserisci **Latitude** (es. `45.464664`)
2. Inserisci **Longitude** (es. `9.188540`)
3. Il marker si sposterà automaticamente

---

### 2.5 Pubblica l'Evento

1. Rivedi tutti i dati
2. Clicca **Pubblica** (o **Aggiorna** se stai modificando)

✅ **Il tuo primo evento è creato!**

---

## 🗺️ STEP 3: Mostrare la Mappa sul Sito

### 3.1 Crea una Pagina per la Mappa

1. Vai su **Pagine** → **Aggiungi Nuova**
2. Dai un titolo (es. "Mappa Eventi")

### 3.2 Inserisci lo Shortcode

Nell'editor della pagina, aggiungi questo codice:

```
[event_map]
```

**Shortcode Base** - Usa le impostazioni predefinite

### 3.3 Shortcode Personalizzato (Opzionale)

Puoi personalizzare altezza, zoom e centro:

```
[event_map height="700px" zoom="12" center_lat="45.464664" center_lng="9.188540"]
```

**Parametri disponibili:**
- `height` → Altezza mappa (default: 500px)
- `zoom` → Livello zoom 1-19 (default: 13)
- `center_lat` → Latitudine centro
- `center_lng` → Longitudine centro

### 3.4 Pubblica la Pagina

1. Clicca **Pubblica**
2. Clicca **Visualizza Pagina**

🎉 **La tua mappa interattiva è live!**

---

## 🎯 Come Funziona la Mappa per gli Utenti

Gli utenti del tuo sito vedranno una mappa interattiva con:

### Controlli Disponibili

**Barra in alto alla mappa:**

1. **Campo Ricerca** 🔍
   - Cerca qualsiasi località nel mondo
   - Premi Invio o clicca l'icona lente

2. **Filtro per Tipo** 📋
   - Menu a tendina "All Types"
   - Mostra solo eventi di un tipo specifico
   - Esempio: Seleziona "Concert" per vedere solo concerti

3. **Locate Me** 📍
   - Trova la posizione dell'utente via GPS
   - Richiede permesso del browser

4. **Reset View** 🔄
   - Torna alla vista iniziale con tutti gli eventi

### Marker Colorati

Ogni tipo di evento ha un **colore e icona** diversi:

- 🟣 **Viola** → Concert
- 🔴 **Rosso** → Exhibition
- 🔵 **Blu** → Conference
- 🟠 **Arancione** → Workshop
- 🟤 **Arancio Scuro** → Festival
- 🟢 **Verde** → Sports
- ⚫ **Grigio** → Other

### Clustering Automatico

Se hai molti eventi vicini, vengono **raggruppati** in cerchi con numeri.

- Clicca sul cerchio per espanderlo
- Più zoom fai, più marker separati vedrai

### Popup Informativi

Cliccando su un marker si apre un popup con:
- **Titolo evento**
- **Badge tipo** (colorato)
- **Data e ora** (con icona calendario)
- **Estratto descrizione**
- **Link "View Details"** → Va alla pagina completa dell'evento

---

## 📂 Gestire Multipli Eventi

### Aggiungere Altri Eventi

1. **Event POIs** → **Aggiungi Nuovo**
2. Ripeti la procedura dello STEP 2
3. Ogni evento apparirà automaticamente sulla mappa

### Modificare un Evento Esistente

1. **Event POIs** → **Tutti gli Event POIs**
2. Clicca sul titolo dell'evento da modificare
3. Modifica i dati
4. Clicca **Aggiorna**

### Eliminare un Evento

1. **Event POIs** → **Tutti gli Event POIs**
2. Passa il mouse sull'evento
3. Clicca **Cestina**
4. Scomparirà automaticamente dalla mappa

### Vedere Tutti gli Eventi in Lista

1. **Event POIs** (menu laterale)
2. Vedrai una tabella con:
   - Titolo
   - Tipo (se impostato)
   - Data pubblicazione
   - Autore

---

## 🔧 Risoluzione Problemi Comuni

### ❌ La mappa admin non si carica

**Soluzioni:**

1. **Controlla la Console Browser**:
   - Premi F12 (Windows) o Cmd+Opt+I (Mac)
   - Vai su tab "Console"
   - Cerca errori in rosso
   - Se vedi "Leaflet not loaded" → Problema con librerie

2. **Svuota Cache**:
   - Premi Ctrl+F5 (Windows) o Cmd+Shift+R (Mac)
   - Se usi plugin di cache, svuotala

3. **Verifica Conflitti Plugin**:
   - Disattiva temporaneamente altri plugin
   - Riattiva Event Interactive Map
   - Testa se funziona

4. **Controlla JavaScript**:
   - Alcuni plugin blocca-ads potrebbero interferire
   - Prova in modalità incognito

### ❌ Le coordinate non si salvano

1. Verifica di aver cliccato **Pubblica** o **Aggiorna**
2. Controlla che i campi Lat/Lng siano compilati
3. Assicurati che non ci siano errori in console

### ❌ La ricerca indirizzo non funziona

1. Verifica connessione internet
2. Prova con un indirizzo più specifico:
   ❌ "Milano" → ✅ "Piazza Duomo, Milano, Italia"
3. Nominatim OSM ha rate limiting (max 1 richiesta al secondo)

### ❌ Gli eventi non appaiono sulla mappa frontend

1. Verifica che gli eventi siano **Pubblicati** (non in bozza)
2. Controlla che abbiano coordinate valide
3. Apri Console browser (F12) e cerca errori
4. Controlla che lo shortcode sia corretto: `[event_map]`

### ❌ Il marker è nella posizione sbagliata

1. Ricontrolla le coordinate Lat/Lng
2. Ricerca di nuovo l'indirizzo
3. Verifica di non aver invertito Latitudine e Longitudine

---

## 💡 Tips & Tricks

### 1. Trovare Coordinate di un Luogo

**Metodo Google Maps:**
1. Apri Google Maps
2. Cerca il luogo
3. Click destro sul punto
4. Clicca "Coordinate GPS"
5. Copia e incolla nel plugin

**Metodo Diretto:**
- Usa il campo Address e clicca Search!

### 2. Testare la Mappa Prima di Pubblicare

1. Salva la pagina come **Bozza**
2. Clicca **Anteprima**
3. Controlla che tutto funzioni
4. Poi clicca **Pubblica**

### 3. Creare Pagine Dedicate per Ogni Evento

Gli eventi sono un Custom Post Type, quindi hanno URL propri:
```
https://tuosito.com/events/nome-evento/
```

Puoi linkare direttamente a queste pagine!

### 4. Widget Sidebar (Tema che supporta widget HTML)

1. Vai su **Aspetto** → **Widget**
2. Aggiungi widget **HTML Personalizzato**
3. Inserisci:
```
[event_map height="400px" zoom="10"]
```
4. Salva

### 5. Template PHP (per sviluppatori)

Nel tuo tema, puoi usare:
```php
<?php echo do_shortcode('[event_map]'); ?>
```

### 6. Filtrare Eventi per Data (Futuro)

Attualmente tutti gli eventi pubblicati vengono mostrati.

Per mostrare solo eventi futuri, devi modificare:
`includes/map-rest-api.php` (linea 12-13)

### 7. Limitare Numero Eventi

In `includes/map-rest-api.php`, cambia linea 10:
```php
'numberposts' => 50,  // Invece di -1
```

---

## 🎨 Personalizzazione Avanzata

### Cambiare Colori Marker

Modifica `assets/css/style.css` (circa linea 128-189):

```css
.eim-marker-concert {
    border-color: #9b59b6;  /* Cambia questo colore */
    background: #e8daef;     /* E questo */
}
```

### Aggiungere Nuovi Tipi Eventi

Richiede modifiche a 3 file:
1. `includes/cpt-poi.php` (linea 81-89) - Aggiungi option al select
2. `event-interactive-map.php` (linea 67-74) - Aggiungi traduzione
3. `assets/js/map.js` (linea 12-61) - Aggiungi icona
4. `assets/css/style.css` - Aggiungi stili marker

### Cambiare Icone Marker

Le icone usano WordPress Dashicons.

Lista icone disponibili:
https://developer.wordpress.org/resource/dashicons/

In `assets/js/map.js`, cambia l'HTML dell'icona:
```javascript
html: '<span class="dashicons dashicons-tua-icona"></span>'
```

---

## 📞 Supporto

### Hai ancora problemi?

1. **Controlla README.md** - Sezione troubleshooting completa
2. **Console Browser** - Apri F12 e guarda errori
3. **Debug Mode WordPress** - Attiva in wp-config.php
4. **Contatta Supporto** - Apri issue su GitHub

### Risorse Utili

- **Documentazione Leaflet**: https://leafletjs.com/
- **WordPress Codex**: https://codex.wordpress.org/
- **Dashicons**: https://developer.wordpress.org/resource/dashicons/

---

## ✅ Checklist Finale

Prima di dichiarare il plugin funzionante:

- [ ] Plugin installato e attivato
- [ ] Voce "Event POIs" visibile nel menu admin
- [ ] Creato almeno 1 evento di test
- [ ] Mappa admin si carica correttamente
- [ ] Coordinate impostate correttamente
- [ ] Evento salvato e pubblicato
- [ ] Pagina con shortcode creata
- [ ] Mappa frontend visualizzata
- [ ] Marker appare sulla mappa
- [ ] Popup si apre cliccando sul marker
- [ ] Filtri funzionano
- [ ] Nessun errore in console browser

---

## 🎉 Congratulazioni!

Hai completato la configurazione di **Event Interactive Map**!

Ora puoi:
- ✅ Aggiungere illimitati eventi
- ✅ Mostrare mappe interattive
- ✅ Filtrare per tipo evento
- ✅ Geolocalizzare utenti
- ✅ Creare un'esperienza utente moderna

**Buon lavoro con il tuo plugin! 🗺️**
