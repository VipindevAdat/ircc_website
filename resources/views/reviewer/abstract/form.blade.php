<div class="modal-header">
    <h5 class="modal-title" id="scoreModalLabel">Add Score</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<form action="{{ route('reviewer.storeScore', ['id' => $abstractReviewer->id??'']) }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="clarity">Clarity (Out of 5)</label>
            <input type="number" class="form-control" name="clarity" id="clarity" min="0" max="5" value="{{ old('clarity', $abstractReviewer->clarity ?? '') }}">
            <span class="text-danger clarity-error"></span>
        </div>
        
        <div class="form-group mt-3">
            <label for="relevance">Relevance (Out of 5)</label>
            <input type="number" class="form-control" name="relevance" id="relevance" min="0" max="5" value="{{ old('relevance', $abstractReviewer->relevance ?? '') }}">
            <span class="text-danger relevance-error"></span>
        </div>
        
        <div class="form-group mt-3">
            <label for="originality">Originality (Out of 5)</label>
            <input type="number" class="form-control" name="originality" id="originality" min="0" max="5" value="{{ old('originality', $abstractReviewer->originality ?? '') }}">
            <span class="text-danger originality-error"></span>
        </div>

        <div class="form-group mt-3">
            <label for="comments">Comments</label>
            <textarea class="form-control" name="comments" id="comments">{{ old('comments', $abstractReviewer->comments ?? '') }}</textarea>
            <span class="text-danger comments-error"></span>
        </div>

        <div class="form-group mt-3">
            <label for="review_status">Review Status</label>
            <select class="form-control" name="review_status" id="review_status">
                <option value="">Select Status</option>
                @foreach ($reviewStatuses as $status)
                    <option value="{{ $status->id }}" {{ $status->id == old('review_status', $abstractReviewer->review_status ?? '') ? 'selected' : '' }}>
                        {{ $status->status }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger review-status-error"></span>
        </div>

        <div class="form-group mt-3 d-flex justify-content-end">
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">Save Score</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>


<script>
    $(document).ready(function () {

        let minScore = {{ $minScore }};

        $('input[type="number"]').on('input', function () {
            let totalScore = 0;
            let numberOfScores = 0;
            let isScoreValid = true;

            $('input[type="number"]').each(function () {
                let value = parseFloat($(this).val());
                let errorSpan = $(this).siblings('span.text-danger');

                if (isNaN(value) || value > 5 || value < 0) {
                    errorSpan.text('Score must be between 0 and 5.');
                    isScoreValid = false;
                } else {
                    errorSpan.text('');
                    totalScore += value;
                    numberOfScores++;
                }
            });

            let averageScore = numberOfScores > 0 ? totalScore / numberOfScores : 0;

            if (isScoreValid && averageScore > minScore) {
                $('#review_status').val('{{ $reviewStatuses->firstWhere("status", "Approved")->id }}');
            } else {
                $('#review_status').val('');
            }
        });


        $('#comments').on('input', function () {
            let value = $(this).val().trim();
            let errorSpan = $(this).siblings('span.text-danger');

            if (value === '') {
                errorSpan.text('Comments field is required.');
            } else {
                errorSpan.text('');
            }
        });

        $('#review_status').on('change', function () {
            let value = $(this).val();
            let errorSpan = $(this).siblings('span.text-danger');

            if (value === '') {
                errorSpan.text('Please select a review status.');
            } else {
                errorSpan.text(''); 
            }
        });

        $('form').on('submit', function (e) {
            let isValid = true;

            $('input[type="number"]').each(function () {
                let value = $(this).val();
                let errorSpan = $(this).siblings('span.text-danger');

                if (value === '') {
                    errorSpan.text('This field is required.');
                    isValid = false;
                } else {
                    value = parseInt(value);
                    if (isNaN(value) || value > 5 || value < 0) {
                        errorSpan.text('Score must be between 0 and 5.');
                        isValid = false;
                    } else {
                        errorSpan.text('');
                    }
                }
            });

            let commentValue = $('#comments').val().trim();
            let commentErrorSpan = $('#comments').siblings('span.text-danger');

            if (commentValue === '') {
                commentErrorSpan.text('Comments field is required.');
                isValid = false;
            } else {
                commentErrorSpan.text(''); 
            }

            let statusValue = $('#review_status').val();
            let statusErrorSpan = $('#review_status').siblings('span.text-danger');

            if (statusValue === '') {
                statusErrorSpan.text('Please select a review status.');
                isValid = false;
            } else {
                statusErrorSpan.text(''); 
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>