@php
    $abstracts = $abstracts ?? collect();
@endphp
@if($abstracts->isNotEmpty())
    <div class="row mt-3">
        <div class="col-12">
            <h5 class="mb-2">Your abstracts</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 registration-abstracts-table" style="font-size: 16px; table-layout: fixed; width: 100%; color: #333; word-break: break-word;">
                    <colgroup>
                        <col style="width: 8%;">
                        <col style="width: 30%;">
                        <col style="width: 12%;">
                        <col style="width: 35%;">
                        <col style="width: 15%;">
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th scope="col">PID</th>
                            <th scope="col">Title</th>
                            <th scope="col">Status</th>
                            <th scope="col">Review</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($abstracts as $abs)
                            @php
                                $ar = $abs->abstractReviewer;
                            @endphp
                            <tr>
                                <td class="text-dark">{{ $abs->pid }}</td>
                                <td class="text-dark">{{ \Illuminate\Support\Str::limit($abs->abstract_title ?? '', 80) }}</td>
                                <td>
                                    @if((int) $abs->status === 1)
                                        <span class="badge bg-success">Submitted</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ar && $ar->review_status)
                                        @if((int) $ar->review_status === 1)
                                            <span class="text-warning"><strong>Under Review</strong></span>
                                        @elseif((int) $ar->review_status === 2)
                                            <span class="text-success"><strong>Approved</strong></span>
                                        @elseif((int) $ar->review_status === 3)
                                            <span class="text-danger"><strong>Rejected</strong></span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                        <div class="text-muted mt-2 mb-0" style="line-height: 1.5;">
                                            <span class="text-dark"><strong>Score:</strong></span> {{ $ar->score ?? 'N/A' }}
                                            @if(!empty($ar->comments))
                                                <br>
                                                <span class="text-dark"><strong>Comments:</strong></span> {{ $ar->comments }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if((int) $abs->status === 0)
                                        <a href="javascript:void(0);"
                                           class="dynamic-link btn btn-sm btn-primary"
                                           style="background-color:#F15B43;border-color:#F15B43;"
                                           data-url="{{ route('abstract.edit', $abs->id) }}"
                                           data-class="link-abstract">Edit</a>
                                    @else
                                        <a href="javascript:void(0);"
                                           class="dynamic-link btn btn-sm btn-outline-secondary"
                                           data-url="{{ route('abstract.edit', $abs->id) }}"
                                           data-class="link-abstract">View</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
