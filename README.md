# Event Interactive Map

Un plugin WordPress professionale per creare mappe interattive di eventi con POI (Points of Interest), filtri avanzati e ottimizzazione per mobile.

## ⚡ Quick Start

1. **Installa** il plugin caricando `event-interactive-map.zip` in WordPress
2. **Attiva** il plugin dal menu Plugin
3. **Crea** un nuovo evento da **Event POIs → Add New**
4. **Imposta** la posizione sulla mappa interattiva
5. **Inserisci** lo shortcode `[event_map]` in una pagina
6. **Visualizza** la tua mappa interattiva con gli eventi!

## 🎯 Caratteristiche

### Funzionalità Base
- ✅ **Custom Post Type** per gestire eventi/POI
- ✅ **Mappa interattiva** basata su Leaflet.js
- ✅ **Shortcode** `[event_map]` per inserire la mappa ovunque
- ✅ **REST API** sicura per recuperare i dati

### Funzionalità Avanzate
- ✅ **Meta Box Admin** con mappa interattiva per impostare coordinate
- ✅ **Ricerca indirizzo** integrata (Nominatim OSM)
- ✅ **7 tipi di eventi** con icone personalizzate
- ✅ **Filtri per tipo** evento
- ✅ **Marker clustering** per performance ottimali
- ✅ **Auto-centering** della mappa sui POI
- ✅ **Geolocalizzazione** utente
- ✅ **Ricerca località** in tempo reale
- ✅ **Responsive design** ottimizzato per mobile
- ✅ **Dark mode** support
- ✅ **Internazionalizzazione** (i18n ready)
- ✅ **Gestione errori** completa

## 📦 Installazione

### Metodo 1: Upload tramite WordPress Admin (Consigliato)

1. Scarica il file `event-interactive-map.zip`
2. Accedi al pannello admin di WordPress
3. Vai su **Plugin → Aggiungi Nuovo → Carica Plugin**
4. Clicca su **Scegli File** e seleziona `event-interactive-map.zip`
5. Clicca su **Installa Ora**
6. Una volta installato, clicca su **Attiva Plugin**
7. Il plugin è pronto! Vai su **Event POIs** nel menu admin

### Metodo 2: Installazione Manuale via FTP

1. Estrai il file `event-interactive-map.zip` sul tuo computer
2. Carica la cartella `event-interactive-map` nella directory `/wp-content/plugins/` del tuo server
3. Connettiti via FTP o usa il File Manager del tuo hosting
4. Vai nel pannello admin WordPress → **Plugin**
5. Trova "Event Interactive Map" nella lista e clicca **Attiva**

### Metodo 3: Installazione da Repository (se pubblicato)

1. Nel pannello admin WordPress, vai su **Plugin → Aggiungi Nuovo**
2. Cerca "Event Interactive Map"
3. Clicca **Installa Ora** e poi **Attiva**

### Post-Installazione

Dopo l'attivazione:
- Troverai una nuova voce **Event POIs** nel menu admin
- Il plugin creerà automaticamente il custom post type
- Inizia a creare eventi subito!

## 🚀 Utilizzo

### Shortcode Base
```
[event_map]
```

### Shortcode con Parametri
```
[event_map height="600px" zoom="14" center_lat="45.0" center_lng="7.6"]
```

#### Parametri disponibili:
- `height` - Altezza della mappa (default: 500px)
- `zoom` - Livello di zoom iniziale (default: 13)
- `center_lat` - Latitudine centro mappa (default: 45.0)
- `center_lng` - Longitudine centro mappa (default: 7.6)

### Creare un Evento/POI

1. Vai su **Event POIs → Add New**
2. Inserisci titolo e descrizione
3. Compila i campi:
   - **Tipo evento** (Concert, Exhibition, Conference, Workshop, Festival, Sports, Other)
   - **Data e ora** evento
   - **Indirizzo** (opzionale, per ricerca automatica)
