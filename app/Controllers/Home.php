<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('index', [
            'title' => 'Welcome to ITE311 Learning Management System'
        ]);
    }

    public function about()
    {
        return view('about', [
            'title' => 'About Us'
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'title' => 'Contact Us'
        ]);
    }
}
