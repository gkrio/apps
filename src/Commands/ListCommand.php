<?php

namespace Gkr\Apps\Commands;

use Gkr\Support\Console\ColorTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ListCommand extends Command
{
    use ColorTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkr:app:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'list all the apps';

    protected function getHeaders()
    {
        $headers = new Collection(['name', 'type', 'enabled']);
        if ($this->laravel->bound('gkr.themes')){
            $headers->push('default theme');
            $headers->push('all themes');
        }
        return $headers->toArray();
    }
    protected function getRows()
    {
        $rows = [];
        foreach ($this->laravel['gkr.apps']->all() as $value) {
            $row = $this->laravel['gkr.apps']->getConfig($value['name']);
            if ($this->laravel->bound('gkr.themes')){
                $themesService = $this->laravel['gkr.themes'];
                $currentTheme = $row->get('theme') ?: 'No Active Theme';
                $row->put('theme',$currentTheme);
                $themesPrefix = '[';
                $themesContent = '';
                foreach ($themesService->findFromApp($value['name']) as $theme){
                    $themesContent .= "{$theme->getName()},";
                }
                $themesSuffix = ']';
                $themes = $themesPrefix.trim($themesContent,',').$themesSuffix;
                $row->put('themes',$themes);
            }
            $rows[] = $row->toArray();
        }
        return $rows;
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->table($this->getHeaders(), $this->getRows());
    }
}