4. **Imposta la posizione sulla mappa**:
   - Cerca un indirizzo usando il pulsante "Search Address"
   - Oppure clicca sulla mappa
   - Oppure trascina il marker
   - Le coordinate si aggiornano automaticamente
5. Pubblica l'evento

### Esempi di Utilizzo Avanzato

#### Mappa eventi per una città specifica
```
[event_map center_lat="45.464664" center_lng="9.188540" zoom="12" height="700px"]
```
Questo esempio centra la mappa su Milano con zoom 12.

#### Mappa full-screen per landing page eventi
```html
<div style="height: 100vh; width: 100%;">
    [event_map height="100vh"]
</div>
```

#### Incorporare in sidebar (widget HTML)
```
[event_map height="400px" zoom="10"]
```

#### Mappa in template PHP
```php
<?php echo do_shortcode('[event_map height="600px"]'); ?>
```

#### Mappa con coordinate personalizzate
```
[event_map center_lat="41.9028" center_lng="12.4964" zoom="13"]
```
Esempio per Roma.

### Uso con Page Builder

#### Elementor
1. Aggiungi widget **Shortcode**
2. Inserisci: `[event_map height="600px"]`
3. Personalizza altezza nel codice

#### Divi Builder
1. Aggiungi modulo **Code**
2. Inserisci lo shortcode
3. Imposta altezza del modulo

#### Visual Composer
1. Aggiungi elemento **Raw HTML**
2. Inserisci shortcode
3. Salva e visualizza

## 🎨 Tipi di Eventi e Icone

Il plugin supporta 7 tipi di eventi con icone e colori distintivi:

| Tipo | Icona | Colore |
|------|-------|--------|
| **Concert** | 🎵 | Viola |
| **Exhibition** | 🎨 | Rosso |
| **Conference** | 👥 | Blu |
| **Workshop** | 📚 | Arancione |
| **Festival** | 🎫 | Arancio Scuro |
| **Sports** | 🏆 | Verde |
| **Other** | 📍 | Grigio |

## 🔧 Funzionalità della Mappa

### Controlli Utente
- **Ricerca località** - Cerca qualsiasi indirizzo o luogo
- **Filtro per tipo** - Mostra solo eventi di un tipo specifico
- **Geolocalizzazione** - Trova la tua posizione attuale
- **Reset vista** - Torna alla vista originale con tutti gli eventi

### Popup Informazioni
Ogni marker mostra:
- Titolo evento
- Tipo evento (badge colorato)
- Data e ora
- Estratto descrizione
- Link "View Details" all'evento completo

### Performance
- **Marker clustering** - Raggruppa marker vicini per migliori prestazioni
- **Lazy loading** - Carica dati solo quando necessario
- **Caching browser** - Riduce chiamate API

## 🔒 Sicurezza

Il plugin implementa le best practice di sicurezza WordPress:
- ✅ Sanitizzazione di tutti gli input
- ✅ Validazione dati lato server
- ✅ Nonce verification
- ✅ Permission callbacks su REST API
- ✅ Escape di tutti gli output (prevenzione XSS)
- ✅ Prepared statements per query DB

## 📱 Mobile & Responsive

- Design completamente responsive
- Touch-friendly controls (44px minimum)
- Controlli ottimizzati per schermi piccoli
- Marker ridimensionati su mobile
- Popup adattivi
- Supporto gesture (pinch-to-zoom, pan)

## 🌍 REST API

### Endpoint Principale
```
GET /wp-json/eim/v1/pois
```

### Parametri Query
- `type` - Filtra per tipo evento (es. `?type=concert`)

### Risposta
```json
[
  {
    "id": 123,
    "title": "Summer Music Festival",
    "content": "<p>Description...</p>",
    "excerpt": "Short description...",
    "lat": 45.123456,
    "lng": 7.654321,
    "type": "festival",
    "date": "2025-07-15",
    "time": "20:00",
    "permalink": "https://example.com/events/summer-festival"
  }
]
```

