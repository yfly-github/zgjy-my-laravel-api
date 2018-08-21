<?php

namespace App\Http\Controllers\Admin;

use App\Tools\SysHookValide;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function index(Request $req){

        SysHookValide::srsHookCallback($req,[
            'page'        => '',
            'app_name'    => 'required',
        ]);




        return view('welcome');



    }





}
