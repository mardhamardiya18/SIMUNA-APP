<?php

namespace App\Http\Controllers;

use App\Models\Immunization;
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
            'allImmunizations' => Immunization::getAllOrStatic(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $record = ImmunizationRecord::findOrFail($id);

        $validated = $request->validate([
            'immunization_types' => 'nullable|array',
            'immunization_types.*' => 'string',
            'immunization_status' => 'required|in:lengkap,tidak lengkap',
            'incomplete_reason' => 'nullable|string',
        ]);

        $record->update([
            'immunization_types' => $validated['immunization_types'] ?? [],
            'immunization_status' => $validated['immunization_status'],
            'incomplete_reason' => $validated['incomplete_reason'] ?? null,
        ]);

        return back()->with('success', 'Status & rekap imunisasi berhasil diperbarui.');
    }

    public function exportExcel(Request $request)
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

        $records = $query->get();

        $allVaccines = Immunization::getAllOrStatic();
        $vaccineMap = [];
        foreach ($allVaccines as $v) {
            $vaccineMap[$v['code']] = $v['name'];
        }

        $filename = 'Rekap_Imunisasi_SIMUNA_Puskesmas_Bulusan_' . date('Ymd_His') . '.xls';

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Imunisasi</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><style>
            th { background-color: #059669; color: #ffffff; font-weight: bold; border: 1px solid #047857; text-align: center; vertical-align: middle; height: 35px; }
            td { border: 1px solid #cbd5e1; vertical-align: middle; padding: 6px; font-family: sans-serif; font-size: 12px; }
            .title { font-size: 16px; font-weight: bold; color: #047857; text-align: center; height: 30px; }
            .subtitle { font-size: 12px; color: #475569; text-align: center; height: 20px; }
            .badge-lengkap { color: #047857; font-weight: bold; }
            .badge-tidak-lengkap { color: #b91c1c; font-weight: bold; }
        </style></head>';
        $html .= '<body>';
        $html .= '<table>';
        $html .= '<tr><td colspan="11" class="title">REKAPITULASI DATA IMUNISASI ANAK (SIMUNA)</td></tr>';
        $html .= '<tr><td colspan="11" class="subtitle">PUSKESMAS BULUSAN - KOTA SEMARANG | Tanggal Cetak: ' . date('d-m-Y H:i') . ' WIB</td></tr>';
        $html .= '<tr><td colspan="11"></td></tr>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="width:40px;">No</th>';
        $html .= '<th style="width:110px;">ID Verifikasi</th>';
        $html .= '<th style="width:160px;">Nama Anak</th>';
        $html .= '<th style="width:160px;">Nama Orang Tua / Wali</th>';
        $html .= '<th style="width:100px;">Jenis Kelamin</th>';
        $html .= '<th style="width:100px;">Tanggal Lahir</th>';
        $html .= '<th style="width:100px;">Usia</th>';
        $html .= '<th style="width:250px;">Alamat Lengkap</th>';
        $html .= '<th style="width:120px;">Status Imunisasi</th>';
        $html .= '<th style="width:300px;">Jenis Vaksin Diterima</th>';
        $html .= '<th style="width:200px;">Alasan Belum Lengkap</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($records as $index => $row) {
            $uniqueCode = $row->user ? $row->user->unique_code : '-';
            $gender = $row->gender === 'laki-laki' ? 'Laki-laki' : ($row->gender === 'perempuan' ? 'Perempuan' : '-');
            $statusClass = $row->immunization_status === 'lengkap' ? 'badge-lengkap' : 'badge-tidak-lengkap';
            $statusText = strtoupper($row->immunization_status ?? 'TIDAK LENGKAP');

            $givenTypes = is_array($row->immunization_types) ? $row->immunization_types : [];
            $givenNames = [];
            foreach ($givenTypes as $code) {
                if (isset($vaccineMap[$code])) {
                    $givenNames[] = $vaccineMap[$code];
                } else {
                    $givenNames[] = strtoupper($code);
                }
            }
            $vaccinesStr = !empty($givenNames) ? implode(', ', $givenNames) : '-';

            $bg = ($index % 2 === 0) ? '#ffffff' : '#f8fafc';

            $html .= "<tr style='background-color: {$bg};'>";
            $html .= "<td style='text-align:center;'>" . ($index + 1) . "</td>";
            $html .= "<td style='text-align:center; font-weight:bold;'>{$uniqueCode}</td>";
            $html .= "<td>" . htmlspecialchars($row->child_name ?? '-') . "</td>";
            $html .= "<td>" . htmlspecialchars($row->head_of_family ?? '-') . "</td>";
            $html .= "<td style='text-align:center;'>{$gender}</td>";
            $html .= "<td style='text-align:center;'>" . ($row->birth_date ? date('d-m-Y', strtotime($row->birth_date)) : '-') . "</td>";
            $html .= "<td style='text-align:center;'>" . htmlspecialchars($row->age_text ?? '-') . "</td>";
            $html .= "<td>" . htmlspecialchars($row->address ?? '-') . "</td>";
            $html .= "<td class='{$statusClass}' style='text-align:center;'>{$statusText}</td>";
            $html .= "<td>" . htmlspecialchars($vaccinesStr) . "</td>";
            $html .= "<td>" . htmlspecialchars($row->incomplete_reason ?? '-') . "</td>";
            $html .= "</tr>";
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</body></html>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function destroy($id)
    {
        $record = ImmunizationRecord::findOrFail($id);
        $record->delete();

        return back()->with('success', 'Data responden berhasil dihapus.');
    }
}
