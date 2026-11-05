# Changelog tema Betnero

## 0.7.2
- Guide Correlate dell'Academy ordinate per numero di tag in comune (i tag presenti su più di metà delle guide non contano), poi stessa sezione, poi data di aggiornamento.

## 0.7.1
- Academy: in fondo a ogni guida tornano "Ultimo aggiornamento", "Guide Correlate" (prima per tag in comune, poi stessa sezione), box autore e banner che porta alla home dell'Academy.
- Tag attivi anche sulle pagine, per scegliere le guide correlate.

## 0.6.8
- Pagine convertite: stili per colonne affiancate (.bn-cols), riquadri colorati (.bn-box), schede autore (.bn-author-card) e testo centrato (.bn-center); immagini ridimensionate come nell'originale.
- Guide correlate su mobile: schede che scorrono in orizzontale invece di 4 colonne strette.

## 0.6.7
- Indice dell'Academy: anche le voci principali con peso normale.

## 0.6.6
- Sidebar articoli: larga 241 px come in produzione, pulsante "Vai al sito" accanto al logo.
- Indice dell'Academy: link con peso normale come in produzione.

## 0.6.5
- Academy: sidebar "Indice dell'Academy" a sinistra come nel sito originale (stesse voci e sottovoci del menu Academy, sezione corrente aperta, voce corrente evidenziata), fissa durante lo scorrimento, nascosta su tablet e mobile.
- Articoli: la sidebar non è più fissa, come nell'originale. Il contenuto della sidebar arriva da Betnero Core 0.2.9.

## 0.6.4
- Calcolatore di Rischio: anche lo stile è un file del tema (assets/css/risk-calculator.css), caricato solo sulla sua pagina.

## 0.6.3
- Calcolatore di Rischio: lo script è ora un file del tema (assets/js/risk-calculator.js), caricato solo sulla pagina del calcolatore ed escluso dal ritardo di Velox. In linea nella pagina veniva rovinato dai filtri del contenuto e, sullo staging, era marcato per WP Rocket (che è spento) e non partiva.

## 0.6.2
- Barra icone mobile come nel sito originale: non compare sulle recensioni (indice e singole) e sulle pagine dell'Academy; c'è su tutte le altre, home compresa. Filtro betnero_mobile_bar_enabled per cambiarlo.
- "Torna su" e barra "Vai al sito" si posizionano in base alla presenza della barra icone.

## 0.6.1
- Barra icone fissa in basso su mobile (Casino, Live Casino, Slot, Scommesse, Promozioni, Blog) direttamente nel tema, su tutte le pagine; la voce della sezione corrente è evidenziata. Il vecchio popup Elementor/JetPopup della barra viene nascosto.
- Pulsante "torna su" in basso a destra: compare dopo un po' di scroll e riporta in cima con uno scorrimento morbido.
- Su mobile la barra "Vai al sito" dell'operatore si appoggia sopra la barra icone.

## 0.6.0
- Header sempre fisso in alto (sticky) su desktop e mobile: prima lo sticky era sul gruppo interno e non funzionava.
- Header mobile: menu a sinistra, logo al centro, cerca a destra; la ricerca si apre in una fascia bianca sotto l'header.
- Home: hero compatta come in produzione. Titolo più piccolo e a sinistra, una sola riga chiusa "Le domande più frequenti sui siti di gioco online" che contiene le 5 FAQ, numeri su una riga, podio ridotto (nascosto su mobile). Pattern "Home Betnero" aggiornato.

## 0.5.9
- Pagine convertite: articolo largo quanto tabelle e classifica (1200 px).
- Ricerca nell'header: il campo si apre sopra l'header senza spostare il menu (prima lo spingeva a sinistra e alla chiusura scivolava indietro); pulsante x per chiuderlo; bordo blu al posto del contorno nero.
- Menu mobile: sottomenu chiusi, si aprono a tendina con la freccia.
- Academy nel menu con più spazio interno.
- Nuovo script assets/js/header.js, escluso dal ritardo di Velox Optimizer.

## 0.5.8
- Guide correlate: immagine delle card intera, non più tagliata.

## 0.5.7
- Stili per le pagine convertite da Elementor: fascia in alto con sfumatura blu, titolo e testo bianchi e immagine a destra (.bn-ph); articolo con larghezza di lettura, titoli e tabelle nello stile Betnero (.bn-article); card delle guide correlate (.bn-related).

## 0.5.6
- Corretto un difetto dei colori: il colore della palette "text" generava la classe has-text-color, che WordPress mette su ogni testo colorato, e forzava tutti i testi colorati al grigio #333 (es. titolo bianco della fascia blu negli archivi). Il colore ora si chiama "testo".
- Nessuno spazio bianco tra l'header e la fascia blu (o il contenuto) che segue.
- Menu: Academy in evidenza con il pulsante con bordo blu; il Calcolatore di Rischio torna in arancione come nel vecchio sito.

## 0.5.5
- FAQ della hero: durante la chiusura animata (Betnero Core 0.2.3) l'icona torna subito da × a +.

## 0.5.4
- Home: la riga dei numeri resta dentro i margini della pagina (larga quanto FAQ e podio).

## 0.5.3
- Home: podio allineato in alto con le FAQ; i numeri del sito in una riga unica sotto FAQ e podio, a tutta larghezza (su mobile due per riga).

## 0.5.2
- Home: titolo e sottotitolo della hero a tutta larghezza, centrati, tutti bianchi e con il titolo più piccolo; sotto restano FAQ e numeri a sinistra e podio a destra.
- Sfumatura della hero più scura (dal blu del logo al blu notte).

## 0.5.1
- Home: hero centrata nella pagina (prima era allineata a sinistra).
- Pulsanti pieni con testo bianco anche dove il reset del vecchio tema colorava i link (es. "Apri il calcolatore").

## 0.5.0
- Colore principale: il blu del logo (#3C95D1), con un blu più scuro per i link (#226FA6) e per il passaggio del mouse (#2F7FB6). Nuovi colori "Inchiostro" (#0A1222) e "Blu per testi".
- Nuova home (pattern "Home Betnero"): hero con sfumatura dal blu chiaro al blu scuro, FAQ, numeri del sito e podio; classifica a schede; metodologia; ultimi pronostici; fascia del Calcolatore di Rischio; blog; Academy; gioco responsabile. Usa i blocchi di Betnero Core 0.2.0.
- Footer scuro.
- Header: "Calcolatore di Rischio" come pulsante con bordo blu.
- Tag della versione creato in automatico da GitHub Actions a ogni push su main.

## 0.4.0
- Con il plugin Betnero Core attivo, i blocchi tabella e toplist li registra il plugin (dati dagli Operatori invece che da JetEngine). Senza plugin il tema funziona come prima.
- Codice dell'Academy spostato in inc/academy.php.

## 0.3.4
- Aggiunto questo changelog. Prima versione pubblicata con Git Updater.

## 0.3.3
- Collegamento al repository GitHub per Git Updater.

## 0.3.2
- Card degli articoli: immagini a tutta larghezza.

## 0.3.1
- Home: sezioni a tutta larghezza, titolo bianco, guide Academy corrette.

## 0.3.0
- Pattern "Home Betnero" a blocchi.

## 0.2.x
- Blocchi Tabella casino, Tabella scommesse e Toplist compatta con filtri.

## 0.1.x
- Base del tema: header, footer, template articolo, archivi, ricerca, 404.
