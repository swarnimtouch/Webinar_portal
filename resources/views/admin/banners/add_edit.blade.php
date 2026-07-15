@extends('layouts.admin')

@push('styles')
<style>
    .banner-item { border: 1px solid #e4e6ef; border-radius: .75rem; }
    .banner-preview { width: 150px; height: 100px; border-radius: .5rem; object-fit: cover; background: #f5f8fa; }
</style>
@endpush

@section('content')
@php
    $isEdit = $banner->exists;
    $initialBanners = old('banners', [[
        'title' => $banner->title ?? '',
        'type' => $banner->type ?? 'image',
        'video_source' => !empty($banner->video_url) ? 'url' : 'upload',
        'video_url' => $banner->video_url ?? '',
    ]]);
@endphp
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ route('admin.banners.save', $banner->id ?? null) }}" enctype="multipart/form-data" id="bannerForm">
                @csrf
                @if($isEdit) @method('PUT') @endif
                <div class="card">
                    <div class="card-header align-items-center">
                        <div><h3 class="card-title fw-bolder mb-1">{{ $isEdit ? 'Edit Banner' : 'Add Banners' }}</h3>@if(!$isEdit)<div class="text-muted fs-7">Add several image or video banners in one submission.</div>@endif</div>
                        @if(!$isEdit)<button type="button" class="btn btn-sm btn-light-primary" id="addBanner"><i class="bi bi-plus-lg"></i> Add another banner</button>@endif
                    </div>
                    <div class="card-body p-9">
                        <div class="row mb-8">
                            <label class="col-lg-3 col-form-label required fw-bold">Event</label>
                            <div class="col-lg-9">
                                @if(auth()->user()->type === 'admin')
                                    <select name="event_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Select event" required>
                                        <option value=""></option>
                                        @foreach($events as $event)<option value="{{ $event->id }}" @selected(old('event_id', $banner->event_id) == $event->id)>{{ $event->name }}</option>@endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="event_id" value="{{ auth()->user()->event_id }}">
                                    <input class="form-control form-control-solid" value="{{ auth()->user()->event?->name }}" disabled>
                                @endif
                            </div>
                        </div>

                        <div id="bannerItems">
                            @foreach($initialBanners as $index => $item)
                                @include('admin.banners.partials.form_item', ['index' => $index, 'item' => $item, 'isEdit' => $isEdit, 'banner' => $banner])
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('admin.banners') }}" class="btn btn-light me-3">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="saveBanners"><span class="indicator-label">{{ $isEdit ? 'Update Banner' : 'Save Banners' }}</span><span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm ms-2"></span></span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!$isEdit)
<template id="bannerItemTemplate">
    @include('admin.banners.partials.form_item', ['index' => '__INDEX__', 'item' => ['title' => '', 'type' => 'image', 'video_source' => 'upload', 'video_url' => ''], 'isEdit' => false, 'banner' => new \App\Models\Banner()])
</template>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const container = document.getElementById('bannerItems');
    const syncItem = item => {
        const type = item.querySelector('.banner-type').value;
        const source = item.querySelector('.video-source:checked')?.value || 'upload';
        item.querySelector('.image-fields').classList.toggle('d-none', type !== 'image');
        item.querySelector('.video-fields').classList.toggle('d-none', type !== 'video');
        item.querySelector('.video-upload-fields').classList.toggle('d-none', type !== 'video' || source !== 'upload');
        item.querySelector('.video-url-fields').classList.toggle('d-none', type !== 'video' || source !== 'url');
    };
    const renumber = () => container.querySelectorAll('.banner-item').forEach((item, index) => {
        item.querySelector('.item-number').textContent = index + 1;
        item.querySelectorAll('[name]').forEach(field => field.name = field.name.replace(/banners\[[^\]]+\]/, `banners[${index}]`));
    });
    const bind = item => {
        item.querySelector('.banner-type').addEventListener('change', () => syncItem(item));
        item.querySelectorAll('.video-source').forEach(radio => radio.addEventListener('change', () => syncItem(item)));
        item.querySelector('.remove-banner')?.addEventListener('click', () => { item.remove(); renumber(); });
        item.querySelector('.image-input-file')?.addEventListener('change', event => {
            const file = event.target.files[0], preview = item.querySelector('.image-preview');
            if (file && preview) preview.src = URL.createObjectURL(file);
        });
        item.querySelector('.video-input-file')?.addEventListener('change', event => {
            const file = event.target.files[0], preview = item.querySelector('.video-preview');
            if (file && preview) { preview.src = URL.createObjectURL(file); preview.classList.remove('d-none'); }
        });
        syncItem(item);
    };
    container.querySelectorAll('.banner-item').forEach(bind);
    document.getElementById('addBanner')?.addEventListener('click', () => {
        const template = document.getElementById('bannerItemTemplate');
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', container.children.length);
        const item = wrapper.firstElementChild; container.appendChild(item); bind(item); renumber();
    });
    document.getElementById('bannerForm').addEventListener('submit', () => {
        const button = document.getElementById('saveBanners'); button.setAttribute('data-kt-indicator', 'on'); button.disabled = true;
    });
})();
</script>
@endpush
