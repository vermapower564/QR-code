<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\ImageUploadService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdminTemplateController extends Controller
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

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
            'preview_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'css_styles' => 'nullable|string',
        ]);

        $previewPath = null;
        if ($request->hasFile('preview_image')) {
            $previewPath = $this->imageUploadService->uploadImage(
                $request->file('preview_image'),
                'templates',
                400,
                300
            );
        }

        $template = Template::create([
            'name' => $request->name,
            'slug' => strtolower($request->slug),
            'category' => $request->category,
            'is_premium' => $request->boolean('is_premium'),
            'preview_image' => $previewPath ?? '/templates/default.png',
            'css_styles' => $request->css_styles ?? '',
            'status' => 'active',
        ]);

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'template.create',
            'Template',
            $template->id,
            "Created template {$template->name} ({$template->slug})",
            ['category' => $template->category, 'is_premium' => $template->is_premium],
            $request->ip()
        );

        return back()->with('success', 'Template created successfully.');
    }

    public function toggleStatus(Request $request, int $id)
    {
        $template = Template::findOrFail($id);
        $oldStatus = $template->status ?? 'active';
        $newStatus = ($oldStatus === 'active') ? 'inactive' : 'active';
        $template->status = $newStatus;
        $template->save();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'template.toggle_status',
            'Template',
            $template->id,
            "Template {$template->name} status changed to {$newStatus}",
            ['old_status' => $oldStatus, 'new_status' => $newStatus],
            $request->ip()
        );

        return back()->with('success', "Template status updated to {$newStatus}.");
    }

    public function duplicate(Request $request, int $id)
    {
        $template = Template::findOrFail($id);
        $newSlug = $template->slug . '-copy-' . time();

        $newTemplate = Template::create([
            'name' => $template->name . ' (Copy)',
            'slug' => $newSlug,
            'category' => $template->category,
            'is_premium' => $template->is_premium,
            'preview_image' => $template->preview_image,
            'css_styles' => $template->css_styles,
            'status' => 'active',
        ]);

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'template.duplicate',
            'Template',
            $newTemplate->id,
            "Duplicated template {$template->name} to {$newTemplate->name}",
            ['original_id' => $template->id],
            $request->ip()
        );

        return back()->with('success', "Template duplicated as {$newTemplate->name}.");
    }
}

