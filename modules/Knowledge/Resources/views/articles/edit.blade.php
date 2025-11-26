@extends('general.index', $setup)
@section('cardbody')
<form action="{{ $setup['action'] }}" method="POST" enctype="multipart/form-data" id="articleForm">
    @csrf
    @isset($setup['isupdate'])
        @method('PUT')
    @endisset

    <!-- Add hidden field for content -->
    <input type="hidden" name="content_hidden" id="content_hidden">

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Title and Content Fields -->
                    @foreach($fields as $field)
                        @if(in_array($field['id'], ['title','slug']))
                            @include('partials.fields', ['fields' => [$field]])
                        @elseif($field['id'] == 'content')
                            <!-- Custom Quill Editor Implementation -->
                            <div class="form-group mb-3">
                                <label for="content" class="form-label">{{ $field['name'] }}</label>
                                <!-- Hidden textarea to maintain compatibility -->
                                <textarea name="content" id="content" class="form-control" style="display: none;">{{ $field['value'] ?? '' }}</textarea>
                                <!-- Quill editor container -->
                                <div id="content-editor" style="height: 500px;"></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Settings -->
        <div class="col-md-4">
            <!-- Publish Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Publish</h4>
                </div>
                <div class="card-body">
                    @foreach($fields as $field)
                        @if(in_array($field['id'], ['status', 'category_id']))
                            @include('partials.fields', ['fields' => [$field]])
                        @endif
                    @endforeach
                    
                    <div class="text-end mt-3">
                        @if (isset($setup['isupdate']))
                            <button type="submit" class="btn btn-primary" onclick="syncContent(event)">{{ __('Update Article')}}</button>  
                        @else
                            <button type="submit" class="btn btn-primary" onclick="syncContent(event)">{{ __('Create Article')}}</button>  
                        @endif
                    </div>
                </div>
            </div>

            <!-- Article Settings Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Article Settings</h4>
                </div>
                <div class="card-body">
                    @foreach($fields as $field)
                        @if(in_array($field['id'], ['sort_order', 'is_featured']))
                            @include('partials.fields', ['fields' => [$field]])
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Excerpt Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Excerpt</h4>
                </div>
                <div class="card-body">
                    @foreach($fields as $field)
                        @if(isset($field['id']) && $field['id'] == 'excerpt')
                            @include('partials.fields', ['fields' => [$field]])
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.card {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    margin-bottom: 1rem;
}
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0,0,0,.125);
}
.card-title {
    font-size: 1.1rem;
    font-weight: 500;
}
.card-body .form-control,
.card-body .form-select {
    width: 100%;
    margin-bottom: 1rem;
}
.card-body .form-check {
    padding: 1rem 0;
}
.card-body label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #495057;
}

/* Quill Editor Styles */
.ql-editor {
    min-height: 400px;
    font-size: 16px;
    line-height: 1.6;
}

.ql-toolbar {
    border-top: 1px solid #ccc;
    border-left: 1px solid #ccc;
    border-right: 1px solid #ccc;
    background-color: #f8f9fa;
}

.ql-container {
    border-bottom: 1px solid #ccc;
    border-left: 1px solid #ccc;
    border-right: 1px solid #ccc;
}

#content-editor {
    background: white;
    border-radius: 0.375rem;
    overflow: hidden;
}
</style>

@section('head')
    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Quill.js JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
    <script>
        // Enhanced slug generation function
        function generateSlug(text) {
            return text
                .toLowerCase()
                .replace(/[áàãâä]/g, 'a')
                .replace(/[éèêë]/g, 'e')
                .replace(/[íìîï]/g, 'i')
                .replace(/[óòõôö]/g, 'o')
                .replace(/[úùûü]/g, 'u')
                .replace(/[ýÿ]/g, 'y')
                .replace(/[ñ]/g, 'n')
                .replace(/[ç]/g, 'c')
                .replace(/&/g, 'and')
                .replace(/\+/g, 'plus')
                .replace(/\$/g, 'dollar')
                .replace(/€/g, 'euro')
                .replace(/£/g, 'pound')
                .replace(/[^a-z0-9\s-]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()
                .replace(/\s/g, '-')
                .replace(/-+/g, '-')
                .substring(0, 60)
                .replace(/-$/, '');
        }

        let quill;

        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.querySelector('input[name="title"]');
            const slugInput = document.querySelector('input[name="slug"]');
            const isEditMode = {{ isset($setup['isupdate']) ? 'true' : 'false' }};
            
            // Initialize slug generation
            if (titleInput && slugInput && !isEditMode) {
                titleInput.addEventListener('input', function() {
                    if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
                        const slugValue = generateSlug(this.value);
                        slugInput.value = slugValue;
                        slugInput.dataset.autoGenerated = 'true';
                    }
                });

                slugInput.addEventListener('input', function() {
                    this.dataset.autoGenerated = 'false';
                });
            }

            // Initialize Quill editor
            const toolbarOptions = [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'font': [] }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'align': [] }],
                ['blockquote', 'code-block'],
                ['link', 'image', 'video'],
                ['clean']
            ];

            quill = new Quill('#content-editor', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: 'Start writing your knowledge article...'
            });

            // Get existing content from the textarea and set it in Quill
            const existingContent = document.getElementById('content').value;
            if (existingContent) {
                quill.root.innerHTML = existingContent;
            }

            // Update hidden field when content changes
            quill.on('text-change', function() {
                const html = quill.root.innerHTML;
                document.getElementById('content_hidden').value = html;
                document.getElementById('content').value = html;
            });

            // Set initial content in hidden field
            document.getElementById('content_hidden').value = quill.root.innerHTML;
        });

        // Function to ensure content is synced before form submission
        function syncContent(e) {
            e.preventDefault();
            const content = quill.root.innerHTML;
            document.getElementById('content_hidden').value = content;
            document.getElementById('content').value = content;
            document.getElementById('articleForm').submit();
        }
    </script>
@endsection
@endsection
