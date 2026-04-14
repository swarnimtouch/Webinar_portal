@extends('layouts.website')
@push('styles')

@endpush

@section('body')
    <body>

    <div class="webinar-app">
        @include('partials.website.dashboardheader')

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

                    {{--                    <div class="presenter-info">--}}
                    {{--                        <img src="{{asset('assets/website/images/user.png')}}" alt="Alex Morgan" class="presenter-avatar">--}}
                    {{--                        <div class="presenter-details">--}}
                    {{--                            <span class="name">Alex Morgan</span>--}}
                    {{--                            <span class="title">Principal AI Engineer @ TechCorp</span>--}}
                    {{--                        </div>--}}
                    {{--                        <button class="follow-btn">--}}
                    {{--                            <i class="fa-solid fa-user-plus"></i> Follow--}}
                    {{--                        </button>--}}
                    {{--                    </div>--}}

                    <div class="webinar-info-container">

                        <div class="about-webinar">
                            <h3>About This Webinar</h3>
                            <p>{!!$home_setting->about_us!!}</p>
                        </div>

                        {{--                        <div class="key-topics">--}}
                        {{--                            <h3>Key Topics Covered</h3>--}}
                        {{--                            <div class="key-topics-grid">--}}
                        {{--                                <div class="topic-item">--}}
                        {{--                                    <div class="topic-icon"><i class="fa-solid fa-brain"></i></div>--}}
                        {{--                                    <div class="topic-text">--}}
                        {{--                                        <h4>Machine Learning Fundamentals</h4>--}}
                        {{--                                        <p>Core concepts and applications</p>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                                <div class="topic-item">--}}
                        {{--                                    <div class="topic-icon"><i class="fa-solid fa-shield-halved"></i></div>--}}
                        {{--                                    <div class="topic-text">--}}
                        {{--                                        <h4>AI Ethics & Governance</h4>--}}
                        {{--                                        <p>Responsible AI implementation</p>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                                <div class="topic-item">--}}
                        {{--                                    <div class="topic-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>--}}
                        {{--                                    <div class="topic-text">--}}
                        {{--                                        <h4>ROI Optimization</h4>--}}
                        {{--                                        <p>Measuring AI impact</p>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                                <div class="topic-item">--}}
                        {{--                                    <div class="topic-icon"><i class="fa-solid fa-rocket"></i></div>--}}
                        {{--                                    <div class="topic-text">--}}
                        {{--                                        <h4>Future Trends</h4>--}}
                        {{--                                        <p>What's coming in 2025</p>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}

                        <div class="webinar-actions">
                            {{--                            <div class="action-group-left">--}}
                            {{--                                <button class="action-btn"><i class="fa-solid fa-thumbs-up"></i> 1,234</button>--}}
                            {{--                                <button class="action-btn"><i class="fa-solid fa-comment"></i> 456</button>--}}
                            {{--                                <button class="action-btn"><i class="fa-solid fa-share"></i> Share</button>--}}
                            {{--                            </div>--}}
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
                                {{--                                <button class="action-btn save"><i class="fa-solid fa-bookmark"></i> Save</button>--}}
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
    </div>

    <button id="scrollToBottomBtn" title="Scroll to Bottom">
        <i class="fa-solid fa-arrow-down"></i>
    </button>

    @endsection

    @push('scripts')

        <script>
            let attendanceInterval;

            function startAttendanceTracking() {
                updateSessionTime();

                attendanceInterval = setInterval(function () {
                    updateSessionTime();
                }, 30000); 
            }

            function updateSessionTime() {
                fetch('{{ route("dashboard.attendance.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Session time updated:', data.session_time, 'seconds');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating session time:', error);
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                @if(isset($home_setting) && $home_setting->user_attendance)
                startAttendanceTracking();
                @endif
            });

            window.addEventListener('beforeunload', function () {
                if (attendanceInterval) {
                    clearInterval(attendanceInterval);
                    navigator.sendBeacon('{{ route("dashboard.attendance.update") }}', JSON.stringify({}));
                }
            });

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    if (attendanceInterval) {
                        clearInterval(attendanceInterval);
                    }
                } else {
                    @if(isset($home_setting) && $home_setting->user_attendance)
                    startAttendanceTracking();
                    @endif
                }
            });
        </script>
    @endpush
