<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

class LogController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $logViewer = new LogViewerController();
        return $logViewer->index();
    }
}