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
        $agendas = Agenda::all();
        return response()->json($agendas);
    }


    public function store(StoreAgendaRequest $request)
    {
        $fileds = $request->validated([
            'title' => 'required|max:255',
            'description' => 'required',
            'date' => 'required|date',
        ]);

        $agenda = $request->user()->agendas()->create($fileds);

        return response()->json([
            'message' => 'Agenda created successfully',
            'agenda' => [
                'id' => $agenda->id,
                'title' => $agenda->title,
                'description' => $agenda->description,
                'date' => $agenda->date,
            ],
        ], 201);
    }


    public function show(Agenda $agenda)
    {
        return response()->json($agenda);
    }

    public function update(UpdateAgendaRequest $request, Agenda $agenda)
    {
        $agenda->update($request->validated());
        return response()->json($agenda);
    }


    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return response()->json(null, 204);
    }
}
