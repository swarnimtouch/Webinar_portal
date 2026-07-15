<?php

namespace App\Support;

use App\Models\Events;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventStorage
{
    public static function disk(): string
    {
        return config('filesystems.event_assets_disk', 's3');
    }

    public static function path(Events $event, string $folder, string $filename = ''): string
    {
        $eventFolder = Str::slug($event->name);
        $path = trim(config('filesystems.event_assets_root', 'webinar_portal'), '/')
            . '/' . $eventFolder . '/' . trim($folder, '/');

        return $filename === '' ? $path : $path . '/' . ltrim($filename, '/');
    }

    public static function store(UploadedFile $file, Events $event, string $folder, ?string $filename = null): string
    {
        $filename ??= Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
        $path = self::path($event, $folder, $filename);
        Storage::disk(self::disk())->putFileAs(dirname($path), $file, basename($path), [
            'visibility' => config('filesystems.event_assets_visibility', 'public'),
        ]);

        return $path;
    }

    public static function put(string $path, string $contents): void
    {
        $stored = Storage::disk(self::disk())->put($path, $contents, [
            'visibility' => config('filesystems.event_assets_visibility', 'public'),
        ]);

        if (!$stored) {
            throw new \RuntimeException("Unable to store file on S3: {$path}");
        }
    }

    public static function url(?string $path, ?string $legacyPath = null): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'webinar_portal/')) {
            return Storage::disk(self::disk())->url($path);
        }

        return asset('storage/' . ltrim($legacyPath ?? $path, '/'));
    }

    public static function delete(?string $path, ?string $legacyPath = null): void
    {
        if (!$path) {
            return;
        }

        if (str_starts_with($path, 'webinar_portal/')) {
            Storage::disk(self::disk())->delete($path);
            return;
        }

        Storage::disk('public')->delete($legacyPath ?? $path);
    }

    public static function exists(string $path, ?string $legacyPath = null): bool
    {
        return str_starts_with($path, 'webinar_portal/')
            ? Storage::disk(self::disk())->exists($path)
            : Storage::disk('public')->exists($legacyPath ?? $path);
    }

    public static function contents(string $path, ?string $legacyPath = null): string
    {
        return str_starts_with($path, 'webinar_portal/')
            ? Storage::disk(self::disk())->get($path)
            : Storage::disk('public')->get($legacyPath ?? $path);
    }

    public static function downloadUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (!str_starts_with($path, 'webinar_portal/')) {
            return asset('storage/' . ltrim($path, '/'));
        }

        if (config('filesystems.event_assets_visibility', 'public') === 'private') {
            return Storage::disk(self::disk())->temporaryUrl($path, now()->addMinutes(15));
        }

        return Storage::disk(self::disk())->url($path);
    }
}
