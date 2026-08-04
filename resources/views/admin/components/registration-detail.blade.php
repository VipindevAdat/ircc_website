<link rel="stylesheet" href="{{ url('libs/datepicker/daterangepicker.css') }}" type="text/css">

<div class="card mb-4">
    <form class="donate-form" method="post" action="{{ route('admin.updateRegistration') }}" id="registration-form">
        @csrf
        <input type="hidden" name="registrationId" value="{{ $registration->id }}">

        <div class="donate-detais form-inner">
            <div class="row mb-3">
                <!-- Name -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $registration->first_name }}" required>
                        <span class="form-validation" id="name_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $registration->last_name }}" required>
                        <span class="form-validation" id="name_error"></span>
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $registration->email }}" required>
                        <span class="form-validation" id="email_error"></span>
                    </div>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $registration->whatsapp_number }}" required>
                        <span class="form-validation" id="phone_error"></span>
                    </div>
                </div>

{{--                <!-- Event -->--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="mb-3">--}}
{{--                        <label class="form-label">Event</label>--}}
{{--                        <select name="event_id" class="form-control" required>--}}
{{--                            <option value="" disabled>Select Event</option>--}}
{{--                            @foreach($events as $event)--}}
{{--                                <option value="{{ $event->id }}" {{ $event->id == $registration->event_id ? 'selected' : '' }}>--}}
{{--                                    {{ $event->name }}--}}
{{--                                </option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                        <span class="form-validation" id="event_error"></span>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <!-- Institution -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Institution</label>
                        <input type="text" name="institution" class="form-control" value="{{ $registration->institution_name }}" required>
                        <span class="form-validation" id="institution_error"></span>
                    </div>
                </div>

{{--                <!-- Payment Details -->--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="mb-3">--}}
{{--                        <label class="form-label">Payment Reference</label>--}}
{{--                        <input type="text" name="payment_reference" class="form-control" value="{{ $registration->payment_reference }}" required>--}}
{{--                        <span class="form-validation" id="payment_reference_error"></span>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="col-md-6">--}}
{{--                    <div class="mb-3">--}}
{{--                        <label class="form-label">Payment Amount</label>--}}
{{--                        <input type="number" name="payment_amount" class="form-control" value="{{ $registration->payment_amount }}" required>--}}
{{--                        <span class="form-validation" id="payment_amount_error"></span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <!-- Students Section -->--}}
{{--        @if ($registration->participation_type == 'team')--}}
{{--            <div class="donate-detais form-inner mb-4">--}}
{{--                <section class="card card-body border mb-0">--}}
{{--                    <h5>Team Members</h5>--}}
{{--                    <div class="row" id="team_members_section">--}}
{{--                        @foreach ($registration->team_members as $index => $member)--}}
{{--                            <div class="col-md-6">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Member {{ $index + 1 }}</label>--}}
{{--                                    <input type="text" name="team_members[{{ $index }}]" class="form-control" value="{{ $member }}" required>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </section>--}}
{{--            </div>--}}
{{--        @endif--}}

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-info mt-4 pull-right">Save</button>
            </div>
        </div>
    </form>
</div>

<script src="{{ url('libs/datepicker/daterangepicker.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('registration-form');
        const participationType = form.querySelector('[name="participation_type"]');
        const teamMembersSection = document.getElementById('team_members_section');

        // Show or hide team members dynamically based on participation type
        // participationType.addEventListener('change', function () {
        //     if (this.value === 'team') {
        //         teamMembersSection.style.display = 'block';
        //     } else {
        //         teamMembersSection.style.display = 'none';
        //     }
        // });

        // // Initial check on load
        // if (participationType.value === 'team') {
        //     teamMembersSection.style.display = 'block';
        // } else {
        //     teamMembersSection.style.display = 'none';
        // }

        // Validation examples (extend per field as needed)
        form.addEventListener('submit', function (event) {
            const errors = [];
            const emailField = form.querySelector('[name="email"]');
            if (!validateEmail(emailField.value)) {
                errors.push('Invalid email format');
                emailField.nextElementSibling.textContent = "Invalid email format.";
            }

            if (errors.length > 0) {
                event.preventDefault();
            }
        });

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    });
</script>
