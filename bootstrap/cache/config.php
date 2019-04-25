<?php return array (
  'app' => 
  array (
    'ip_address' => false,
    'password' => '12345',
    'name' => 'Segresta 2.0',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost:8000',
    'nome_parrocchia' => 'NOME PARROCCHIA',
    'indirizzo_parrocchia' => 'INDIRIZZO PARROCCHIA',
    'email_parrocchia' => 'EMAIL PARROCCHIA',
    'timezone' => 'Europe/Rome',
    'locale' => 'it',
    'fallback_locale' => 'en',
    'key' => 'base64:vCXs9qjoAWrzu9PTla8Ga2eCpDpjWVK0EAq7H3NEyMg=',
    'cipher' => 'AES-256-CBC',
    'log' => 'single',
    'log_level' => 'debug',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Zizaco\\Entrust\\EntrustServiceProvider',
      23 => 'Barryvdh\\DomPDF\\ServiceProvider',
      24 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      25 => 'Intervention\\Image\\ImageServiceProvider',
      26 => 'Telegram\\Bot\\Laravel\\TelegramServiceProvider',
      27 => 'Nwidart\\Modules\\LaravelModulesServiceProvider',
      28 => 'Spatie\\CookieConsent\\CookieConsentServiceProvider',
      29 => 'Nayjest\\Grids\\ServiceProvider',
      30 => 'Collective\\Html\\HtmlServiceProvider',
      31 => 'Lavary\\Menu\\ServiceProvider',
      32 => 'App\\Providers\\AppServiceProvider',
      33 => 'App\\Providers\\AuthServiceProvider',
      34 => 'App\\Providers\\EventServiceProvider',
      35 => 'App\\Providers\\RouteServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Form' => 'Collective\\Html\\FormFacade',
      'Html' => 'Collective\\Html\\HtmlFacade',
      'Grids' => 'Nayjest\\Grids\\Grids',
      'Input' => 'Illuminate\\Support\\Facades\\Input',
      'Entrust' => 'Zizaco\\Entrust\\EntrustFacade',
      'PDF' => 'Barryvdh\\DomPDF\\Facade',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'Image' => 'Intervention\\Image\\Facades\\Image',
      'Telegram' => 'Telegram\\Bot\\Laravel\\Facades\\Telegram',
      'Menu' => 'Lavary\\Menu\\Facade',
      'Module' => 'Nwidart\\Modules\\Facades\\Module',
    ),
  ),
  'attributo' => 
  array (
    'name' => 'Attributo',
    'permissions' => 
    array (
      'view-attributo' => 'Visualizza la finestra degli attributi',
      'edit-attributo' => 'Modifica gli attributi degli utenti',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'Modules\\User\\Entities\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'array',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/storage/framework/cache',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
    'prefix' => 'laravel',
  ),
  'compile' => 
  array (
    'files' => 
    array (
    ),
    'providers' => 
    array (
    ),
  ),
  'contabilita' => 
  array (
    'name' => 'Contabilita',
    'permissions' => 
    array (
      'edit-contabilita-opzioni' => 'Modifica le opzioni contabilità',
      'edit-contabilita' => 'Modifica la contabilità',
    ),
  ),
  'cookie-consent' => 
  array (
    'enabled' => true,
    'cookie_name' => 'laravel_cookie_consent',
    'cookie_lifetime' => 7300,
  ),
  'database' => 
  array (
    'fetch' => 5,
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'segresta',
        'prefix' => '',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'segresta',
        'username' => 'segresta',
        'password' => 'UW8Z3UjNVZRizAvI',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => 'innodb',
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'segresta',
        'username' => 'segresta',
        'password' => 'UW8Z3UjNVZRizAvI',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'cluster' => false,
      'default' => 
      array (
        'host' => 'localhost',
        'password' => NULL,
        'port' => 6379,
        'database' => 0,
      ),
    ),
  ),
  'datatables' => 
  array (
    'search' => 
    array (
      'smart' => true,
      'multi_term' => true,
      'case_insensitive' => true,
      'use_wildcards' => false,
    ),
    'index_column' => 'DT_RowIndex',
    'engines' => 
    array (
      'eloquent' => 'Yajra\\DataTables\\EloquentDataTable',
      'query' => 'Yajra\\DataTables\\QueryDataTable',
      'collection' => 'Yajra\\DataTables\\CollectionDataTable',
      'resource' => 'Yajra\\DataTables\\ApiResourceDataTable',
    ),
    'builders' => 
    array (
    ),
    'nulls_last_sql' => '%s %s NULLS LAST',
    'error' => NULL,
    'columns' => 
    array (
      'excess' => 
      array (
        0 => 'rn',
        1 => 'row_num',
      ),
      'escape' => '*',
      'raw' => 
      array (
        0 => 'action',
      ),
      'blacklist' => 
      array (
        0 => 'password',
        1 => 'remember_token',
      ),
      'whitelist' => '*',
    ),
    'json' => 
    array (
      'header' => 
      array (
      ),
      'options' => 0,
    ),
  ),
  'datatables-buttons' => 
  array (
    'namespace' => 
    array (
      'base' => 'DataTables',
      'model' => '',
    ),
    'pdf_generator' => 'snappy',
    'snappy' => 
    array (
      'options' => 
      array (
        'no-outline' => true,
        'margin-left' => '0',
        'margin-right' => '0',
        'margin-top' => '10mm',
        'margin-bottom' => '10mm',
      ),
      'orientation' => 'landscape',
    ),
    'parameters' => 
    array (
      'dom' => 'Bfrtip',
      'order' => 
      array (
        0 => 
        array (
          0 => 0,
          1 => 'desc',
        ),
      ),
      'buttons' => 
      array (
        0 => 'create',
        1 => 'export',
        2 => 'print',
        3 => 'reset',
        4 => 'reload',
      ),
    ),
  ),
  'datatables-html' => 
  array (
    'table' => 
    array (
      'class' => 'table',
      'id' => 'dataTableBuilder',
    ),
    'callback' => 
    array (
      0 => '$',
      1 => '$.',
      2 => 'function',
    ),
    'script' => 'datatables::script',
    'editor' => 'datatables::editor',
  ),
  'diocesi' => 
  array (
    'name' => 'Diocesi',
    'email' => 'admin@email.it',
    'permissions' => 
    array (
      'edit-oratori' => 'Modifica e aggiungi oratori',
      'view-users-diocesi' => 'Vedi gli utenti di tutti gli oratori',
      'add-events-diocesi' => 'Aggiungi eventi diocesani',
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => 
    array (
      'font_dir' => '/home/roberto/Documenti/Clienti/Segresta/segresta/storage/fonts/',
      'font_cache' => '/home/roberto/Documenti/Clienti/Segresta/segresta/storage/fonts/',
      'temp_dir' => '/tmp',
      'chroot' => '/home/roberto/Documenti/Clienti/Segresta/segresta',
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => false,
    ),
  ),
  'elenco' => 
  array (
    'name' => 'Elenco',
  ),
  'email' => 
  array (
    'name' => 'Email',
    'permissions' => 
    array (
      'view-email' => 'Archivio Email',
      'send-email' => 'Invia email',
    ),
  ),
  'entrust' => 
  array (
    'role' => 'App\\Role',
    'roles_table' => 'roles',
    'permission' => 'App\\Permission',
    'permissions_table' => 'permissions',
    'permission_role_table' => 'permission_role',
    'role_user_table' => 'role_user',
    'user_foreign_key' => 'user_id',
    'role_foreign_key' => 'role_id',
  ),
  'event' => 
  array (
    'name' => 'Event',
    'permissions' => 
    array (
      'view-event' => 'Visualizza la finestra degli eventi',
      'edit-event' => 'Modifica le informazioni degli eventi',
      'manage-week' => 'Visualizza e modifica le settimane',
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'transactions' => 
    array (
      'handler' => 'db',
    ),
    'temporary_files' => 
    array (
      'local_path' => '/tmp',
      'remote_disk' => NULL,
    ),
  ),
  'famiglia' => 
  array (
    'name' => 'Famiglia',
    'permissions' => 
    array (
      'view-famiglia' => 'Visualizza la famiglia',
      'edit-famiglia' => 'Modifica le informazioni sulla famiglia',
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/home/roberto/Documenti/Clienti/Segresta/segresta/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/home/roberto/Documenti/Clienti/Segresta/segresta/storage/app/public',
        'visibility' => 'public',
        'url' => 'http://localhost:8000/storage',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => 'your-key',
        'secret' => 'your-secret',
        'region' => 'your-region',
        'bucket' => 'your-bucket',
      ),
    ),
  ),
  'generators' => 
  array (
    'config' => 
    array (
      'model_template_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/xethron/laravel-4-generators/src/Way/Generators/templates/model.txt',
      'scaffold_model_template_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/xethron/laravel-4-generators/src/Way/Generators/templates/scaffolding/model.txt',
      'controller_template_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/xethron/laravel-4-generators/src/Way/Generators/templates/controller.txt',
      'scaffold_controller_template_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/xethron/laravel-4-generators/src/Way/Generators/templates/scaffolding/controller.txt',
      'migration_template_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/xethron/laravel-4-generators/src/Way/Generators/templates/migration.txt',
      'seed_template_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/xethron/laravel-4-generators/src/Way/Generators/templates/seed.txt',
      'view_template_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/xethron/laravel-4-generators/src/Way/Generators/templates/view.txt',
      'model_target_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/app',
      'controller_target_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/app/Http/Controllers',
      'migration_target_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/database/migrations',
      'seed_target_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/database/seeds',
      'view_target_path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/resources/views',
    ),
  ),
  'group' => 
  array (
    'name' => 'Group',
    'permissions' => 
    array (
      'view-gruppo' => 'Visualizza la finestra dei gruppi',
      'edit-gruppo' => 'Modifica gruppi e componenti',
    ),
  ),
  'image' => 
  array (
    'driver' => 'gd',
  ),
  'laravel-cookie-consent' => 
  array (
    'enabled' => true,
    'cookie_name' => 'laravel_cookie_consent',
  ),
  'laravel-menu' => 
  array (
    'settings' => 
    array (
      'default' => 
      array (
        'auto_activate' => true,
        'activate_parents' => true,
        'active_class' => 'active',
        'restful' => false,
        'cascade_data' => true,
        'rest_base' => '',
        'active_element' => 'item',
      ),
    ),
    'views' => 
    array (
      'bootstrap-items' => 'laravel-menu::bootstrap-navbar-items',
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'ssl0.ovh.net',
    'port' => '465',
    'from' => 
    array (
      'address' => 'info@elephantech.it',
      'name' => 'ElephanTech',
    ),
    'encryption' => 'ssl',
    'username' => 'info@elephantech.it',
    'password' => '2gnxTohjMb8mN04JLPqI',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/home/roberto/Documenti/Clienti/Segresta/segresta/resources/views/vendor/mail',
      ),
    ),
    'sendmail' => '/usr/sbin/sendmail -bs',
    'pretend' => false,
  ),
  'modules' => 
  array (
    'namespace' => 'Modules',
    'stubs' => 
    array (
      'enabled' => false,
      'path' => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/nwidart/laravel-modules/src/Commands/stubs',
      'files' => 
      array (
        'start' => 'start.php',
        'routes' => 'Http/routes.php',
        'views/index' => 'Resources/views/index.blade.php',
        'views/master' => 'Resources/views/layouts/master.blade.php',
        'scaffold/config' => 'Config/config.php',
        'composer' => 'composer.json',
      ),
      'replacements' => 
      array (
        'start' => 
        array (
          0 => 'LOWER_NAME',
          1 => 'ROUTES_LOCATION',
        ),
        'routes' => 
        array (
          0 => 'LOWER_NAME',
          1 => 'STUDLY_NAME',
          2 => 'MODULE_NAMESPACE',
        ),
        'json' => 
        array (
          0 => 'LOWER_NAME',
          1 => 'STUDLY_NAME',
          2 => 'MODULE_NAMESPACE',
        ),
        'views/index' => 
        array (
          0 => 'LOWER_NAME',
        ),
        'views/master' => 
        array (
          0 => 'STUDLY_NAME',
        ),
        'scaffold/config' => 
        array (
          0 => 'STUDLY_NAME',
        ),
        'composer' => 
        array (
          0 => 'LOWER_NAME',
          1 => 'STUDLY_NAME',
          2 => 'VENDOR',
          3 => 'AUTHOR_NAME',
          4 => 'AUTHOR_EMAIL',
          5 => 'MODULE_NAMESPACE',
        ),
      ),
      'gitkeep' => true,
    ),
    'paths' => 
    array (
      'modules' => '/home/roberto/Documenti/Clienti/Segresta/segresta/Modules',
      'assets' => '/home/roberto/Documenti/Clienti/Segresta/segresta/public/modules',
      'migration' => '/home/roberto/Documenti/Clienti/Segresta/segresta/database/migrations',
      'generator' => 
      array (
        'config' => 
        array (
          'path' => 'Config',
          'generate' => true,
        ),
        'command' => 
        array (
          'path' => 'Console',
          'generate' => true,
        ),
        'migration' => 
        array (
          'path' => 'Database/Migrations',
          'generate' => true,
        ),
        'seeder' => 
        array (
          'path' => 'Database/Seeders',
          'generate' => true,
        ),
        'factory' => 
        array (
          'path' => 'Database/factories',
          'generate' => true,
        ),
        'model' => 
        array (
          'path' => 'Entities',
          'generate' => true,
        ),
        'controller' => 
        array (
          'path' => 'Http/Controllers',
          'generate' => true,
        ),
        'filter' => 
        array (
          'path' => 'Http/Middleware',
          'generate' => true,
        ),
        'request' => 
        array (
          'path' => 'Http/Requests',
          'generate' => true,
        ),
        'provider' => 
        array (
          'path' => 'Providers',
          'generate' => true,
        ),
        'assets' => 
        array (
          'path' => 'Resources/assets',
          'generate' => true,
        ),
        'lang' => 
        array (
          'path' => 'Resources/lang',
          'generate' => true,
        ),
        'views' => 
        array (
          'path' => 'Resources/views',
          'generate' => true,
        ),
        'test' => 
        array (
          'path' => 'Tests',
          'generate' => true,
        ),
        'repository' => 
        array (
          'path' => 'Repositories',
          'generate' => false,
        ),
        'event' => 
        array (
          'path' => 'Events',
          'generate' => false,
        ),
        'listener' => 
        array (
          'path' => 'Listeners',
          'generate' => false,
        ),
        'policies' => 
        array (
          'path' => 'Policies',
          'generate' => false,
        ),
        'rules' => 
        array (
          'path' => 'Rules',
          'generate' => false,
        ),
        'jobs' => 
        array (
          'path' => 'Jobs',
          'generate' => false,
        ),
        'emails' => 
        array (
          'path' => 'Emails',
          'generate' => false,
        ),
        'notifications' => 
        array (
          'path' => 'Notifications',
          'generate' => false,
        ),
        'resource' => 
        array (
          'path' => 'Transformers',
          'generate' => false,
        ),
      ),
    ),
    'scan' => 
    array (
      'enabled' => false,
      'paths' => 
      array (
        0 => '/home/roberto/Documenti/Clienti/Segresta/segresta/vendor/*/*',
      ),
    ),
    'composer' => 
    array (
      'vendor' => 'nwidart',
      'author' => 
      array (
        'name' => 'Nicolas Widart',
        'email' => 'n.widart@gmail.com',
      ),
    ),
    'cache' => 
    array (
      'enabled' => false,
      'key' => 'laravel-modules',
      'lifetime' => 60,
    ),
    'register' => 
    array (
      'translations' => true,
      'files' => 'register',
    ),
  ),
  'modulo' => 
  array (
    'name' => 'Modulo',
    'permissions' => 
    array (
      'view-modulo' => 'Visualizza la finestra dei moduli',
      'edit-modulo' => 'Modifica le informazioni dei moduli',
    ),
  ),
  'oratorio' => 
  array (
    'name' => 'Oratorio',
    'permissions' => 
    array (
      'view-select' => 'Visualizza gli elenchi con tutte le opzioni',
      'edit-select' => 'Modifica gli elenchi con tutte le opzioni',
      'edit-oratorio' => 'Modifica le informazioni relative all\'oratorio',
      'edit-permission' => 'Gestisci permessi',
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
      ),
    ),
    'failed' => 
    array (
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'report' => 
  array (
    'name' => 'Report',
    'permissions' => 
    array (
      'generate-report' => 'Genera report',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'model' => 'Modules\\User\\Entities\\User',
      'key' => NULL,
      'secret' => NULL,
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/home/roberto/Documenti/Clienti/Segresta/segresta/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
  ),
  'sms' => 
  array (
    'name' => 'Sms',
  ),
  'subscription' => 
  array (
    'name' => 'Subscription',
    'permissions' => 
    array (
      'view-iscrizioni' => 'Visualizza iscrizioni',
      'edit-iscrizioni' => 'Modifica iscrizioni',
      'edit-admin-iscrizioni' => 'Modifica iscrizioni come segreteria',
    ),
  ),
  'telegram' => 
  array (
    'bot_token' => '305880668:AAHY8PzersKLz2LD7yGxYtZ_12x3-eUiNQU',
    'async_requests' => false,
    'http_client_handler' => NULL,
    'commands' => 
    array (
    ),
  ),
  'user' => 
  array (
    'name' => 'User',
    'permissions' => 
    array (
      'view-users' => 'Visualizza la finestra dell\'anagrafica',
      'edit-users' => 'Modifica le informazioni degli utenti',
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/home/roberto/Documenti/Clienti/Segresta/segresta/resources/views',
    ),
    'compiled' => '/home/roberto/Documenti/Clienti/Segresta/segresta/storage/framework/views',
  ),
  'whatsapp' => 
  array (
    'name' => 'Whatsapp',
  ),
);
