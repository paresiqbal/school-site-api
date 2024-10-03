<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public function store(Request $request)
    {
        Log::info('Received data: ', $request->all());

        $fields = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'date' => 'required|date_format:d-m-Y',
        ]);

        Log::info('Validated data: ', $fields);

        $fields['date'] = Carbon::createFromFormat('d-m-Y', $fields['date'])->format('Y-m-d');

        Log::info('Converted date: ', ['date' => $fields['date']]);

        $agenda = $request->user()->agendas()->create($fields);

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

    public function update(Request $request, Agenda $agenda)
    {
        Gate::authorize('modified', $agenda);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'date' => 'required|date',
        ]);

        $agenda->update($fields);

        return response()->json([
            'message' => 'Agenda updated successfully',
            'data' => $agenda
        ], 200);
    }

    public function destroy(Agenda $agenda)
    {
        Gate::authorize('modified', $agenda);

        $agenda->delete();

        return response()->json([
            'message' => 'Agenda deleted',
        ]);
    }
}
