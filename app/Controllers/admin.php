<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        // Must be logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        // Must be admin
        if (session()->get('role') !== 'admin') {
            session()->flashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        // Render your existing admin dashboard template in tempates/
        return view('tempates/admin', [
            'name' => session()->get('name'),
        ]);
    }
}