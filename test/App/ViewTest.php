<?php

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;

class ViewTest extends  TestCase
{
    public function testRender()
    {
        View::render('Home/index', [
            "Login"
        ]);

        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[html]');
        $this->expectOutputRegex('[body]');
        $this->expectOutputRegex('[Register]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Login Management]');
    }
}
