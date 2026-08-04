<div class="modal-header">
    <h4 class="modal-title" id="abstractModalLabel">{{$title}}</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="donate-form form-inner" style="margin-bottom: 20px;!important;">
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-12">
                <div class="event-content">

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Student Authors</h5>
                            @if (!empty($student_authors))
                                <ul>
                                    @foreach ($student_authors as $author)
                                        <li>{{ $author }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No student authors available.</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5>Additional Authors</h5>
                            @if (!empty($additional_authors))
                                <ul>
                                    @foreach ($additional_authors as $author)
                                        <li>{{ $author }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No additional authors specified.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Category</h6>
                            <p>{{ $category }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Abstract Domain</h6>
                            <p>{{ $abstractDomain->domain }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Institution Name</h6>
                            <p>{{ $institute_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Corresponding Author</h6>
                            <p>{{ $correspondent_author }}</p>
                        </div>
                    </div>

                    <h4>Abstract</h4>
                    <div class="row form-wrap">
                        <div class="col-lg-12">
                            <p>{{ $content }}</p>
                        </div>
                    </div>

                    <div class="row mt-3" style="color:darkred">
                        <h5>Reviewer Scores</h5>
                        <hr>
                        @if($reviews)
                            <div class="d-flex justify-content-start" style=" gap: 150px">
                                <div class="d-block">
                                    <p style="font-size: 14px;font-weight: 500;margin: 1px;">Clarity</p>
                                    <p style="margin-bottom: 0">{{ $reviews->clarity ?? 'N/A'}}</p>
                                </div>
                                <div class="d-block">
                                    <p style="font-size: 14px;font-weight: 500;margin: 1px;">Relevance</p>
                                    <p style="margin-bottom: 0">{{ $reviews->relevance ?? 'N/A'}}</p>
                                </div>
                                <div class="d-block">
                                    <p style="font-size: 14px;font-weight: 500;margin: 1px;">Originality</p>
                                    <p style="margin-bottom: 0">{{ $reviews->originality ?? 'N/A'}}</p>
                                </div>
                                <div class="d-block">
                                    <p style="font-size: 14px;font-weight: 500;margin: 1px;">Score</p>
                                    <p style="margin-bottom: 0">{{ $reviews->score ?? 'N/A'}}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p style="font-size: 14px;font-weight: 500;margin: 1px;">Comments: </p>
                                <span>{{$reviews->comments ?? "No Comments Added"}}</span>
                            </div>
                        @else
                            <p>No reviewers assigned or no scores available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
