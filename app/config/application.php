<?php
return [
    'general' =>  [
        'timezone' => 'America/Sao_Paulo',
        'language' => 'pt',
        'application' => 'avalia_fit',
        'title' => 'AvaliaFit',
        'theme' => 'adminbs5',
        'seed' => 'odfu6asnodf8as',
        'rest_key' => '',
        'multiunit' => '2',
        'public_view' => '0',
        'public_entry' => '',
        'debug' => '1',
        'strict_request' => '0',
        'multi_lang' => '1',
        'require_terms' => '0',
        'concurrent_sessions' => '1',
        'lang_options' => [
          'pt' => 'Português',
          'en' => 'English',
          'es' => 'Español',
        ],
        'multi_database' => '1',
        'validate_strong_pass' => '1',
        'notification_login' => '0',
        'welcome_message' => 'Have a great jorney!',
        'request_log_service' => 'SystemRequestLogService',
        'request_log' => '0',
        'request_log_types' => 'cli,web,rest',
        /*'password_renewal_interval' => '',*/
    ],
    
    'recaptcha' => [
        'enabled' => '0',
        'key' => '...',
        'secret' => '...'
    ],
    'permission' =>  [
        'public_classes' => [
          'SystemRequestPasswordResetForm',
          'SystemPasswordResetForm',
          'SystemRegistrationForm',
          'SystemPasswordRenewalForm',
          'SystemConcurrentAccessView'
        ],
        'user_register' => '2',
        'reset_password' => '1',
        'default_groups' => '2',
        'default_screen' => '30',
        'default_units' => '2',
    ],
    'highlight' => [
        'comment' => '#808080',
        'default' => '#FFFFFF',
        'html' => '#C0C0C0',
        'keyword' => '#62d3ea',
        'string' => '#FFC472',
    ],
    'login' => [
        'logo' => '../images/logoescuro 500x336.jpg',
        'background' => ''
    ],
    'template' => [
        'navbar' => [
            'has_program_search' => '2',
            'has_notifications' => '2',
            'has_messages' => '2',
            'has_docs' => '2',
            'has_contacts' => '2',
            'has_support_form' => '2',
            'has_wiki' => '2',
            'has_news' => '2',
            'has_menu_mode_switch' => '1',
            'has_main_mode_switch' => '1'
        ],
        'dialogs' => [
            'use_swal' => '1'
        ],
        'theme' => [
            'menu_dark_color' => 'black',
            'menu_mode'  => 'dark',
            'main_mode'  => 'dark'
        ]
    ]
];
