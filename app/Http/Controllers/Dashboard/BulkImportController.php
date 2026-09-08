<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateBulkQR;
use App\Models\QRProfile;
use App\Services\FeatureService;
use App\Services\LinkValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BulkImportController extends Controller
{
    public function __construct(
        protected FeatureService $featureService,
        protected LinkValidationService $linkValidator
    ) {}

    public function index()
    {
        $user = Auth::user();
        $hasFeature = $this->featureService->hasFeature($user, 'bulk_import');

        return view('dashboard.profiles.bulk', compact('hasFeature'));
    }

    public function preview(Request $request)
    {
        $user = Auth::user();
        if (!$this->featureService->hasFeature($user, 'bulk_import')) {
            return back()->with('error', 'Bulk CSV import is only available on the Business plan.');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        if (empty($rows) || count($rows) < 2) {
            return back()->with('error', 'CSV file is empty or missing headers.');
        }

        $headers = array_map('trim', array_map('strtolower', array_shift($rows)));
        $parsedRows = [];

        foreach ($rows as $index => $row) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, array_map('trim', $row));
            
            // Formula injection sanitization
            $name = $this->linkValidator->sanitizeCsvCell($data['name'] ?? 'Imported Profile');
            $designation = $this->linkValidator->sanitizeCsvCell($data['designation'] ?? '');
            $company = $this->linkValidator->sanitizeCsvCell($data['company'] ?? '');
            $email = $this->linkValidator->sanitizeCsvCell($data['email'] ?? '');
            $rawPhone = $this->linkValidator->sanitizeCsvCell($data['phone'] ?? '');
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            $phone = (strlen($cleanPhone) === 10) ? $cleanPhone : '';
            $website = $this->linkValidator->sanitizeCsvCell($data['website'] ?? '');

            $slugBase = Str::slug($name);
            if (empty($slugBase)) {
                $slugBase = 'profile';
            }
            $slug = $slugBase . '-' . rand(1000, 9999);

            $parsedRows[] = [
                'row_number' => $index + 2,
                'name' => $name,
                'slug' => $slug,
                'designation' => $designation,
                'company' => $company,
                'email' => $email,
                'phone' => $phone,
                'website' => $website,
            ];
        }

        session(['bulk_rows' => $parsedRows]);

        return view('dashboard.profiles.bulk', [
            'hasFeature' => true,
            'previewRows' => $parsedRows,
        ]);
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        if (!$this->featureService->hasFeature($user, 'bulk_import')) {
            return back()->with('error', 'Bulk CSV import is only available on the Business plan.');
        }

        $rows = session('bulk_rows', []);
        if (empty($rows)) {
            return redirect()->route('dashboard.profiles.bulk')->with('error', 'No previewed data found. Please upload a CSV again.');
        }

        $createdIds = [];
        foreach ($rows as $row) {
            $profile = QRProfile::create([
                'user_id' => $user->id,
                'slug' => strtolower($row['slug']),
                'name' => $row['name'],
                'designation' => $row['designation'] ?: null,
                'company' => $row['company'] ?: null,
                'email' => $row['email'] ?: null,
                'phone' => $row['phone'] ?: null,
                'website' => $row['website'] ?: null,
                'status' => 'active',
            ]);

            $createdIds[] = $profile->id;
        }

        session()->forget('bulk_rows');

        // Dispatch background queue job for QR code batch generation
        GenerateBulkQR::dispatch($createdIds);

        return redirect()->route('dashboard.profiles.index')->with('success', count($createdIds) . ' profiles queued for creation and QR generation!');
    }
}
