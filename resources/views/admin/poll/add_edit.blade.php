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
                            @if(auth()->user()->type === 'admin')
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Event</label>
                                    <div class="col-lg-8">
                                        <select name="event_id" id="event_id"
                                                class="form-select form-select-solid form-select-lg"
                                                data-control="select2" data-placeholder="Select a Event "
                                                data-hide-search="true">
                                            <option value="Select Event " disabled>
                                                Select Event
                                            </option>
                                            <option value="" disabled selected>Select Event</option>

                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}"
                                                    {{ old('event_id', $poll->event_id ?? '') == $event->id ? 'selected' : '' }}>
                                                    {{ $event->name }}
                                                </option>
                                            @endforeach


                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="event_id" value="{{ auth()->user()->event_id }}">
                            @endif
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Interaction Type</label>
                                <div class="col-lg-8">
                                    <select name="interaction_type" id="interaction_type" class="form-select form-select-solid form-select-lg">
                                        <option value="single_choice" @selected(old('interaction_type', $poll->interaction_type ?? 'single_choice') === 'single_choice')>Single Choice</option>
                                        <option value="multiple_choice" @selected(old('interaction_type', $poll->interaction_type ?? '') === 'multiple_choice')>Multiple Choice</option>
                                        <option value="text" @selected(old('interaction_type', $poll->interaction_type ?? '') === 'text')>Typing / Text Response</option>
                                        <option value="rating" @selected(old('interaction_type', $poll->interaction_type ?? '') === 'rating')>Rating</option>
                                    </select>
                                </div>
                            </div>
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
                            <div class="row mb-6" id="multiple-choice-settings">
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

                            <div class="row mb-6" id="rating-settings" style="display:none">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Rating Scale</label>
                                <div class="col-lg-8">
                                    <select name="rating_max" id="rating_max" class="form-select form-select-solid form-select-lg">
                                        @foreach([3, 5, 7, 10] as $scale)
                                            <option value="{{ $scale }}" @selected((int) old('rating_max', $poll->rating_max ?? 5) === $scale)>1 to {{ $scale }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Users will select one rating from this scale.</div>
                                </div>
                            </div>

                            <!-- Status -->


                            <!-- Is Hidden -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Hidden</label>
                                <div class="col-lg-8">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="is_hidden"
                                               id="is_hidden"
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
            const isAdmin = {{ auth()->user()->type === 'admin' ? 'true' : 'false' }};

            const init = () => {
                form = document.getElementById('kt_poll_form');
                submitBtn = document.getElementById('kt_poll_submit');

                if (!form) return;

                const interactionType = document.getElementById('interaction_type');
                const toggleTypeSettings = () => {
                    const hasAnswerOptions = ['single_choice', 'multiple_choice'].includes(interactionType.value);
                    document.getElementById('multiple-choice-settings').style.display = hasAnswerOptions ? '' : 'none';
                    document.getElementById('rating-settings').style.display = interactionType.value === 'rating' ? '' : 'none';
                };
                interactionType.addEventListener('change', toggleTypeSettings);
                toggleTypeSettings();

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
                        ...(isAdmin && {
                            event_id: {
                                validators: {
                                    notEmpty: {message: 'Event is required'}
                                }
                            }
                        }),
                       
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.row'
                        })
                    }
                });
                if (isAdmin && $('#event_id').length) {
                    $('#event_id').select2();

                    $('#event_id').on('change', function () {
                        validator.revalidateField('event_id');
                    });
                }
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

                    if (['single_choice', 'multiple_choice'].includes(interactionType.value) && answers.length < 2) {
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
