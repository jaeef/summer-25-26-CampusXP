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
            <div style="background: rgba(37,99,235,0.15); border:1px solid rgba(37,99,235,0.3); padding:10px; border-radius:6px; margin-bottom:1rem;">
                Currently Assigned to: <strong>${assignedClub}</strong>
            </div>
        `;
    } else {
        document.getElementById('booth-status-info').innerHTML = `
            <div style="background: rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); padding:10px; border-radius:6px; margin-bottom:1rem; color:#34D399;">
                Status: <strong>Available for Assignment</strong>
            </div>
        `;
    }

    openModal('booth-modal');
}
