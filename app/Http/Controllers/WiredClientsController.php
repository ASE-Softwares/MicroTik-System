<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WiredClient;

class WiredClientsController extends Controller
{
    public function index(){
        $users = WiredClient::all();
        return view('wired_clients.index', compact('users'));
    }
    public function create(){
        $users = WiredClient::all();
        return view('wired_clients.create', compact('users'));
    }
}
