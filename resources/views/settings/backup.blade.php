@extends('layouts.app', ['title' => __('Backup')])
@section('admin_title')
    {{__('Backup')}}
@endsection
@section('content')
<div class="header pb-7 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">⚙️ {{__('Backup')}}</h1>
            <div class="row align-items-center pt-2">
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <!-- Backup Section -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{__('Backup Languages')}}</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{__('Create a backup of all language files. This will download a zip file containing all translations.')}}</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.backup.download.languages') }}" class="btn btn-success text-white" >
                            {{__('Download Language Backup')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore Section -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{__('Restore Languages')}}</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{__('Restore language files from a previously created backup. Select a zip file containing language translations.')}}</p>
                    
                    <form method="POST" action="{{ route('admin.backup.restore.languages') }}" enctype="multipart/form-data" id="restoreForm">
                        @csrf
                        <div class="form-group">
                            <label for="language_backup" class="form-control-label">{{__('Select Language Backup File')}}</label>
                            <input type="file" 
                                   class="form-control @error('language_backup') is-invalid @enderror" 
                                   id="language_backup" 
                                   name="language_backup" 
                                   accept=".zip"
                                   onchange="handleFileSelect(this)"
                                   style="display: none;">
                            
                            <div class="custom-file-upload" onclick="document.getElementById('language_backup').click();">
                                <div class="file-upload-content">
                                    <i class="ni ni-cloud-upload-96 text-primary" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0" id="file-upload-text">{{__('Click to select zip file')}}</p>
                                    <small class="text-muted">{{__('Only .zip files are allowed')}}</small>
                                </div>
                            </div>
                            
                            @error('language_backup')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mt-4" id="restore-actions" style="display: none;">
                            <button type="submit" class="btn btn-success me-2" onclick="return confirmRestore()">
                                <i class="ni ni-check-bold"></i>
                                {{__('Restore Languages')}}
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearFileSelection()">
                                {{__('Cancel')}}
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-3">
                        <small class="text-danger">
                           
                            {{__('Warning: This will overwrite existing language files. Make sure to backup current languages before restoring.')}}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-file-upload {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.custom-file-upload:hover {
    border-color: #5e72e4;
    background-color: #f0f3ff;
}

.custom-file-upload.file-selected {
    border-color: #2dce89;
    background-color: #f0fff4;
}

.file-upload-content {
    pointer-events: none;
}
</style>

<script>
function handleFileSelect(input) {
    const fileUploadText = document.getElementById('file-upload-text');
    const restoreActions = document.getElementById('restore-actions');
    const customFileUpload = document.querySelector('.custom-file-upload');
    
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        fileUploadText.innerHTML = `<strong>${fileName}</strong><br><small class="text-success">{{__('File selected successfully')}}</small>`;
        restoreActions.style.display = 'block';
        customFileUpload.classList.add('file-selected');
    } else {
        clearFileSelection();
    }
}

function clearFileSelection() {
    const fileInput = document.getElementById('language_backup');
    const fileUploadText = document.getElementById('file-upload-text');
    const restoreActions = document.getElementById('restore-actions');
    const customFileUpload = document.querySelector('.custom-file-upload');
    
    fileInput.value = '';
    fileUploadText.innerHTML = '{{__('Click to select zip file')}}';
    restoreActions.style.display = 'none';
    customFileUpload.classList.remove('file-selected');
}

function confirmRestore() {
    return confirm('{{__('Are you sure you want to restore the language files? This will overwrite existing translations.')}}');
}
</script>
@endsection