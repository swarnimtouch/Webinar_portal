@extends('layouts.website')
@section('body')

    <main class="main-content">

        <section class="video-section">
            <div class="video-player">
                {!! app('event')->player_iframe !!}
            </div>

            <div class="webinar-details">
                <p class="category">TECHNOLOGY & AI</p>
                <h1>{{ app('event')->name }}</h1>
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
                        <p>{!! app('event')->description !!}</p>
                    </div>
                    <div class="webinar-actions">
                        <div class="action-group-right">
                            @if(!empty($file))
                                <a href="{{ asset('storage/site_settings/'.$file) }}"
                                   class="action-btn download" download target="_blank">
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
                                <div class="agenda-time"><span>2:00 PM</span><span>15 min</span></div>
                                <h4>Welcome & Introduction</h4>
                                <p>Overview of today's session and speaker introduction</p>
                            </div>
                            <div class="agenda-status"><span class="status-badge live">Live Now</span></div>
                        </div>
                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time"><span>2:15 PM</span><span>30 min</span></div>
                                <h4>AI Landscape 2024</h4>
                                <p>Current state of AI technology and market trends</p>
                            </div>
                            <div class="agenda-status"><span class="status-badge">Upcoming</span></div>
                        </div>
                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time"><span>2:45 PM</span><span>25 min</span></div>
                                <h4>Implementation Strategies</h4>
                                <p>Step-by-step guide to deploying AI solutions</p>
                            </div>
                            <div class="agenda-status"><span class="status-badge">Upcoming</span></div>
                        </div>
                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time"><span>3:10 PM</span><span>20 min</span></div>
                                <h4>Case Studies</h4>
                                <p>Real-world examples from leading organizations</p>
                            </div>
                            <div class="agenda-status"><span class="status-badge">Upcoming</span></div>
                        </div>
                        <div class="agenda-item">
                            <div class="agenda-timeline"></div>
                            <div class="agenda-details">
                                <div class="agenda-time"><span>3:30 PM</span><span>30 min</span></div>
                                <h4>Q&A Session</h4>
                                <p>Live questions and expert answers</p>
                            </div>
                            <div class="agenda-status"><span class="status-badge">Upcoming</span></div>
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
    {{-- Reverb CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script>
        window.reverbKey = "{{ config('broadcasting.connections.reverb.key') }}";
        window.reverbHost = "{{ config('broadcasting.connections.reverb.options.host', request()->getHost()) }}";
        window.reverbPort = {{ config('broadcasting.connections.reverb.options.port', 8080) }};
        window.reverbScheme = "{{ config('broadcasting.connections.reverb.options.scheme', 'http') }}";
        window.currentUser = {
            id: {{ auth()->guard('web')->id() ?? 'null' }},
            name: "{{ auth()->guard('web')->user()->name ?? 'Guest' }}"
        };
        window.csrfToken = "{{ csrf_token() }}";
        window.eventSlug = "{{ request()->route('slug') }}";
        window.trackingEnabled = {{ $is_log_attendance }};
        window.chatMessagesUrl = "{{ route('chat.messages',             ['slug' => request()->route('slug')]) }}";
        window.chatSendUrl = "{{ route('chat.send',                 ['slug' => request()->route('slug')]) }}";
        window.raiseHandUrl = "{{ route('raise.hand',               ['slug' => request()->route('slug')]) }}";
        window.handStatusUrl = "{{ route('hand.status',              ['slug' => request()->route('slug')]) }}";
        window.pollUrl = "{{ route('poll',                      ['slug' => request()->route('slug')]) }}";
        window.pollVoteUrl = "{{ route('poll.vote',                 ['slug' => request()->route('slug')]) }}";
        window.feedbackStoreUrl = "{{ route('feedback.save',             ['slug' => request()->route('slug')]) }}";
        window.attendanceJoinUrl = "{{ route('attendance.join',           ['slug' => request()->route('slug')]) }}";
        window.attendanceLeaveUrl = "{{ route('attendance.leave',          ['slug' => request()->route('slug')]) }}";

        $(function () {

            function csrf() {
                return {'X-CSRF-TOKEN': window.csrfToken, 'Content-Type': 'application/json'};
            }

            function escHtml(str) {
                return $('<div>').text(str ?? '').html();
            }

            function avatarInitials(name) {
                return (name || 'U').trim().split(' ')
                    .map(w => w[0]).slice(0, 2).join('').toUpperCase();
            }

            const slug = window.eventSlug;

            const echo = new Echo({
                broadcaster: 'reverb',
                key: window.reverbKey,
                wsHost: window.reverbHost,
                wsPort: window.reverbPort,
                wssPort: window.reverbPort,
                forceTLS: window.reverbScheme === 'https',
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json',
                        'X-Event-Slug': slug,
                    }
                }
            });

            if (window.trackingEnabled) {

                function attendanceBeacon(url) {
                    const payload = new Blob(
                        [JSON.stringify({ _token: window.csrfToken })],
                        { type: 'application/json' }
                    );
                    navigator.sendBeacon(url, payload);
                }

                function attendanceJoin() {
                    $.ajax({
                        url:     window.attendanceJoinUrl,
                        method:  'POST',
                        headers: csrf(),
                        data:    JSON.stringify({}),
                    });
                }

                function attendanceLeave() {
                    attendanceBeacon(window.attendanceLeaveUrl);
                }

                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        attendanceLeave();
                    } else {
                        attendanceJoin();
                    }
                });

                window.addEventListener('beforeunload', function () {
                    attendanceLeave();
                });
            }

            const onlineUsers = {};
            let myHandRaised = false;

            const presenceChannel = echo.join(`webinar.${slug}.presence`);

            presenceChannel

                .here(function (users) {
                    users.forEach(u => { onlineUsers[u.id] = u; });
                    renderParticipants();

                    if (window.trackingEnabled) attendanceJoin();
                })

                .joining(function (user) {
                    onlineUsers[user.id] = user;
                    renderParticipants();
                    showJoinToast(user.name);
                })

                .leaving(function (user) {
                    const $item = $(`#participant-${user.id}`);
                    if ($item.length) {
                        $item.addClass('leaving');
                        setTimeout(function () {
                            delete onlineUsers[user.id];
                            renderParticipants();
                        }, 300);
                    } else {
                        delete onlineUsers[user.id];
                        renderParticipants();
                    }

                    if (user.id === window.currentUser.id && window.trackingEnabled) {
                        attendanceLeave();
                    }
                })

                .listen('.hand.raised', function (data) {
                    if (onlineUsers[data.user_id]) {
                        onlineUsers[data.user_id].hand = data.raised;
                    }
                    renderParticipants();
                    renderHandsRaised();
                    if (data.user_id !== window.currentUser.id) {
                        showHandToast(data.user_name, data.raised);
                    }
                });

            function renderParticipants() {
                const users = Object.values(onlineUsers);
                $('#onlineCountNum').text(users.length);
                const $list = $('#participantsList').empty();

                if (!users.length) {
                    $list.html('<li class="participants-loading">No participants yet</li>');
                    return;
                }

                users.sort(function (a, b) {
                    if (a.id === window.currentUser.id) return -1;
                    if (b.id === window.currentUser.id) return 1;
                    return a.name.localeCompare(b.name);
                });

                users.forEach(function (u) {
                    const isMe = u.id === window.currentUser.id;
                    const initials = avatarInitials(u.name);
                    $list.append(`
                <li class="participant-item entering" id="participant-${u.id}">
                    <div class="participant-avatar-wrap">
                        <div class="participant-avatar-circle ${isMe ? 'is-me' : ''}">${initials}</div>
                        <span class="presence-dot"></span>
                    </div>
                    <div class="participant-info">
                        <span class="p-name ${isMe ? 'is-me' : ''}">${escHtml(u.name)}</span>
                    </div>
                    ${u.hand ? '<i class="fa-solid fa-hand participant-hand-icon"></i>' : ''}
                </li>`);
                });

                renderHandsRaised();
            }

            function renderHandsRaised() {
                const raised = Object.values(onlineUsers).filter(u => u.hand);
                const $section = $('#handsRaisedSection');

                if (!raised.length) {
                    $section.hide();
                    $('#handBadge').hide();
                    return;
                }

                $section.show();
                $('#handsCountBadge').text(raised.length);
                $('#handBadge').text(raised.length).show();
                const $list = $('#handsList').empty();
                raised.forEach(function (u) {
                    $list.append(`
                <li class="hand-list-item">
                    <i class="fa-solid fa-hand"></i>
                    ${escHtml(u.name)}
                    ${u.id === window.currentUser.id ? '<span style="color:var(--accent-blue);font-size:0.7rem"> (You)</span>' : ''}
                </li>`);
                });
            }

            function toggleRaiseHand() {
                $.ajax({
                    url: window.raiseHandUrl,
                    method: 'POST',
                    headers: csrf(),
                    data: JSON.stringify({}),
                    success: function (res) {
                        myHandRaised = res.raised;
                        if (onlineUsers[window.currentUser.id]) {
                            onlineUsers[window.currentUser.id].hand = res.raised;
                        }
                        const $btn = $('#raiseHandBtn');
                        const $actionBtn = $('#raiseHandActionBtn');
                        if (res.raised) {
                            $btn.addClass('raised').find('.hand-label').text('Lower Hand');
                            $actionBtn.addClass('hand-active');
                            toastr.info('✋ Your hand is raised!', '', {timeOut: 2000});
                        } else {
                            $btn.removeClass('raised').find('.hand-label').text('Raise Hand');
                            $actionBtn.removeClass('hand-active');
                        }
                        renderParticipants();
                    },
                    error: function () {
                        toastr.error('Failed to update hand status');
                    },
                });
            }

            $('#raiseHandBtn, #raiseHandActionBtn').on('click', toggleRaiseHand);

            $.get(window.handStatusUrl, function (res) {
                myHandRaised = res.raised;
                if (res.raised) {
                    $('#raiseHandBtn').addClass('raised').find('.hand-label').text('Lower Hand');
                    $('#raiseHandActionBtn').addClass('hand-active');
                    if (onlineUsers[window.currentUser.id]) {
                        onlineUsers[window.currentUser.id].hand = true;
                    }
                    renderParticipants();
                }
            });

            function showHandToast(name, raised) {
                $('#handToastText').text(raised ? `${name} raised their hand` : `${name} lowered their hand`);
                $('#handToast').show();
                clearTimeout(window._handToastTimer);
                window._handToastTimer = setTimeout(() => $('#handToast').fadeOut(400), 3500);
            }

            function showJoinToast(name) {
                toastr.info(`${escHtml(name)} joined`, '', {
                    timeOut: 2000, positionClass: 'toast-bottom-right',
                });
            }

            $('.tab-content').hide();
            $('#chat').show();
            $('#chatInputArea').show();

            $('.tab-link').on('click', function () {
                const tab = $(this).data('tab');
                $('.tab-link').removeClass('active');
                $(this).addClass('active');
                $('.tab-content').hide();
                $('#' + tab).show();

                $('#chatInputArea').toggle(tab === 'chat');

                if (tab === 'chat') loadChatMessages();
                if (tab === 'polls') loadPoll();
            });

            function buildMessageHtml(msg) {
                const isMe = msg.userId === window.currentUser.id;
                const initials = avatarInitials(msg.userName || 'U');
                const rowClass = isMe ? 'chat-msg-row own' : 'chat-msg-row';
                return `
            <div class="${rowClass}" data-id="${msg.id ?? ''}">
                <div class="msg-avatar">${initials}</div>
                <div class="msg-group">
                    ${!isMe ? `<span class="msg-sender">${escHtml(msg.userName)}</span>` : ''}
                    <div class="msg-bubble">${escHtml(msg.message)}</div>
                    <span class="msg-time">${msg.timestamp ?? ''}</span>
                </div>
            </div>`;
            }

            function loadChatMessages() {
                $('#chatSkeleton').show();
                $.get(window.chatMessagesUrl, function (res) {
                    $('#chatSkeleton').hide();
                    const $box = $('#chatMessages');
                    $box.find('.chat-msg-row, .chat-date-divider, .chat-empty-state').remove();

                    if (!res.messages || !res.messages.length) {
                        $box.append(`
                    <div class="chat-empty-state">
                        <i class="fa-regular fa-comments"></i>
                        <p>No messages yet.<br>Be the first to say hello!</p>
                    </div>`);
                        return;
                    }

                    $box.append('<div class="chat-date-divider"><span>Today</span></div>');
                    res.messages.forEach(m => $box.append(buildMessageHtml(m)));
                    scrollChatToBottom(true);
                });
            }

            function scrollChatToBottom(force) {
                const $box = $('#chatMessages');
                const el = $box[0];
                if (!el) return;
                const atBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 80;
                if (force || atBottom) {
                    $box.animate({scrollTop: el.scrollHeight}, 250);
                    $('#chatScrollChip').removeClass('visible');
                } else {
                    $('#chatScrollChip').addClass('visible');
                }
            }

            $('#chatScrollChip').on('click', () => scrollChatToBottom(true));

            $('#chatMessages').on('scroll', function () {
                const atBottom = this.scrollHeight - this.scrollTop - this.clientHeight < 80;
                $('#chatScrollChip').toggleClass('visible', !atBottom);
            });

            $('#chatInput').on('input', function () {
                const len = $(this).val().length;
                const $ctr = $('#chatCharCount');
                $ctr.text(`${len} / 500`).toggleClass('visible', len > 0);
                $ctr.removeClass('warning danger');
                if (len > 400) $ctr.addClass('warning');
                if (len > 470) $ctr.removeClass('warning').addClass('danger');
                $('#sendChatBtn').prop('disabled', len === 0);
            });

            $('#sendChatBtn').on('click', sendChatMessage);
            $('#chatInput').on('keypress', function (e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendChatMessage();
                }
            });

            function sendChatMessage() {
                const msg = $.trim($('#chatInput').val());
                if (!msg) return;

                $('#chatInput').val('').prop('disabled', true);
                $('#sendChatBtn').prop('disabled', true);
                $('#chatCharCount').removeClass('visible');

                $.ajax({
                    url: window.chatSendUrl,
                    method: 'POST',
                    headers: csrf(),
                    data: JSON.stringify({message: msg}),
                    success: function (res) {
                        if (res.status) {
                            $('#chatMessages .chat-empty-state').remove();
                            $('#chatMessages').append(buildMessageHtml(res.message));
                            scrollChatToBottom(true);
                        }
                    },
                    error: function () {
                        toastr.error('Failed to send message');
                    },
                    complete: function () {
                        $('#chatInput').prop('disabled', false).focus();
                        $('#sendChatBtn').prop('disabled', false);
                    },
                });
            }

            echo.channel(`webinar.${slug}.chat`)
                .listen('.message.sent', function (data) {
                    if (data.userId === window.currentUser.id) return;
                    $('#chatMessages .chat-empty-state').remove();
                    $('#chatMessages').append(buildMessageHtml(data));
                    scrollChatToBottom(false);
                });

            loadChatMessages();

            function loadPoll() {
                $.get(window.pollUrl, function (res) {
                    renderPoll(res.poll, res.voted);
                });
            }

            function renderPoll(poll, voted) {
                const $question = $('#pollQuestion');
                const $options = $('#pollOptions');
                const $message = $('#pollMessage');
                const $footer = $('#pollFooter');

                $options.empty();
                $footer.empty();

                if (!poll) {
                    $question.hide();
                    $options.hide();
                    $message.text('Poll is not active right now.').show();
                    return;
                }

                $message.hide();
                $question.text(poll.question).show();
                $options.show();

                const answers = typeof poll.options === 'string' ? JSON.parse(poll.options) : (poll.options || []);
                const totalVotes = poll.answers_count || 0;

                answers.forEach(function (opt) {
                    const optText = typeof opt === 'object' ? opt.text : opt;
                    const count = typeof opt === 'object' ? (opt.count || 0) : 0;
                    const pct = totalVotes > 0 ? Math.round((count / totalVotes) * 100) : 0;
                    const isSelected = voted && voted.answer === optText;

                    if (voted) {
                        $options.append(`
                    <div class="poll-result-item ${isSelected ? 'selected' : ''}">
                        <div class="poll-result-label ${isSelected ? 'selected' : ''}">
                            <span>${escHtml(optText)}</span>
                            <span>${pct}%</span>
                        </div>
                        <div class="poll-result-track">
                            <div class="poll-result-fill" style="width:0%" data-width="${pct}%"></div>
                        </div>
                    </div>`);
                    } else {
                        $options.append(`
                    <button class="poll-option-btn" data-poll="${poll.id}" data-answer="${escHtml(optText)}">
                        ${escHtml(optText)}
                    </button>`);
                    }
                });

                if (voted) {
                    setTimeout(function () {
                        $('.poll-result-fill').each(function () {
                            $(this).css('width', $(this).data('width'));
                        });
                    }, 50);
                    $footer.text(`${totalVotes} total vote${totalVotes !== 1 ? 's' : ''}`);
                }
            }

            $(document).on('click', '.poll-option-btn', function () {
                const pollId = $(this).data('poll');
                const answer = $(this).data('answer');
                $.ajax({
                    url: window.pollVoteUrl,
                    method: 'POST',
                    headers: csrf(),
                    data: JSON.stringify({poll_id: pollId, answer: answer}),
                    success: function (res) {
                        if (res.status) {
                            toastr.success('Vote submitted!');
                            loadPoll();
                        } else {
                            toastr.warning(res.message);
                        }
                    },
                    error: function (xhr) {
                        toastr.warning(xhr.responseJSON?.message || 'Already voted');
                    },
                });
            });

            loadPoll();

            let selectedRating = 0;

            $(document).on('click', '.star', function () {
                selectedRating = parseInt($(this).data('value'));
                $('.star').each(function () {
                    $(this).toggleClass('active', parseInt($(this).data('value')) <= selectedRating);
                });
                $('.rating-text').text(['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][selectedRating]);
            });

            $('#submitFeedbackBtn').on('click', function () {
                if (!selectedRating) {
                    toastr.warning('Please select a rating');
                    return;
                }
                $.ajax({
                    url: window.feedbackStoreUrl,
                    method: 'POST',
                    headers: csrf(),
                    data: JSON.stringify({rating: selectedRating, comment: $('#feedbackText').val()}),
                    success: function (res) {
                        if (res.status) toastr.success('Feedback submitted. Thank you!');
                    },
                    error: function () {
                        toastr.error('Failed to submit feedback');
                    },
                });
            });


            $('#mobileChatBtn').on('click', function () {
                $('.chat-sidebar').toggleClass('mobile-open');
            });

            $('#scrollToBottomBtn').on('click', function () {
                $('html, body').animate({scrollTop: $(document).height()}, 300);
            });
        });
    </script>
@endpush
