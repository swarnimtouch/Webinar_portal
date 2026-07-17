@extends('layouts.website')
@section('body')

    <main class="main-content">

        <section class="video-section">
            <div class="video-player">
                {!! app('event')->player_iframe !!}
            </div>

            <div class="webinar-details">
                <h1>{{ app('event')->name }}</h1>


                @php $hasAbout = filled(strip_tags((string) app('event')->description)); @endphp
                @if($hasAbout || $resources->isNotEmpty())
                <div class="webinar-info-container {{ $hasAbout ? '' : 'no-about' }}">
                    @if($hasAbout)
                        <div class="about-webinar">
                            <h3>About This Webinar</h3>
                            <div>{!! app('event')->description !!}</div>
                        </div>
                    @endif

                </div>
                @endif

                @if(!empty(app('event')->session_agenda))
                    <div class="session-agenda">
                        <h3>Session Agenda</h3>
                        <div class="agenda-list">
                            @foreach(app('event')->session_agenda as $agenda)
                                @php
                                    $status = $agenda['status'] ?? 'upcoming';
                                    $statusLabel = match($status) {
                                        'live' => 'Live Now',
                                        'completed' => 'Completed',
                                        default => 'Upcoming',
                                    };
                                @endphp
                                <div class="agenda-item {{ $status === 'live' ? 'active' : '' }}">
                                    <div class="agenda-timeline"></div>
                                    <div class="agenda-details">
                                        @if(filled($agenda['time'] ?? null) || filled($agenda['duration'] ?? null))
                                            <div class="agenda-time">
                                                <span>{{ $agenda['time'] ?? '' }}</span>
                                                <span>{{ $agenda['duration'] ?? '' }}</span>
                                            </div>
                                        @endif
                                        <h4>{{ $agenda['title'] }}</h4>
                                        @if(filled($agenda['description'] ?? null))
                                            <p>{{ $agenda['description'] }}</p>
                                        @endif
                                    </div>
                                    <div class="agenda-status">
                                        <span class="status-badge {{ $status === 'live' ? 'live' : '' }}">{{ $statusLabel }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($resources->isNotEmpty())
                    <div class="session-resources" id="session-resources">
                        <h3>Session Resources</h3>
                        <div class="resources-grid">
                            @foreach($resources as $resource)
                                <div class="resource-card">
                                    <div class="card-header">
                                        <div class="card-icon"><i class="fa-solid fa-file-arrow-down"></i></div>
                                        <span class="card-meta">PDF</span>
                                    </div>
                                    <div class="card-body">
                                        <h4>{{ $resource->title }}</h4>
                                        <p>{{ $resource->original_name }}</p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ event_route('resource.download', ['resourceId' => $resource->id]) }}"
                                           class="card-link">
                                            Download <i class="fa-solid fa-arrow-down"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
        window.reverbHost = @json(config('broadcasting.connections.reverb.browser.host') ?: request()->getHost());
        window.reverbPort = {{ (int) (config('broadcasting.connections.reverb.browser.port') ?: (request()->isSecure() ? 443 : 80)) }};
        window.reverbScheme = @json(config('broadcasting.connections.reverb.browser.scheme') ?: (request()->isSecure() ? 'https' : 'http'));
        window.reverbPath = @json(config('broadcasting.connections.reverb.browser.path', '/webinar-reverb'));
        window.currentUser = {
            id: {{ auth()->guard('web')->id() ?? 'null' }},
            name: "{{ auth()->guard('web')->user()->name ?? 'Guest' }}"
        };
        window.csrfToken = "{{ csrf_token() }}";
        window.eventSlug = "{{ request()->route('slug') }}";
        window.trackingEnabled = @json((bool) $is_log_attendance);
        window.chatMessagesUrl = "{{ event_route('chat.messages') }}";
        window.chatSendUrl = "{{ event_route('chat.send') }}";
        window.raiseHandUrl = "{{ event_route('raise.hand') }}";
        window.handStatusUrl = "{{ event_route('hand.status') }}";
        window.pollUrl = "{{ event_route('poll') }}";
        window.pollVoteUrl = "{{ event_route('poll.vote') }}";
        window.feedbackStoreUrl = "{{ event_route('feedback.save') }}";
        window.attendanceJoinUrl = "{{ event_route('attendance.join') }}";
        window.attendanceLeaveUrl = "{{ event_route('attendance.leave') }}";
        window.initialFeedback = @json([
            'rating' => $feedback->rating ?? null,
            'comment' => $feedback->comment ?? null,
        ]);

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

            // Attendance must not depend on Reverb/Echo. If the websocket client
            // fails to initialise, users should still be marked as attending.
            if (window.trackingEnabled) {

                function attendanceBeacon(url) {
                    const payload = new Blob(
                        [JSON.stringify({_token: window.csrfToken})],
                        {type: 'application/json'}
                    );
                    navigator.sendBeacon(url, payload);
                }

                function attendanceJoin() {
                    $.ajax({
                        url: window.attendanceJoinUrl,
                        method: 'POST',
                        headers: csrf(),
                        data: JSON.stringify({}),
                    });
                }

                function attendanceLeave() {
                    attendanceBeacon(window.attendanceLeaveUrl);
                }

                attendanceJoin();

                const attendanceHeartbeat = window.setInterval(function () {
                    if (!document.hidden) attendanceJoin();
                }, 30000);

                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        attendanceLeave();
                    } else {
                        attendanceJoin();
                    }
                });

                window.addEventListener('beforeunload', function () {
                    window.clearInterval(attendanceHeartbeat);
                    attendanceLeave();
                });

                window.addEventListener('pagehide', function () {
                    window.clearInterval(attendanceHeartbeat);
                    attendanceLeave();
                });
            }

            const echo = new Echo({
                broadcaster: 'reverb',
                key: window.reverbKey,
                wsHost: window.reverbHost,
                wsPort: window.reverbPort,
                wssPort: window.reverbPort,
                wsPath: window.reverbPath,
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

            const onlineUsers = {};
            let myHandRaised = false;

            const presenceChannel = echo.join(`webinar.${slug}.presence`);

            presenceChannel

                .here(function (users) {
                    users.forEach(u => {
                        onlineUsers[u.id] = u;
                    });
                    renderParticipants();

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

                })

                .listen('.hand.raised', function (data) {
                    const userId = Number(data.user_id ?? data.userId);
                    const userName = data.user_name ?? data.userName ?? onlineUsers[userId]?.name ?? 'User';

                    if (onlineUsers[userId]) {
                        onlineUsers[userId].hand = data.raised;
                    }
                    renderParticipants();
                    renderHandsRaised();
                    if (userId !== window.currentUser.id) {
                        showHandToast(userName, data.raised);
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

            function loadChatMessages(silent = false) {
                if (!silent) $('#chatSkeleton').show();
                $.get(window.chatMessagesUrl, function (res) {
                    $('#chatSkeleton').hide();
                    const $box = $('#chatMessages');

                    if (silent) {
                        const existingIds = new Set(
                            $box.find('.chat-msg-row[data-id]').map(function () {
                                return String($(this).data('id'));
                            }).get().filter(Boolean)
                        );
                        const newMessages = (res.messages || []).filter(message =>
                            message.id && !existingIds.has(String(message.id))
                        );

                        if (newMessages.length) {
                            $box.find('.chat-empty-state').remove();
                            newMessages.forEach(message => $box.append(buildMessageHtml(message)));
                            scrollChatToBottom(false);
                        }
                        return;
                    }

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
                    if (data.senderType === 'user' && data.userId === window.currentUser.id) return;
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

                const answers = typeof poll.poll_answers === 'string' ? JSON.parse(poll.poll_answers) : (poll.poll_answers || []);
                const totalVotes = poll.votes_count || 0;

                answers.forEach(function (opt) {
                    const optText = typeof opt === 'object' ? opt.answer : opt;
                    const optId = typeof opt === 'object' ? opt.id : opt;
                    const count = typeof opt === 'object' ? (opt.user_voted_count || 0) : 0;
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
                    <button class="poll-option-btn" data-poll="${poll.id}" data-answer-id="${optId}" data-answer="${escHtml(optText)}">
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
                const answerId = $(this).data('answer-id');
                $.ajax({
                    url: window.pollVoteUrl,
                    method: 'POST',
                    headers: csrf(),
                    data: JSON.stringify({poll_id: pollId, answer: answer, answer_id: answerId}),
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
            echo.channel(`webinar.${slug}.poll`)
                .listen('.poll.updated', function (data) {
                    const pollIsLive = data.poll
                        && data.poll.status === 'active'
                        && !Boolean(Number(data.poll.is_hidden));

                    if (pollIsLive) {
                        toastr.info('Poll is live!', 'Live Poll', {
                            closeButton: true,
                            timeOut: 8000,
                            extendedTimeOut: 2000
                        });
                    }

                    renderPoll(data.poll, null);
                    loadPoll();
                });

            loadPoll();

            // Keep chat and polls usable when the WebSocket proxy is temporarily
            // unavailable. Reverb still provides instant updates when connected.
            window.setInterval(function () {
                if (!document.hidden) {
                    loadChatMessages(true);
                    loadPoll();
                }
            }, 5000);

            let selectedRating = 0;
            let feedbackSubmitted = false;

            function feedbackLabel(rating) {
                return ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][rating] || 'Select stars to rate';
            }

            function paintFeedbackStars() {
                $('.star').each(function () {
                    $(this)
                        .toggleClass('active', parseInt($(this).data('value')) <= selectedRating)
                        .toggleClass('is-locked', feedbackSubmitted);
                });
            }

            function setFeedbackSubmittedState(isSubmitted, feedback = {}) {
                feedbackSubmitted = isSubmitted;

                if (feedback.rating) {
                    selectedRating = parseInt(feedback.rating);
                }

                if (typeof feedback.comment !== 'undefined') {
                    $('#feedbackText').val(feedback.comment || '');
                }

                paintFeedbackStars();
                $('#feedbackRatingText').text(feedbackLabel(selectedRating));

                $('#feedbackText').prop('disabled', isSubmitted);
                $('#submitFeedbackBtn')
                    .prop('disabled', isSubmitted)
                    .text(isSubmitted ? 'Feedback Submitted' : 'Submit Feedback');

                $('#feedbackSubtitle').text(
                    isSubmitted
                        ? 'Your response has been saved for this session.'
                        : 'Share a quick rating and comment about this webinar.'
                );

                $('#feedbackSuccessState').toggle(isSubmitted);
                $('#feedbackSuccessText').text(
                    isSubmitted && (feedback.comment || '').trim()
                        ? 'Your rating and comments have been recorded.'
                        : 'Your rating has been recorded.'
                );
            }

            $(document).on('click', '.star', function () {
                if (feedbackSubmitted) {
                    return;
                }

                selectedRating = parseInt($(this).data('value'));
                paintFeedbackStars();
                $('#feedbackRatingText').text(feedbackLabel(selectedRating));
            });

            $('#submitFeedbackBtn').on('click', function () {
                if (feedbackSubmitted) {
                    return;
                }

                if (!selectedRating) {
                    toastr.warning('Please select a rating');
                    return;
                }

                const $button = $(this);
                $button.prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: window.feedbackStoreUrl,
                    method: 'POST',
                    headers: csrf(),
                    data: JSON.stringify({rating: selectedRating, comment: $('#feedbackText').val()}),
                    success: function (res) {
                        if (res.status) {
                            setFeedbackSubmittedState(true, res.feedback || {
                                rating: selectedRating,
                                comment: $('#feedbackText').val(),
                            });
                            toastr.success('Feedback submitted. Thank you!');
                        } else {
                            $button.prop('disabled', false).text('Submit Feedback');
                        }
                    },
                    error: function () {
                        $button.prop('disabled', false).text('Submit Feedback');
                        toastr.error('Failed to submit feedback');
                    },
                });
            });

            if (window.initialFeedback?.rating) {
                setFeedbackSubmittedState(true, window.initialFeedback);
            }


            $('#mobileChatBtn').on('click', function () {
                const chatTab = document.querySelector('.tab-link[data-tab="chat"]');
                const chatSidebar = document.querySelector('.chat-sidebar');

                chatTab?.click();
                chatSidebar?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            $('#scrollToBottomBtn').on('click', function () {
                $('html, body').animate({scrollTop: $(document).height()}, 300);
            });
        });
    </script>
@endpush
