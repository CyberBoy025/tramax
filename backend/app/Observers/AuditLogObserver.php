<?php

namespace App\Observers;

use App\Models\AuditLogEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

// Logs administrative writes (discovery.md §2's AUDIT_LOG_ENTRY; README.md
// §4/§13 "audit logging on administrative actions"). Registered per-model in
// AppServiceProvider::boot() only for entities admins directly manage
// through /admin/* routes — nested rows (RoyaltyLineItem, Track) aren't
// observed separately; their parent's "updated" entry covers them.
//
// Gated on an authenticated user: public-site POSTs (applications,
// licensing-requests, partners, contact) create these same models with no
// authenticated user, so they're excluded automatically — this is a trail
// of admin actions, not a general changelog.
class AuditLogObserver
{
    private const HIDDEN = ['password', 'remember_token', 'created_at', 'updated_at'];

    public function created(Model $model): void
    {
        $this->log('created', $model, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);
        if ($changes === []) {
            return;
        }
        $this->log('updated', $model, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model, $model->getAttributes());
    }

    private function log(string $action, Model $model, array $attributes): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $metadata = array_diff_key($attributes, array_flip(self::HIDDEN));
        if (array_key_exists('password', $attributes)) {
            $metadata['password_changed'] = true;
        }

        AuditLogEntry::create([
            'user_id' => $user->id,
            'action' => $action,
            'entity_type' => class_basename($model),
            'entity_id' => $model->getKey(),
            'metadata' => $metadata,
        ]);
    }
}
