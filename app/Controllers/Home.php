<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Ensure a gate token exists and pass it to the view
        $token = session()->get('home_gate');
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            session()->set('home_gate', $token);
        }

        return view('index', [
            'title' => 'Welcome to ITE311 Learning Management System',
            'gate'  => $token,
        ]);
    }

    public function about()
    {
        // Reuse or create gate token and pass standard data
        $token = session()->get('home_gate');
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            session()->set('home_gate', $token);
        }
        return view('about', [
            'title' => 'About Us',
            'gate'  => $token,
            'page'  => 'about',
        ]);
    }

    public function contact()
    {
        // Reuse or create gate token and pass standard data
        $token = session()->get('home_gate');
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            session()->set('home_gate', $token);
        }
        return view('contact', [
            'title' => 'Contact Us',
            'gate'  => $token,
            'page'  => 'contact',
        ]);
    }
}