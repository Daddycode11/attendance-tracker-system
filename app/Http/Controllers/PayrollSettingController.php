<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollSetting;

class PayrollSettingController extends Controller
{
    public function edit()
    {
        $settings = PayrollSetting::current();
        return view('admin.settings.payroll', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'working_days_per_month' => 'required|integer|min:1|max:31',
            'working_hours_per_day'  => 'required|integer|min:1|max:24',
            'ot_rate_multiplier'     => 'required|numeric|min:1|max:5',
            'late_grace_minutes'     => 'required|integer|min:0|max:60',
        ]);

        $settings = PayrollSetting::current();
        $settings->update($request->only([
            'working_days_per_month', 'working_hours_per_day',
            'ot_rate_multiplier', 'late_grace_minutes',
        ]));

        return back()->with('success', 'Payroll settings updated.');
    }
}
