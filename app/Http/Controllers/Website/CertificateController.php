<?php

namespace App\Http\Controllers\Website;

use Intervention\Image\Laravel\Facades\Image;
use App\Models\Certificate;
use App\Models\CertificateLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\EventStorage;

class CertificateController
{
    public function generate(Request $request)
    {
        $event = app('event');
        $certificateId = $request->route('certificateId');
        $user = Auth::guard('web')->user();
        abort_unless($user, 403);

        $certificate = Certificate::whereKey($certificateId)
            ->where('event_id', $event->id)
            ->where('status', 'active')
            ->firstOrFail();

        if (!$certificate->background_image || !$certificate->font_file
            || !EventStorage::exists($certificate->background_image)
            || !EventStorage::exists($certificate->font_file)) {
            abort(404, 'Certificate assets not found');
        }

        $bgPath = tempnam(sys_get_temp_dir(), 'certificate-bg-');
        $fontPath = tempnam(sys_get_temp_dir(), 'certificate-font-');
        file_put_contents($bgPath, EventStorage::contents($certificate->background_image));
        file_put_contents($fontPath, EventStorage::contents($certificate->font_file));

        $img = Image::read($bgPath);


        $text = $user->name;
        $fontSize = (int)$certificate->font_size;

        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = $bbox[2] - $bbox[0];

        $startX = (int)$certificate->start_x;
        $endX = (int)$certificate->end_x;

        $x = $startX + (($endX - $startX - $textWidth) / 2);
        $y = (int)$certificate->y;

        $img->text($text, (int)$x, $y, function ($font) use ($fontSize, $fontPath, $certificate) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->color($certificate->font_color);
        });


        $fileName = Str::slug($user->name) . '-certificate-' . time() . '.jpg';
        $jpeg = $img->toJpeg(quality: 95)->toString();

        $storedPath = EventStorage::path($event, 'certificates/downloaded', $fileName);
        EventStorage::put($storedPath, $jpeg);

        if (!EventStorage::exists($storedPath)) {
            throw new \RuntimeException('Generated certificate was not found on S3 after upload.');
        }

        @unlink($bgPath);
        @unlink($fontPath);


        CertificateLogs::create([
            'certificate_id' => $certificate->id,
            'user_id' => $user->id,
            'file_path' => $storedPath,
        ]);


        return response($jpeg, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

}
