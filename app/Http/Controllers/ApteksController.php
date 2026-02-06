<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apteks\Apteks;

class ApteksController extends Controller
{
    public function showStatusPage(string $status)
    {
        // Відповідність статусу до Blade-шаблону
        $views = [
            'working'   => 'apteks.status.working',
            'projected' => 'apteks.status.projected',
            'closed'    => 'apteks.status.closed',
            'connected' => 'apteks.status.connected',
        ];

        // На випадок некоректного статусу (хоча whereIn вже фільтрує)
        if (!isset($views[$status])) {
            abort(404);
        }

        return view($views[$status], [
            'status'  => $status,
            'variant' => $status, // зручно для Vue preset
        ]);
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
