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
        'componente_conferma_insert' => '../public/componente_conferma_insert.php',
        'componente_conferma_update' => '../public/componente_conferma_update.php',

        // Actions - Commenti
        'commento_insert' => '../actions/commento_insert.php',
        'commento_delete' => '../actions/commento_delete.php',
        'commento_risposta_insert' => '../actions/commento_risposta_insert.php',
        'commento_risposta_delete' => '../actions/commento_risposta_delete.php',

        // Actions - Profili
        'profilo_insert' => '../actions/profilo_insert.php',
        'profilo_delete' => '../actions/profilo_delete.php',
        'profilo_nome_update' => '../actions/profilo_nome_update.php',

        // Actions - Skills
        'skill_insert' => '../actions/skill_insert.php',
        'skill_update' => '../actions/skill_update.php',
        'skill_curriculum_insert' => '../actions/skill_curriculum_insert.php',
        'skill_curriculum_update' => '../actions/skill_curriculum_update.php',
        'skill_curriculum_delete' => '../actions/skill_curriculum_delete.php',
        'skill_profilo_insert' => '../actions/skill_profilo_insert.php',
        'skill_profilo_update' => '../actions/skill_profilo_update.php',
        'skill_profilo_delete' => '../actions/skill_profilo_delete.php',

        // Actions - Foto
        'foto_insert' => '../actions/foto_insert.php',
        'foto_delete' => '../actions/foto_delete.php',

        // Actions - Componenti
        'componente_insert' => '../actions/componente_insert.php',
        'componente_update' => '../actions/componente_update.php',
        'componente_delete' => '../actions/componente_delete.php',

        // Actions - Candidature
        'candidatura_insert' => '../actions/candidatura_insert.php',
        'candidatura_update' => '../actions/candidatura_update.php',

        // Actions - Authentication
        'login_handler' => '../actions/login_handler.php',
        'register_handler' => '../actions/register_handler.php',
        'logout' => '../actions/logout.php',

        // Actions - Finanziamenti
        'finanziamento_insert' => '../actions/finanziamento_insert.php',

        // Actions - Progetti
        'progetto_insert' => '../actions/progetto_insert.php',
        'progetto_descrizione_update' => '../actions/progetto_descrizione_update.php',
        'progetto_budget_update' => '../actions/progetto_budget_update.php',

        // Actions - Rewards
        'reward_insert' => '../actions/reward_insert.php',

        // Actions - Altri
        'utente_convert_creatore' => '../actions/utente_convert_creatore.php'
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
        $escaped_params = array_map(function ($value) {
            return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
        }, $params);

        $query = http_build_query($escaped_params);
        $url .= "?$query";
    }

    return $url;
}