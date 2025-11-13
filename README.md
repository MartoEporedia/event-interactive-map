# Event Interactive Map

Un plugin WordPress professionale per creare mappe interattive di eventi con POI (Points of Interest), filtri avanzati e ottimizzazione per mobile.

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

1. Carica la cartella `event-interactive-map` nella directory `/wp-content/plugins/`
2. Attiva il plugin dal menu "Plugin" in WordPress
3. Inizia a creare eventi da "Event POIs" nel menu admin

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

## 🤝 Contributing

Per segnalare bug o proporre miglioramenti, apri una issue nel repository.

## 📞 Supporto

Per supporto tecnico o domande:
- Consulta la documentazione
- Controlla la sezione Troubleshooting
- Contatta l'autore

---

**Grazie per aver scelto Event Interactive Map!** 🗺️✨
