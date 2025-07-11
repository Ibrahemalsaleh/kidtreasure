@extends('admin.layouts.app')

@section('title', 'Create Workshop Event')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .image-preview {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image-preview-item {
            position: relative;
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 25px;
            cursor: pointer;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            line-height: 38px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Create Workshop Event</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.workshop-events.index') }}">Workshop Events</a></li>
            <li class="breadcrumb-item active">Create Event</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-plus me-1"></i>
                Event Details
            </div>
            <div class="card-body">

                <form action="{{ route('admin.workshop-events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @if(request()->has('redirect_to'))
                        <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                    @endif
                    
                    @if(request()->has('workshop_id'))
                        <input type="hidden" name="workshop_id" value="{{ request('workshop_id') }}">
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                            name="title" value="{{ old('title') }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="workshop_id" class="form-label">Associated Workshop Template</label>
                                        <select class="form-select workshop-select @error('workshop_id') is-invalid @enderror"
                                            id="workshop_id" name="workshop_id" {{ request()->has('workshop_id') ? 'disabled' : '' }}>
                                            <option value="">-- Select Workshop Template (Optional) --</option>
                                            @foreach($workshopTemplates as $template)
                                                <option value="{{ $template->id }}" 
                                                    {{ (old('workshop_id') == $template->id || (isset($selectedWorkshopId) && $selectedWorkshopId == $template->id)) ? 'selected' : '' }}>
                                                    {{ $template->name_en }} ({{ $template->target_age_group }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('workshop_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Linking to a workshop template is optional but
                                            recommended for consistency.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Event Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                    name="description" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="duration_hours" class="form-label">Duration (hours) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('duration_hours') is-invalid @enderror"
                                            id="duration_hours" name="duration_hours" value="{{ old('duration_hours', 2) }}" step="0.5"
                                            min="0.5" required>
                                        @error('duration_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="max_attendees" class="form-label">Maximum Attendees <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('max_attendees') is-invalid @enderror"
                                            id="max_attendees" name="max_attendees" value="{{ old('max_attendees', 20) }}" min="1"
                                            required>
                                        @error('max_attendees')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="price_jod" class="form-label">Price (JOD) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('price_jod') is-invalid @enderror"
                                            id="price_jod" name="price_jod" value="{{ old('price_jod', 0) }}" step="0.01" min="0"
                                            required>
                                        @error('price_jod')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Use 0 for free events</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="event_date" class="form-label">Event Date <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('event_date') is-invalid @enderror"
                                            id="event_date" name="event_date" value="{{ old('event_date') }}" required>
                                        @error('event_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                                            id="location" name="location" value="{{ old('location') }}" required>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="age_group" class="form-label">Target Age Group <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('age_group') is-invalid @enderror" id="age_group"
                                    name="age_group" value="{{ old('age_group') }}" required>
                                @error('age_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Example: "5-7 years", "8-12 years", "Adults", "All ages"</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-images me-1"></i>
                                    Event Images
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                        <div class="mb-3">
                                                <label for="image_path" class="form-label">Main Image</label>
                                                <input type="file" class="form-control @error('image_path') is-invalid @enderror" id="image_path" name="image_path" accept="image/*">
                                                @error('image_path')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">This will be the primary image shown for the event.</small>
                                                <div id="main-image-preview-container" class="mt-2 d-none">
                                                    <img id="main-image-preview" src="#" alt="Main image preview" class="img-thumbnail" style="max-height: 150px;">
                                                    <div class="d-flex mt-1 gap-2">
                                                        <button type="button" class="btn btn-danger btn-sm remove-image-btn" data-type="main">
                                                            <i class="fas fa-trash-alt"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="mb-3">
                                        <label for="gallery_images" class="form-label">Gallery Images</label>
                                        <div class="d-flex gap-2 mb-2">
                                            <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" id="gallery_images" name="gallery_images[]" multiple accept="image/*">
                                            <button type="button" class="btn btn-success" id="add-gallery-images-btn">
                                                 Add
                                            </button>
                                        </div>
                                        @error('gallery_images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">You can upload multiple images for the event gallery.</small>
                                        <div id="gallery-preview-container" class="mt-2 d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <i class="fas fa-cog me-1"></i>
                                    Event Settings
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_open_for_registration" name="is_open_for_registration" value="1" {{ old('is_open_for_registration', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_open_for_registration">Open for Registration</label>
                                        <small class="form-text text-muted d-block">Enable to allow users to register for this event.</small>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Featured Event</label>
                                        <small class="form-text text-muted d-block">Featured events may be highlighted on the homepage or in promotional areas.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('admin.workshop-events.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Workshop Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this image?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-remove-btn">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="resultModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date picker
            flatpickr("#event_date", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                minDate: "today"
            });

            // Initialize select2
            $('.workshop-select').select2({
                placeholder: "Select a workshop template",
                allowClear: true
            });

            // Initialize Bootstrap modals
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            const confirmRemoveBtn = document.getElementById('confirm-remove-btn');
            
            let currentRemoveType = null;
            let currentItemToRemove = null;
            let currentGalleryIndex = null;

            // Main image preview
            const mainImageInput = document.getElementById('image_path');
            const mainImagePreview = document.getElementById('main-image-preview');
            const mainImagePreviewContainer = document.getElementById('main-image-preview-container');

            if (mainImageInput) {
                mainImageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            mainImagePreview.src = e.target.result;
                            mainImagePreviewContainer.classList.remove('d-none');
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }

            // Gallery images preview
            const galleryImagesInput = document.getElementById('gallery_images');
            const galleryPreviewContainer = document.getElementById('gallery-preview-container');

            if (galleryImagesInput) {
                galleryImagesInput.addEventListener('change', function() {
                    if (this.files) {
                        updateGalleryPreview(this.files);
                    }
                });
            }

            // Function to update gallery preview
            function updateGalleryPreview(files) {
                galleryPreviewContainer.innerHTML = '';
                
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    const previewItem = document.createElement('div');
                    previewItem.className = 'position-relative gallery-image-item';
                    previewItem.dataset.index = index;
                    
                    reader.onload = function(e) {
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" 
                                style="height: 100px; width: 100px; object-fit: cover;"
                                alt="Gallery image ${index + 1}">
                            <button type="button"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 rounded-circle gallery-remove-btn"
                                style="width: 24px; height: 24px;" data-index="${index}">
                                <i class="fas fa-times" style="font-size: 12px;"></i>
                            </button>
                        `;
                        galleryPreviewContainer.appendChild(previewItem);
                        
                        // Add event listener to the newly created remove button
                        const removeBtn = previewItem.querySelector('.gallery-remove-btn');
                        if (removeBtn) {
                            removeBtn.addEventListener('click', function() {
                                currentRemoveType = 'gallery';
                                currentItemToRemove = previewItem;
                                currentGalleryIndex = index;
                                confirmationModal.show();
                            });
                        }
                    }
                    
                    reader.readAsDataURL(file);
                });
            }

            // Add gallery images button
            const addGalleryImagesBtn = document.getElementById('add-gallery-images-btn');
            if (addGalleryImagesBtn) {
                addGalleryImagesBtn.addEventListener('click', function() {
                    const newInput = document.createElement('input');
                    newInput.type = 'file';
                    newInput.accept = 'image/*';
                    newInput.multiple = true;
                    newInput.style.display = 'none';
                    document.body.appendChild(newInput);
                    
                    newInput.addEventListener('change', function() {
                        if (this.files && this.files.length > 0) {
                            // Create a merged FileList from existing and new files
                            const existingFiles = galleryImagesInput.files || [];
                            const newFiles = this.files;
                            const dataTransfer = new DataTransfer();
                            
                            Array.from(existingFiles).forEach(file => {
                                dataTransfer.items.add(file);
                            });
                            
                            Array.from(newFiles).forEach(file => {
                                dataTransfer.items.add(file);
                            });
                            
                            galleryImagesInput.files = dataTransfer.files;
                            updateGalleryPreview(dataTransfer.files);
                        }
                        
                        // Remove the temporary input
                        document.body.removeChild(newInput);
                    });
                    
                    newInput.click();
                });
            }

            // Main image remove button
            document.querySelectorAll('.remove-image-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentRemoveType = this.dataset.type;
                    confirmationModal.show();
                });
            });

            // Handle confirmation button click
            confirmRemoveBtn.addEventListener('click', function() {
                confirmationModal.hide();
                
                if (currentRemoveType === 'main') {
                    console.log('Removing main image');
                    
                    // Clear the input and hide the preview
                    mainImageInput.value = '';
                    mainImagePreviewContainer.classList.add('d-none');
                    
                    // Show success message
                    document.getElementById('resultModalLabel').textContent = 'Success';
                    document.getElementById('resultModalBody').textContent = 'Image removed successfully';
                    document.getElementById('resultModalBody').className = 'modal-body text-success';
                    resultModal.show();
                } else if (currentRemoveType === 'gallery' && currentItemToRemove) {
                    console.log('Removing gallery image at index:', currentGalleryIndex);
                    
                    // Remove the item from the DOM
                    currentItemToRemove.remove();
                    
                    // Remove the file from the input
                    const files = Array.from(galleryImagesInput.files);
                    files.splice(currentGalleryIndex, 1);
                    
                    // Create a new FileList
                    const dataTransfer = new DataTransfer();
                    files.forEach(file => {
                        dataTransfer.items.add(file);
                    });
                    
                    // Update the input
                    galleryImagesInput.files = dataTransfer.files;
                    
                    // Update the preview to refresh indices
                    updateGalleryPreview(dataTransfer.files);
                    
                    // Show success message
                    document.getElementById('resultModalLabel').textContent = 'Success';
                    document.getElementById('resultModalBody').textContent = 'Gallery image removed successfully';
                    document.getElementById('resultModalBody').className = 'modal-body text-success';
                    resultModal.show();
                }
                
                // Reset variables
                currentRemoveType = null;
                currentItemToRemove = null;
                currentGalleryIndex = null;
            });
        });
    </script>
@endsection