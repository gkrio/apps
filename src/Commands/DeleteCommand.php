<?php

namespace Gkr\Apps\Commands;

use Gkr\Support\Console\ColorTrait;
use Illuminate\Console\Command;

class DeleteCommand extends Command
{
    use ColorTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkr:app:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete an app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lists = $this->laravel['gkr.apps']->list();
        $name = $this->choice($this->askColor('App Index','select indexOf app to delete'), $lists);
        $this->laravel['gkr.apps']->delete($name);
        $this->info("Delete App {$name} Success!");
    }
}
