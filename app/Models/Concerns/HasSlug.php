<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            $base = Str::slug((string) ($model->slug ?: $model->slugSource()));

            if ($base === '') {
                return;
            }

            $slug = $base;
            $suffix = 1;

            while (static::query()
                ->withoutGlobalScopes()
                ->where('slug', $slug)
                ->when($model->exists, fn (Builder $query) => $query->whereKeyNot($model->getKey()))
                ->exists()) {
                $slug = $base.'-'.(++$suffix);
            }

            $model->slug = $slug;
        });
    }

    abstract public function slugSource(): string;
}
