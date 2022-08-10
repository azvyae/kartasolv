<?php

namespace App\Controllers;


class Home extends BaseController
{
    private $lm, $am, $mm, $hm;
    public function __construct()
    {
        $this->lm = new \App\Models\LandingModel();
        $this->am = new \App\Models\ActivitiesModel();
        $this->mm = new \App\Models\MembersModel();
        $this->hm = new \App\Models\HistoryModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Halaman Utama | Karta Sarijadi',
            'landingInfo' => $this->lm->find(1, true),
            'activitiesInfo' => $this->am->find(1, true),
            'members' => $this->mm->getMembers()
        ];
        return view('home/index', $data);
    }
    public function history()
    {
        $data = [
            'title' => 'Sejarah Kami | Karta Sarijadi',
            'historyInfo' => $this->hm->find(1, true)
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
