<?php

namespace App\Http\Controllers;

use App\Classes\Settings;
use App\Jobs\RunNearOsManually;
use App\Jobs\RunRetailNearOsManually;
use App\Models\Module;
use App\Models\Nearoutofstock;
use App\Models\Retailnearoutofstock;
use Illuminate\Http\Request;

class ReportsController extends Controller
{


    public Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function index()
    {
        $data = [];
        $data['modules'] = Module::with(['tasks'])->whereIn('id' ,Settings::$reports)->get();
        return setPageContent('reports', $data);
    }


    public function run_nearos()
    {
        dispatch(new RunNearOsManually());
        $this->settings->put('m_run_nears', 'running');
        Nearoutofstock::truncate();
        return redirect()->route('run_nearos');
    }

    public function run_retail_nearos()
    {
        dispatch(new RunRetailNearOsManually());
        $this->settings->put('m_retail_run_nears', 'running');
        Retailnearoutofstock::truncate();
        return redirect()->route('run_retail_nearos');
    }



}
