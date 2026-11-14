// ==============================================================================
// Admin & Moderator Portal DOM Interactions
// ==============================================================================

function selectBoothForAllocation(boothId, boothNumber, locationName, isOccupied, assignedClub) {
    document.getElementById('modal-booth-num').innerText = boothNumber;
    document.getElementById('modal-booth-location').innerText = locationName;
    document.getElementById('form-booth-num').value = boothNumber;
    if (document.getElementById('form-booth-id')) {
        document.getElementById('form-booth-id').value = boothId;
    }

    if (isOccupied) {
        document.getElementById('booth-status-info').innerHTML = `
            <div class="flash-alert flash-info">Assigned to <strong>${escapeHtml(assignedClub)}</strong></div>
        `;
    } else {
        document.getElementById('booth-status-info').innerHTML = `
            <div class="flash-alert flash-success">This booth is available.</div>
        `;
    }

    openModal('booth-modal');
}
