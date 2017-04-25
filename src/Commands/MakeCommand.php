<?php

namespace Gkr\Apps\Commands;

use Gkr\Support\Console\ColorTrait;
use Illuminate\Console\Command;

class MakeCommand extends Command
{
    use ColorTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkr:app:make 
    {name? : app name}
    {type? : app type should be web,api or console}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = [];
        $types = ['web', 'api','console'];
        $options['name'] = $this->argument('name') ? : $this->ask($this->askColor('App Name','like Frontend,Backend'));
        $type = $this->argument('type');
        if (!$type || !in_array($type,$types)){
            $message = $this->askColor('App Type','which type for your app');
            $options['type'] = $this->choice($message,$types , 0);
        }else{
            $options['type'] = $type;
        }
        $options['enabled'] = $this->confirm($this->confirmColor('Enabled App','Do you wish to enabled this app'),true) ? : false;
        $this->laravel['gkr.apps']->create($options['name'],$options);
        $this->info("Create App with name {$options['name']} Success!");
    }
}
