<?php 
 
namespace Andromeda\Belajar\PHP\MVC\App;

class View{

    public static function render(string $view, $model): void
    {
        require __DIR__ . "/../View/header.html";
        require __DIR__ . "/../View/" . $view . ".php";
        require __DIR__ . "/../View/footer.html";
    }
} 

