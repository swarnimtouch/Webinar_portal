@extends('layouts.admin')

@push('styles')
<style>
    .dashboard-stat { border: 0; box-shadow: 0 2px 16px rgba(33, 37, 41, .06); height: 100%; }
    .dashboard-stat .stat-icon { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 12px; font-size: 1.35rem; }
    .dashboard-stat .stat-value { font-size: 1.75rem; line-height: 1; }
    .dashboard-chart { width: 100%; height: 285px; }
    .event-logo { width: 38px; height: 38px; object-fit: contain; border-radius: 8px; background: #f5f8fa; }
    .live-dot { width: 8px; height: 8px; display: inline-block; border-radius: 50%; background: #50cd89; box-shadow: 0 0 0 4px rgba(80,205,137,.15); }
</style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-7">
                <div>
                    <h2 class="fw-bolder text-dark mb-1">{{ $isSubAdmin ? ($assignedEvent?->name ?? 'Assigned event') : 'Event overview' }}</h2>
                    <div class="text-muted fw-bold">
                        {{ $isSubAdmin ? 'Statistics are limited to your assigned event.' : 'Live performance across all events.' }}
                    </div>
                </div>
                @if(!$isSubAdmin || $assignedEvent?->show_live_users)
                    <div class="badge badge-light-success fs-7 px-4 py-3 mt-3 mt-sm-0">
                        <span class="live-dot me-2"></span> Live updates use the last 2 minutes
                    </div>
                @endif
            </div>

            @php
                $showAttendance = !$isSubAdmin || $assignedEvent?->is_log_attendance;
                $showLiveUsers = !$isSubAdmin || $assignedEvent?->show_live_users;
                $showLiveChat = !$isSubAdmin || $assignedEvent?->enable_live_chat;
                $showComments = !$isSubAdmin || $assignedEvent?->enable_comments;
                $showPolls = !$isSubAdmin || $assignedEvent?->enable_polls;
                $showFeedback = !$isSubAdmin || $assignedEvent?->enable_feedback;

                $cards = [
                    ['label' => 'Events', 'value' => $stats['events'], 'icon' => 'bi-calendar-event', 'color' => 'primary'],
                    ['label' => 'Registered users', 'value' => $stats['registrations'], 'icon' => 'bi-people', 'color' => 'info'],
                    ['label' => 'New today', 'value' => $stats['new_today'], 'icon' => 'bi-person-plus', 'color' => 'warning'],
                ];
                if ($showAttendance) $cards[] = ['label' => 'Total attendees', 'value' => $stats['attendees'], 'icon' => 'bi-person-check', 'color' => 'primary'];
                if ($showLiveUsers) $cards[] = ['label' => 'Live now', 'value' => $stats['live_users'], 'icon' => 'bi-broadcast', 'color' => 'success'];
                if ($showLiveChat) $cards[] = ['label' => 'Chat threads', 'value' => $stats['chat_threads'], 'icon' => 'bi-chat-dots', 'color' => 'primary'];
                if ($showComments) $cards[] = ['label' => 'Total comments', 'value' => $stats['comments'], 'icon' => 'bi-chat-square-text', 'color' => 'warning'];
                if ($showPolls) {
                    $cards[] = ['label' => 'Polls', 'value' => $stats['polls'], 'icon' => 'bi-bar-chart', 'color' => 'danger'];
                    $cards[] = ['label' => 'Poll voters', 'value' => $stats['voters'], 'icon' => 'bi-check2-square', 'color' => 'success'];
                    $cards[] = ['label' => 'Votes submitted', 'value' => $stats['votes'], 'icon' => 'bi-ui-checks', 'color' => 'info'];
                }
            @endphp
            <div class="row g-5 mb-8">
                @foreach($cards as $card)
                    <div class="col-sm-6 col-xl-3">
                        <div class="card dashboard-stat">
                            <div class="card-body d-flex align-items-center p-6">
                                <div class="stat-icon bg-light-{{ $card['color'] }} text-{{ $card['color'] }} me-5"><i class="bi {{ $card['icon'] }} text-{{ $card['color'] }}"></i></div>
                                <div>
                                    <div class="stat-value fw-bolder text-dark">{{ number_format($card['value']) }}</div>
                                    <div class="text-muted fw-bold fs-7 mt-2">{{ $card['label'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($isSubAdmin && $locationReports->isNotEmpty())
                <div class="row g-5 mb-8">
                    @foreach($locationReports as $dimension => $rows)
                        <div class="col-xl-4 col-md-6">
                            <div class="card h-100">
                                <div class="card-header border-0 pt-5 align-items-center">
                                    <h3 class="card-title fw-bolder text-dark">{{ ucfirst($dimension) }}-wise registrations</h3>
                                    <a class="btn btn-sm btn-light-primary"
                                       href="{{ route('admin.dashboard.location_report.export', ['dimension' => $dimension]) }}">
                                        <i class="bi bi-download me-1"></i>Export CSV
                                    </a>
                                </div>
                                <div class="card-body pt-2 table-responsive">
                                    <table class="table align-middle table-row-dashed gy-3">
                                        <thead><tr class="text-muted fw-bolder fs-7 text-uppercase"><th>{{ ucfirst($dimension) }}</th><th class="text-end">Registered</th></tr></thead>
                                        <tbody>
                                        @forelse($rows as $row)
                                            <tr><td class="fw-bold text-dark">{{ $row->location }}</td><td class="text-end fw-bolder">{{ number_format($row->total) }}</td></tr>
                                        @empty
                                            <tr><td colspan="2" class="text-center text-muted py-6">No registration data.</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-xl-8">
                    <div class="card h-100">
                        <div class="card-header border-0 pt-5">
                            <div><h3 class="card-title fw-bolder text-dark mb-1">Registrations & attendance</h3><div class="text-muted fw-bold fs-7">Last 7 days</div></div>
                            <div class="d-flex gap-4 align-items-center fs-7 fw-bold"><span class="text-primary">● Registrations</span><span class="text-success">● Attendees</span></div>
                        </div>
                        <div class="card-body pt-2"><canvas id="activityChart" class="dashboard-chart"></canvas></div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-0 pt-5"><h3 class="card-title fw-bolder text-dark">Engagement summary</h3></div>
                        <div class="card-body pt-2">
                            @php
                                $engagement = [];
                                if ($showFeedback) $engagement[] = ['Feedback received', $stats['feedback'], 'bi-chat-square-heart', 'danger'];
                                if ($showLiveChat) $engagement[] = ['Chat threads', $stats['chat_threads'], 'bi-chat-dots', 'primary'];
                                if ($showComments) $engagement[] = ['Comments received', $stats['comments'], 'bi-chat-square-text', 'info'];
                                if ($showAttendance) {
                                    $engagement[] = ['Total watch time', number_format($stats['session_seconds'] / 3600, 1).' hrs', 'bi-clock-history', 'warning'];
                                    $engagement[] = ['Attendance rate', $stats['registrations'] ? round(($stats['attendees'] / $stats['registrations']) * 100).'%' : '0%', 'bi-graph-up-arrow', 'success'];
                                }
                            @endphp
                            @foreach($engagement as [$label, $value, $icon, $color])
                                <div class="d-flex align-items-center py-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="stat-icon bg-light-{{ $color }} me-4"><i class="bi {{ $icon }} text-{{ $color }}"></i></div>
                                    <div class="flex-grow-1 text-muted fw-bold">{{ $label }}</div>
                                    <div class="fw-bolder fs-4 text-dark">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-5 g-xl-8">
                <div class="col-xl-7">
                    <div class="card h-100">
                        <div class="card-header border-0 pt-5"><h3 class="card-title fw-bolder text-dark">Event performance</h3></div>
                        <div class="card-body pt-2 table-responsive">
                            <table class="table align-middle table-row-dashed gy-4">
                                <thead><tr class="text-muted fw-bolder fs-7 text-uppercase"><th>Event</th><th>Registered</th>@if(!$isSubAdmin || $assignedEvent?->show_live_users)<th>Live</th>@endif<th>Attended</th><th>Polls</th><th>Votes</th></tr></thead>
                                <tbody>
                                @forelse($eventRows as $row)
                                    <tr>
                                        <td><div class="d-flex align-items-center"><img class="event-logo me-3" src="{{ $row['event']->logo }}" alt=""><div><div class="fw-bolder text-dark">{{ $row['event']->name }}</div><span class="badge badge-light-{{ $row['event']->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($row['event']->status) }}</span></div></div></td>
                                        <td class="fw-bold">{{ number_format($row['registrations']) }}</td>
                                        @if(!$isSubAdmin || $assignedEvent?->show_live_users)
                                            <td><span class="live-dot me-2"></span><span class="fw-bold">{{ number_format($row['live']) }}</span></td>
                                        @endif
                                        <td class="fw-bold">{{ number_format($row['attendees']) }}</td><td class="fw-bold">{{ number_format($row['polls']) }}</td><td class="fw-bold">{{ number_format($row['votes']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ (!$isSubAdmin || $assignedEvent?->show_live_users) ? 6 : 5 }}" class="text-center text-muted py-10">No event data is available.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="card h-100">
                        <div class="card-header border-0 pt-5"><h3 class="card-title fw-bolder text-dark">Recent registrations</h3><a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-light-primary">View users</a></div>
                        <div class="card-body pt-2">
                            @forelse($recentUsers as $user)
                                <div class="d-flex align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <img src="{{ $user->avatar }}" class="rounded-circle me-4" width="42" height="42" alt="">
                                    <div class="flex-grow-1 overflow-hidden"><div class="fw-bolder text-dark text-truncate">{{ $user->name ?: trim($user->first_name.' '.$user->last_name) }}</div><div class="text-muted fs-7 text-truncate">{{ $user->email }}</div></div>
                                    <div class="text-end ms-3"><div class="text-muted fs-8">{{ $user->created_at?->diffForHumans() }}</div>@if(!$isSubAdmin)<div class="text-primary fs-8 text-truncate" style="max-width:120px">{{ $user->event?->name ?? 'No event' }}</div>@endif</div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-10">No registrations yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const canvas = document.getElementById('activityChart');
    const points = @json($chart);
    if (!canvas || !points.length) return;
    const draw = () => {
        const ratio = window.devicePixelRatio || 1, rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio; canvas.height = rect.height * ratio;
        const ctx = canvas.getContext('2d'); ctx.scale(ratio, ratio);
        const w = rect.width, h = rect.height, pad = {l:42,r:15,t:20,b:38};
        const max = Math.max(1, ...points.flatMap(p => [p.registered, p.attended]));
        ctx.font = '12px Poppins'; ctx.textAlign = 'right'; ctx.textBaseline = 'middle';
        for (let i=0;i<=4;i++) { const y=pad.t+(h-pad.t-pad.b)*i/4; ctx.strokeStyle='#eef0f3'; ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(w-pad.r,y); ctx.stroke(); ctx.fillStyle='#a1a5b7'; ctx.fillText(Math.round(max*(4-i)/4),pad.l-8,y); }
        const x = i => pad.l + (w-pad.l-pad.r) * (points.length === 1 ? .5 : i/(points.length-1));
        const y = value => pad.t + (h-pad.t-pad.b) * (1-value/max);
        const line = (key,color) => { ctx.strokeStyle=color; ctx.lineWidth=3; ctx.lineJoin='round'; ctx.beginPath(); points.forEach((p,i)=>i?ctx.lineTo(x(i),y(p[key])):ctx.moveTo(x(i),y(p[key]))); ctx.stroke(); points.forEach((p,i)=>{ctx.fillStyle='#fff';ctx.strokeStyle=color;ctx.lineWidth=2;ctx.beginPath();ctx.arc(x(i),y(p[key]),4,0,Math.PI*2);ctx.fill();ctx.stroke();}); };
        line('registered','#009ef7'); line('attended','#50cd89');
        ctx.textAlign='center';ctx.textBaseline='top';ctx.fillStyle='#7e8299';points.forEach((p,i)=>ctx.fillText(p.label,x(i),h-pad.b+12));
    };
    draw(); window.addEventListener('resize', draw);
})();
</script>
@endpush
