<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(){
        return view('client.pages.service.index');
    }

    public function add(){
        return view('client.pages.service.add');
    }
}
