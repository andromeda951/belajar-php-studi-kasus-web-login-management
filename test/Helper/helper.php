<?php 
 
 namespace Andromeda\Belajar\PHP\MVC\App{

    function header(string $value) {
        echo $value;
    }
} 

namespace Andromeda\Belajar\PHP\MVC\Service{

    function setcookie(string $name, string $value): void {
        echo "$name: $value";
    }
}
