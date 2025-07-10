<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Project;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = \App\Models\Admin\Project::orderByDesc('id');
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('q')) {
            $q = request('q');
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%") ;
            });
        }
        $projects = $query->get();
        if (request()->ajax()) {
            return view('admin.project._table', compact('projects'))->render();
        }
        return view('admin.project.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255', // Имя
            'project_name' => 'required|string|max:255', // Название проекта
            'email' => 'required|email|max:255',
            'registered_at' => 'required|date',
            'language' => 'required|string|max:10',
            'status' => 'required|string|max:20',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'social_links' => 'nullable|string',
        ]);

        // Обработка загрузки логотипа
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('projects/logos', 'public');
        }

        // Преобразование соцсетей в массив
        if (!empty($validated['social_links'])) {
            $validated['social_links'] = array_map('trim', explode(',', $validated['social_links']));
        }

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Проект успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255', // Имя
            'project_name' => 'required|string|max:255', // Название проекта
            'email' => 'required|email|max:255',
            'registered_at' => 'required|date',
            'language' => 'required|string|max:10',
            'status' => 'required|string|max:20',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'social_links' => 'nullable|string',
        ]);

        // Обработка загрузки логотипа
        if ($request->hasFile('logo')) {
            // Удалить старый логотип, если есть
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            $validated['logo'] = $request->file('logo')->store('projects/logos', 'public');
        }

        // Преобразование соцсетей в массив
        if (!empty($validated['social_links'])) {
            $validated['social_links'] = array_map('trim', explode(',', $validated['social_links']));
        }

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Проект успешно обновлён!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
