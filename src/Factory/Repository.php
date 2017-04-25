<?php

namespace Gkr\Apps\Factory;

use Gkr\Support\Config\Drivers\Yaml;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Repository
{
    protected $app;
    protected $config;
    protected $name;

    public function __construct(Application $app, Yaml $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function keyName($name)
    {
        return Str::lower(Str::studly($name));
    }
    public function getConfig($name)
    {
        $config = $this->config->get("scan.{$this->keyName($name)}");
        return new Collection($config);
    }

    public function set($name, $key, $value = null)
    {
        if ($this->has($name)){
            $this->config->set("scan.{$this->keyName($name)}.{$key}", $value);
        }
        return $this;
    }

    public function unset($name,$key)
    {
        if ($this->has($name)){
            $this->config->unset("scan.{$this->keyName($name)}.{$key}");
        }
        return $this;
    }

    public function get($name,$key)
    {
        return $this->has($name) ? $this->getConfig($name)->get($key) : null;
    }
    public function create($name, $config = [])
    {
        if ($this->has($name)) {
            $message = "App named {$name} is exists in config!";
            throw new \Exception($message);
        }
        $config['name'] = Str::studly($name);
        $this->config->set("scan.{$this->keyName($name)}", $config);
    }

    public function has($name)
    {
        return in_array($this->keyName($name), $this->list());
    }

    public function check($name, $message = null)
    {
        $message = $message ?: "app {$name} is not exists!";
        if (!$this->has($name)) {
            throw new \Exception($message);
        }
        return $this->keyName($name);
    }

    function list()
    {
        return array_keys($this->all());
    }

    public function all()
    {
        return $this->config->get('scan', []);
    }

    public function delete($name)
    {
        if (!$this->has($name)) {
            $message = "App named {$name} is not exists in config,so can not delete it!";
            throw new \Exception($message);
        }
        return $this->config->unset("scan.{$this->keyName($name)}");
    }

    public function type($type)
    {
        if (!in_array($type,['web','api','console'])){
            throw new \Exception('app type should be web,api or console!');
        }
        return with(new Collection($this->all()))
            ->filter(function ($value) use ($type) {
                return $value['type'] == $type;
            });
    }
}
