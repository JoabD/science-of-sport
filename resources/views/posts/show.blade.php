@extends('layouts.public')
@section('title', 'Event Directory - Science of Sport')

@section('content')
    <!-- Hero Section -->
    <header class="hero-section text-center py-5 text-white">
        <div class="container">
            <h1 class="display-4 fw-bold">Event Directory</h1>
            <h3 class="fw-light mb-4">Science of Sport Tournaments</h3>

            <!-- Creation Button with Auth Logic -->
            @can('create', \App\Models\Post::class)
                <button class="btn btn-lg btn-light fw-bold shadow-sm text-brand-blue" data-bs-toggle="modal" data-bs-target="#createEventModal">
                    + Create New Event
                </button>
            @else
                @guest
                    <button class="btn btn-lg btn-warning fw-bold shadow-sm" onclick="showAuthToast('You must log in as an Admin to create an event.')">
                        + Create New Event
                    </button>
                @else
                    <button class="btn btn-lg btn-warning fw-bold shadow-sm" onclick="showAuthToast('Only Administrators can perform this action.')">
                        + Create New Event
                    </button>
                @endguest
            @endcan
        </div>
    </header>

    <div class="container mt-5 mb-5">

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="text-center my-4" style="display: none;">
            <div class="spinner-border" style="color: var(--sos-green);" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive shadow-sm rounded bg-white">
            <table class="table table-hover table-striped align-middle border mb-0">
                <thead class="text-white bg-brand-blue">
                <tr>
                    <th scope="col" class="py-3 px-4">Date</th>
                    <th scope="col" class="py-3">Event Name</th>
                    <th scope="col" class="py-3">Location</th>
                    <th scope="col" class="py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody id="eventsContainer">
                <!-- Data injected via JS -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Event pagination" id="paginationNav" class="mt-4" style="display: none;">
            <ul class="pagination justify-content-center" id="paginationList"></ul>
        </nav>
    </div>

    <!-- Create / Edit Event Modal (shared, see resources/js/public.js) -->
    @php
        $editingPostId = old('post_id');
        $isEditingEvent = $editingPostId !== null && $editingPostId !== '';
    @endphp
    <div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white bg-brand-blue">
                    <h5 class="modal-title fw-bold" id="createEventModalLabel">{{ $isEditingEvent ? 'Edit Event' : 'Create New Event' }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ $isEditingEvent ? route('posts.update', $editingPostId) : route('posts.store') }}"
                      method="POST"
                      data-create-action="{{ route('posts.store') }}">
                    @csrf
                    <input type="hidden" name="_method" value="{{ $isEditingEvent ? 'PUT' : '' }}">
                    <input type="hidden" name="form" value="create-event">
                    <input type="hidden" name="post_id" value="{{ $editingPostId }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Event Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="subtitle" class="form-label fw-bold">Subtitle</label>
                            <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle') }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label fw-bold">Event Date</label>
                                <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date" name="event_date" value="{{ old('event_date') }}" required>
                                @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label fw-bold">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="overview" class="form-label fw-bold">Overview</label>
                            <textarea class="form-control @error('overview') is-invalid @enderror" id="overview" name="overview" rows="4" required>{{ old('overview') }}</textarea>
                            @error('overview')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn fw-bold btn-brand">{{ $isEditingEvent ? 'Save Changes' : 'Save Event' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white bg-brand-blue">
                    <h5 class="modal-title fw-bold" id="eventDetailsModalLabel">Event Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="eventDetailsBody">
                    <!-- Injected via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="authToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toastMessage">
                    <!-- Message injected via JS -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection
