@extends('layouts.master')

@section($title,'title')

@section('body')
    @include('partials.header')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->

    <div class="post d-flex flex-column-fluid" id="kt_post">

        <div id="kt_content_container" class="container-xxl">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Edit Content Card -->
            <div class="card mb-5 mb-xl-10">


                <div id="kt_content_wrapper">
                    <form method="POST"
                          action="{{ route('content.update', $content->id) }}"
                          id="kt_content_form">

                        @csrf
                        @method('PUT')

                        <div class="card-body border-top p-9">

                            <!-- Title -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Title</label>
                                <div class="col-lg-8">
                                    <input type="text"
                                           name="title"
                                           value="{{ old('title', $content->title) }}"
                                           class="form-control form-control-lg form-control-solid @error('title') is-invalid @enderror"
                                           placeholder="Enter title" />
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Slug -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Slug</label>
                                <div class="col-lg-8">
                                    <input type="text"
                                           name="slug"
                                           value="{{ old('slug', $content->slug) }}"
                                           class="form-control form-control-lg form-control-solid @error('slug') is-invalid @enderror"
                                           placeholder="Enter slug (e.g., about-us)" />
                                    <div class="form-text">URL-friendly version (lowercase, hyphens only)</div>
                                    @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Content</label>
                                <div class="col-lg-8">
                                    <textarea name="content"
                                              rows="10"
                                              class="form-control form-control-lg form-control-solid @error('content') is-invalid @enderror"
                                              placeholder="Enter content">{{ old('content', $content->content) }}</textarea>
                                    @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="{{ route('content') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="kt_content_submit">
                                <span class="indicator-label">Save</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
@endsection

@push('scripts')
    <script>
        "use strict";

        KTUtil.onDOMContentLoaded(function () {
            const form = document.querySelector('#kt_content_form');
            const submitBtn = document.querySelector('#kt_content_submit');

            const validator = FormValidation.formValidation(form, {
                fields: {
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'Title is required'
                            }
                        }
                    },
                    slug: {
                        validators: {
                            notEmpty: {
                                message: 'Slug is required'
                            },
                            regexp: {
                                regexp: /^[a-z0-9]+(?:-[a-z0-9]+)*$/,
                                message: 'Slug can only contain lowercase letters, numbers and hyphens'
                            }
                        }
                    },
                    content: {
                        validators: {
                            notEmpty: {
                                message: 'Content is required'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.row'
                    })
                }
            });

            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();

                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitBtn.setAttribute('data-kt-indicator', 'on');
                        submitBtn.disabled = true;
                        form.submit();
                    }
                });
            });

            // Auto-generate slug from title
            const titleInput = document.querySelector('input[name="title"]');
            const slugInput = document.querySelector('input[name="slug"]');

            titleInput.addEventListener('input', function() {
                if (!slugInput.dataset.manuallyEdited) {
                    const slug = this.value
                        .toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim();
                    slugInput.value = slug;
                }
            });

            slugInput.addEventListener('input', function() {
                this.dataset.manuallyEdited = 'true';
            });
        });
    </script>
@endpush
