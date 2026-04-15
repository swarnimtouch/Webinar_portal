@extends('layouts.website')
@section('body')

    <main class="main-content">

        <section class="video-section">
            <div class="video-player">
                {!! $home_setting->url !!}
            </div>

            <div class="webinar-details">
                <p class="category">TECHNOLOGY & AI</p>
                <h1>{{ $home_setting->title}}</h1>
                <p class="description">
                    Join industry experts as we explore the cutting-edge applications of generative AI, from code
                    generation to automated testing and beyond.
                </p>

                <div class="mobile-chat-button-container">
                    <button class="mobile-chat-btn" id="mobileChatBtn">
                        <i class="fa-solid fa-comments"></i> Live Chat
                    </button>
                </div>

                <div class="webinar-info-container">

                    <div class="about-webinar">
                        <h3>About This Webinar</h3>
                        <p>{!!$home_setting->about_us!!}</p>
                    </div>
                    <div class="webinar-actions">
                        <div class="action-group-right">
                            @php
                                $file = siteSetting('resources');
                            @endphp

                            @if(!empty($file))
                                <a href="{{ asset('storage/site_settings/'.$file) }}"
                                   class="action-btn download"
                                   download
                                   target="_blank">
                                    <i class="fa-solid fa-download"></i> Download Resources
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="session-agenda">
                    <h3>Session Agenda</h3>
                    <div class="agenda-list">

                        <div class="agenda-item active">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time">
                                    <span>2:00 PM</span>
                                    <span>15 min</span>
                                </div>
                                <h4>Welcome & Introduction</h4>
                                <p>Overview of today's session and speaker introduction</p>
                            </div>
                            <div class="agenda-status">
                                <span class="status-badge live">Live Now</span>
                            </div>
                        </div>

                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time">
                                    <span>2:15 PM</span>
                                    <span>30 min</span>
                                </div>
                                <h4>AI Landscape 2024</h4>
                                <p>Current state of AI technology and market trends</p>
                            </div>
                            <div class="agenda-status">
                                <span class="status-badge">Upcoming</span>
                            </div>
                        </div>

                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time">
                                    <span>2:45 PM</span>
                                    <span>25 min</span>
                                </div>
                                <h4>Implementation Strategies</h4>
                                <p>Step-by-step guide to deploying AI solutions</p>
                            </div>
                            <div class="agenda-status">
                                <span class="status-badge">Upcoming</span>
                            </div>
                        </div>

                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time">
                                    <span>3:10 PM</span>
                                    <span>20 min</span>
                                </div>
                                <h4>Case Studies</h4>
                                <p>Real-world examples from leading organizations</p>
                            </div>
                            <div class="agenda-status">
                                <span class="status-badge">Upcoming</span>
                            </div>
                        </div>

                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time">
                                    <span>3:30 PM</span>
                                    <span>30 min</span>
                                </div>
                                <h4>Q&A Session</h4>
                                <p>Live questions and expert answers</p>
                            </div>
                            <div class="agenda-status">
                                <span class="status-badge">Upcoming</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="session-resources">
                    <h3>Session Resources</h3>
                    <div class="resources-grid">

                        <div class="resource-card">
                            <div class="card-header">
                                <div class="card-icon"><i class="fa-solid fa-file-pdf"></i></div>
                                <span class="card-meta">PDF • 2.4 MB</span>
                            </div>
                            <div class="card-body">
                                <h4>AI Strategy Framework 2025</h4>
                                <p>Complete guide to building your AI roadmap</p>
                            </div>
                            <div class="card-footer">
                                <a href="#" class="card-link">Download <i class="fa-solid fa-arrow-down"></i></a>
                            </div>
                        </div>

                        <div class="resource-card">
                            <div class="card-header">
                                <div class="card-icon"><i class="fa-solid fa-file-powerpoint"></i></div>
                                <span class="card-meta">PPTX • 8.7 MB</span>
                            </div>
                            <div class="card-body">
                                <h4>Presentation Slides</h4>
                                <p>Full deck from today's session</p>
                            </div>
                            <div class="card-footer">
                                <a href="#" class="card-link">Download <i class="fa-solid fa-arrow-down"></i></a>
                            </div>
                        </div>

                        <div class="resource-card">
                            <div class="card-header">
                                <div class="card-icon"><i class="fa-solid fa-file-zipper"></i></div>
                                <span class="card-meta">ZIP • 15.2 MB</span>
                            </div>
                            <div class="card-body">
                                <h4>Code Examples</h4>
                                <p>Sample implementations and templates</p>
                            </div>
                            <div class="card-footer">
                                <a href="#" class="card-link">Download <i class="fa-solid fa-arrow-down"></i></a>
                            </div>
                        </div>

                        <div class="resource-card">
                            <div class="card-header">
                                <div class="card-icon"><i class="fa-solid fa-link"></i></div>
                                <span class="card-meta">External</span>
                            </div>
                            <div class="card-body">
                                <h4>Additional Resources</h4>
                                <p>Curated list of articles and tools</p>
                            </div>
                            <div class="card-footer">
                                <a href="#" class="card-link">View Links <i
                                        class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        @include('partials.website.aside')
    </main>

    <button id="scrollToBottomBtn" title="Scroll to Bottom">
        <i class="fa-solid fa-arrow-down"></i>
    </button>

@endsection

@push('scripts')
    @if(session('toast_success'))
        <script>window._toastSuccess = "{{ session('toast_success') }}";</script>
    @endif
    <script>
        window.feedbackStoreUrl = "{{ route('feedback.save') }}";
        window.chatMessagesUrl = "{{ route('chat.messages') }}";
        window.chatSendUrl = "{{ route('chat.send') }}";
        window.pollUrl = "{{ route('poll') }}";
        window.pollVoteUrl = "{{ route('poll.vote') }}";
        window.attendanceUrl = "{{ route('dashboard.attendance.update') }}";
        window.csrfToken = "{{ csrf_token() }}";
        window.trackingEnabled = {{ (isset($home_setting) && $home_setting->user_attendance) ? 'true' : 'false' }};
    </script>
@endpush
