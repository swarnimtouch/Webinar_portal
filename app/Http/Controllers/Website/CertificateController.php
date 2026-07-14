<?php

namespace App\Http\Controllers\Website;

use Intervention\Image\Laravel\Facades\Image;
use App\Models\Certificate;
use App\Models\CertificateLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            || !Storage::disk('public')->exists($certificate->background_image)
            || !Storage::disk('public')->exists($certificate->font_file)) {
            abort(404, 'Certificate assets not found');
        }

        $bgPath = Storage::disk('public')->path($certificate->background_image);
        $fontPath = Storage::disk('public')->path($certificate->font_file);

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

        $folderPath = storage_path('app/public/certificates/user-certificates');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $fileName = Str::slug($user->name) . '-certificate-' . time() . '.jpg';
        $fullPath = $folderPath . '/' . $fileName;

        $img->save($fullPath, quality: 95);


        CertificateLogs::create([
            'certificate_id' => $certificate->id,
            'user_id' => $user->id,
            'file_path' => 'certificates/user-certificates/' . $fileName,
        ]);


        return response()->download($fullPath);
    }

}