## 🎨 Personalizzazione

### CSS Custom
Puoi sovrascrivere gli stili aggiungendo CSS personalizzato nel tuo tema:

```css
/* Cambia colore marker concert */
.eim-marker-concert {
    border-color: #your-color !important;
    background: #your-bg-color !important;
}

/* Cambia altezza mappa */
#event-map {
    min-height: 700px !important;
}
```

### Modificare Tipi di Eventi
Puoi aggiungere nuovi tipi modificando `includes/cpt-poi.php` (linea 81-89)

## 🔌 Dipendenze

Il plugin carica automaticamente:
- **Leaflet.js** v1.9.4 - Libreria mappe
- **Leaflet.markercluster** v1.5.3 - Clustering marker
- **Nominatim OSM** - Geocoding (ricerca indirizzi)
- **WordPress Dashicons** - Icone UI

## 🌐 Browser Supportati

- Chrome/Edge (ultime 2 versioni)
- Firefox (ultime 2 versioni)
- Safari (ultime 2 versioni)
- Mobile browsers (iOS Safari, Chrome Mobile)

## ⚙️ Requisiti di Sistema

- WordPress 5.0+
- PHP 7.4+
- Browser moderno con supporto JavaScript ES6

## ❓ FAQ (Domande Frequenti)

### Come cambio le coordinate predefinite della mappa?

Usa i parametri dello shortcode:
```
[event_map center_lat="41.9028" center_lng="12.4964" zoom="12"]
```

### Posso usare Google Maps invece di OpenStreetMap?

Attualmente il plugin usa Leaflet con tile di OpenStreetMap. Per usare Google Maps dovresti modificare il codice JavaScript in `assets/js/map.js` e ottenere una API key di Google Maps. Questa feature potrebbe essere aggiunta in futuro.

### Come aggiungo nuovi tipi di eventi?

1. Modifica `includes/cpt-poi.php` (linea 81-89) per aggiungere opzioni nel select
2. Modifica `event-interactive-map.php` (linea 67-74) per tradurre i nuovi tipi
3. Aggiungi icone in `assets/js/map.js` (linea 12-61)
4. Aggiungi stili in `assets/css/style.css` per i colori dei marker

### La mappa può mostrare eventi passati?

Sì, tutti gli eventi pubblicati vengono mostrati. Se vuoi filtrare automaticamente gli eventi passati, dovresti modificare la REST API in `includes/map-rest-api.php` aggiungendo un filtro per data.

### Posso limitare il numero di eventi mostrati?

Sì, modifica `includes/map-rest-api.php` linea 10:
```php
'numberposts' => 50,  // Invece di -1 (tutti)
```

### Come faccio a tradurre il plugin in un'altra lingua?

1. Il plugin è i18n-ready con text domain `event-interactive-map`
2. Usa software come Poedit per creare file .po/.mo
3. Carica i file nella cartella `/languages/`
4. Esempio: `event-interactive-map-it_IT.po` per italiano

### Posso usare più mappe sulla stessa pagina?

Al momento il plugin supporta una mappa per pagina. Per usare più mappe dovresti modificare il JavaScript per supportare istanze multiple.

### Come esporto tutti gli eventi?

Al momento non c'è una funzione di export. Puoi usare:
- Plugin "WP All Export" per esportare il Custom Post Type
- REST API endpoint `/wp-json/eim/v1/pois` per ottenere JSON
- Feature in roadmap per CSV/JSON export

### Il plugin funziona con Gutenberg?

Sì, usa lo shortcode `[event_map]` nel blocco "Shortcode" di Gutenberg. Un blocco Gutenberg nativo è nella roadmap.

### Supporta cache plugins?

Sì, il plugin è compatibile con:
- WP Super Cache
- W3 Total Cache
- WP Rocket
- LiteSpeed Cache

