<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Generate a gate token to restrict direct access to auth pages
        $token = bin2hex(random_bytes(16));
        session()->set('home_gate', $token);

        return view('index', [
            'title' => 'Welcome to ITE311 Learning Management System',
            'gate'  => $token,
        ]);
    }

    public function about()
    {
        // Gate: allow only when arriving from homepage or with gate token
        if ($this->request->getMethod() === 'get') {
            $provided = $this->request->getGet('gate');
            $ref = $this->request->getHeaderLine('Referer');
            $home = rtrim(base_url('/'), '/');
            $expected = session()->get('home_gate');
            $gateValid = $provided && (!$expected || hash_equals($expected, $provided));
            $fromHome  = $ref && (stripos($ref, $home) === 0);
            if (!($gateValid || $fromHome)) {
                return redirect()->to('/');
            }
        }
        // Ensure a gate token exists and pass it to the view for links
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
        // Gate: allow only when arriving from homepage or with gate token
        if ($this->request->getMethod() === 'get') {
            $provided = $this->request->getGet('gate');
            $ref = $this->request->getHeaderLine('Referer');
            $home = rtrim(base_url('/'), '/');
            $expected = session()->get('home_gate');
            $gateValid = $provided && (!$expected || hash_equals($expected, $provided));
            $fromHome  = $ref && (stripos($ref, $home) === 0);
            if (!($gateValid || $fromHome)) {
                return redirect()->to('/');
            }
        }
        // Ensure a gate token exists and pass it to the view for links
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
