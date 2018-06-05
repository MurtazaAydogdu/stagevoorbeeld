<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;



class KeyGenerate extends Command {

    protected $name = 'key:generate';


    protected $description = 'Run this command the generate a php key:generate';

    public function handle() {
        $key = $this->getRandomKey();
        if ($this->option('show')) {
            return $this->line('<comment>'.$key.'</comment>');
        }
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents(
                $path,
                str_replace(env('APP_KEY'), $key, file_get_contents($path))
            );
        }
        $this->info("Application key [$key] set successfully.");
    }

    protected function getRandomKey()
    {
        return Str::random(32);
    }

    protected function getOptions()
    {
        return array(
            array('show', null, InputOption::VALUE_NONE, 'Simply display the key instead of modifying files.'),
        );
    }
}