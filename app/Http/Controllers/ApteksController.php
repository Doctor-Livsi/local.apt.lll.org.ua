<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apteks\Apteks;

class ApteksController extends Controller
{
    public function showStatusPage($status = 'working')
    {
        return view("apteks.status", ['status' => $status]);
    }

    public function index()
    {
        return redirect()->route('apteks.status', ['status' => 'working']);
    }

    public function details($id)
    {
        // TODO: Реалізувати отримання повної інформації по аптеці з MSSQL
        return view('apteks.details', ['id' => $id]);
    }

    public function getConnectionData(Request $request)
    {
        // TODO: Реалізувати витяг інформації про проблеми зі зв'язком
        return response()->json(['status' => 'ok']);
    }
}
