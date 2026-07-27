<?php

namespace App\Http\Controllers;

use App\Models\ImmunizationRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = ImmunizationRecord::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('child_name', 'like', "%{$search}%")
                    ->orWhere('head_of_family', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('unique_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($status && in_array($status, ['lengkap', 'tidak lengkap'])) {
            $query->where('immunization_status', $status);
        }

        $records = $query->paginate(15)->withQueryString();

        $stats = [
            'total_respondents' => ImmunizationRecord::count(),
            'total_users' => User::where('role', 'user')->count(),
            'complete_count' => ImmunizationRecord::where('immunization_status', 'lengkap')->count(),
            'incomplete_count' => ImmunizationRecord::where('immunization_status', 'tidak lengkap')->count(),
            'male_count' => ImmunizationRecord::where('gender', 'laki-laki')->count(),
            'female_count' => ImmunizationRecord::where('gender', 'perempuan')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'records' => $records,
            'stats' => $stats,
            'filters' => [
                'search' => $search ?? '',
                'status' => $status ?? 'all',
            ],
        ]);
    }

    public function destroy($id)
    {
        $record = ImmunizationRecord::findOrFail($id);
        $record->delete();

        return back()->with('success', 'Data responden berhasil dihapus.');
    }
}
