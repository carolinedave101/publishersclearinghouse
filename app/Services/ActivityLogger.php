<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    public function log(string $action, string $collection, ?string $documentId = null, ?int $userId = null, ?array $changes = null, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'action' => $action,
            'collection' => $collection,
            'document_id' => $documentId,
            'user_id' => $userId,
            'changes' => $changes,
            'description' => $description,
        ]);
    }
}
