<?php

/*

Plugin Name: Delivery Date Box
Description: Plugin per mostrare le date di consegna e spedizione dei vostri prodotti all'interno di un box.
Version: 1.0
Author: Matteo - Pianoweb

*/
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }



// Aggiungi stili e script

function delivery_plugin_enqueue_scripts() {

    // Stile CSS
    wp_enqueue_style('delivery_plugin-style', plugins_url('style.css', __FILE__));
    // Script JavaScript
    wp_enqueue_script('delivery_plugin-script', plugins_url('script.js', __FILE__), array('jquery'), '1.0', true);

}
// Aggiungi stili e script all'interno della pagina
add_action('wp_enqueue_scripts', 'delivery_plugin_enqueue_scripts');

// includo pagina opzioni plugin
include 'settings-page.php';


// shortcode delivery date
function delivery_date_shortcode(){
    // Imposta il fuso orario desiderato
    date_default_timezone_set('Europe/Rome');

    // Imposta la lingua in italiano
    setlocale(LC_TIME, 'it_IT');


    // Ottieni la data e l'ora attuali
    $oggi = new DateTime();

    // Ottieni la data di domani
    $domani = new DateTime('+'. get_option('data_spedizione') . 'days');

    // Ottengo la data fra due giorni
    $dataconsegna = new DateTime('+'. get_option('data_consegna') .' days');

    // Imposta l'orario di reset (13:00:00)
    $reset_time = new DateTime('13:00:00');

    // Se l'ora attuale è successiva all'orario di reset, aggiungi un giorno
    if ($oggi > $reset_time) {
            $scadenza = $domani;
        }  else {
        // La scadenza rimane la stessa
        $scadenza = $reset_time;
    }

    // Calcola il tempo rimanente in secondi
    $diff = $scadenza->getTimestamp() - $oggi->getTimestamp();

    // Verifica se l'ordine è stato effettuato prima dell'orario di reset
    if ($oggi <= $reset_time) {
        // Se l'ordine è stato effettuato prima dell'orario di reset, la data di spedizione è lo stesso giorno
        $data_spedizione = strftime('%e %B', $oggi->getTimestamp());
        $giornoconsegna = strftime('%e %B', $dataconsegna->getTimestamp());
    } else {
        // Se l'ordine è stato effettuato dopo l'orario di reset
        if ($oggi->format('w') == 0) { // Se oggi è Domenica
            // Prolunga la data di spedizione di un giorno
            $data_spedizione = strftime('%e %B', $domani->modify('+1 day')->getTimestamp());
            $giornoconsegna = strftime('%e %B', $dataconsegna->modify('+4 days')->getTimestamp());
        } else {
            // La data di spedizione è il giorno successivo
            $data_spedizione = strftime('%e %B', $domani->getTimestamp());
            $giornoconsegna = strftime('%e %B', $dataconsegna->modify('+1 day')->getTimestamp());
        }
    }


    // Mostra il countdown
    echo "<span id='countdown'></span>
        
    <div class='box-spedizioni' style='display:flex;align-items:center;margin-top:1em;margin-bottom:1em'>
        <div style='padding:1em 1.5em; border: 1px solid #eee; text-align:center'>
            <img src= " . plugins_url('/images/checkout.png', __FILE__) ." width='40' ></img>
            <p style='margin:0;font-weight:600'>Ordinato</p>"
            . "<p style='margin:0'>" . strftime('%e %B', $oggi->getTimestamp()) . "</p>" ."
        </div>
        <div style='padding:1em 1.5em; border: 1px solid #eee; text-align:center'>
            <img src=". plugins_url('/images/delivery.png', __FILE__) ." width='40' ></img>
            <p style='margin:0;font-weight:600'>Spedito</p>
            <p style='margin:0'>" . $data_spedizione ."</p>
        </div>
        <div style='padding:1em 1.5em; border: 1px solid #eee;text-align:center'>
            <img src=". plugins_url('/images/pin.png', __FILE__) ." width='40' ></img>
            <p style='margin:0;font-weight:600'>Consegna</p>"
            . "<p style='margin:0'>" .$giornoconsegna . "</p>" ."
        </div>
    </div>";

    // Includi lo script JavaScript
    ?>
    <script>
        // Funzione per aggiornare il countdown ogni secondo
        function updateCountdown() {
            var now = new Date();
            var diff = Math.floor((<?php echo $scadenza->getTimestamp(); ?> * 1000 - now.getTime()) / 1000); // Differenza in secondi

            var hours = Math.floor(diff / 3600);
            var minutes = Math.floor((diff % 3600) / 60);
            var seconds = diff % 60;
            var tomorrow = new Date();
            tomorrow.setDate(now.getDate() + 1);
            var dayafter = new Date();
            dayafter.setDate(tomorrow.getDate() + 2);

            var options = { day: 'numeric', month: 'long' };
            var tomorrowString = tomorrow.toLocaleDateString('it-IT', options);
            var dayafterString = dayafter.toLocaleDateString('it-IT', options);

            document.getElementById('countdown').innerHTML = 'Se ordini entro ' + '<span style="font-weight:600">' + hours + ' ore, ' + minutes + ' minuti, e ' + seconds + ' secondi </span>.<br>' + ' Consegna stimata  entro 2-3 giorni lavorativi. '
        }

        // Aggiorna il countdown ogni secondo
        setInterval(updateCountdown, 1000);

        // Esegui la funzione all'avvio per evitare un ritardo di un secondo
        updateCountdown();
    </script>
    <?php
}
add_shortcode('delivery', 'delivery_date_shortcode');
?>
