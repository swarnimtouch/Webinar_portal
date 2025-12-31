@extends('layouts.master')

@section('title', 'View Banner')

@section('body')
    @include('partials.header')

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">

            <!-- Banner Details Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bolder m-0">Banner Details</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('banners') }}" class="btn btn-sm btn-light me-2">
                            Back to List
                        </a>
                        <a href="{{ route('banner.edit', $banner->id) }}" class="btn btn-sm btn-primary">
                            Edit Banner
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted">Title</label>
                        <div class="col-lg-8">
                            <span class="fw-bolder fs-6 text-gray-800">{{ $banner->title }}</span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted">Type</label>
                        <div class="col-lg-8">
                            @if($banner->type === 'image')
                                <span class="badge badge-light-primary">Image</span>
                            @else
                                <span class="badge badge-light-info">Video</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted">Status</label>
                        <div class="col-lg-8">
                            @if($banner->status === 'active')
                                <span class="badge badge-light-success">Active</span>
                            @else
                                <span class="badge badge-light-danger">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted">Created At</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $banner->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted">Last Updated</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $banner->updated_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted">Banner Preview</label>
                        <div class="col-lg-8">
                            <div class="border rounded p-3" style="background-color: #f5f8fa;">
                                @if($banner->filename)
                                    @if($banner->type === 'image')
                                        <img src="{{ asset('storage/banners/'.$banner->filename) }}"
                                             class="img-fluid rounded"
                                             style="max-height: 400px; width: auto;"
                                             alt="{{ $banner->title }}">
                                    @else
                                        <video controls style="max-height: 400px; width: auto;">
                                            <source src="{{ asset('storage/banners/'.$banner->filename) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">No file available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('banners') }}" class="btn btn-light me-2">Back</a>
                    <a href="{{ route('banner.edit', $banner->id) }}" class="btn btn-primary">Edit Banner</a>
                </div>
            </div>

        </div>
    </div>

    @include('partials.footer')
@endsection
