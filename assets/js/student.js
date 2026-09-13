// ==============================================================================
// Student Portal DOM Interactions (Course JS Style + AJAX Bookmarking + Live Preview)
// ==============================================================================

function filterCategory(category, element, saveState) {
    if (saveState === undefined) saveState = true;

    var pills = document.querySelectorAll('.category-pills .cat-pill');
    for (var i = 0; i < pills.length; i++) {
        pills[i].classList.remove('active');
    }
    if (element) {
        element.classList.add('active');
    }

    var items = document.querySelectorAll('.radar-item');
    for (var j = 0; j < items.length; j++) {
        var item = items[j];
        var itemCat = (item.getAttribute('data-cat') || '').toLowerCase();
        var itemType = (item.getAttribute('data-type') || '').toLowerCase();
        var itemSaved = item.getAttribute('data-saved');

        if (category === 'all') {
            item.style.display = 'flex';
        } else if (category === 'saved') {
            if (itemSaved === '1') {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        } else if (category === 'corporate') {
            if (itemType === 'corporate' || itemCat === 'corporate') {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        } else {
            if (itemCat === category || itemCat.indexOf(category) !== -1) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        }
    }

    if (saveState) {
        try {
            localStorage.setItem('CampusXP_radar_category', category);
            var url = new URL(window.location);
            if (category !== 'all') {
                url.searchParams.set('view', category);
            } else {
                url.searchParams.delete('view');
            }
            window.history.replaceState({}, '', url);
        } catch (e) {
            console.log('Storage notice:', e);
        }
    }
}

function filterRadarSearch() {
    var query = document.getElementById('radar-search').value.toLowerCase();
    var items = document.querySelectorAll('.radar-item');
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var text = item.innerText.toLowerCase();
        if (text.indexOf(query) !== -1) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    }
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

function handleQuickApplyClick(btn) {
    if (!btn) return;
    var driveId = btn.getAttribute('data-drive-id');
    var driveTitle = btn.getAttribute('data-drive-title') || '';
    var organizer = btn.getAttribute('data-organizer') || '';
    var driveType = btn.getAttribute('data-drive-type') || 'club';
    var minCgpa = parseFloat(btn.getAttribute('data-min-cgpa') || 0);
    var fixedRole = btn.getAttribute('data-fixed-role') || '';
    var customFieldsJson = btn.getAttribute('data-custom-fields') || '[]';

    var studentCgpaInput = document.getElementById('student-cgpa');
    var studentCgpa = studentCgpaInput ? parseFloat(studentCgpaInput.value || 0) : 0;

    // Check minimum CGPA cap for corporate drives
    if (driveType === 'corporate' && minCgpa > 0 && studentCgpa < minCgpa) {
        var reqEl = document.getElementById('cgpa-warn-required');
        var curEl = document.getElementById('cgpa-warn-current');
        if (reqEl) reqEl.innerText = minCgpa.toFixed(2);
        if (curEl) curEl.innerText = studentCgpa.toFixed(2);
        
        if (typeof openModal === 'function') {
            openModal('cgpa-warning-modal');
        } else {
            alert("⚠️ Minimum CGPA Requirement Notice:\nThis position requires a minimum CGPA of " + minCgpa.toFixed(2) + ".\nYour current recorded CGPA is " + studentCgpa.toFixed(2) + ".\nYou do not meet the minimum requirement to apply for this position.");
        }
        return;
    }

    var vaultCv = document.getElementById('vault-cv-url') ? document.getElementById('vault-cv-url').value : '';
    var vaultSop = document.getElementById('vault-default-sop') ? document.getElementById('vault-default-sop').value : '';

    openQuickApplyModal(driveId, driveTitle, organizer, driveType, vaultCv, vaultSop, fixedRole, customFieldsJson);
}

function openQuickApplyModal(driveId, driveTitle, organizerName, driveType, savedCv, savedSop, fixedRole, customFieldsJson) {
    var idInput = document.getElementById('apply-drive-id');
    var typeInput = document.getElementById('apply-drive-type');
    var titleEl = document.getElementById('apply-drive-title');
    var orgEl = document.getElementById('apply-club-name');
    var cvInput = document.getElementById('apply-cv-link');
    var sopInput = document.getElementById('apply-sop');
    var roleInput = document.getElementById('apply-role-input');
    var roleLabel = document.getElementById('apply-role-label');
    var roleHint = document.getElementById('apply-role-hint');

    if (idInput) idInput.value = driveId || '';
    if (typeInput) typeInput.value = driveType || 'club';
    if (titleEl) titleEl.innerText = driveTitle || '';
    if (orgEl) orgEl.innerText = organizerName || '';

    // Auto-fill Profile Vault credentials if not passed directly
    if (!savedCv) {
        var vCv = document.getElementById('vault-cv-url');
        if (vCv) savedCv = vCv.value;
    }
    if (!savedSop) {
        var vSop = document.getElementById('vault-default-sop');
        if (vSop) savedSop = vSop.value;
    }

    if (cvInput) cvInput.value = savedCv || '';
    if (sopInput) sopInput.value = savedSop || '';

    if (driveType === 'corporate') {
        if (roleLabel) roleLabel.innerHTML = 'Position Applied For <span style="font-size:0.75rem; color:var(--brand-purple); font-weight:700;">(Fixed by Recruiter)</span>';
        if (roleInput) {
            roleInput.value = fixedRole || driveTitle;
            roleInput.readOnly = true;
            roleInput.style.background = '#f1f5f9';
            roleInput.style.color = '#1e293b';
            roleInput.style.cursor = 'not-allowed';
            roleInput.style.fontWeight = '700';
            roleInput.title = 'The application position is set by the hiring recruiter and cannot be altered.';
        }
        if (roleHint) roleHint.style.display = 'block';
    } else {
        if (roleLabel) roleLabel.innerText = 'Position / Role Applied For';
        if (roleInput) {
            roleInput.value = fixedRole || 'Candidate Member';
            roleInput.readOnly = false;
            roleInput.style.background = '#ffffff';
            roleInput.style.color = 'var(--text-main)';
            roleInput.style.cursor = 'text';
            roleInput.style.fontWeight = 'normal';
            roleInput.title = '';
        }
        if (roleHint) roleHint.style.display = 'none';
    }

    // Dynamic Custom Questions Rendering
    var customSection = document.getElementById('apply-custom-fields-container');
    var customList = document.getElementById('apply-custom-fields-list');

    if (customSection && customList) {
        customList.innerHTML = '';
        var fields = [];
        if (customFieldsJson) {
            try {
                fields = typeof customFieldsJson === 'string' ? JSON.parse(customFieldsJson) : customFieldsJson;
            } catch (e) {
                console.log('Error parsing custom fields json', e);
            }
        }

        if (driveType === 'corporate' && fields && fields.length > 0) {
            customSection.style.display = 'block';
            for (var f = 0; f < fields.length; f++) {
                var field = fields[f];
                var fId = field.id || ('f_' + f);
                var fLabel = field.field_label || ('Question ' + (f + 1));
                var fType = field.field_type || 'text';

                var formGroup = document.createElement('div');
                formGroup.className = 'form-group';
                formGroup.style.marginBottom = '8px';

                var labelEl = document.createElement('label');
                labelEl.className = 'form-label';
                labelEl.style.fontSize = '0.82rem';
                labelEl.style.fontWeight = '600';

                var inputEl;
                if (fType === 'url') {
                    labelEl.innerHTML = escapeHtml(fLabel) + ' <span style="color:var(--brand-purple); font-size:0.75rem;">(Valid URL Link)</span>';
                    inputEl = document.createElement('input');
                    inputEl.type = 'url';
                    inputEl.className = 'form-control';
                    inputEl.placeholder = 'https://github.com/your-profile';
                    inputEl.pattern = 'https?:\\/\\/.+';
                    inputEl.title = 'Please enter a valid web URL link starting with http:// or https://';
                    inputEl.required = true;
                } else if (fType === 'textarea') {
                    labelEl.innerHTML = escapeHtml(fLabel) + ' <span style="color:var(--text-dim); font-size:0.75rem;">(Detailed Paragraph)</span>';
                    inputEl = document.createElement('textarea');
                    inputEl.className = 'form-control';
                    inputEl.rows = 3;
                    inputEl.placeholder = 'Write your response here...';
                    inputEl.required = true;
                    inputEl.minLength = 5;
                } else {
                    labelEl.innerHTML = escapeHtml(fLabel) + ' <span style="color:var(--text-dim); font-size:0.75rem;">(Short Text)</span>';
                    inputEl = document.createElement('input');
                    inputEl.type = 'text';
                    inputEl.className = 'form-control';
                    inputEl.placeholder = 'Short response...';
                    inputEl.required = true;
                    inputEl.minLength = 2;
                }

                inputEl.name = 'custom_answers[' + (field.id ? field.id : fLabel) + ']';
                inputEl.setAttribute('data-label', fLabel);
                formGroup.appendChild(labelEl);
                formGroup.appendChild(inputEl);
                customList.appendChild(formGroup);
            }
        } else {
            customSection.style.display = 'none';
        }
    }
    
    openModal('apply-modal');
}

/**
 * Instant Asynchronous Opportunity Bookmarking (No Page Reload)
 */
function toggleBookmarkAjax(event, buttonElement, itemType, itemId) {
    if (event) {
        event.preventDefault();
    }

    var formData = new FormData();
    formData.append('item_type', itemType);
    formData.append('item_id', itemId);

    // Visual feedback indicator
    var originalHtml = buttonElement.innerHTML;
    buttonElement.disabled = true;

    fetch('ajax/bookmark.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        buttonElement.disabled = false;

        if (data && data.success) {
            var card = buttonElement.closest('.radar-item');
            var topBadge = card ? card.querySelector('.bookmarked-tag') : null;

            if (data.bookmarked) {
                // Marked as Saved
                buttonElement.innerHTML = '★ Saved';
                buttonElement.className = 'btn btn-sm btn-bookmark-active';
                buttonElement.title = 'Remove Bookmark';
                
                if (card) {
                    card.setAttribute('data-saved', '1');
                    if (!topBadge) {
                        var badgeContainer = card.querySelector('.card-badge-row');
                        if (badgeContainer) {
                            var span = document.createElement('span');
                            span.className = 'bookmarked-tag';
                            span.style.fontSize = '0.75rem';
                            span.style.color = '#b45309';
                            span.style.fontWeight = '700';
                            span.style.background = '#fef3c7';
                            span.style.padding = '2px 6px';
                            span.style.borderRadius = '4px';
                            span.innerText = '★ Bookmarked';
                            badgeContainer.appendChild(span);
                        }
                    } else {
                        topBadge.style.display = 'inline-block';
                    }
                }
                showToast(data.message || 'Saved to your Bookmarks!', 'success');
            } else {
                // Marked as Unsaved
                buttonElement.innerHTML = '☆ Save';
                buttonElement.className = 'btn btn-outline btn-sm';
                buttonElement.title = 'Save to Bookmarks';

                if (card) {
                    card.setAttribute('data-saved', '0');
                    if (topBadge) {
                        topBadge.remove();
                    }

                    // If currently viewing only 'saved' category, hide card smoothly
                    var activePill = document.querySelector('.category-pills .cat-pill.active');
                    if (activePill && activePill.id === 'cat-pill-saved') {
                        card.style.display = 'none';
                    }
                }
                showToast(data.message || 'Removed from your Bookmarks.', 'info');
            }

            // Update stats & pill numbers on dashboard
            var savedStat = document.getElementById('saved-count-stat');
            if (savedStat && data.total_saved !== undefined) {
                savedStat.innerText = data.total_saved;
            }

            var pillSaved = document.getElementById('cat-pill-saved');
            if (pillSaved && data.total_saved !== undefined) {
                pillSaved.innerText = '★ Saved Bookmarks (' + data.total_saved + ')';
            }
        } else {
            buttonElement.innerHTML = originalHtml;
            showToast((data && data.message) ? data.message : 'Error updating bookmark.', 'error');
        }
    })
    .catch(function(error) {
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalHtml;
        console.log('Bookmark AJAX error:', error);
        showToast('Network error updating bookmark.', 'error');
    });

    return false;
}

/**
 * Lightweight Toast Notification
 */
function showToast(message, type) {
    var toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.position = 'fixed';
        toastContainer.style.bottom = '24px';
        toastContainer.style.right = '24px';
        toastContainer.style.zIndex = '99999';
        toastContainer.style.display = 'flex';
        toastContainer.style.flexDirection = 'column';
        toastContainer.style.gap = '8px';
        toastContainer.style.pointerEvents = 'none';
        document.body.appendChild(toastContainer);
    }

    var toast = document.createElement('div');
    toast.className = 'toast-bubble';
    toast.style.padding = '10px 16px';
    toast.style.borderRadius = '8px';
    toast.style.fontSize = '0.85rem';
    toast.style.fontWeight = '600';
    toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.15), 0 4px 6px -4px rgba(0,0,0,0.1)';
    toast.style.transition = 'all 0.3s ease';
    toast.style.transform = 'translateY(20px)';
    toast.style.opacity = '0';
    toast.style.pointerEvents = 'auto';

    if (type === 'success') {
        toast.style.background = '#15803d';
        toast.style.color = '#ffffff';
    } else if (type === 'error') {
        toast.style.background = '#dc2626';
        toast.style.color = '#ffffff';
    } else {
        toast.style.background = '#1e3a8a';
        toast.style.color = '#ffffff';
    }

    toast.innerText = message;
    toastContainer.appendChild(toast);

    setTimeout(function() {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 10);

    setTimeout(function() {
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 2500);
}

/**
 * Real-Time Profile Live Preview Binder
 */
function initProfileLivePreview() {
    var inId = document.getElementById('input-aiub-id');
    var inDept = document.getElementById('input-dept');
    var inCgpa = document.getElementById('input-cgpa');
    var inSkills = document.getElementById('input-skills');
    var inCv = document.getElementById('input-cv-url');
    var inSop = document.getElementById('input-sop');

    if (!inId) return; // Not on profile page

    var pId = document.getElementById('preview-aiub-id');
    var pDept = document.getElementById('preview-dept');
    var pCgpa = document.getElementById('preview-cgpa');
    var pSkillsContainer = document.getElementById('preview-skills-container');
    var pCvContainer = document.getElementById('preview-cv-container');
    var pSop = document.getElementById('preview-sop');
    var hId = document.getElementById('header-aiub-id');
    var hCgpa = document.getElementById('header-cgpa');

    function updatePreview() {
        if (pId) pId.innerText = inId.value.trim() || 'Not Set';
        if (hId) hId.innerText = 'ID: ' + (inId.value.trim() || 'Pending Setup');
        
        if (pDept) pDept.innerText = inDept.value;

        if (pCgpa) {
            var val = parseFloat(inCgpa.value);
            pCgpa.innerText = isNaN(val) ? '0.00' : val.toFixed(2);
            if (hCgpa) hCgpa.innerText = 'CGPA: ' + (isNaN(val) ? '0.00' : val.toFixed(2));
        }

        if (pCvContainer) {
            var cvUrl = inCv.value.trim();
            if (cvUrl) {
                pCvContainer.innerHTML = '<a href="' + encodeURI(cvUrl) + '" target="_blank" style="font-size:0.85rem; font-weight:600;">View Document</a>';
            } else {
                pCvContainer.innerHTML = '<span style="color:var(--text-dim); font-size:0.85rem;">No URL Provided</span>';
            }
        }

        if (pSop) {
            var sopVal = inSop.value.trim();
            pSop.innerHTML = '"' + (sopVal ? sopVal.replace(/\n/g, '<br>') : 'No default Statement of Purpose saved yet.') + '"';
        }

        if (pSkillsContainer) {
            var rawSkills = inSkills.value.trim();
            if (rawSkills) {
                var skillsArr = rawSkills.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s.length > 0; });
                if (skillsArr.length > 0) {
                    var html = '';
                    for (var k = 0; k < skillsArr.length; k++) {
                        var safeSkill = skillsArr[k].replace(/[&<>"']/g, function(m) {
                            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
                        });
                        html += '<span class="badge-tag" style="background:#eff6ff; color:var(--brand-blue); border:1px solid #bfdbfe; font-size:0.75rem;">' + safeSkill + '</span>';
                    }
                    pSkillsContainer.innerHTML = html;
                } else {
                    pSkillsContainer.innerHTML = '<span style="color:var(--text-dim); font-size:0.8rem; font-style:italic;">No skills specified yet</span>';
                }
            } else {
                pSkillsContainer.innerHTML = '<span style="color:var(--text-dim); font-size:0.8rem; font-style:italic;">No skills specified yet</span>';
            }
        }
    }

    inId.addEventListener('input', updatePreview);
    inDept.addEventListener('change', updatePreview);
    inCgpa.addEventListener('input', updatePreview);
    inSkills.addEventListener('input', updatePreview);
    inCv.addEventListener('input', updatePreview);
    inSop.addEventListener('input', updatePreview);
}

// Auto-restore radar filter state on page load
document.addEventListener('DOMContentLoaded', function() {
    initProfileLivePreview();

    // Check if on Opportunity Radar page
    var radarGrid = document.getElementById('radar-grid');
    if (radarGrid) {
        var urlParams = new URLSearchParams(window.location.search);
        var savedCategory = urlParams.get('view') || localStorage.getItem('CampusXP_radar_category');
        
        if (savedCategory && savedCategory !== 'all') {
            var pillMap = {
                'saved': 'cat-pill-saved',
                'recruitment': 'cat-pill-rec',
                'internship': 'cat-pill-intern',
                'seminar': 'cat-pill-sem',
                'workshop': 'cat-pill-ws',
                'fest': 'cat-pill-fest',
                'corporate': 'cat-pill-corp'
            };
            var pillId = pillMap[savedCategory];
            var targetPill = pillId ? document.getElementById(pillId) : null;
            if (targetPill) {
                filterCategory(savedCategory, targetPill, false);
            }
        }
    }
});

