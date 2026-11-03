<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\CertificateSetting;
use Illuminate\Http\Request;

class CertificateAdminController extends Controller
{
    public function index()
    {
        $fees = CertificateSetting::latest()->get();
        return view('admin.certificates.index', compact('fees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'fee'  => 'required|numeric|min:0',
        ]);

        CertificateSetting::create([
            'name'      => $request->name,
            'fee'       => $request->fee,
            'is_active' => true,
        ]);

        return back()->with('success', 'Certificate added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'fee'  => 'required|numeric|min:0',
        ]);

        CertificateSetting::findOrFail($id)->update([
            'name'      => $request->name,
            'fee'       => $request->fee,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Certificate updated.');
    }

    public function destroy($id)
    {
        CertificateSetting::findOrFail($id)->delete();
        return back()->with('success', 'Certificate deleted.');
    }
}