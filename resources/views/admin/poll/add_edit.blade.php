@extends('layouts.admin')

@push('style')
    <style>
        .answer-item {
            position: relative;
        }

        .remove-answer {
            position: absolute;
            right: -35px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
@endpush

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
                        <div class="card-title fs-3 fw-bolder">{{ $title }}</div>
                    </div>
                    <div class="card-body border-top p-9">
                        <form method="POST"
                              action="{{ route('admin.poll.save', $poll->id ?? null) }}"
                              id="kt_poll_form">

                            @csrf
                            @if($poll->exists)
                                @method('PUT')
                            @endif

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Question</label>
                                <div class="col-lg-8">
                                    <input type="text" name="question" id="question"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('question', $poll->question ?? '') }}"
                                           placeholder="Enter poll question"/>
                                </div>
                            </div>

                            <!-- Answers -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Answers</label>
                                <div class="col-lg-8">
                                    <div id="answers-container">
                                        @php
                                            $answers = old('answers', $poll->answers ?? ['', '']);
                                            if (is_string($answers)) {
                                                $answers = json_decode($answers, true) ?? ['', ''];
                                            }
                                            if (count($answers) < 2) {
                                                $answers = array_merge($answers, array_fill(0, 2 - count($answers), ''));
                                            }
                                        @endphp

                                        @foreach($answers as $index => $answer)
                                            <div class="answer-item mb-3 position-relative">
                                                <input type="text" name="answers[]"
                                                       class="form-control form-control-lg form-control-solid"
                                                       value="{{ $answer }}"
                                                       placeholder="Enter answer option {{ $index + 1 }}"/>
                                                @if($index >= 2)
                                                    <button type="button"
                                                            class="btn btn-sm btn-icon btn-light-danger remove-answer">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" class="btn btn-sm btn-light-primary mt-2" id="add-answer">
                                        <i class="bi bi-plus-lg"></i> Add Another Answer
                                    </button>
                                    <div class="form-text">Minimum 2 answers required. You can add up to 10 answers.
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Status</label>
                                <div class="col-lg-8">
                                    <select name="status" id="status"
                                            class="form-select form-select-solid form-select-lg"
                                            data-control="select2" data-placeholder="Select status"
                                            data-hide-search="true">
                                        <option
                                            value="active" {{ old('status', $poll->status ?? 'active') === 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option
                                            value="inactive" {{ old('status', $poll->status ?? '') === 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Is Hidden -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Hidden</label>
                                <div class="col-lg-8">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="is_hidden" id="is_hidden"
                                               value="1" {{ old('is_hidden', $poll->is_hidden ?? false) ? 'checked' : '' }}/>
                                        <label class="form-check-label" for="is_hidden">
                                            Hide this poll from public view
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('admin.poll') }}"
                           class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="kt_poll_submit">
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

        const KTPollEdit = (() => {

            let form, submitBtn, validator;
            let answerCount = {{ count($answers) }};

            const init = () => {
                form = document.getElementById('kt_poll_form');
                submitBtn = document.getElementById('kt_poll_submit');

                if (!form) return;

                $('#status').select2({minimumResultsForSearch: Infinity});

                validator = FormValidation.formValidation(form, {
                    fields: {
                        question: {
                            validators: {
                                notEmpty: {
                                    message: 'Question is required'
                                },
                                stringLength: {
                                    min: 5,
                                    max: 500,
                                    message: 'Question must be between 5 and 500 characters'
                                }
                            }
                        },
                        status: {
                            validators: {
                                notEmpty: {
                                    message: 'Status is required'
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.row'
                        })
                    }
                });

                document.getElementById('add-answer').addEventListener('click', () => {
                    if (answerCount >= 10) {
                        Swal.fire({
                            text: "Maximum 10 answers allowed",
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                        return;
                    }

                    answerCount++;
                    const container = document.getElementById('answers-container');
                    const newAnswer = document.createElement('div');
                    newAnswer.className = 'answer-item mb-3 position-relative';
                    newAnswer.innerHTML = `
                        <input type="text" name="answers[]"
                               class="form-control form-control-lg form-control-solid"
                               placeholder="Enter answer option ${answerCount}"/>
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-answer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    `;
                    container.appendChild(newAnswer);
                });

                document.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-answer')) {
                        const answerItems = document.querySelectorAll('.answer-item');
                        if (answerItems.length > 2) {
                            e.target.closest('.answer-item').remove();
                            answerCount--;
                        } else {
                            Swal.fire({
                                text: "Minimum 2 answers required",
                                icon: "warning",
                                confirmButtonText: "OK"
                            });
                        }
                    }
                });

                submitBtn.addEventListener('click', e => {
                    e.preventDefault();

                    const answers = Array.from(document.querySelectorAll('input[name="answers[]"]'))
                        .map(input => input.value.trim())
                        .filter(val => val !== '');

                    if (answers.length < 2) {
                        Swal.fire({
                            text: "Please provide at least 2 answers",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                        return;
                    }

                    validator.validate().then(status => {
                        if (status !== 'Valid') return;

                        submitBtn.setAttribute('data-kt-indicator', 'on');
                        submitBtn.disabled = true;
                        form.submit();
                    });
                });
            };

            return {init};

        })();

        KTUtil.onDOMContentLoaded(() => {
            KTPollEdit.init();
        });
    </script>
@endpush
