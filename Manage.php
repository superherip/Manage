<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


//define('LOCATION', 'http://mysite/');

class Manage
{
    private $file;
    private $newfile;
    private $dir;
    private $message = [];

    public function __construct(string $url)
    {
        $this->run($url);
    }

    private function arrayPop(string $url): string
    {
        return array_pop(explode('/', $url));
    }

    private function fileget(string $link): array
    {
        return explode(' ', file_get_contents($link));
    }

    private function getlinks(string $key, string $value): array
    {
        return array_combine($this->fileget($key), $this->fileget($value));
    }

    private function file(string $url)
    {

        $pop = $this->arrayPop($url);

        if($pop === 'Manage.php')
        {
            $this->message[] = 'Введите название пакета например <b> Manage.php/laravel </b>';
        }


        $links = $this->getlinks('http://localhost/key.php', 'http://localhost/value.php');

        if (!isset($links[$pop]))
        {
            $this->message[] = 'Не найден путь к файлу для загрузки';
        }
        $this->file = $links[$pop];

    }

    private function newfile()
    {
        $this->newfile = basename($this->file);
    }

    private function load()
    {
        if (!copy($this->file, $this->newfile))
        {
            $this->message[] = 'Не удалось скопировать';
        }

    }

    private function host()
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
    }

    private function redirect()
    {
        $host = $this->host();
        header('Location:' . $host);
        exit;
    }

    private function dir()
    {
        $this->dir = __DIR__ . '/';
    }

    private function zip()
    {
        $zip = new ZipArchive;

        $res = $zip->open($this->newfile);

        if ($res === TRUE)
        {
            $zip->extractTo($this->dir);

            $zip->close();
        }
        else
        {
            $this->message[] = 'Не удалось распаковать zip';
        }
    }

    private function deletefile()
    {
        unlink($this->newfile);
    }

    private function message()
    {

        if (!empty($this->message))
        {
            foreach ($this->message as $value)
            {
                echo $value . '<br>';
            }
        }

        else
        {
            $this->redirect();
        }
    }

    public function run(string $url)
    {
        $this->file($url);
        $this->dir();
        $this->newfile();
        $this->load();
        $this->zip();
        $this->deletefile();
        $this->message();

    }

}

$manage = new Manage($_SERVER['REQUEST_URI']);
