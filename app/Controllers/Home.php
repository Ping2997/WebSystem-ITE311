<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Mark that user has visited homepage in this session (one-time, short-lived)
        session()->set('from_home', true);
        session()->set('from_home_time', time());

        return view('tempates/index', [
            'title' => 'Welcome to ITE311 Learning Management System',
        ]);
    }

    public function about()
    {
        // Enforce: must come from homepage very recently (one-time)
        if (!session()->get('from_home')) {
            return redirect()->to(base_url('/'));
        }
        $fromTime = (int) (session()->get('from_home_time') ?? 0);
        if ($fromTime === 0 || (time() - $fromTime) > 15) {
            session()->remove('from_home');
            session()->remove('from_home_time');
            return redirect()->to(base_url('/'));
        }
        // Consume flags so the page cannot be reloaded directly
        session()->remove('from_home');
        session()->remove('from_home_time');

        return view('tempates/about', [
            'title' => 'About Us',
            'page'  => 'about',
        ]);
    }

    public function contact()
    {
        // Enforce: must come from homepage very recently (one-time)
        if (!session()->get('from_home')) {
            return redirect()->to(base_url('/'));
        }
        $fromTime = (int) (session()->get('from_home_time') ?? 0);
        if ($fromTime === 0 || (time() - $fromTime) > 15) {
            session()->remove('from_home');
            session()->remove('from_home_time');
            return redirect()->to(base_url('/'));
        }
        // Consume flags so the page cannot be reloaded directly
        session()->remove('from_home');
        session()->remove('from_home_time');

        return view('tempates/contact', [
            'title' => 'Contact Us',
            'page'  => 'contact',
        ]);
    }
}