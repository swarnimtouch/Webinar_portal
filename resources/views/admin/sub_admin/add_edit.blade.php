@extends('layouts.admin')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
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

                <div class="card">
                    <div class="card-header">
                        <div class="card-title fs-3 fw-bolder">{{$title}}</div>
                    </div>
                    <div class="card-body border-top p-9">
                        <form method="POST"
                              action="{{ route('admin.sub_admin.save',[$sub_admin->id??null]) }}"
                              id="kt_sub_admin_form"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Event</label>
                                <div class="col-lg-8">
                                    <select id="event_id" name="event_id"
                                            class="form-control form-control-lg form-control-solid">
                                        <option value="">Select Event</option>
                                        @foreach($events as $key=>$event)
                                            <option
                                                value="{{$event->id}}" {{$event->id == $sub_admin->event_id?'selected':''}}>{{$event->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Name</label>
                                <div class="col-lg-8">
                                    <input type="text" name="name" id="name"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('name', $sub_admin->name ?? '') }}"
                                           placeholder="Enter name"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Email</label>
                                <div class="col-lg-8">
                                    <input type="email" name="email" id="email"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('email', $sub_admin->email ?? '') }}"
                                           placeholder="Enter email"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Mobile</label>
                                <div class="col-lg-8">
                                    <input type="text" name="mobile" id="mobile"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('mobile', $sub_admin->mobile ?? '') }}"
                                           placeholder="Enter mobile"/>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label {{!$sub_admin->id?'required':''}} fw-bold fs-6">Password</label>
                                <div class="col-lg-8">
                                    <input type="password" name="password" id="password"
                                           class="form-control form-control-lg form-control-solid"
                                           value=""
                                           placeholder="Enter password"/>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-bold fs-6 {{ !isset($sub_admin->id) ? 'required' : '' }}">
                                    Avatar
                                </label>
                                <div class="col-lg-8">
                                    @if($sub_admin->getRawOriginal('avatar') && $sub_admin->getRawOriginal('avatar') !== null)
                                        <div class="mb-3 d-flex align-items-center gap-3">
                                            <img id="avatar-current"
                                                 src="{{$sub_admin->avatar}}"
                                                 alt="Current Avatar"
                                                 class="rounded border"
                                                 style="max-width:160px;max-height:64px;object-fit:contain;background:#f5f8fa;padding:4px;">
                                            <span class="text-muted fs-7">Current Avatar</span>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center gap-4">
                                        <input type="file" name="avatar" id="avatar_input"
                                               accept=".jpg,.jpeg,.png,.webp"
                                               class="d-none">

                                        <div id="avatar-preview-box"
                                             class="border rounded d-flex align-items-center justify-content-center bg-light-primary"
                                             style="width:80px;height:80px;cursor:pointer;flex-shrink:0;"
                                             onclick="document.getElementById('avatar_input').click()">
                                            <img id="avatar-preview-img"
                                                 src="#" alt=""
                                                 class="rounded d-none"
                                                 style="width:72px;height:72px;object-fit:contain;">
                                            <span id="avatar-preview-placeholder" class="text-center text-muted p-1">
                                                <i class="bi bi-image fs-2x d-block mb-1"></i>
                                                <small>Click to upload</small>
                                            </span>
                                        </div>

                                        <div>
                                            <button type="button"
                                                    class="btn btn-sm btn-light-primary mb-2"
                                                    onclick="document.getElementById('avatar_input').click()">
                                                <i class="bi bi-upload me-1"></i>
                                                {{ isset($sub_admin->avatar) && $sub_admin->getRawOriginal('avatar') !== null ? 'Change Avatar' : 'Upload Avatar' }}
                                            </button>
                                            <div class="text-muted fs-7">Accepted: JPG, PNG, WEBP &bull; Max 5MB</div>
                                            <div id="avatar-filename" class="text-primary fs-7 mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{route('admin.sub_admin')}}" class="btn btn-light btn-active-light-primary me-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="kt_sub_admin_submit">
                            <span class="indicator-label">Save</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        "use strict";

        function setupImageUpload(inputId, previewImgId, placeholderId, filenameId) {
            const input = document.getElementById(inputId);
            const previewImg = document.getElementById(previewImgId);
            const placeholder = document.getElementById(placeholderId);
            const filenameEl = document.getElementById(filenameId);

            if (!input) return;

            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({icon: 'warning', title: 'File too large', text: 'Maximum file size is 5 MB.'});
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                    filenameEl.textContent = '✓ ' + file.name;
                };
                reader.readAsDataURL(file);
            });
        }

        KTUtil.onDOMContentLoaded(function () {
            setupImageUpload('avatar_input', 'avatar-preview-img', 'avatar-preview-placeholder', 'avatar-filename');

            const form = document.getElementById('kt_sub_admin_form');
            const submitBtn = document.getElementById('kt_sub_admin_submit');
            const isEdit = {{ isset($sub_admin->id) ? 'true' : 'false' }};

            const validator = FormValidation.formValidation(form, {
                fields: {
                    event_id: {
                        validators: {
                            notEmpty: {message: 'Event is required'}
                        }
                    },
                    name: {
                        validators: {
                            notEmpty: {message: 'Name is required'}
                        }
                    },
                    email: {
                        validators: {
                            notEmpty: {
                                message: "Email is required"
                            },
                            emailAddress: {
                                message: "The value is not a valid email address"
                            },
                            remote: {
                                url: "{{ route('admin.check-email-exists') }}",
                                method: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                data: () => ({
                                    email: form.querySelector('[name="email"]').value,
                                    id: {{ $sub_admin->id ?? 0 }}
                                }),
                                message: "Email already exists"
                            }
                        }
                    },

                    mobile: {
                        validators: {
                            notEmpty: {message: "Mobile is required"},
                            remote: {
                                url: "{{ route('admin.check-mobile-exists') }}",
                                method: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                data: () => ({
                                    mobile: form.querySelector('[name="mobile"]').value,
                                    id: {{ $sub_admin->id  ?? 0 }}
                                }),
                                message: "Mobile already exists"
                            }
                        }
                    },
                    password: {
                        validators: {
                            callback: {
                                message: "Password is required",
                                callback: () => (isEdit || $("#password").val() !== '')
                            }
                        }
                    },
                    avatar: {
                        validators: {
                            callback: {
                                message: 'Avatar is required',
                                callback: () => isEdit || document.getElementById('avatar_input').files.length > 0
                            },
                            file: {
                                extension: 'jpg,jpeg,png,webp',
                                type: 'image/jpeg,image/png,image/webp',
                                maxSize: 5 * 1024 * 1024,
                                message: 'Invalid file. Use JPG/PNG/WEBP up to 5 MB'
                            }
                        }
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({rowSelector: '.row'})
                }
            });


            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                validator.validate().then(function (status) {
                    if (status !== 'Valid') return;
                    submitBtn.setAttribute('data-kt-indicator', 'on');
                    submitBtn.disabled = true;
                    form.submit();
                });
            });

        });
    </script>
@endpush