Assicurati di svuotare la cache dopo aggiornamenti.

## 🐛 Debug & Troubleshooting

### La mappa non si carica
1. Verifica che JavaScript sia abilitato
2. Controlla la console browser per errori
3. Verifica che le coordinate siano valide
4. Controlla che ci siano eventi pubblicati

### Marker non appaiono
1. Verifica che gli eventi abbiano coordinate valide
2. Controlla che lat/lng siano numeri (non stringhe vuote)
3. Verifica stato pubblicazione eventi

### Ricerca indirizzo non funziona
1. Controlla connessione internet
2. Nominatim OSM potrebbe avere rate limiting
3. Prova con indirizzi più specifici

### Plugin non si attiva
1. Verifica requisiti minimi (WordPress 5.0+, PHP 7.4+)
2. Controlla conflitti con altri plugin
3. Attiva modalità debug WordPress per vedere errori dettagliati

### Coordinate sbagliate nella mappa admin
1. Cancella cache del browser
2. Ricarica la pagina con Ctrl+F5 (Windows) o Cmd+Shift+R (Mac)
3. Verifica che Leaflet.js si carichi correttamente (Console Developer Tools)

## 📄 Licenza

Questo plugin è rilasciato sotto licenza GPL v2 o successiva.

## 👨‍💻 Autore

**MartoEporedia**

## 🔄 Changelog

### Version 1.0.0 (2025)
- ✨ Release iniziale
- ✅ Meta box admin con mappa interattiva
- ✅ 7 tipi di eventi con icone personalizzate
- ✅ Marker clustering
- ✅ Filtri e ricerca
- ✅ Geolocalizzazione
- ✅ Auto-centering
- ✅ Responsive design
- ✅ Internazionalizzazione
- ✅ Sicurezza avanzata
- ✅ Gestione errori completa
- ✅ Dark mode support

## 🚀 Roadmap Future

Possibili sviluppi futuri:
- [ ] Widget WordPress
- [ ] Gutenberg block
- [ ] Esportazione CSV/JSON eventi
- [ ] Importazione eventi da iCal
- [ ] Integrazione Google Maps (opzionale)
- [ ] Categorie eventi personalizzate
- [ ] Campo prezzo/ticket
- [ ] Galleria immagini eventi
- [ ] Registrazione utenti agli eventi
- [ ] Notifiche email
- [ ] Integrazione social sharing

## 🧪 Test in Locale

### Ambiente di Sviluppo

Per testare il plugin in locale, hai diverse opzioni:

#### 1. Local by Flywheel (Consigliato)
```bash
# Scarica e installa Local by Flywheel
# https://localwp.com/

# Crea un nuovo sito WordPress
# Naviga in: /app/public/wp-content/plugins/
# Clona o copia la cartella del plugin
```

#### 2. XAMPP/WAMP/MAMP
```bash
# Dopo aver installato XAMPP/WAMP/MAMP
# Copia la cartella in:
# - Windows: C:\xampp\htdocs\wordpress\wp-content\plugins\
# - Mac: /Applications/MAMP/htdocs/wordpress/wp-content/plugins/
# - Linux: /opt/lampp/htdocs/wordpress/wp-content/plugins/
```

#### 3. Docker (WordPress)
```bash
# Usa WordPress con Docker
docker run -d \
  -p 8080:80 \
  -e WORDPRESS_DB_HOST=db \
  -e WORDPRESS_DB_USER=wordpress \
  -e WORDPRESS_DB_PASSWORD=wordpress \
  -v $(pwd)/event-interactive-map:/var/www/html/wp-content/plugins/event-interactive-map \
  wordpress:latest
```

### Debug Mode

Abilita il debug in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

Questo ti permetterà di:
- Vedere errori PHP nel file `/wp-content/debug.log`
- Caricare versioni non-minified di script
- Identificare problemi rapidamente

### Test Checklist

