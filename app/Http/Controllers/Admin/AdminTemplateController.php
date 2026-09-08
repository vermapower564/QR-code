<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class AdminTemplateController extends Controller
{
    public function index()
    {
        $templates = Template::all();
        return view('admin.templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:templates,slug',
            'category' => 'required|string',
            'is_premium' => 'nullable|boolean',
        ]);

        Template::create([
            'name' => $request->name,
            'slug' => strtolower($request->slug),
            'category' => $request->category,
            'is_premium' => $request->boolean('is_premium'),
            'status' => 'active',
        ]);

        return back()->with('success', 'Template created.');
    }
}
