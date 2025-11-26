@isset($separator)
    <br />
    <h4 id="sep{{ $id }}" class="display-4 mb-0">{{ __($separator) }}</h4>
    <hr />
@endisset
<div id="form-group-{{ $id }}" class="form-group{{ $errors->has($id) ? ' has-danger' : '' }} @isset($class) {{$class}} @endisset">
    <label class="form-control-label" for="{{ $id }}">{{ __($name) }}</label>
    @isset($help)
        <small class="form-text text-muted">{{ __($help) }}</small>
    @endisset
    
    <div class="icon-selector-container mt-2">
        <input type="hidden" name="{{ $id }}" id="{{ $id }}" value="{{ old($id, $value ?? '') }}">
        
        <!-- Selected Icon Display -->
        <div class="selected-icon-display mb-4 p-4 border rounded-lg shadow-sm" style="background: #f8f9fa;">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="selected-icon-preview">
                        <div id="icon-preview-{{ $id }}" class="icon-preview d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white; font-size: 1.5rem;">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-1">
                        <strong style="font-size: 1.1rem;">Selected Icon</strong>
                    </div>
                    <div class="selected-icon-name text-muted" id="icon-name-{{ $id }}" style="font-size: 0.95rem;">Default (folder)</div>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary px-4 py-2" onclick="openIconModal{{ $id }}()" style="border-radius: 8px;">
                        <i class="fas fa-edit me-2"></i>Change Icon
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
</div>

