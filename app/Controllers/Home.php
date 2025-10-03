<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        session()->set('from_home', true);
        session()->set('from_home_time', time());
        
        return view('tempates/index', [
            'title' => 'Welcome to Autida Learning Management System',
        ]);
    }

    public function about()
    {
        return view('tempates/about', [
            'title' => 'About Us',
        ]);
    }

    public function contact()
    {
        return view('tempates/contact', [
            'title' => 'Contact Us',
        ]);
    }
}