<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\TestFeeSetting;
use Illuminate\Http\Request;

class TestFeeAdminController extends Controller
{
    public function index()
    {
        $fees = TestFeeSetting::latest()->get();
        return view('admin.test-fees.index', compact('fees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'fee'  => 'required|numeric|min:0',
        ]);

        TestFeeSetting::create([
            'name'      => $request->name,
            'fee'       => $request->fee,
            'is_active' => true,
        ]);

        return back()->with('success', 'Test fee added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'fee'  => 'required|numeric|min:0',
        ]);

        TestFeeSetting::findOrFail($id)->update([
            'name'      => $request->name,
            'fee'       => $request->fee,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Test fee updated.');
    }

    public function destroy($id)
    {
        TestFeeSetting::findOrFail($id)->delete();
        return back()->with('success', 'Test fee deleted.');
    }
}