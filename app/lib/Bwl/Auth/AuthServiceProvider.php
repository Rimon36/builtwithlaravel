<?php namespace Bwl\Auth;
use Bwl\Auth\Authorize as Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->bind('GHAuth', function(){
            return new Auth([
                'client_id' => \Config::get('github.key'),
                'client_secret' => \Config::get('github.secret')
            ]);
        });

        // Add new filter for checking Github logged in peeps
        $router = $app->make('router');
        $router->addFilter('ghauth', '\Bwl\Auth\AuthFilter');
    }
}