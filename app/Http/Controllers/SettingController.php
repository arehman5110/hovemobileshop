<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'shop_name'      => Setting::get('shop_name','Mobile Shop'),
            'shop_phone'     => Setting::get('shop_phone'),
            'shop_email'     => Setting::get('shop_email'),
            'shop_address'   => Setting::get('shop_address'),
            'email_subject'  => Setting::get('email_subject','Your Repair Receipt — Job #{job_id}'),
            'email_template' => Setting::get('email_template'),
            'terms_buy'      => Setting::get('terms_buy'),
            'terms_sell'     => Setting::get('terms_sell'),
        ];
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_name'      => 'required|string|max:100',
            'shop_phone'     => 'nullable|string|max:30',
            'shop_email'     => 'nullable|email|max:100',
            'shop_address'   => 'nullable|string|max:200',
            'email_subject'  => 'required|string|max:200',
            'email_template' => 'required|string',
            'terms_buy'      => 'nullable|string',
            'terms_sell'     => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'Settings saved!');
    }
}
