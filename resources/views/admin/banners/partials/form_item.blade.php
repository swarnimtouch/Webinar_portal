@php
    $type = $item['type'] ?? 'image';
    $source = $item['video_source'] ?? 'upload';
@endphp
<div class="banner-item p-6 mb-6" data-index="{{ $index }}">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h4 class="fw-bolder mb-0">Banner <span class="item-number">{{ is_numeric($index) ? $index + 1 : '' }}</span></h4>
        @if(!$isEdit)<button type="button" class="btn btn-sm btn-icon btn-light-danger remove-banner" title="Remove"><i class="bi bi-trash"></i></button>@endif
    </div>
    <div class="row g-5">
        <div class="col-lg-8">
            <label class="form-label required fw-bold">Title</label>
            <input type="text" name="banners[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}" class="form-control form-control-solid" maxlength="255" required>
        </div>
        <div class="col-lg-4">
            <label class="form-label required fw-bold">Type</label>
            <select name="banners[{{ $index }}][type]" class="form-select form-select-solid banner-type" required>
                <option value="image" @selected($type === 'image')>Image</option><option value="video" @selected($type === 'video')>Video</option>
            </select>
        </div>
        <div class="col-12 image-fields {{ $type !== 'image' ? 'd-none' : '' }}">
            <label class="form-label fw-bold">Image @if(!$isEdit || $banner->type !== 'image')<span class="text-danger">*</span>@endif</label>
            <input type="file" name="banners[{{ $index }}][image_file]" class="form-control form-control-solid image-input-file" accept="image/jpeg,image/png,image/webp,image/gif">
            <div class="form-text">JPG, PNG, WebP or GIF. Maximum 5 MB.</div>
            @if($isEdit && $banner->type === 'image' && $banner->filename)<img src="{{ $banner->media_url }}" class="banner-preview image-preview mt-3" alt="Current banner">@else<img src="{{ asset('assets/media/no_image.png') }}" class="banner-preview image-preview mt-3" alt="Preview">@endif
        </div>
        <div class="col-12 video-fields {{ $type !== 'video' ? 'd-none' : '' }}">
            <label class="form-label required fw-bold d-block">Video source</label>
            <div class="d-flex gap-6">
                <label class="form-check form-check-custom form-check-solid"><input class="form-check-input video-source" type="radio" name="banners[{{ $index }}][video_source]" value="upload" @checked($source === 'upload')><span class="form-check-label">Upload video</span></label>
                <label class="form-check form-check-custom form-check-solid"><input class="form-check-input video-source" type="radio" name="banners[{{ $index }}][video_source]" value="url" @checked($source === 'url')><span class="form-check-label">Video URL</span></label>
            </div>
        </div>
        <div class="col-12 video-upload-fields {{ $type !== 'video' || $source !== 'upload' ? 'd-none' : '' }}">
            <label class="form-label fw-bold">Video file @if(!$isEdit || $banner->type !== 'video')<span class="text-danger">*</span>@endif</label>
            <input type="file" name="banners[{{ $index }}][video_file]" class="form-control form-control-solid video-input-file" accept="video/mp4,video/webm,video/quicktime">
            <div class="form-text">MP4, WebM or MOV. Maximum 20 MB.</div>
            @if($isEdit && $banner->type === 'video' && $banner->filename)<video src="{{ $banner->media_url }}" class="banner-preview video-preview mt-3" controls muted></video>@else<video class="banner-preview video-preview mt-3 d-none" controls muted></video>@endif
        </div>
        <div class="col-12 video-url-fields {{ $type !== 'video' || $source !== 'url' ? 'd-none' : '' }}">
            <label class="form-label required fw-bold">Direct video URL</label>
            <input type="url" name="banners[{{ $index }}][video_url]" value="{{ $item['video_url'] ?? '' }}" class="form-control form-control-solid" maxlength="2048" placeholder="https://example.com/video.mp4">
            <div class="form-text">Use a publicly accessible direct MP4, WebM or MOV URL. YouTube page URLs are not supported by the banner player.</div>
        </div>
    </div>
</div>
