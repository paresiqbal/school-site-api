<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Http\Requests\StoreAgendaRequest;
use App\Http\Requests\UpdateAgendaRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AgendaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        //
    }


    public function store(StoreAgendaRequest $request)
    {
        //
    }


    public function show(Agenda $agenda)
    {
        //
    }

    public function update(UpdateAgendaRequest $request, Agenda $agenda)
    {
        //
    }


    public function destroy(Agenda $agenda)
    {
        //
    }
}
