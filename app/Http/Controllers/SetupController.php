<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function index()
    {
        return view('platform.setups');
    }

    public function update(Request $request)
    {
        return redirect()->back()->with('success', 'Settings updated.');
    }

    public function testEmail(Request $request)
    {
        return redirect()->back()->with('success', 'Test email sent.');
    }

    public function testSms(Request $request)
    {
        return redirect()->back()->with('success', 'Test SMS sent.');
    }
}
