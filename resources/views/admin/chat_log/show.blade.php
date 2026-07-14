@extends('layouts.admin')
@push('styles')
    <style>
        .participant-hidden {
            display: none !important;
            height: 0 !important;
            overflow: hidden !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* ── Date Separator ── */
        .date-separator {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            user-select: none;
        }

        .date-separator::before,
        .date-separator::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e4e6ef;
        }

        .date-separator span {
            font-size: 0.75rem;
            font-weight: 600;
            color: #a1a5b7;
            background: #fff;
            padding: 4px 14px;
            border-radius: 20px;
            border: 1px solid #e4e6ef;
            white-space: nowrap;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
@endpush
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="d-flex flex-column flex-lg-row">

                <div class="flex-column flex-lg-row-auto w-100 w-lg-300px w-xl-400px mb-10 mb-lg-0">
                    <div class="card card-flush">
                        <div class="card-header pt-7" id="kt_chat_contacts_header">
                            <form class="w-100 position-relative" autocomplete="off" id="participant_search_form">

                                <span
                                    class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                              transform="rotate(45 17.0365 15.1223)" fill="black"/>
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="black"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control form-control-solid px-15" name="search" value=""
                                       placeholder="Search by username or email..."/>
                            </form>
                        </div>

                        <div class="card-body pt-5" id="kt_chat_contacts_body">
                            <div class="scroll-y me-n5 pe-5 h-200px h-lg-auto" data-kt-scroll="true"
                                 data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                                 data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_contacts_header"
                                 data-kt-scroll-wrappers="#kt_content, #kt_chat_contacts_body"
                                 data-kt-scroll-offset="0px">

                                <div class="d-flex flex-stack py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px symbol-circle">
                                            <span class="symbol-label bg-light-primary text-primary fs-6 fw-bolder">
                                                {{ strtoupper(substr($thread->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ms-5">
                                            <span
                                                class="fs-5 fw-bolder text-gray-900 mb-2 d-block">{{ $thread->name }}</span>
                                            <div class="fw-bold text-muted">
                                                {{ optional($thread->event)->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end ms-2">
                                        <span class="text-muted fs-7 mb-1">
                                            {{ $thread->created_at?->format('d M Y') ?? '' }}
                                        </span>
                                        <span class="badge badge-sm badge-circle badge-light-primary"
                                              id="msg_count_badge">
                                            {{ $messages->count() }}
                                        </span>
                                    </div>
                                </div>

                                <div class="separator separator-dashed"></div>

                                @foreach($participants as $participant)
                                    @php
                                        $firstLetter = strtoupper(substr($participant->name, 0, 1));
                                        $colors      = ['danger','primary','success','warning','info'];
                                        $colorIndex  = crc32($participant->name) % count($colors);
                                        $color       = $colors[abs($colorIndex)];
                                    @endphp
                                    <div class="d-flex flex-stack py-4" data-participant="true">

                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px symbol-circle">
                                                <span
                                                    class="symbol-label bg-light-{{ $color }} text-{{ $color }} fs-6 fw-bolder">
                                                    {{ $firstLetter }}
                                                </span>
                                            </div>
                                            <div class="ms-5">
                                                <span
                                                    class="fs-5 fw-bolder text-gray-900 mb-2 d-block">{{ $participant->name }}</span>
                                                <div class="fw-bold text-muted">{{ $participant->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator separator-dashed d-none"></div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
                    <div class="card" id="kt_chat_messenger">

                        <div class="card-header" id="kt_chat_messenger_header">
                            <div class="card-title">
                                <div class="symbol-group symbol-hover">
                                    @foreach($participants->take(7) as $participant)
                                        @php
                                            $firstLetter = strtoupper(substr($participant->name, 0, 1));
                                            $colors      = ['danger','primary','success','warning','info'];
                                            $colorIndex  = crc32($participant->name) % count($colors);
                                            $color       = $colors[abs($colorIndex)];
                                        @endphp
                                        <div class="symbol symbol-35px symbol-circle" title="{{ $participant->name }}">
                                            <span class="symbol-label bg-light-{{ $color }} text-{{ $color }}">
                                                {{ $firstLetter }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($participants->count() > 7)
                                        <span class="symbol symbol-35px symbol-circle">
                                            <span
                                                class="symbol-label fs-8 fw-bolder">+{{ $participants->count() - 7 }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-toolbar">
                                <div class="me-n3">
                                    <button class="btn btn-sm btn-icon btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <i class="bi bi-three-dots fs-2"></i>
                                    </button>
                                    <div
                                        class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3"
                                        data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Actions
                                            </div>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="{{ route('admin.chat_log') }}" class="menu-link px-3">Back to Chat
                                                Log</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body" id="kt_chat_messenger_body">
                            <div class="scroll-y me-n5 pe-5 h-300px h-lg-auto" data-kt-element="messages"
                                 data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                                 data-kt-scroll-max-height="auto"
                                 data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer"
                                 data-kt-scroll-wrappers="#kt_content, #kt_chat_messenger_body"
                                 data-kt-scroll-offset="-2px">

                                @php
                                    $currentAdminId  = auth()->id();
                                    $today           = \Carbon\Carbon::today();
                                    $yesterday       = \Carbon\Carbon::yesterday();
                                    $lastShownDate   = null;   // track which date separator we already printed
                                @endphp

                                @forelse($messages as $message)
                                    @php
                                        $senderName    = optional($message->sender)->name ?? 'Unknown';
                                        $senderId      = optional($message->sender)->id;
                                        $isMine        = $senderId === $currentAdminId;
                                        $firstLetter   = strtoupper(substr($senderName, 0, 1));
                                        $colors        = ['danger','primary','success','warning','info'];
                                        $colorIndex    = crc32($senderName) % count($colors);
                                        $color         = $colors[abs($colorIndex)];
                                        $formattedDate = $message->created_at?->format('H:i') ?? '';

                                        // ── Date separator logic ──────────────────────────────
                                        $msgDate = $message->created_at
                                            ? $message->created_at->startOfDay()->toDateString()
                                            : null;

                                        $showSeparator = $msgDate && $msgDate !== $lastShownDate;

                                        if ($showSeparator) {
                                            $msgCarbon = $message->created_at->startOfDay();

                                            if ($msgCarbon->isSameDay($today)) {
                                                $separatorLabel = 'Today';
                                            } elseif ($msgCarbon->isSameDay($yesterday)) {
                                                $separatorLabel = 'Yesterday';
                                            } else {
                                                $separatorLabel = $message->created_at->format('d M Y'); // e.g. 28 Apr 2025
                                            }

                                            $lastShownDate = $msgDate;
                                        }
                                        // ─────────────────────────────────────────────────────
                                    @endphp

                                    {{-- ── Date Separator ── --}}
                                    @if($showSeparator)
                                        <div class="date-separator">
                                            <span>{{ $separatorLabel }}</span>
                                        </div>
                                    @endif

                                    @if($isMine)
                                        <div class="d-flex justify-content-end mb-10">
                                            <div class="d-flex flex-column align-items-end">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="me-3">
                                                        <span
                                                            class="fs-5 fw-bolder text-gray-900 ms-1">{{ $senderName }}</span>
                                                    </div>
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-{{ $color }} text-{{ $color }} fs-6 fw-bolder">
                                                            {{ $firstLetter }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end"
                                                    data-kt-element="message-text">
                                                    {{ $message->message }}
                                                </div>
                                                <span class="text-muted fs-7 mb-1">{{ $formattedDate }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex justify-content-start mb-10">
                                            <div class="d-flex flex-column align-items-start">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-{{ $color }} text-{{ $color }} fs-6 fw-bolder">
                                                            {{ $firstLetter }}
                                                        </span>
                                                    </div>
                                                    <div class="ms-3">
                                                        <span
                                                            class="fs-5 fw-bolder text-gray-900 me-1">{{ $senderName }}</span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start"
                                                    data-kt-element="message-text">
                                                    {{ $message->message }}
                                                </div>
                                                <span class="text-muted fs-7 mb-1">{{ $formattedDate }}</span>
                                            </div>
                                        </div>
                                    @endif

                                @empty
                                    <div class="text-center text-muted py-10">No messages found in this thread.</div>
                                @endforelse

                                <div id="broadcast_messages_container"></div>

                            </div>
                        </div>

                        <div class="card-footer pt-4" id="kt_chat_messenger_footer">
                            <textarea class="form-control form-control-flush mb-3" rows="1"
                                      id="admin_message_input"
                                      placeholder="Type a message"></textarea>
                            <div class="d-flex flex-stack">
                                <div class="d-flex align-items-center me-2">
                                </div>
                                <button class="btn btn-primary" type="button" id="admin_send_btn">Send</button>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/custom/widgets.js')}}"></script>
    <script src="{{ asset('assets/js/custom/apps/chat/chat.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script>
        console.log(document.querySelectorAll('[data-participant="true"]').length);

        const THREAD_ID = {{ $thread->id }};
        const SEND_URL = "{{ route('admin.chat_log.send', $thread->id) }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const ADMIN_ID = {{ auth()->id() }};
        const ADMIN_NAME = "{{ addslashes(auth()->user()->name) }}";
        const EVENT_SLUG = "{{ $thread->event->slug ?? '' }}";
        window.reverbKey = "{{ config('broadcasting.connections.reverb.key') }}";
        window.reverbHost = "{{ config('broadcasting.connections.reverb.options.host', request()->getHost()) }}";
        window.reverbPort = {{ config('broadcasting.connections.reverb.options.port', 8080) }};
        window.reverbScheme = "{{ config('broadcasting.connections.reverb.options.scheme', 'http') }}";
        const msgInput = document.getElementById('admin_message_input');
        const sendBtn = document.getElementById('admin_send_btn');
        const broadcastBox = document.getElementById('broadcast_messages_container');
        const msgScroll = document.querySelector('[data-kt-element="messages"]');
        const msgBadge = document.getElementById('msg_count_badge');

        let lastBroadcastDate = null;


        (function seedLastDate() {
            const separators = document.querySelectorAll('.date-separator span');
            if (separators.length === 0) return;

            const now = new Date();
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
            lastBroadcastDate = `${yyyy}-${mm}-${dd}`;
        })();


        function scrollToBottom() {
            msgScroll.scrollTop = msgScroll.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        function incrementBadge() {
            const current = parseInt(msgBadge.textContent.trim()) || 0;
            msgBadge.textContent = current + 1;
        }

        function colorFromName(name) {
            const colors = ['danger', 'primary', 'success', 'warning', 'info'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) hash += name.charCodeAt(i);
            return colors[Math.abs(hash) % colors.length];
        }


        function separatorLabel(date) {
            const now = new Date();
            const todayStr = toDateStr(now);
            const yestStr = toDateStr(new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1));
            const dateStr = toDateStr(date);

            if (dateStr === todayStr) return 'Today';
            if (dateStr === yestStr) return 'Yesterday';

            return date.toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'});
        }

        function toDateStr(date) {
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function buildSeparatorHtml(label) {
            return `<div class="date-separator"><span>${escapeHtml(label)}</span></div>`;
        }

        function buildMessageHtml(data) {
            const isMine = data.userId === ADMIN_ID;
            const color = colorFromName(data.userName);
            const firstLetter = data.userName.charAt(0).toUpperCase();
            const message = escapeHtml(data.message);
            const timestamp = escapeHtml(data.timestamp);
            const name = escapeHtml(data.userName);

            if (isMine) {
                return `
                    <div class="d-flex justify-content-end mb-10">
                        <div class="d-flex flex-column align-items-end">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    <span class="fs-5 fw-bolder text-gray-900 ms-1">${name}</span>
                                </div>
                                <div class="symbol symbol-35px symbol-circle">
                                    <span class="symbol-label bg-light-${color} text-${color} fs-6 fw-bolder">${firstLetter}</span>
                                </div>
                            </div>
                            <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end"
                                 data-kt-element="message-text">${message}</div>
                            <span class="text-muted fs-7 mb-1">${timestamp}</span>
                        </div>
                    </div>`;
            } else {
                return `
                    <div class="d-flex justify-content-start mb-10">
                        <div class="d-flex flex-column align-items-start">
                            <div class="d-flex align-items-center mb-2">
                                <div class="symbol symbol-35px symbol-circle">
                                    <span class="symbol-label bg-light-${color} text-${color} fs-6 fw-bolder">${firstLetter}</span>
                                </div>
                                <div class="ms-3">
                                    <span class="fs-5 fw-bolder text-gray-900 me-1">${name}</span>
                                </div>
                            </div>
                            <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start"
                                 data-kt-element="message-text">${message}</div>
                            <span class="text-muted fs-7 mb-1">${timestamp}</span>
                        </div>
                    </div>`;
            }
        }

        function sendMessage() {
            const messageText = msgInput.value.trim();
            if (!messageText) return;

            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending...';

            fetch(SEND_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({message: messageText})
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        msgInput.value = '';
                    } else {
                        alert(data.message || 'Failed to send message.');
                    }
                })
                .catch(() => alert('Network error. Please try again.'))
                .finally(() => {
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Send';
                    msgInput.focus();
                });
        }

        const adminEcho = new Echo({
            broadcaster: 'reverb',
            key: window.reverbKey,
            wsHost: window.reverbHost,
            wsPort: window.reverbPort,
            wssPort: window.reverbPort,
            forceTLS: window.reverbScheme === 'https',
            enabledTransports: ['ws', 'wss'],
        });

        adminEcho.channel('webinar.' + EVENT_SLUG + '.chat')
            .listen('.message.sent', function (data) {
                console.log('[Admin Chat] Message received:', data);


                const msgDate = data.sentAt ? new Date(data.sentAt) : new Date();
                const msgDateStr = toDateStr(msgDate);

                if (msgDateStr !== lastBroadcastDate) {
                    broadcastBox.insertAdjacentHTML('beforeend', buildSeparatorHtml(separatorLabel(msgDate)));
                    lastBroadcastDate = msgDateStr;
                }

                const html = buildMessageHtml(data);
                broadcastBox.insertAdjacentHTML('beforeend', html);
                scrollToBottom();
                incrementBadge();
            });

        sendBtn.addEventListener('click', sendMessage);

        msgInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        document.getElementById('participant_search_form').addEventListener('submit', function (e) {
            e.preventDefault();
        });

        const searchInput = document.querySelector('input[name="search"]');

        function filterParticipants() {
            const query = searchInput.value.trim().toLowerCase();

            document.querySelectorAll('[data-participant="true"]').forEach(function (item) {
                const name = item.querySelector('.fs-5.fw-bolder')?.textContent.trim().toLowerCase() ?? '';
                const email = item.querySelector('.fw-bold.text-muted')?.textContent.trim().toLowerCase() ?? '';
                const matches = !query || name.includes(query) || email.includes(query);
                item.classList.toggle('participant-hidden', !matches);
            });

            const visibleCount = document.querySelectorAll('[data-participant="true"]:not(.participant-hidden)').length;
            let noResult = document.getElementById('no_participant_result');
            if (!noResult) {
                noResult = document.createElement('div');
                noResult.id = 'no_participant_result';
                noResult.className = 'text-center text-muted py-5';
                noResult.textContent = 'No participant found.';
                document.querySelector('#kt_chat_contacts_body .scroll-y').appendChild(noResult);
            }
            noResult.style.display = (query && visibleCount === 0) ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterParticipants);
        searchInput.addEventListener('keyup', filterParticipants);
        searchInput.addEventListener('change', filterParticipants);

        scrollToBottom();
    </script>
@endpush
