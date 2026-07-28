<?php

namespace App\Http\Controllers;

use App\Models\Immunization;
use App\Models\ImmunizationRecord;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RespondenDashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $record = ImmunizationRecord::where('user_id', $user->id)->latest()->first();
        $allImmunizations = Immunization::getAllOrStatic();

        $completedCodes = $record ? ($record->immunization_types ?? []) : [];

        // Split into completed vs upcoming
        $completedList = $allImmunizations->filter(fn ($item) => in_array($item->code, $completedCodes))->values();
        $upcomingList = $allImmunizations->filter(fn ($item) => ! in_array($item->code, $completedCodes))->values();

        $puskesmasInfo = [
            'name' => 'Puskesmas Bulusan',
            'address' => 'Jalan Timoho Raya, Kelurahan Bulusan, Kecamatan Tembalang, Kota Semarang, Jawa Tengah 50277',
            'phone' => '0812-3456-7890',
            'hours' => 'Senin - Sabtu: 08.00 - 12.00 WIB',
            'midwife' => 'Bidan Tim Posyandu Puskesmas Bulusan',
            'posyandu_schedule' => 'Setiap Hari Selasa Minggu ke-2 & ke-4',
        ];

        return Inertia::render('Responden/Dashboard', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'unique_code' => $user->unique_code,
            ],
            'record' => $record,
            'completedList' => $completedList,
            'upcomingList' => $upcomingList,
            'allImmunizations' => $allImmunizations,
            'puskesmasInfo' => $puskesmasInfo,
        ]);
    }
}
