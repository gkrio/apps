<?php
namespace Gkr\Apps;


use Gkr\Apps\Commands\DeleteCommand;
use Gkr\Apps\Commands\ListCommand;
use Gkr\Apps\Commands\MakeCommand;
use Gkr\Apps\Factory\Repository;
use Gkr\Support\SupportServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppsServiceProvider extends ServiceProvider
{
    protected $commands = [
        MakeCommand::class,
        DeleteCommand::class,
        ListCommand::class
    ];
    public function boot()
    {
        $this->registerConfig();
    }
    public function register()
    {
        if (!$this->app->bound('gkr.support')){
            $this->app->register(SupportServiceProvider::class);
        }
        $this->app->singleton('gkr.apps',function($app){
            $path = $app['config']->get('gkr.apps.path');
            $config = $this->app['gkr.support.config']
                ->driver('yaml',$path)
                ->merge('gkr.apps');
            return new Repository($app,$config);
        });
        $this->commands($this->commands);
    }
    protected function registerConfig()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->publishes([$configPath => config_path('gkr/apps.php')]);

        $this->mergeConfigFrom($configPath, 'gkr.apps');
    }
}