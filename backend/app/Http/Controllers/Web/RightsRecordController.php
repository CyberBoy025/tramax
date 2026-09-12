<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\RightsRecord;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\RightsRecordAdminController.
class RightsRecordController extends Controller
{
    private const STATUSES_COPYRIGHT = ['Active', 'Disputed', 'Expired'];

    private const STATUSES_LICENSING = ['Unlicensed', 'Licensed', 'Exclusive'];

    public function index(): View
    {
        return view('admin.rights.index', [
            'rights' => RightsRecord::query()->with('release:id,title')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.rights.form', [
            'right' => null,
            'releases' => Release::query()->orderByDesc('release_date')->get(['id', 'title']),
            'copyrightStatuses' => self::STATUSES_COPYRIGHT,
            'licensingStatuses' => self::STATUSES_LICENSING,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        RightsRecord::create($this->validated($request));

        return redirect()->route('admin.rights.index')->with('success', 'Rights record created.');
    }

    public function edit(RightsRecord $right): View
    {
        return view('admin.rights.form', [
            'right' => $right,
            'releases' => Release::query()->orderByDesc('release_date')->get(['id', 'title']),
            'copyrightStatuses' => self::STATUSES_COPYRIGHT,
            'licensingStatuses' => self::STATUSES_LICENSING,
        ]);
    }

    public function update(Request $request, RightsRecord $right): RedirectResponse
    {
        $right->update($this->validated($request, sometimes: true));

        return redirect()->route('admin.rights.index')->with('success', 'Rights record updated.');
    }

    public function destroy(Request $request, RightsRecord $right): RedirectResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            abort(403, 'Only a Super Administrator can delete a rights record.');
        }

        $right->delete();

        return redirect()->route('admin.rights.index')->with('success', 'Rights record deleted.');
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['nullable', $r];

        return $request->validate([
            'release_id' => ['nullable', 'exists:releases,id'],
            'master_owner' => [...$rule('string'), 'max:255'],
            'publishing_owner' => [...$rule('string'), 'max:255'],
            'songwriter' => [...$rule('string'), 'max:255'],
            'producer' => [...$rule('string'), 'max:255'],
            'copyright_status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES_COPYRIGHT)],
            'licensing_status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES_LICENSING)],
        ]);
    }
}
