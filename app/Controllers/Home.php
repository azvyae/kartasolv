<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $landingModel = model('LandingModel');
        $activitiesModel = model('ActivitiesModel');
        $membersModel = model('MembersModel');
        $data = [
            'title' => 'Halaman Utama | Karta Sarijadi',
            'landingInfo' => $landingModel->getLandingInfo(),
            'activitiesInfo' => $activitiesModel->getActivitiesInfo(),
            'members' => $membersModel->getAllMembers()
        ];
        return view('home/index', $data);
    }
    public function history()
    {
        $historyModel = model('HistoryModel');
        $data = [
            'title' => 'Sejarah Kami | Karta Sarijadi',
            'historyInfo' => $historyModel->getHistoryInfo()
        ];
        return view('home/history', $data);
    }

    public function contactUs()
    {
        $data = [
            'title' => 'Hubungi Kami | Karta Sarijadi'
        ];
        return view('home/contact_us', $data);
    }
}
