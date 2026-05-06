<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    public function index()
    {
        $users = $this->isAdmin()
            ? User::orderBy('name')->get(['id', 'name'])
            : collect();

        return view('app.logs.index', compact('users'));
    }

    public function datatable(Request $request)
    {
        $query = $this->buildQuery($request);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('user', fn($log) => $log->user?->name ?? 'System')
            ->addColumn('event_action', fn($log) => sprintf(
                '<span class="badge-event">%s</span> <span class="badge-action">%s</span>',
                e($log->event),
                e($log->action)
            ))
            ->addColumn('status_code', fn($log) => $log->status_code ?: '—')
            ->addColumn('occurred_at', fn($log) => optional($log->occurred_at)->format('d M Y, h:i:s A') ?: '—')
            ->addColumn('route_name', fn($log) => $log->route_name ?: '—')
            ->addColumn('description', fn($log) => $log->description ?: '—')
            ->rawColumns(['event_action'])
            ->make(true);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->buildQuery($request)->orderByDesc('occurred_at');
        $rows = $query->get();

        $filename = 'logs-export-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'User',
                'Event',
                'Action',
                'Description',
                'Entity Type',
                'Entity ID',
                'Method',
                'URL',
                'Route Name',
                'IP',
                'Status Code',
                'Occurred At',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->user?->name ?? 'System',
                    $row->event,
                    $row->action,
                    $row->description,
                    $row->entity_type,
                    $row->entity_id,
                    $row->method,
                    $row->url,
                    $row->route_name,
                    $row->ip_address,
                    $row->status_code,
                    optional($row->occurred_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function buildQuery(Request $request)
    {
        $query = LogEntry::with('user')->orderByDesc('occurred_at');

        if (!$this->isAdmin() ||!$this->isRP()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('event')) {
            $query->where('event', $request->string('event')->toString());
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', (int) $request->status_code);
        }

        if ($request->filled('route_name')) {
            $query->where('route_name', 'like', '%' . $request->string('route_name') . '%');
        }

        if ($request->filled('user_id') && $this->isAdmin()) {
            $query->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function isAdmin(): bool
    {
        $userId = auth()->id();
        if (!$userId) {
            return false;
        }

        return User::whereKey($userId)
            ->whereHas('roles', fn($query) => $query->where('name', 'admin'))
            ->exists();
    }

    private function isRP(): bool
    {
        $userId = auth()->id();
        if (!$userId) {
            return false;
        }
dd($userId);
        return User::whereKey($userId)
            ->whereHas('roles', fn($query) => $query->where('name', 'Resolution Professional (RP)'))
            ->exists();
    }
}
