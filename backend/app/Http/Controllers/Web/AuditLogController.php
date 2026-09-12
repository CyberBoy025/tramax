<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLogEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\AuditLogAdminController — read-only,
// entries are written automatically by AuditLogObserver.
class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLogEntry::query()->with('user:id,name,email')->latest('id');

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->string('entity_type'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        return view('admin.audit-log.index', [
            'entries' => $query->limit(200)->get(),
            'actions' => AuditLogEntry::query()->distinct()->orderBy('action')->pluck('action'),
            'entityTypes' => AuditLogEntry::query()->distinct()->orderBy('entity_type')->pluck('entity_type'),
            'filters' => $request->only(['action', 'entity_type', 'user_id']),
        ]);
    }
}
