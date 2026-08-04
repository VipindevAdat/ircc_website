<div class="card mb-2 {{ !empty($adjustmentRequest) ? 'd-block' : 'd-none' }}" id="adjustment-progress">
    <div class="card-body">
        <h5 class="card-title">Inventory Adjustment in progress</h5>
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: {{ $adjustmentProgress }}%;" aria-valuenow="{{ $adjustmentProgress }}" aria-valuemin="0" aria-valuemax="100">{{$adjustmentProgress}}%</div>
        </div>
    </div>
</div>
