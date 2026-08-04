<style>
    /* Global swal button styling to match theme */
    .swal-button { background-color: #F15B43 !important; }
    .swal-button:hover { background-color: #d94a35 !important; }

    /* Make abstract list table header more readable */
    .abstract-table thead th {
        background-color: #f5f5f5;
        color: #333;
        font-weight: 600;
    }
</style>
<div class="donate-section">
    <div class="container">
        <div class="donate-detais">
            <div class="donate-form">
                @if($payment_summary['amountPayable']>0 && empty($paymentApprovePending) && empty($paymentApproveRejected))
                <div class="donate-form form-inner">
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <div class="contact-detail-container item-center" style="align-items: center;text-align: center;">
                            <div class="pb-4">
                            <span style="height: 75px;color:#F15B43;"><i class="fa fa-info-circle" style="font-size: 45px;"></i></span>
                            </div>
                            <div style="font-size: 12px; color: #333; text-align: center;">
                                 <h2 style="font-size: 40px;">Payment is pending</h2>
                                
                            </div>
                            <div style="font-size: 16px; color: #333; text-align: center;">
                            <p>Add payment to continue on adding abstract.</p>
                            </div>
                            </div>
                        </div>
                        @if($payment_summary['amountPayable']>0)
                            <div class="col-md-12 d-flex justify-content-center">
                                <h5 style="font-weight: bold;">
                                    Payment Pending : ${{$payment_summary['amountPayable'] }}
                                </h5>
                            </div>
                            <div class="d-flex justify-content-center align-items-center mt-3 mb-5">
                                <a href="javascript:void(0);" class="dynamic-link button-round reg_register make_payment"
                                    data-url="{{ route('payment.index') }}" data-class="link-payment">Make Payment</a>
                            </div>
                        @endif
                    </div> 
                </div>
                @else
                @if(!empty($paymentApprovePending))
                <div class="donate-form form-inner p-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="contact-detail-container item-center" style="align-items: center;text-align: center;">
                            <div class="pb-1">
                            <span style="height: 75px;color:#F15B43;"><i class="fa fa-info-circle" style="font-size: 28px;"></i></span>
                            </div>
                            <div style="font-size: 12px; color: #333; text-align: center;">
                                 <h2 style="font-size: 25px;">Payment Verification Pending</h2>
                                 <p style="font-size: 16px; color: #333; text-align: center;">Your registration is subject to verification and will be approved within 24 hrs</p>
                            </div>
                            <!-- <div style="font-size: 16px; color: #333; text-align: center;">
                            <p>Your registration is subject to verification and will be approved within 24 hrs</p>
                            </div>
                          -->
                            </div>
                        </div>
                       
                    </div> 
                </div>
                @elseif(!empty($paymentApproveRejected))
                <!-- <div class="donate-form form-inner">
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <div class="contact-detail-container item-center" style="align-items: center;text-align: center;">
                            <div class="pb-4">
                            <span style="height: 75px;color:#F15B43;"><i class="fa fa-info-circle" style="font-size: 45px;"></i></span>
                            </div>
                            <div style="font-size: 12px; color: #333; text-align: center;">
                                 <h2 style="font-size: 40px;">Payment Rejected</h2>
                            </div>
                            <div style="font-size: 16px; color: #333; text-align: center;">
                            <p>Payment with  Reference{{$paymentApproveRejected->payment_reference}} and amount  ${{$paymentApproveRejected->amount}} is rejected.</p>
                            </div>
                            @if($payment_summary['amountPayable']>0)
                            <div class="col-md-12 d-flex justify-content-center">
                                <h5 style="font-weight: bold;">
                                    Payment Pending : ${{$payment_summary['amountPayable'] }}
                                </h5>
                            </div>
                            <div class="d-flex justify-content-center align-items-center mt-3 mb-5">
                                <a href="javascript:void(0);" class="dynamic-link button-round reg_register make_payment"
                                    data-url="{{ route('payment.index') }}" data-class="link-payment">Make Payment</a>
                            </div>
                            @endif
                            </div>
                        </div>
                       
                    </div> 
                </div> -->
                @endif
              
                <div id="registration_div" >
                    @php
                        $userAbstracts = isset($abstracts) ? $abstracts : collect();
                        $isSubmittedCurrent = isset($abstract) && (int) ($abstract->status ?? 0) === 1;
                    @endphp

                    @if($userAbstracts->count())
                        <div class="donate-form form-inner" style="margin-bottom: 20px;">
                            <h4 style="margin-bottom: 15px;">Your Abstracts</h4>
                            <table class="table table-bordered align-middle abstract-table" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>PID</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th style="width: 160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userAbstracts as $item)
                                        <tr>
                                            <td>{{ $item->pid }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($item->abstract_title, 60) }}</td>
                                            <td>
                                                @if((int) $item->status === 1)
                                                    <span class="badge bg-success">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if((int) $item->status === 0)
                                                    <button type="button"
                                                            class="btn btn-sm btn-primary"
                                                            onclick="loadAbstract('{{ route('abstract.edit', $item->id) }}')">
                                                        Edit
                                                    </button>
                                                @else
                                                    <span class="text-muted small">View only</span>
                                                @endif
                                                @if((int) $item->status === 0)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDeleteAbstract('{{ route('abstract.destroy', $item->id) }}')">
                                                        Delete
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($userAbstracts->count() < 2)
                                <div class="text-end">
                                    <button type="button"
                                            class="outline-round"
                                            style="font-size: 13px;"
                                            onclick="loadAbstract('{{ route('abstract.create') }}')">
                                        + Add New Abstract
                                    </button>
                                </div>
                            @else
                                <p style="font-size: 13px; color: #666; margin-top: 8px;">
                                    You have reached the maximum of 2 abstracts for this registration.
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="donate-form form-inner" style="margin-bottom: 20px;">
                            <h4>Your Abstracts</h4>
                            <p style="font-size: 14px; color: #666; margin-bottom: 0;">
                                No abstracts submitted yet. Use the form below to create a draft or submit your first abstract.
                            </p>
                        </div>
                    @endif

                    <form  method="post" class="donate-form" action="{{optional($abstract)->id ?route('abstract.update',optional($abstract)->id)  : route('abstract.store')}}" id="abstract-form">
                        @csrf
                        @if(isset($abstract))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="submit_action" id="submit_action" value="draft">

                        <fieldset class="donate-form form-inner border-0 m-0 p-0" @if($isSubmittedCurrent) disabled @endif>

                            <h4>Enter Abstract Details</h4>
                            <!-- Category -->
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    @php
                                        $selectedCategoryId = (isset($abstract) && $abstract->category_id)
                                            ? (int) $abstract->category_id
                                            : null;
                                    @endphp
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select id="category_id" name="category_id" class="abstract-form-control">
                                        <option value="" disabled @if($selectedCategoryId === null) selected @endif>Select Category</option>
                                        @if($categories && $categories->count() > 0)
                                        @foreach($categories as $category)
                                            @php
                                                $displayName = $category->name;
                                                if ($category->grade_from && $category->grade_to) {
                                                    // Helper function for ordinal suffix
                                                    $getOrdinal = function($num) {
                                                        $suffix = 'th';
                                                        $lastDigit = $num % 10;
                                                        $lastTwoDigits = $num % 100;
                                                        
                                                        if ($lastTwoDigits < 10 || $lastTwoDigits > 20) {
                                                            if ($lastDigit == 1) {
                                                                $suffix = 'st';
                                                            } elseif ($lastDigit == 2) {
                                                                $suffix = 'nd';
                                                            } elseif ($lastDigit == 3) {
                                                                $suffix = 'rd';
                                                            }
                                                        }
                                                        return $num . $suffix;
                                                    };
                                                    $displayName .= ' -- ' . $getOrdinal($category->grade_from) . ' Grade to ' . $getOrdinal($category->grade_to) . ' Grade';
                                                } elseif ($category->name == 'Special School' || $category->type == 1) {
                                                    $displayName .= ' -- No age restrictions';
                                                }
                                            @endphp
                                            <option value="{{ $category->id }}"
                                                    @if($selectedCategoryId !== null && (int) $category->id === $selectedCategoryId) selected @endif>
                                                {{ $displayName }}
                                            </option>
                                        @endforeach
                                        @else
                                        <option value="" disabled>No categories available. Please contact administrator.</option>
                                        @endif
                                    </select>
                                    <div id="category_error" class="category_id1" style="color: #F15B43; display: none;">Category is required.</div>
                                </div>                                
                                <div class="col-md-6 form-group">
                                    <label for="abstract_domain" class="form-label">Abstract Topic Domain *</label>
                                    <select id="abstract_domain" name="abstract_domain" class="abstract-form-control">
                                        <option value="" disabled selected>Select Topic Domain</option>
                                        @foreach($abstractDomains as $domain)
                                            <option
                                                value="{{ $domain->id }}"
                                                @if($domain->id == $selectedDomainId) selected @endif>
                                                {{ $domain->domain }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="domain_error" class="abstract_domain1"  style="color: #F15B43; display: none;">Abstract Topic Domain is required.</div>
                                </div>


                            </div>

                            <!-- Student Authors -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <fieldset class="border-0 p-0 m-0">
                                        <legend class="form-label float-none w-auto p-0 mb-0" style="font-size: 1rem;">Student Authors *</legend>
                                    <div class="border p-3 form-control form-control-abstract mt-1" style="max-height: 90px; overflow-y: auto; border-radius: 5px;">
                                        @foreach($participants as $participant)
                                            @if($participant->type === 0)
                                                <div>
                                                    <input
                                                        type="checkbox"
                                                        id="participant_{{ $participant->id }}"
                                                        name="student_authors[]"
                                                        value="{{ $participant->first_name }} {{ $participant->last_name }}"
                                                        class="abstract-form-control"
                                                        @if(in_array($participant->first_name . ' ' . $participant->last_name, $storedAuthors)) checked @endif
                                                    >
                                                    <label for="participant_{{ $participant->id }}">{{ $participant->first_name }} {{ $participant->last_name }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div id="authors_error"  style="color: #F15B43; display: none;">At least one student author must be selected.</div>
                                    </fieldset>
                                </div>

                                <!-- Additional Authors -->
                                <div class="col-md-6">
                                    <label for="additional_authors" class="form-label">Additional Authors</label>
                                    <textarea id="additional_authors" name="additional_authors" class="form-control abstract-form-control" rows="3" placeholder="Enter author names, separated by commas">{{optional($abstract)->additional_authors}}</textarea>
                                    <div id="additional_authors_error" class="additional_authors1" style="color: #F15B43; display: none;">Please enter valid author names, separated by commas.</div>
                                </div>
                            </div>

                            <!-- Corresponding Author -->
                            <div class="row mt-5">
                                <!-- Institution Name -->
                                <div class="col-md-6 form-group">
                                    <label for="correspondent_author_name" class="form-label">Corresponding Author *</label>
                                    <input type="text" id="correspondent_author_name" name="correspondent_author_name" class="form-control abstract-form-control" value="{{$user->first_name. ' ' . $user->last_name}}">
                                    <div id="correspondent_author_name_error" class="correspondent_author_name1" style="color: #F15B43; display: none;">Corresponding Author Name  is required.</div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="correspondent_author" class="form-label">Corresponding Author Email *</label>
                                    <input type="email" id="correspondent_author" name="correspondent_author" value="{{$user->email}}" class="form-control abstract-form-control">
                                    <div id="email_error" class="correspondent_author1" style="color: #F15B43; display: none;">Please enter a valid corresponding author email.</div>
                                </div>
                                <!-- Abstract Title -->
                                <div class="col-lg-12 col-12 form-group">
                                    <label>Abstract Title *</label>
                                    <input type="text"  id="abstract_title" class="abstract-form-control" name="abstract_title" value="{{optional($abstract)->abstract_title}}">
                                    <div id="title_error" class="abstract_title1" style="color: #F15B43; display: none;">Abstract Title is required.</div>
                                </div>
                                <!-- Abstract Content -->
                                <div class="col-lg-12 col-12 form-group">
                                    <label>Abstract <span style="font-size: 13px; color:gray">{{'(Maximum 250 words)'}}</span> *</label>
                                    <textarea class="abstract-form-control" id="abstractDescription" name="abstract_content" placeholder="Maximum 250 words"  rows="20">{{optional($abstract)->abstract_content}}</textarea>
                                    <small class="text-muted d-block mt-1">Limit is <strong>250 words</strong>, not characters. Pressing Enter for a new line does not add a word; only spaces between words are counted.</small>
                                    <span id="word_error" style="color: #F15B43; display: block;"></span>
                                    <div id="content_error"  class="abstract_content1" style="color: #F15B43; display: none;">Abstract is required.</div>
                                </div>
                                <!-- References -->
                                 
                                <div class="col-lg-12 col-12 form-group repeater-2" id="abstractReferenceDiv">
                                <label>References *</label>
                                <div data-repeater-list="references">
                                @if(optional($abstract)->references)
                                @php $references=json_decode(optional($abstract)->references);@endphp
                                @if(!empty($references))
                                @foreach($references as $reference)
                                <div data-repeater-item>
                                   <div class="row">  
                                   <div class="col-lg-12 col-12 form-group">
                                    <textarea class="abstract-form-control"  name="references" class="form-control" rows="3">{{$reference}}</textarea>
                                   </div>
                                   <div id="reference_error" class="references1" style="color: #F15B43; display: none;">Reference is required.</div>
                                </div>
                                </div>
                                @endforeach
                                @else
                                <div data-repeater-item>
                                   <div class="row">  
                                   <div class="col-lg-12 col-12 form-group">
                                    <textarea class="abstract-form-control"  name="references" class="form-control" rows="3"></textarea>
                                   </div>
                                   <div id="reference_error" class="references1" style="color: #F15B43; display: none;">Reference is required.</div>
                                </div>
                                </div>
                                @endif
                                @else
                                <div data-repeater-item>
                                   <div class="row">  
                                   <div class="col-lg-12 col-12 form-group">
                                    <textarea class="abstract-form-control"  name="references" class="form-control" rows="3"></textarea>
                                   </div>
                                   <div id="reference_error" class="references1" style="color: #F15B43; display: none;">Reference is required.</div>
                                </div>
                                </div>
                                @endif
                                </div>
                                </div>

                                <!-- <div class="col-lg-12 col-12 form-group repeater-2" id="abstractReferenceDiv" >
                                <button type="button" class="btn btn-primary mb-3" data-repeater-create>Add</button>
                                    <div data-repeater-list="group-a">
                                        <div data-repeater-item>
                                            <div class="row">
                                                <div class="col-md-2 col-sm-12 mb-3">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input type="text" class="form-control" name="username" id="username" placeholder="Username">
                                                </div>
                                                <div class="col-md-2 col-sm-12 mb-3">
                                                    <label for="phone" class="form-label">Phone</label>
                                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter email id">
                                                </div>
                                                <div class="col-md-2 col-sm-12 mb-3">
                                                    <div><label class="form-label"> </label></div>
                                                    <button type="button" class="btn btn-danger btn-floating" data-repeater-delete>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>

                        </fieldset>
                        @if(!$isSubmittedCurrent)
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="button"
                                        class="outline-round"
                                        onclick="setAbstractActionAndSubmit('draft');">
                                    Save as Draft
                                </button>
                                <button type="button"
                                        class="button-round"
                                        onclick="setAbstractActionAndSubmit('submit');">
                                    Submit Abstract
                                </button>
                            </div>
                        @else
                            <p style="margin-top: 15px; color: #28a745; font-weight: 600;">
                                This abstract has been submitted and can no longer be edited.
                            </p>
                        @endif
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script src="{{ url('libs/form-repeater/repeater.min.js')}}"></script>
<script>
 

   $(document).ready(function () {
       // Capture existing reference values BEFORE repeater init (repeater can clear them)
       var existingRefValues = [];
       $('#abstractReferenceDiv [data-repeater-item] textarea').each(function() {
           existingRefValues.push($(this).val());
       });

       $('.repeater-2').repeater({
        // isFirstItemUndeletable: true,
          hide: function (deleteElement) {
            if ($('[data-repeater-item]').length > 1) {
                $(this).slideUp(deleteElement); 
            } else {
                alert("At least one row is required.");
            }
        }
       });

       // Restore reference values AFTER repeater init (in case repeater cleared them)
       if (existingRefValues.length > 0) {
           setTimeout(function() {
               $('#abstractReferenceDiv [data-repeater-item] textarea').each(function(index) {
                   if (index < existingRefValues.length && existingRefValues[index]) {
                       $(this).val(existingRefValues[index]);
                   }
               });
           }, 50);
       }

        $('.make_payment').on('click', function (e) {
            $("html, body").animate({ scrollTop: $(document).height()-1200 }, 1000);
        });
        // dynamic-link handler is managed by the parent page (index.blade.php) via event delegation
        $("input[name='student_authors[]']").on("change", function () {

            validateStudentAuthors();

            var selectedParticipants = [];


            $("input[name='student_authors[]']:checked").each(function () {
                selectedParticipants.push($(this).attr('id').split('_')[1]);
            });


            if (selectedParticipants.length > 0) {
                $.ajax({
                    url: '{{ route("participants-category") }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        participants: selectedParticipants
                    },
                    success: function (response) {
                        if (response.error === 0) {
                            var categorySelect = $("#category_id");
                            var previousCategoryId = categorySelect.val();
                            categorySelect.empty();
                            categorySelect.append('<option value="" disabled>Select Category</option>');
                            $.each(response.categories, function (index, category) {
                                categorySelect.append(
                                    '<option value="' + category.id + '">' + category.name + '</option>'
                                );
                            });
                            if (previousCategoryId) {
                                categorySelect.val(previousCategoryId);
                            }
                            if (!categorySelect.val()) {
                                categorySelect.find('option[value=""]').first().prop('selected', true);
                            }
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }
        });


        var valid = 1;
       // $(document).on('input', '.abstract-form-control', function (obj) {

       function validateStudentAuthors() {
            var selectedAuthors = $("input[name='student_authors[]']:checked").length;

            if (selectedAuthors === 0) {
                $("#authors_error").show();
                return false;
            } else {
                $("#authors_error").hide();
                return true;
            }
        }

        $(".abstract-form-control").on("input", function () {
            name = $(this).attr('name')+1;
            $('.' + name).hide();
        });
      
        $("#abstract_title").on("input", function () {
            if ($(this).val().trim() === "") {
                $("#title_error").show();
                valid = 0;
            } else {
                $("#title_error").hide();
            }
        });

        $("#category_id").on("change", function () {
            if ($(this).val() === "") {
                $("#category_error").show();
                valid = 0;
            } else {
                $("#category_error").hide();
            }
        });

        $("#abstract_domain").on("change", function () {
            if ($(this).val() === "") {
                $("#domain_error").show();
                valid = 0;
            } else {
                $("#domain_error").hide();
            }
        });

        $("#additional_authors").on("input", function () {
            var authors = $(this).val().trim();
            if (authors && !/^[a-zA-Z0-9, ]+$/.test(authors)) {
                $("#additional_authors_error").show();
                valid = 0;
            } else {
                $("#additional_authors_error").hide();
            }
        });

        $("#correspondent_author").on("input", function () {
            var email = $(this).val();
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if (!emailPattern.test(email)) {
                $("#email_error").show();
                valid = 0;
            } else {
                $("#email_error").hide();
            }
        });

        function updateWordCount() {
            var abstractText = $("#abstractDescription").val();
            var normalized = $.trim(String(abstractText || '').replace(/\s+/g, ' '));
            var wordCount = normalized === '' ? 0 : normalized.split(/\s+/).filter(Boolean).length;

            if (wordCount > 250) {
                $("#word_error").html('The abstract may contain at most 250 words. Line breaks and extra spaces do not count as extra words—remove some words to continue.');
                $("#word_error").show();
            } else {
                $("#word_error").html(wordCount + ' words out of 250');
                $("#word_error").show();
            }
        }

        // Handle typing events
        $("#abstractDescription").on("input", function () {
            updateWordCount();
        });

        // Handle paste events
        $("#abstractDescription").on("paste", function (e) {
            var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            var pastedData = clipboardData.getData('Text');

            var abstractText = $("#abstractDescription").val();
            var curNorm = $.trim(String(abstractText || '').replace(/\s+/g, ' '));
            var currentWordCount = curNorm === '' ? 0 : curNorm.split(/\s+/).filter(Boolean).length;
            var pasteNorm = $.trim(String(pastedData || '').replace(/\s+/g, ' '));
            var pastedWordCount = pasteNorm === '' ? 0 : pasteNorm.split(/\s+/).filter(Boolean).length;

            if (currentWordCount + pastedWordCount > 250) {
                e.preventDefault();
                $("#word_error").html('The abstract may contain at most 250 words (extra spaces ignored when counting).');
                $("#word_error").show();
            }
        });

        // Submit is now handled by the browser (no AJAX) so that after save
        // the user stays on the abstract page via controller redirects.
    });

    // Set submit_action and trigger form submit (used by Save as Draft / Submit buttons)
    function setAbstractActionAndSubmit(action) {
        $('#submit_action').val(action);
        $('#abstract-form').trigger('submit');
    }

    // Helper to load a specific abstract into the form via AJAX
    function loadAbstract(url) {
        $('#loading').show();
        $.ajax({
            url: url,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (html) {
                $('#loading').hide();
                // Replace the whole donate-section wrapper to refresh list + form
                var wrapper = $('.donate-section').first();
                wrapper.replaceWith(html);
            },
            error: function () {
                $('#loading').hide();
                swal("Error", "Unable to load abstract. Please try again.", "error");
            }
        });
    }

    // Helper to confirm and delete via AJAX, then stay on abstract page
    function confirmDeleteAbstract(deleteUrl) {
        swal({
            title: "Are you sure?",
            text: "Do you really want to delete this abstract?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(function (willDelete) {
            if (!willDelete) {
                return;
            }

            $('#loading').show();

            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#loading').hide();
                    if (response.error === 0) {
                        swal("Deleted", response.message || "Abstract deleted successfully.", "success")
                            .then(function () {
                                if (response.reload_url) {
                                    loadAbstract(response.reload_url);
                                } else {
                                    location.reload();
                                }
                            });
                    } else {
                        swal("Error", response.message || "Unable to delete abstract.", "error");
                    }
                },
                error: function () {
                    $('#loading').hide();
                    swal("Error", "Unable to delete abstract. Please try again.", "error");
                }
            });
        });
    }
</script>