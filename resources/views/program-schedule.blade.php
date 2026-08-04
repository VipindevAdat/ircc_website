@extends('layouts.app')
@section('head')
<style>
.program-schedule-section {
    padding: 60px 0;
}


.schedule-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-top: 4px solid #F15B43;
}


.schedule-day {
    color: #F15B43;
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
    border-bottom: 2px solid #F15B43;
    padding-bottom: 10px;
}

.schedule-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.schedule-table th {
    background: #F15B43;
    color: #ffffff;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 16px;
}

.schedule-table td {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 15px;
    vertical-align: top;
}

.schedule-table tr:hover {
    background: #f8f9fa;
}

.schedule-table tr:last-child td {
    border-bottom: none;
}

.time-column {
    font-weight: 600;
    color: #333;
    width: 15%;
    white-space: nowrap;
}

.activity-column {
    width: 60%;
}

.venue-column {
    width: 25%;
    color: #666;
    font-style: italic;
}

.activity-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.speaker-info {
    margin-top: 8px;
    padding-left: 15px;
    border-left: 3px solid #F15B43;
}

.speaker-name {
    font-weight: 600;
    color: #F15B43;
    margin-bottom: 3px;
    display: inline-block;
    min-width: 320px;
}

.speaker-details {
    color: #666;
    font-size: 14px;
    margin-bottom: 3px;
}

.speaker-title {
    color: #333;
    font-style: italic;
    margin-top: 5px;
}

.chair-info {
    margin-top: 8px;
    font-size: 14px;
    color: #666;
}

.virtual-badge {
    display: inline-block;
    background: #F15B43;
    color: #ffffff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 10px;
}

@media (max-width: 768px) {
    .schedule-card {
        padding: 20px;
    }
    
    .schedule-day {
        font-size: 24px;
    }
    
    .schedule-table {
        font-size: 13px;
        display: block;
        overflow-x: auto;
    }
    
    .schedule-table th,
    .schedule-table td {
        padding: 10px 8px;
    }
    
    .time-column {
        width: 25%;
    }
    
    .activity-column {
        width: 50%;
    }
    
    .venue-column {
        width: 25%;
    }
}
</style>
@endsection

@section('content')
<main id="content" class="site-main bg-light-grey">
    <!-- Inner Banner html start-->
    <section class="inner-banner-wrap">
        <div class="inner-baner-container" style="background-image: url({{ url("front/images/inner-banner-img-1.jpg")}}">
            <div class="container">
                <div class="inner-banner-content">
                    <h1 class="inner-title">Program Schedule</h1>
                </div>
            </div>
        </div>
    </section>
    <!-- Inner Banner html end-->
    
    <!-- Program Schedule Section -->
    <div class="program-schedule-section">
        <div class="container">
            <div class="section-head text-center mb-5">
                @if($pageSettings->conference_title)
                    <h2 class="section-title">{{ $pageSettings->conference_title }}</h2>
                @endif
                @if($pageSettings->venue)
                    <p class="schedule-subtitle" style="font-size: 18px; color: #666; margin-top: 10px;">{{ $pageSettings->venue }}</p>
                @endif
                @if($pageSettings->event_dates)
                    <p class="schedule-date" style="font-size: 20px; color: #333; font-weight: 600; margin-top: 5px;">{{ $pageSettings->event_dates }}</p>
                @endif
            </div>

            @forelse($days as $day)
            <div class="schedule-card">
                <h3 class="schedule-day">{{ $day->title }}</h3>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Activity</th>
                            <th>Venue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($day->activeItems as $item)
                        <tr>
                            <td class="time-column">{{ $item->time_slot }}</td>
                            <td class="activity-column">
                                <div class="activity-title">{{ $item->title }}</div>
                                @if($item->details)
                                    {!! $item->details !!}
                                @endif
                            </td>
                            <td class="venue-column">
                                @if($day->is_virtual || strtoupper(trim($item->venue ?? '')) === 'VIRTUAL')
                                    <span class="virtual-badge">VIRTUAL</span>
                                @elseif($item->venue)
                                    {!! $item->venue !!}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @empty
            <div class="text-center">
                <p class="text-muted">Program schedule will be published soon.</p>
            </div>
            @endforelse

            @if($pageSettings->disclaimer)
            <div class="text-center mt-4">
                <div style="background: #fff3cd; border-left: 4px solid #F15B43; padding: 20px; margin: 30px 0; border-radius: 4px;">
                    <p style="color: #856404; font-size: 16px; font-weight: 600; margin-bottom: 10px;">
                        DISCLAIMER:
                    </p>
                    <p style="color: #856404; font-size: 14px; margin: 0; line-height: 1.6;">
                        {{ $pageSettings->disclaimer }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</main>
@endsection
