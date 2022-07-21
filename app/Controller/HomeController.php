<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Controller;

use Andromeda\Belajar\PHP\MVC\App\View;

class HomeController{

    public function index(): void
    {
        View::render("Home/index", [
            "title" => "PHP Login Management"
        ]);
    }


}

