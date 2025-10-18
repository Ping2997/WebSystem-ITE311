<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

class Announcement extends Controller
{
    public function index()
    {
        $announcementModel = new AnnouncementModel();

        $data['announcements'] = $announcementModel->orderBy('created_at', 'DESC')->findAll();

        return view('announcements', $data);
    }
}
