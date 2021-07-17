<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

class BaseController extends AbstractController
{

    public function renderDefault()
    {
        return [
            'title' => 'Tester',
        ];
    }

}