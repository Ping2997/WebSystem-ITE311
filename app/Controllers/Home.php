<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Welcome to LMS',
            'page' => 'home'
        ];
        return view('index', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About Us',
            'page' => 'about'
        ];
        return view('about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Contact Us',
            'page' => 'contact'
        ];
        return view('contact', $data);
    }
}
