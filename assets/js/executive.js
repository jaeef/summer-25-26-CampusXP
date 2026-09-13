// ==============================================================================
// Club Executive Portal DOM Interactions (Kanban Drag & Drop & Scheduler)
// ==============================================================================

function kanbanDragStart(event, appId) {
    event.dataTransfer.setData("text/plain", appId);
    event.dataTransfer.effectAllowed = "move";
    var card = document.getElementById('exec-cand-card-' + appId);
    if (card) {
        card.style.opacity = '0.5';
    }
}

function kanbanAllowDrop(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = "move";
    var colList = event.currentTarget;
    if (colList) {
        colList.style.background = '#e0f2fe';
        colList.style.outline = '2px dashed #0284c7';
    }
}

function kanbanDragLeave(event) {
    var colList = event.currentTarget;
    if (colList) {
        colList.style.background = '';
        colList.style.outline = '';
    }
}

function kanbanDropCard(event, targetStage) {
    event.preventDefault();
    var colList = event.currentTarget;
    if (colList) {
        colList.style.background = '';
        colList.style.outline = '';
    }

    var appId = event.dataTransfer.getData("text/plain");
    var card = document.getElementById('exec-cand-card-' + appId);

    if (card) {
        card.style.opacity = '1';
        
        // Remove empty column notice if present
        var emptyNotice = colList.querySelector('.empty-col-notice');
        if (emptyNotice) {
            emptyNotice.remove();
        }

        colList.appendChild(card);

        // Update form submission to sync with server
        var hiddenForm = document.getElementById('exec-kanban-status-form');
        var inputAppId = document.getElementById('exec-drag-app-id');
        var inputStatus = document.getElementById('exec-drag-new-status');

        if (hiddenForm && inputAppId && inputStatus) {
            inputAppId.value = appId;
            inputStatus.value = targetStage;
            hiddenForm.submit();
        }
    }
}

// Reset opacity if drag ends without drop
document.addEventListener('dragend', function(event) {
    if (event.target && event.target.classList && event.target.classList.contains('kanban-card')) {
        event.target.style.opacity = '1';
    }
});

function openEvaluationModal(appId, studentName, driveTitle) {
    var inId = document.getElementById('eval-app-id');
    var sName = document.getElementById('eval-student-name');
    var dTitle = document.getElementById('eval-drive-title');
    if (inId) inId.value = appId;
    if (sName) sName.innerText = studentName;
    if (dTitle) dTitle.innerText = driveTitle;
    if (typeof openModal === 'function') {
        openModal('eval-modal');
    }
}
