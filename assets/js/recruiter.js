function validateJobTitleInput(input) {
    const errorEl = document.getElementById('job-title-error');
    if (!errorEl) return;
    const val = input.value;

    if (/\d/.test(val)) {
        errorEl.innerText = "Position Title must contain only letters and spaces (no numbers allowed).";
        errorEl.style.display = "block";
    } else if (val.trim().length > 0 && !/^[a-zA-Z\s\-]+$/.test(val)) {
        errorEl.innerText = "Position Title contains invalid characters. Use a-z, A-Z and spaces only.";
        errorEl.style.display = "block";
    } else {
        errorEl.style.display = "none";
    }
}

function validateDriveBuilderForm() {
    const titleInput = document.getElementById('job-title-input');
    if (!titleInput) return true;

    const val = titleInput.value.trim();
    const errorEl = document.getElementById('job-title-error');
    
    if (/\d/.test(val)) {
        if (errorEl) {
            errorEl.innerText = "Position Title must contain only letters and spaces (no numbers allowed).";
            errorEl.style.display = "block";
        }
        titleInput.focus();
        return false;
    }

    if (!/^[a-zA-Z\s\-]{2,100}$/.test(val)) {
        if (errorEl) {
            errorEl.innerText = "Position Title must be 2-100 characters containing only letters and spaces (a-z, A-Z).";
            errorEl.style.display = "block";
        }
        titleInput.focus();
        return false;
    }

    return true;
}

let fieldIndex = 10;

function addCustomField() {
    fieldIndex++;
    const container = document.getElementById('custom-fields-container') || document.getElementById('dynamic-fields-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'custom-field-row';
    div.id = 'custom-field-' + fieldIndex;
    div.style = 'display: flex; gap: 8px; align-items: center; background: #f8fafc; padding: 8px; border: 1px solid var(--border-light); border-radius: var(--radius-sm); margin-top: 4px;';
    
    div.innerHTML = `
        <input type="text" name="custom_labels[]" class="form-control" placeholder="New Question / Field Prompt" pattern="^[a-zA-Z0-9\\s\\?\\.\\,\\:\\-\\(\\)\\'\\"]+$" title="Please enter a valid question prompt" required style="flex: 2;" oninput="updateCustomFieldsPreview()">
        <select name="custom_types[]" class="form-control" style="flex: 1;" onchange="updateCustomFieldsPreview()">
            <option value="text" selected>Short Text</option>
            <option value="url">URL Link</option>
            <option value="textarea">Long Paragraph</option>
        </select>
        <button type="button" class="btn btn-outline btn-sm" onclick="removeCustomField('custom-field-${fieldIndex}')" style="color: #dc2626; padding: 4px 10px;">✕</button>
    `;
    
    container.appendChild(div);
    updateCustomFieldsPreview();
}

function removeCustomField(id) {
    const el = document.getElementById(id);
    if (el) {
        el.remove();
        updateCustomFieldsPreview();
    }
}

function updateCustomFieldsPreview() {
    const previewList = document.getElementById('preview-custom-list');
    if (!previewList) return;

    const labels = document.querySelectorAll('input[name="custom_labels[]"]');
    const types = document.querySelectorAll('select[name="custom_types[]"]');
    
    let html = '';
    for (let i = 0; i < labels.length; i++) {
        const lbl = labels[i].value.trim() || 'Custom Prompt ' + (i + 1);
        const typ = types[i] ? types[i].value : 'text';
        const typLabel = typ === 'url' ? 'URL Link' : (typ === 'textarea' ? 'Long Paragraph' : 'Short Text');
        html += `<div>• ${escapeHtml(lbl)} [${typLabel}]</div>`;
    }

    if (labels.length === 0) {
        html = '<div style="color: var(--text-dim); font-style: italic;">No custom questions attached.</div>';
    }

    previewList.innerHTML = html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

function filterTalentTable() {
    const cgpaInput = parseFloat(document.getElementById('cgpa-filter').value) || 0;
    const deptInput = document.getElementById('dept-filter').value.toLowerCase();
    const rows = document.querySelectorAll('#talent-tbody tr');

    rows.forEach(row => {
        const rowCgpa = parseFloat(row.getAttribute('data-cgpa')) || 0;
        const rowDept = (row.getAttribute('data-dept') || '').toLowerCase();

        const cgpaMatch = rowCgpa >= cgpaInput;
        const deptMatch = !deptInput || rowDept.includes(deptInput);

        if (cgpaMatch && deptMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
