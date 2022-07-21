<?php 

namespace Andromeda\Belajar\PHP\MVC\Middleware;

interface Middleware{
    
    public function before(): void;
}