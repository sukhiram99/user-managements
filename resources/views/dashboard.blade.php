{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .dashboard-card {
            border-radius: 12px;
            transition: .3s;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, .15);
        }
    </style>

    @php
        use App\Models\UserRemark;

        // Fetch all unseen remarks
        $userDetails = UserRemark::with('user')
            ->whereNotNull('old_remark')
            ->whereNull('seen_user_id')
            ->orderByDesc('id')
            ->get();

        $remarkCount = $userDetails->count();
        $firstUserDetail = $userDetails->first();
    @endphp

    <div class="mb-4">
        <div class="p-4 bg-light border rounded shadow-sm">
            <h4 class="mb-1">Welcome back, <strong>{{ auth()->user()->name }}</strong> 👋</h4>

            {{-- Show message button only for manager --}}
            @if (auth()->user()->is_manager)
                @if ($firstUserDetail)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userDetailsModal"
                        data-user-id="{{ $firstUserDetail->id }}" onclick="showUserDetails(this)">
                        <i class="bi bi-chat-dots me-1"></i>
                        Messages ({{ $remarkCount }})
                    </button>
                @else
                @endif

            @endif

            <p class="text-muted mb-0">
                Roles: <strong>{{ auth()->user()->roles->pluck('name')->implode(', ') }}</strong>
            </p>
        </div>
    </div>

    {{-- MANAGER DASHBOARD --}}
    @if (auth()->user()->is_manager)
        <h4 class="fw-bold mb-3">Manager Overview</h4>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card dashboard-card text-white bg-primary">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Users</h6>
                            <h2>{{ \App\Models\User::count() }}</h2>
                        </div>
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card dashboard-card text-white bg-success">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Roles</h6>
                            <h2>{{ \App\Models\Role::count() }}</h2>
                        </div>
                        <i class="bi bi-shield-lock fs-1"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card dashboard-card text-white bg-warning">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Permissions</h6>
                            <h2>{{ \App\Models\Permission::count() }}</h2>
                        </div>
                        <i class="bi bi-key fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- USER DASHBOARD --}}
    @if (!auth()->user()->is_manager)
        <h4 class="fw-bold mt-4 mb-3">Your Dashboard</h4>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card dashboard-card border-info">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-info mb-1">Your Role(s)</h6>
                            <h3>{{ auth()->user()->roles->pluck('name')->implode(', ') }}</h3>
                        </div>
                        <i class="bi bi-person-badge text-info fs-1"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card dashboard-card border-primary">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-primary mb-1">Account Created</h6>
                            <h3>{{ auth()->user()->created_at->format('M d, Y') }}</h3>
                        </div>
                        <i class="bi bi-calendar-check text-primary fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- USER DETAIL MODAL --}}
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeDetails()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span id="userName"></span></p>
                    <p><strong>Email:</strong> <span id="userEmail"></span></p>
                    <p><strong>Remarks:</strong> <span id="userRemarks"></span></p>
                    <p class="d-none"><strong>RemarkId:</strong> <span id="RemarkId"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeDetails()"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<script>
    function showUserDetails(button) {
        const remarkId = button.getAttribute('data-user-id'); // holds remark ID

        $.ajax({
            url: "{{ url('/manager/users/remark-details') }}/" + remarkId,
            type: "GET",
            success: function(response) {
                // Example: { status: true, data: { user: {...}, new_remark: "...", ... } }

                if (response && response.status && response.data) {
                    const user = response.data.user || {};
                    const newRemark = response.data.new_remark ?? response.data.remark ?? response.data
                        .text ?? '';

                    $("#userName").text(user.name ?? '—');
                    $("#userEmail").text(user.email ?? '—');
                    $("#userRemarks").text(newRemark || 'No remarks found');
                    $("#RemarkId").text(remarkId ?? '—');

                } else {
                    $("#userName").text('—');
                    $("#userEmail").text('—');
                    $("#userRemarks").text('No remarks found');
                    $("#RemarkId").text(remarkId ?? '—');

                }
            },
            error: function() {
                alert("Error fetching user data");
            }
        });
    }
</script>


<script>
    function closeDetails() {
        const remarkId = $('#RemarkId').text(); // holds remark ID

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $.ajax({
            url: "{{ url('/manager/users/close-remark-details') }}",
            type: "POST",
            data: {
                id: remarkId,

            },
            success: function(response) {
                console.log('Close recorded:', response.message);
                location.reload();

            },
            error: function(xhr) {
                console.error('Error recording close:', xhr.responseText);
            }
        });
    }
</script>
