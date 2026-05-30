<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = Setting::first();

        $request->validate([
            'app_name' => 'required|string|max:100',
            'school_name' => 'required|string|max:100',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'timezone' => 'required|string|max:50',
            'address' => 'nullable|string',
        ]);

        $data = $request->only('app_name', 'school_name', 'website', 'timezone', 'address');

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();

            // Save under public/assets/uploads/settings/
            $destinationPath = public_path('assets/uploads/settings');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            // Remove old logo if exists
            if ($settings->logo_url && File::exists($destinationPath . '/' . $settings->logo_url)) {
                File::delete($destinationPath . '/' . $settings->logo_url);
            }

            $logo->move($destinationPath, $logoName);
            $data['logo_url'] = $logoName;
        }

        $settings->update($data);
        \Illuminate\Support\Facades\Cache::forget('app_settings');
        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