Prima di rilasciare una versione:

- [ ] Attivazione/disattivazione plugin funziona
- [ ] Creazione nuovo evento POI funziona
- [ ] Mappa admin si carica correttamente
- [ ] Geocoding indirizzo funziona
- [ ] Marker draggable funziona
- [ ] Shortcode visualizza mappa
- [ ] Filtri per tipo evento funzionano
- [ ] Geolocalizzazione funziona
- [ ] Ricerca località funziona
- [ ] Marker clustering attivo
- [ ] Responsive su mobile
- [ ] Nessun errore in console JavaScript
- [ ] Nessun errore PHP in debug.log
- [ ] REST API risponde correttamente
- [ ] Test su browser diversi (Chrome, Firefox, Safari, Edge)

### Test su Browser

```bash
# Browser supportati da testare:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile Safari (iOS)
- Chrome Mobile (Android)
```

### Test Performance

Verifica le performance con:
- **Google PageSpeed Insights**: https://pagespeed.web.dev/
- **GTmetrix**: https://gtmetrix.com/
- **WebPageTest**: https://www.webpagetest.org/

Obiettivi:
- Tempo di caricamento iniziale < 3s
- Clustering efficace con 100+ marker
- Nessun memory leak su uso prolungato

## 📦 Creare il Pacchetto di Distribuzione

Se stai sviluppando il plugin o vuoi creare il file .zip per la distribuzione:

### Da Riga di Comando (Linux/Mac/WSL)

```bash
# Naviga nella directory parent del plugin
cd /path/to/parent-directory

# Crea il pacchetto .zip escludendo file non necessari
zip -r event-interactive-map.zip event-interactive-map \
  -x "*.git*" \
  -x "*node_modules*" \
  -x "*.DS_Store" \
  -x "*Thumbs.db"
```

### Da Windows (PowerShell)

```powershell
# Naviga nella directory parent
cd C:\path\to\parent-directory

# Comprimi la cartella
Compress-Archive -Path event-interactive-map -DestinationPath event-interactive-map.zip -Force
```

### File da Includere nel Pacchetto

Il pacchetto .zip deve contenere:
```
event-interactive-map/
├── .gitignore
├── README.md
├── event-interactive-map.php (file principale)
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── map.js
├── includes/
│   ├── cpt-poi.php
│   └── map-rest-api.php
└── templates/
    └── map-template.php
```

### File da ESCLUDERE

**NON includere** nel pacchetto:
- `.git/` e `.gitignore` (opzionale per distribuzione)
- `node_modules/`
- File di sistema: `.DS_Store`, `Thumbs.db`
- File IDE: `.vscode/`, `.idea/`
- File di test e sviluppo

### Verifica del Pacchetto

Prima di distribuire, verifica il contenuto:

```bash
# Visualizza il contenuto del .zip
unzip -l event-interactive-map.zip

# Estrai in una directory di test
unzip event-interactive-map.zip -d test-install/
```

### Dimensione Tipica

Il pacchetto finale dovrebbe essere circa **15-20 KB** (compresso).

## 🤝 Contributing

Per segnalare bug o proporre miglioramenti, apri una issue nel repository.

### Come Contribuire

1. **Fork** il repository
2. Crea un **branch** per la tua feature (`git checkout -b feature/amazing-feature`)
3. **Commit** le modifiche (`git commit -m 'Add amazing feature'`)
4. **Push** al branch (`git push origin feature/amazing-feature`)
5. Apri una **Pull Request**

### Coding Standards

- Segui i [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Usa PHPDoc per documentare le funzioni
- Sanitizza e valida tutti gli input
- Testa su diverse versioni di WordPress e PHP

## 📞 Supporto

Per supporto tecnico o domande:
- Consulta la documentazione
- Controlla la sezione Troubleshooting
- Contatta l'autore

---

**Grazie per aver scelto Event Interactive Map!** 🗺️✨
