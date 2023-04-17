<?php

namespace App\Http\Controllers;

use App\Classes\Settings;
use App\Models\Module;
use Illuminate\Http\Request;

class ReportsController extends Controller
{

    public function index()
    {
        $data = [];
        $data['modules'] = Module::with(['tasks'])->whereIn('id' ,Settings::$reports)->get();
        return setPageContent('reports', $data);
    }

}
