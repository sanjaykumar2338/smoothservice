<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LandingPage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function order(Request $request, $landing_no){
        $landingPage = LandingPage::where('landing_no', $landing_no)->first();
        if(!$landingPage){
            abort(404);
        }

        //echo "<pre>"; print_r($landingPage); die;
        return view('client.grapejs_frontend')->with('landingPage', $landingPage)->with('slug', $landing_no);
    }
}
