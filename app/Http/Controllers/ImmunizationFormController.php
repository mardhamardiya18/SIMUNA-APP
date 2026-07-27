<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImmunizationRecordRequest;
use App\Models\Immunization;
use App\Models\ImmunizationRecord;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ImmunizationFormController extends Controller
{
    public function showForm(): Response
    {
        $user = Auth::user();
        $record = ImmunizationRecord::where('user_id', $user->id)->latest()->first();
        $immunizations = Immunization::getAllOrStatic();

        return Inertia::render('Responden/Form', [
            'existingRecord' => $record,
            'immunizationOptions' => $immunizations,
            'defaultUserData' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function submitForm(ImmunizationRecordRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $record = ImmunizationRecord::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($validated, [
                'email' => $validated['email'] ?? $user->email,
            ])
        );

        return redirect()->route('form.confirmation')->with('success', 'Formulir rekap imunisasi berhasil tersimpan!');
    }

    public function showConfirmation(): Response
    {
        $user = Auth::user();
        $record = ImmunizationRecord::where('user_id', $user->id)->latest()->first();

        if (! $record) {
            return redirect()->route('form.show')->with('error', 'Silakan isi formulir imunisasi terlebih dahulu.');
        }

        return Inertia::render('Responden/Confirmation', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'unique_code' => $user->unique_code,
            ],
            'record' => $record,
        ]);
    }
}
