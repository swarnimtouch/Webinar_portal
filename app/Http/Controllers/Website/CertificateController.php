<?php

namespace App\Http\Controllers\Website;

use Intervention\Image\Laravel\Facades\Image;
use App\Models\Certificate;
use App\Models\CertificateLogs;
use App\Models\User;
use Illuminate\Support\Str;

class CertificateController
{
    public function generate($slug,$certificateId, $userId)
    {
        $certificate = Certificate::findOrFail($certificateId);
        $user = User::findOrFail($userId);

        $bgPath = storage_path('app/public/' . $certificate->background_image);
        $fontPath = storage_path('app/public/' . $certificate->font_file);

        if (!file_exists($bgPath) || !file_exists($fontPath)) {
            abort(404, 'File not found');
        }

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
