<?php

namespace App\Models;

use Database\Factories\CarPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CarPhoto extends Model
{
    /** @use HasFactory<CarPhotoFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'car_id',
        'path',
        'sort_order',
        'is_primary',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Resolve the public URL for this photo.
     */
    public function url(): ?string
    {
        if (blank($this->path)) {
            return null;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($this->path)) {
            // Gunakan host dan port request aktif, bukan APP_URL yang bisa berbeda
            // saat aplikasi dijalankan melalui `php artisan serve --port=8000`.
            return $this->storageUrl($this->path);
        }

        return asset('assets/images/'.$this->path);
    }

    /**
     * Resolve the public URL for this photo's thumbnail.
     */
    public function thumbnailUrl(): ?string
    {
        if (blank($this->path)) {
            return null;
        }

        $thumbnailPath = trim(dirname($this->path), '.').'/thumbs/'.basename($this->path);

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return $this->storageUrl($thumbnailPath);
        }

        return $this->url();
    }

    private function storageUrl(string $path): string
    {
        $baseUrl = app()->runningInConsole()
            ? rtrim((string) config('app.url'), '/')
            : rtrim(request()->getSchemeAndHttpHost(), '/');

        return $baseUrl.'/storage/'.ltrim($path, '/');
    }
}
