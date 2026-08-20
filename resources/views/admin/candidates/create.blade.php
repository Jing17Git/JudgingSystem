@extends('layouts.admin')

@section('title', 'Add Candidate')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
    .cropper-modal-container {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        padding: 1rem;
    }
    .cropper-area-wrapper {
        max-height: 52vh;
        min-height: 280px;
        background: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 12px;
    }
    .cropper-area-wrapper img {
        max-width: 100%;
        max-height: 52vh;
        display: block;
    }
    .cropper-view-box,
    .cropper-face {
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-[var(--text-muted)] mb-2">
            <a href="{{ route('admin.candidates.index') }}" class="hover:text-[var(--green-600)] transition-colors">Candidate Management</a>
            <span>/</span>
            <span class="text-[var(--text-secondary)]">Add New</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            Add Candidate
        </h1>
    </div>
    <div>
        <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
    </div>
</div>

<div class="panel animate-fade-in-up delay-100 max-w-2xl">
    <div class="panel-body">
        <form action="{{ route('admin.candidates.store') }}" method="POST" enctype="multipart/form-data" id="candidateForm">
            @csrf

            {{-- Hidden input for cropped Base64 image --}}
            <input type="hidden" name="cropped_picture_data" id="cropped_picture_data">

            <div class="space-y-6">
                <div class="form-group">
                    <label for="candidate_number" class="form-label">Candidate Number</label>
                    <input type="number" id="candidate_number" name="candidate_number" class="form-input @error('candidate_number') border-[var(--danger)] @enderror" value="{{ old('candidate_number') }}" required min="1" placeholder="e.g. 1">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Unique candidate number per gender division.</p>
                    @error('candidate_number')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-input @error('full_name') border-[var(--danger)] @enderror" value="{{ old('full_name') }}" required autofocus placeholder="e.g. Maria Santos" minlength="3" maxlength="100" style="text-transform: capitalize;">
                    @error('full_name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-input @error('gender') border-[var(--danger)] @enderror" required>
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender...</option>
                        <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Photo Upload & Crop Section --}}
                <div class="form-group">
                    <label for="picture" class="form-label flex items-center justify-between">
                        <span>Candidate Photo</span>
                        <span class="text-xs font-normal text-[var(--green-700)] bg-green-50 px-2 py-0.5 rounded-full border border-green-200">Cropping &amp; Adjustments supported</span>
                    </label>
                    
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 border border-[var(--border-default)] rounded-2xl bg-gray-50/50">
                            {{-- Preview Box --}}
                            <div class="relative group">
                                <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-white flex-shrink-0 shadow-sm transition-all" id="previewContainer">
                                    <img id="previewImage" src="" class="w-full h-full object-cover rounded-xl hidden" alt="Candidate Preview">
                                    <div id="placeholderIcon" class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div id="cropBadge" class="hidden absolute -bottom-1 -right-1 bg-[var(--green-600)] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">
                                    ✓ Cropped
                                </div>
                            </div>

                            {{-- File Selection & Crop Trigger --}}
                            <div class="flex-1 min-w-0 w-full space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <input type="file" id="picture" name="picture" accept="image/jpeg,image/jpg,image/png,image/webp"
                                        class="block w-full text-xs text-[var(--text-secondary)]
                                               file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                                               file:text-xs file:font-semibold file:bg-green-100 file:text-[var(--green-700)]
                                               hover:file:bg-green-200 cursor-pointer border border-[var(--border-default)] rounded-xl p-1.5 bg-white">
                                </div>
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <p class="text-xs text-[var(--text-muted)]">Accepted: JPEG, PNG, WebP. Max 2MB.</p>
                                    <button type="button" id="openCropperBtn" class="hidden btn btn-outline btn-sm text-xs font-semibold py-1.5 px-3 rounded-lg border-green-400 text-green-700 hover:bg-green-50 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        Adjust / Crop Photo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('picture')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-[var(--border-default)] flex justify-end gap-3">
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Candidate
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Crop & Adjust Modal --}}
<div id="cropModal" class="cropper-modal-container hidden" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-2xl flex flex-col max-h-[92vh] overflow-hidden animate-fade-in-up">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/80">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-xl bg-green-100 text-[var(--green-700)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 leading-tight">Adjust &amp; Crop Candidate Photo</h3>
                    <p class="text-xs text-[var(--text-muted)]">Pan, zoom, rotate, and crop the candidate's portrait</p>
                </div>
            </div>
            <button type="button" id="closeCropModalBtn" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body / Cropper Area --}}
        <div class="p-5 flex-1 overflow-y-auto space-y-4">
            <div class="cropper-area-wrapper shadow-inner">
                <img id="cropperSourceImage" src="" alt="To Crop">
            </div>

            {{-- Aspect Ratio & Adjustment Toolbar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                {{-- Aspect Ratios --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mr-1">Aspect:</span>
                    <button type="button" class="aspect-btn active px-3 py-1.5 rounded-lg text-xs font-bold bg-green-100 text-green-800 border border-green-300" data-aspect="1">1:1 Square</button>
                    <button type="button" class="aspect-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200" data-aspect="0.75">3:4 Portrait</button>
                    <button type="button" class="aspect-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200" data-aspect="0.8">4:5 Headshot</button>
                    <button type="button" class="aspect-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200" data-aspect="NaN">Free</button>
                </div>

                {{-- Transform Tools --}}
                <div class="flex items-center gap-1">
                    <button type="button" id="zoomInBtn" class="p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors" title="Zoom In">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                    <button type="button" id="zoomOutBtn" class="p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors" title="Zoom Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <button type="button" id="rotateLeftBtn" class="p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors" title="Rotate Counter-Clockwise">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </button>
                    <button type="button" id="rotateRightBtn" class="p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors" title="Rotate Clockwise">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"/>
                        </svg>
                    </button>
                    <button type="button" id="resetCropBtn" class="p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors" title="Reset All Adjustments">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-xs text-[var(--text-muted)]">Drag the crop box or scroll to zoom</span>
            <div class="flex items-center gap-2">
                <button type="button" id="cancelCropBtn" class="btn btn-outline text-xs font-semibold py-2 px-4 text-gray-600 hover:bg-gray-100 border-gray-300">
                    Cancel
                </button>
                <button type="button" id="applyCropBtn" class="btn btn-green text-xs font-bold py-2 px-5 flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Apply Crop &amp; Set Photo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('picture');
    const previewImage = document.getElementById('previewImage');
    const placeholderIcon = document.getElementById('placeholderIcon');
    const openCropperBtn = document.getElementById('openCropperBtn');
    const cropBadge = document.getElementById('cropBadge');
    const croppedDataInput = document.getElementById('cropped_picture_data');

    const cropModal = document.getElementById('cropModal');
    const cropperSourceImage = document.getElementById('cropperSourceImage');
    const closeCropModalBtn = document.getElementById('closeCropModalBtn');
    const cancelCropBtn = document.getElementById('cancelCropBtn');
    const applyCropBtn = document.getElementById('applyCropBtn');

    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    const rotateLeftBtn = document.getElementById('rotateLeftBtn');
    const rotateRightBtn = document.getElementById('rotateRightBtn');
    const resetCropBtn = document.getElementById('resetCropBtn');
    const aspectButtons = document.querySelectorAll('.aspect-btn');

    let cropper = null;
    let originalImageSrc = '';

    function openModalWithImage(src) {
        cropperSourceImage.src = src;
        cropModal.classList.remove('hidden');
        cropModal.style.display = 'flex';

        if (cropper) {
            cropper.destroy();
        }

        cropper = new Cropper(cropperSourceImage, {
            aspectRatio: 1, // Default 1:1 square
            viewMode: 1,
            autoCropArea: 0.9,
            responsive: true,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    }

    function closeModal() {
        cropModal.classList.add('hidden');
        cropModal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    // Handle File Selection
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                originalImageSrc = event.target.result;
                openModalWithImage(originalImageSrc);
            };
            reader.readAsDataURL(file);
        }
    });

    // Re-open cropper button
    openCropperBtn.addEventListener('click', function() {
        if (originalImageSrc) {
            openModalWithImage(originalImageSrc);
        }
    });

    // Close / Cancel modal
    closeCropModalBtn.addEventListener('click', closeModal);
    cancelCropBtn.addEventListener('click', function() {
        // If no crop was previously applied but file was selected, show raw image
        if (!croppedDataInput.value && originalImageSrc) {
            previewImage.src = originalImageSrc;
            previewImage.classList.remove('hidden');
            placeholderIcon.classList.add('hidden');
            openCropperBtn.classList.remove('hidden');
        }
        closeModal();
    });

    // Apply Crop
    applyCropBtn.addEventListener('click', function() {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 600,
            height: 600,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (canvas) {
            const croppedBase64 = canvas.toDataURL('image/jpeg', 0.92);
            croppedDataInput.value = croppedBase64;

            previewImage.src = croppedBase64;
            previewImage.classList.remove('hidden');
            placeholderIcon.classList.add('hidden');
            openCropperBtn.classList.remove('hidden');
            cropBadge.classList.remove('hidden');

            closeModal();
        }
    });

    // Aspect Ratio Switching
    aspectButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (!cropper) return;

            aspectButtons.forEach(b => {
                b.classList.remove('active', 'bg-green-100', 'text-green-800', 'border-green-300', 'font-bold');
                b.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200', 'font-semibold');
            });

            this.classList.add('active', 'bg-green-100', 'text-green-800', 'border-green-300', 'font-bold');
            this.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200', 'font-semibold');

            const aspect = parseFloat(this.dataset.aspect);
            cropper.setAspectRatio(isNaN(aspect) ? NaN : aspect);
        });
    });

    // Toolbar actions
    zoomInBtn.addEventListener('click', () => cropper && cropper.zoom(0.1));
    zoomOutBtn.addEventListener('click', () => cropper && cropper.zoom(-0.1));
    rotateLeftBtn.addEventListener('click', () => cropper && cropper.rotate(-90));
    rotateRightBtn.addEventListener('click', () => cropper && cropper.rotate(90));
    resetCropBtn.addEventListener('click', () => cropper && cropper.reset());
});
</script>
@endpush
