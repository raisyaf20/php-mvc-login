<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App;




class View
{

    public static function render(string $view, $model)
    {
        require __DIR__ . '/../View/fragments/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/fragments/footer.php';
    }

    public static function redirect(string $url)
    {
        header("Location: $url");
        if (getenv('mode') != 'test') {
            exit();
        }
    }
}
