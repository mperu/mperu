<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectUpdate;
use App\Models\ProjectComment;

class BackfillProjectUpdatesMeta extends Command
{
    protected $signature = 'projects:backfill-updates-meta {--dry-run : Non salva, mostra solo cosa farebbe}';
    protected $description = 'Backfill meta.by/admin_name/client_name negli updates vecchi per evitare badge SYSTEM in timeline';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $updates = ProjectUpdate::query()
            ->where(function ($q) {
                $q->whereNull('meta')
                  ->orWhereRaw("JSON_EXTRACT(meta, '$.by') IS NULL");
            })
            ->orderBy('id')
            ->get();

        if ($updates->isEmpty()) {
            $this->info('✅ Nessun update da sistemare.');
            return self::SUCCESS;
        }

        $this->info("Trovati {$updates->count()} updates da backfill. Dry-run: " . ($dry ? 'SI' : 'NO'));

        $fixed = 0;

        foreach ($updates as $u) {
            $meta = $u->meta ?? [];
            $type = $u->type;

            // già sistemato?
            if (data_get($meta, 'by')) {
                continue;
            }

            if ($type === 'comment_added') {
                $commentId = data_get($meta, 'comment_id');

                if ($commentId) {
                    $comment = ProjectComment::query()
                        ->with('user:id,name')
                        ->find($commentId);

                    if ($comment) {
                        if ($comment->is_admin) {
                            $meta['by'] = 'admin';
                            $meta['admin_id'] = $comment->user_id;
                            $meta['admin_name'] = $comment->user?->name;
                        } else {
                            $meta['by'] = 'client';
                            $meta['client_id'] = $comment->user_id;
                            $meta['client_name'] = $comment->user?->name;
                        }
                    } else {
                        $meta['by'] = 'system';
                    }
                } else {
                    $meta['by'] = 'system';
                }
            }

            if ($type === 'admin_notes_updated') {
                // non possiamo sapere chi in modo affidabile se non c'è admin_id:
                $meta['by'] = $meta['admin_id'] ? 'admin' : 'system';
            }

            if (!data_get($meta, 'by')) {
                $meta['by'] = 'system';
            }

            $this->line("Update #{$u->id} type={$type} -> by=" . data_get($meta, 'by'));

            if (!$dry) {
                $u->meta = $meta;
                $u->save();
            }

            $fixed++;
        }

        $this->info("✅ Backfill completato. Updates processati: {$fixed}");

        return self::SUCCESS;
    }
}