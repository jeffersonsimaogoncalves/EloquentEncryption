<?php

namespace RichardStyles\EloquentEncryption;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\ColumnDefinition;
use RichardStyles\EloquentEncryption\Connection\MySqlConnection;
use RichardStyles\EloquentEncryption\Connection\PostgresConnection;
use RichardStyles\EloquentEncryption\Connection\SQLiteConnection;
use Illuminate\Database\Schema\Blueprint;
use RichardStyles\EloquentEncryption\Schema\Grammars\SqlServerGrammar;

class EloquentEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'eloquentencryption');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'eloquentencryption');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('eloquentencryption.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/eloquentencryption'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/eloquentencryption'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/eloquentencryption'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config): MySqlConnection {
            return new MySqlConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('postgres', function ($connection, $database, $prefix, $config): PostgresConnection {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config): SQLiteConnection {
            return new SQLiteConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('sqlsrv', function ($connection, $database, $prefix, $config): SqlServerGrammar {
            return new SqlServerGrammar($connection, $database, $prefix, $config);
        });

        Blueprint::macro('encrypted', function ($column): ColumnDefinition {
            /** @var Blueprint $this */
            return $this->addColumn('encrypted', $column);
        });

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'eloquentencryption');

        // Register the main class to use with the facade
        $this->app->singleton('eloquentencryption', function () {
            return new EloquentEncryption;
        });
    }
}