<!-- Icon Selection Modal -->
<div class="modal fade" id="iconModal{{ $id }}" tabindex="-1" aria-labelledby="iconModalLabel{{ $id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header border-0 pb-0" style="padding: 2rem 2rem 1rem 2rem;">
                <div>
                    <h4 class="modal-title mb-1" id="iconModalLabel{{ $id }}" style="font-weight: 600; color: #1f2937;">Select Category Icon</h4>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Choose an icon that best represents your category</p>
                </div>
                <button type="button" class="btn-close" onclick="closeIconModal{{ $id }}()" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; opacity: 0.5;">×</button>
            </div>
            <div class="modal-body" style="padding: 0 2rem 2rem 2rem;">
                <!-- Search Icons -->
               
                
                <div class="row g-3" id="iconGrid{{ $id }}" style="max-height: 400px; overflow-y: auto;">
                    <!-- Icons will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldId = '{{ $id }}';
    const hiddenInput = document.getElementById(fieldId);
    const iconPreview = document.getElementById('icon-preview-' + fieldId);
    const iconName = document.getElementById('icon-name-' + fieldId);
    const iconGrid = document.getElementById('iconGrid' + fieldId);
    const searchInput = document.getElementById('iconSearch' + fieldId);
    
    // Available icons - using Font Awesome icons that work well for categories
    const availableIcons = [
        { class: 'fas fa-folder', name: 'Folder' },
        { class: 'fas fa-book', name: 'Book' },
        { class: 'fas fa-graduation-cap', name: 'Education' },
        { class: 'fas fa-cog', name: 'Settings' },
        { class: 'fas fa-question-circle', name: 'Help' },
        { class: 'fas fa-info-circle', name: 'Information' },
        { class: 'fas fa-rocket', name: 'Getting Started' },
        { class: 'fas fa-tools', name: 'Tools' },
        { class: 'fas fa-shield-alt', name: 'Security' },
        { class: 'fas fa-credit-card', name: 'Billing' },
        { class: 'fas fa-users', name: 'Users' },
        { class: 'fas fa-chart-bar', name: 'Analytics' },
        { class: 'fas fa-download', name: 'Downloads' },
        { class: 'fas fa-upload', name: 'Uploads' },
        { class: 'fas fa-mobile-alt', name: 'Mobile' },
        { class: 'fas fa-desktop', name: 'Desktop' },
        { class: 'fas fa-cloud', name: 'Cloud' },
        { class: 'fas fa-database', name: 'Database' },
        { class: 'fas fa-code', name: 'Development' },
        { class: 'fas fa-bug', name: 'Troubleshooting' },
        { class: 'fas fa-heart', name: 'Favorites' },
        { class: 'fas fa-star', name: 'Featured' },
        { class: 'fas fa-bell', name: 'Notifications' },
        { class: 'fas fa-envelope', name: 'Email' },
        { class: 'fas fa-phone', name: 'Support' },
        { class: 'fas fa-globe', name: 'General' },
        { class: 'fas fa-puzzle-piece', name: 'Integrations' },
        { class: 'fas fa-key', name: 'API' },
        { class: 'fas fa-lock', name: 'Privacy' },
        { class: 'fas fa-file-alt', name: 'Documentation' },
        { class: 'fas fa-lightbulb', name: 'Ideas' },
        { class: 'fas fa-wrench', name: 'Maintenance' },
        { class: 'fas fa-shopping-cart', name: 'Commerce' },
        { class: 'fas fa-money-bill', name: 'Pricing' },
        { class: 'fas fa-handshake', name: 'Partnership' }
    ];
    
    // Modal functions
    window['openIconModal' + fieldId] = function() {
        const modalElement = document.getElementById('iconModal' + fieldId);
        if (modalElement) {
            // Try Bootstrap 5 first, then Bootstrap 4
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalElement).modal('show');
            } else {
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
            }
        }
    };

    window['closeIconModal' + fieldId] = function() {
        const modalElement = document.getElementById('iconModal' + fieldId);
        if (modalElement) {
            // Try Bootstrap 5 first, then Bootstrap 4
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalElement).modal('hide');
            } else {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
            }
        }
    };
    
    // Populate icon grid
    availableIcons.forEach(icon => {
        const iconItem = document.createElement('div');
        iconItem.className = 'col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6';
        iconItem.innerHTML = `
            <div class="icon-item text-center p-3 border rounded cursor-pointer" 
                 data-icon="${icon.class}" 
                 style="cursor: pointer; transition: all 0.3s ease; height: 80px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div class="icon-container d-flex align-items-center justify-content-center" 
                     style="width: 44px; height: 44px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; color: white; font-size: 1.1rem;">
                    <i class="${icon.class}"></i>
                </div>
                <small class="mt-2 text-muted" style="font-size: 0.75rem; font-weight: 500; line-height: 1.2;"></small>
            </div>
        `;
        iconGrid.appendChild(iconItem);
        
        // Add click handler
        iconItem.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('#iconGrid' + fieldId + ' .icon-item').forEach(item => {
                item.classList.remove('border-primary', 'bg-light', 'selected');
                item.style.borderColor = '';
                item.style.backgroundColor = '';
                item.style.borderWidth = '';
                item.style.boxShadow = '';
            });
            
            // Add selection to clicked item
            this.classList.add('selected');
            this.style.borderColor = '#3b82f6';
            this.style.backgroundColor = '#eff6ff';
            this.style.borderWidth = '2px';
            this.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.2)';
            
            // Update hidden input
            hiddenInput.value = icon.class;
            
            // Update preview
            iconPreview.innerHTML = `<i class="${icon.class}"></i>`;
            iconName.textContent = icon.name;
            
            // Close modal after selection
            setTimeout(() => {
                window['closeIconModal' + fieldId]();
            }, 300);
        });
    });
    
    // Initialize with current value
    const currentValue = hiddenInput.value || 'fas fa-folder';
    const currentIcon = availableIcons.find(icon => icon.class === currentValue) || availableIcons[0];
    
    iconPreview.innerHTML = `<i class="${currentIcon.class}"></i>`;
    iconName.textContent = currentIcon.name;
    
    // Highlight current selection in grid
    setTimeout(() => {
        const currentItem = document.querySelector(`[data-icon="${currentIcon.class}"]`);
        if (currentItem) {
            currentItem.classList.add('selected');
            currentItem.style.borderColor = '#3b82f6';
            currentItem.style.backgroundColor = '#eff6ff';
            currentItem.style.borderWidth = '2px';
            currentItem.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.2)';
        }
    }, 100);
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const iconItems = document.querySelectorAll('#iconGrid' + fieldId + ' .col-xl-2');
            
            iconItems.forEach(item => {
                const iconName = item.querySelector('small')?.textContent?.toLowerCase() || '';
                if (iconName.includes(searchTerm) || searchTerm === '') {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>

<style>
.icon-item {
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.icon-item:hover {
    background-color: #f0f4ff !important;
    border-color: #3b82f6 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.icon-item.selected {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
    border-width: 2px !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.cursor-pointer {
    cursor: pointer;
}

.rounded-lg {
    border-radius: 0.75rem;
}

.shadow-sm {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
}

.modal-content {
    border: none !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
}

.btn-close {
    background: none !important;
    border: none !important;
    opacity: 0.5;
    font-size: 1.2rem;
    padding: 0.5rem;
}

.btn-close:hover {
    opacity: 1;
}

/* Custom scrollbar for modal */
#iconGrid{{ $id }}::-webkit-scrollbar {
    width: 6px;
}

#iconGrid{{ $id }}::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#iconGrid{{ $id }}::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#iconGrid{{ $id }}::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
