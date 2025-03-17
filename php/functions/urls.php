<?php
/**
 * Genera un URL per una route specifica con parametri opzionali.
 * Centralizza la gestione delle route per facilitare eventuali modifiche ai nomi dei file.
 *
 * @param string $route La route da generare (es. 'progetto_dettagli', 'home')
 * @param array $params Array associativo dei parametri della query string (es. ['nome' => 'ProgettoAlpha'])
 *
 * @return string L'URL completo con parametri codificati
 */
function generate_url(string $route, array $params = []): string
{
    $routes = [
        // Pagine principali
        'index' => '../public/index.php',
        'login' => '../public/login.php',
        'register' => '../public/register.php',
        'home' => '../public/home.php',

        // Progetti
        'progetti' => '../public/progetti.php',
        'progetto_dettagli' => '../public/progetto_dettagli.php',
        'progetto_crea' => '../public/progetto_crea.php',
        'progetto_aggiorna' => '../public/progetto_aggiorna.php',

        // Finanziamenti
        'finanziamenti' => '../public/finanziamenti.php',

        // Candidature
        'candidature' => '../public/candidature.php',

        // Curriculum
        'curriculum' => '../public/curriculum.php',
        'curriculum_skill_update' => '../public/curriculum_skill_update.php',
        'curriculum_skill_global_update' => '../public/curriculum_skill_global_update.php',

        // Statistiche e amministrazione
        'statistiche' => '../public/statistiche.php',
        'logs' => '../public/logs.php',

        // Conferme
        'finanziamento_conferma' => '../public/finanziamento_conferma.php',
        'componente_conferma' => '../public/componente_conferma.php'
    ];

    if (!isset($routes[$route])) {
        error_log("Errore di routing: route '$route' non definita");
        redirect(
            false,
            'Route non definita',
            generate_url('home')
        );
    }

    $url = $routes[$route];

    // Aggiunge parametri query
    if (!empty($params)) {
        $query = http_build_query($params);
        $url .= "?$query";
    }

    return $url;
}