<?php
$nome_parrocchia = config('app.nome_parrocchia');
$indirizzo_parrocchia = config('app.indirizzo_parrocchia');
$nome_sito = config('app.url');
$email_parrocchia = config('app.email_parrocchia');;
?>

@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center" style="margin-top: 20px;">
    <div class="col-10">
      <div class="card">
        <div class="card-body">
          <h2 style="text-align: center">Informativa privacy e cookie policy ex Art 13 del Regolamento Generale per la Protezione dei Dati UE 2016/679 (GDPR)</h2>
          <p>{{ $nome_parrocchia }} con sede legale in {{ $indirizzo_parrocchia }} si impegna costantemente per tutelare la privacy on-line dei suoi utenti.</p>

          <h3>1. Fonte dei dati personali e Titolare del trattamento</h3>
          <p>
            Questo documento è stato redatto ai sensi dell’art. 13 del Regolamento UE 2016/679 (di seguito: “Regolamento“) al fine di permetterle di conoscere
            la nostra politica sulla privacy. Vengono descritte le modalità generali del trattamento dei dati personali degli utenti del sito e dei cookies e
            come le sue informazioni personali vengono gestite quando utilizza il nostro sito {{ $nome_sito }} (di seguito “Sito”).
            Le informazioni ed i dati da lei forniti od altrimenti acquisiti nell’ambito dell’utilizzo dei servizi di “{{ $nome_parrocchia }}”, – come ad esempio:
            l’accesso all’area riservata del Sito, le newsletter, etc., di seguito “Servizi” -, saranno oggetto di trattamento nel rispetto delle disposizioni del
            Regolamento e degli obblighi di riservatezza che ispirano l’attività di “{{ $nome_parrocchia }}”.
          </p>
          <p>
            Secondo le norme del Regolamento, i trattamenti effettuati da “{{ $nome_parrocchia }}” saranno improntati ai principi di liceità, correttezza, trasparenza,
            limitazione delle finalità e della conservazione, minimizzazione dei dati, esattezza, integrità e riservatezza.
          </p>
          <p>
            Il titolare dei trattamenti svolti attraverso il Sito è “{{ $nome_parrocchia }}” con sede legale in {{ $indirizzo_parrocchia }} come sopra definito
            a cui può scrivere per qualunque informazione inerente il trattamento dei dati personali.
          </p>
          <p>
            La presente informativa è resa solo per questo Sito e non anche per altri siti web eventualmente consultati dall’utente tramite link.
            Si rimanda ad eventuali sezioni specifiche del Sito dove potrà trovare le informative specifiche e le eventuali richieste di consenso per singoli trattamenti.
          </p>

          <h3>2. Tipi di dati</h3>
          <p>
            A seguito della navigazione del Sito, la informiamo che “{{ $nome_parrocchia }}” tratterà dati personali (art. 4(1) del Regolamento) di seguito solo “Dati Personali”.
            In particolare i Dati Personali trattati attraverso il Sito sono i seguenti:
          </p>

          <h4>2.1 Dati di navigazione</h4>
          <p>
            I sistemi informatici e le procedure software preposte al funzionamento del Sito acquisiscono, nel corso del loro normale esercizio,
            alcuni Dati Personali la cui trasmissione è implicita nell’uso dei protocolli di comunicazione di Internet.
            Si tratta di informazioni che non sono raccolte per essere associate a interessati identificati, ma che per loro stessa natura potrebbero,
            attraverso elaborazioni ed associazioni con dati detenuti da terzi, permettere di identificare gli utenti.
            In questa categoria di dati rientrano gli indirizzi IP o i nomi a dominio dei computer utilizzati dagli utenti che si connettono al Sito,
            gli indirizzi in notazione URI (Uniform Resource Identifier) delle risorse richieste, l’orario della richiesta, il metodo utilizzato nel sottoporre la richiesta al server,
            la dimensione del file ottenuto in risposta, il codice numerico indicante lo stato della risposta data dal server (buon fine, errore, etc.)
            ed altri parametri relativi al sistema operativo e all’ambiente informatico dell’utente.
            Questi dati vengono utilizzati al solo fine di ricavare informazioni statistiche anonime sull’uso del Sito e per controllarne il corretto funzionamento
            (si veda infra il paragrafo sui cookie), per identificare anomalie e/o abusi, e vengono cancellati immediatamente dopo l’elaborazione.
            I dati potrebbero essere utilizzati dalle autorità competenti per l’accertamento di responsabilità in caso di ipotetici reati informatici ai danni del sito.
          </p>

          <h4>2.2 Dati forniti volontariamente dall’interessato</h4>
          <p>
            A parte quanto specificato per i dati di navigazione, l’utente è libero di fornire i dati personali riportati negli eventuali moduli di richiesta presenti
            all’interno del sito web (es. per attivare newsletter, registrazione gratuita, acquisto, etc).
            Il loro mancato conferimento potrebbe comportare l’impossibilità di erogare il servizio. In questi casi saranno richieste le sole informazioni necessarie
            per il servizio richiesto (si vedano nel dettaglio le informative specifiche).
          </p>
          <p>
            Nell’utilizzo di alcuni Servizi del Sito potrebbe verificarsi un trattamento di Dati Personali di terzi soggetti da Lei inviati a “{{ $nome_parrocchia }}”.
            Rispetto a tali ipotesi, lei si pone come autonomo titolare del trattamento, assumendosi tutti gli obblighi e le responsabilità di legge.
            In tal senso, conferisce sul punto la più ampia manleva rispetto ad ogni contestazione, pretesa, richiesta di risarcimento del danno da trattamento, ecc.
            che dovesse pervenire a “{{ $nome_parrocchia }}” da terzi soggetti i cui Dati Personali siano stati trattati attraverso il suo utilizzo delle funzioni del Sito
            in violazione delle norme sulla tutela dei dati personali applicabili.
            In ogni caso, qualora fornisse o in altro modo trattasse Dati Personali di terzi nell’utilizzo del Sito, garantisce fin da ora – assumendosene ogni connessa responsabilità –
            che tale particolare ipotesi di trattamento si fonda su un’idonea base giuridica ai sensi dell’art. 6 del Regolamento che legittima il trattamento delle
            informazioni in questione.
          </p>

          <h3>3. Finalità del trattamento e basi giuridiche</h3>
          <p>
            Il trattamento dei dati personali che intendiamo effettuare, dietro suo specifico consenso ove necessario, ha la finalità di consentire l’erogazione dei Servizi del Sito.
          </p>
          <p>
            La base giuridica del trattamento è l’esecuzione di misure precontrattuali adottate su richiesta dello stesso (art. 6(1)(b) del Regolamento) in quanto
            il trattamento è necessario all’erogazione del Servizio.
            Il conferimento dei Dati Personali per queste finalità è facoltativo ma l’eventuale mancato conferimento comporterebbe l’impossibilità di attivare i Servizi forniti dal Sito.
          </p>

          <h3>4. Destinatari dei dati personali</h3>
          <p>
            I suoi Dati Personali potranno essere condivisi, per le finalità di cui sopra, con:
          </p>
          <ul>
            <li>
              persone autorizzate da “{{ $nome_parrocchia }}” al trattamento di Dati Personali necessario a svolgere attività strettamente correlate all’erogazione dei Servizi,
              che si siano impegnate alla riservatezza o abbiano un adeguato obbligo legale di riservatezza (es. dipendenti e amministratori di sistema).
            </li>
            <li>
              soggetti terzi eventualmente addetti alla gestione del Sito che agiscono tipicamente in qualità di Responsabili del trattamento.
            </li>
            <li>
              soggetti, enti o autorità a cui sia obbligatorio comunicare i suoi Dati Personali in forza di disposizioni di legge o di ordini delle autorità.
            </li>
          </ul>

          <h3>5. Destinatari dei dati personali</h3>
          <p>
            Alcuni dei suoi Dati Personali sono condivisi con Destinatari che si potrebbero trovare al di fuori dello Spazio Economico Europeo.
            “{{ $nome_parrocchia }}” assicura che il trattamento dei suoi Dati Personali da parte di questi Destinatari avviene nel rispetto del Regolamento.
            Invero, i trasferimenti si possono basare su una decisione di adeguatezza, sulle Standard Contractual Clauses approvate dalla Commissione Europea
            o su un altro idonea base giuridica.
          </p>

          <h3>6. I Suoi diritti privacy ex artt. 15 e ss. del Regolamento</h3>
          <p>
            Ai sensi degli articoli 15 e seguenti del Regolamento, ha il diritto di chiedere a “{{ $nome_parrocchia }}”, in qualunque momento, l’accesso ai suoi Dati Personali,
            la rettifica o la cancellazione degli stessi o di opporsi al loro trattamento, ha diritto di richiedere la limitazione del trattamento nei casi previsti
            dall’art. 18 del Regolamento, nonché di ottenere in un formato strutturato, di uso comune e leggibile da dispositivo automatico i dati che la riguardano,
            nei casi previsti dall’art. 20 del Regolamento.
          </p>
          <p>
            Le richieste vanno rivolte per iscritto al Titolare al seguente indirizzo: {{ $email_parrocchia }}.
          </p>
          <p>
            In ogni caso hai sempre diritto di proporre reclamo all’Autorità di Controllo competente (Garante per la Protezione dei Dati Personali), ai sensi dell’art. 77 del Regolamento,
            qualora ritenga che il trattamento dei tuoi Dati Personali sia contrario alla normativa in vigore.
          </p>

          <h3>7. Modifiche</h3>
          <p>
            La presente privacy policy è in vigore dal 01/04/2019. “{{ $nome_parrocchia }}” si riserva di modificarne o semplicemente aggiornarne il contenuto, in parte o completamente,
            anche a causa di variazioni della normativa applicabile. “{{ $nome_parrocchia }}” la informerà di tali variazioni non appena verranno introdotte ed esse saranno
            vincolanti non appena pubblicate sul Sito.
          </p>
          <p>
            "{{ $nome_parrocchia }}” la invita quindi a visitare con regolarità questa sezione per prendere cognizione della più recente ed aggiornata versione della privacy policy
            in modo da essere sempre aggiornato sui dati raccolti e sull’uso che ne fa “{{ $nome_parrocchia }}”.
          </p>

          <h2>Cookie policy</h2>
          <h3>Definizioni, caratteristiche e applicazione della normativa</h3>
          <p>
            I cookie sono piccoli file di testo che i siti visitati dall’utente inviano e registrano sul suo computer o dispositivo mobile, per essere poi ritrasmessi agli stessi siti alla successiva visita. Proprio grazie ai cookie un sito ricorda le azioni e preferenze dell’utente (come, ad esempio, i dati di login, la lingua prescelta, le dimensioni dei caratteri, altre impostazioni di visualizzazione, ecc.) in modo che non debbano essere indicate nuovamente quando l’utente torni a visitare detto sito o navighi da una pagina all’altra di esso. I cookie, quindi, sono usati per eseguire autenticazioni informatiche, monitoraggio di sessioni e memorizzazione di informazioni riguardanti le attività degli utenti che accedono ad un sito e possono contenere anche un codice identificativo unico che consente di tenere traccia della navigazione dell’utente all’interno del sito stesso per finalità statistiche o pubblicitarie. Nel corso della navigazione su un sito, l’utente può ricevere sul suo computer anche cookie di siti o di web server diversi da quello che sta visitando (c.d. cookie di “terze parti”). Alcune operazioni non potrebbero essere compiute senza l’uso dei cookie, che in certi casi sono quindi tecnicamente necessari per lo stesso funzionamento del sito.
          </p>
          <p>
            Esistono vari tipi di cookie, a seconda delle loro caratteristiche e funzioni, e questi possono rimanere nel computer dell’utente per periodi di tempo diversi: c.d. cookie di sessione, che viene automaticamente cancellato alla chiusura del browser; c.d. cookie persistenti, che permangono sull’apparecchiatura dell’utente fino ad una scadenza prestabilita.
          </p>
          <p>
            In base alla normativa vigente in Italia, per l’utilizzo dei cookie non sempre è richiesto un espresso consenso dell’utente. In particolare, non richiedono tale consenso i “cookie tecnici”, cioè quelli utilizzati al solo fine di effettuare la trasmissione di una comunicazione su una rete di comunicazione elettronica, o nella misura strettamente necessaria per erogare un servizio esplicitamente richiesto dall’utente. Si tratta, in altre parole, di cookie indispensabili per il funzionamento del sito o necessari per eseguire attività richieste dall’utente.
          </p>
          <p>
            Tra i cookie tecnici, che non richiedono un consenso espresso per il loro utilizzo, il Garante per la protezione dei dati personali italiano (cfr. Provvedimento Individuazione delle modalità semplificate per l’informativa e l’acquisizione del consenso per l’uso dei cookie dell’8 maggio 2014 e successivi chiarimenti, di seguito solo “Provvedimento”) ricomprende anche:
          </p>
          <ul>
            <li>i “cookie analytics” laddove utilizzati direttamente dal gestore del sito per raccogliere informazioni, in forma aggregata, sul numero degli utenti e su come questi visitano il sito stesso</li>
            <li>i cookie di navigazione o di sessione (per autenticarsi)</li>
            <li>i cookie di funzionalità, che permettono all’utente la navigazione in funzione di una serie di criteri selezionati (ad esempio, la lingua, i prodotti selezionati per l’acquisto) al fine di migliorare il servizio reso allo stesso.</li>
          </ul>
          <p>
            Per i “cookie di profilazione”, viceversa, cioè quelli volti a creare profili relativi all’utente e utilizzati al fine di inviare messaggi pubblicitari in linea con le preferenze manifestate dallo stesso nell’ambito della navigazione in rete, è richiesto un preventivo consenso dell’utente
          </p>

          <h3>Tipologie di cookie utilizzate dal Sito</h3>
          <p>Abbiamo classificato i cookie che usiamo in base al loro tipo di utilizzo in:</p>
          <ol>
            <li>Cookie tecnici
              <ol>
                <li>di navigazione o di sessione</li>
                <li>di funzionalità</li>
                <li>di analytics</li>
              </ol>
            </li>
            <li>Cookie di terze parti
              <ol>
                <li>cookie tecnici</li>
              </ol>
            </li>
          </ol>

          <h4>Cookie di navigazione o di sessione</h4>
          <p>
            Sono quei cookie che consentono la normale fruizione del sito e dei servizi in esso presenti come ad esempio realizzare un acquisto, autenticarsi per accedere alle aree riservate o mantenere le proprie preferenze personali. Questa tipologia di cookie possono essere poi distinti in ragione della loro persistenza sul terminale dell’utente: quelli che si cancellano automaticamente alla fine di ogni navigazione, si chiamano “cookie di sessione” (ad esempio il già citato carrello di acquisti on line). Se viceversa essi hanno una vita più lunga, si parla di “cookie permanenti”. Questi cookie sono indispensabili per il corretto funzionamento del sito e per l’installazione non è richiesto il preventivo consenso degli utenti.
          </p>

          <h4>Cookie di funzionalità</h4>
          <p>
            Utilizziamo questi cookie per fornire servizi o per ricordare le tue impostazioni, per migliorare la visita e l’esperienza di navigazione nel nostro sito. Grazie ad essi riusciamo a gestire la navigazione in base ai prodotti selezionati e/o a gestire la Lista dei Desideri. Per l’installazione di tali cookie non è richiesto il preventivo consenso dell’utente.
          </p>

          <h4>Cookie di analytics</h4>
          <p>
            Questi cookie raccolgono, in forma anonima, informazioni su come è utilizzato il nostro sito internet e ci permettono di avere una migliore conoscenza degli utenti e di migliorare il funzionamento del sito stesso. I cookie analytics sono assimilati ai cookie tecnici laddove utilizzati direttamente dal gestore del sito per raccogliere informazioni, in forma aggregata, sul numero degli utenti e su come questi visitano il sito stesso: per l’installazione di tali cookie non è richiesto il preventivo consenso dell’utente.
          </p>

          <h4>Cookie di terze parti</h4>
          <p>
            I cookie di “terza parte” sono legati ai servizi forniti da terze parti: essi vengono utilizzati per diversi scopi quali l’analisi dell’andamento delle campagne di marketing e/o per erogare pubblicità personalizzate sul nostro e su siti web partner. Questa attività viene chiamata retargeting ed è basata sulle attività di navigazione, come la destinazione cercata, le strutture visualizzate ed altro. Il soggetto terzo fornisce questi servizi in cambio di informazioni in merito alla visita dell’utente al nostro sito. Ne discende anche per i terzi fornitori di cookie l’obbligo di rispettare la normativa in materia. Per tale motivo rimandiamo al link delle pagine web dei siti della terza parte, nelle quali l’utente potrà trovare i moduli di raccolta del consenso ai cookie e le loro relative informative.
          </p>

          <h3>Le vostre decisioni in materia di cookie</h3>
          <p>
            Potete selezionare una funzionalità secondo la quale il vostro computer vi avviserà ogniqualvolta un cookie viene impostato, oppure potete decidere di disattivare tutti i cookie. Potete selezionare tali funzionalità attraverso le impostazioni del vostro browser. Ogni browser è leggermente diverso, vi invitiamo quindi ad identificare nel menù “Aiuto” del vostro browser il modo più corretto per modificare le funzionalità relative ai cookie. Se decidete di disattivare i cookie, non avrete accesso a molte funzioni che rendono la vostra esperienza sul nostro sito più efficiente ed alcuni dei nostri servizi non funzioneranno correttamente.
          </p>






        </div>
      </div>
    </div>
  </div>
</div>
@endsection
