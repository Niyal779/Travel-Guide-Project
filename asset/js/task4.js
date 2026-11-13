/* ===================================
   Task 4: Travel Guide - Browse & Details Pages JavaScript
   =================================== */

// ============ Browse Page Functions ============

// Escape HTML to prevent XSS attacks
function escapeHtml(value) {
    if (typeof value !== 'string') {
        value = String(value);
    }
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Filter the approved cards already rendered by PHP.
function updateSearch() {
    const query = (document.getElementById('searchBox')?.value || '').trim().toLowerCase();
    const country = (document.getElementById('countryFilter')?.value || '').toLowerCase();
    const genre = (document.getElementById('genreFilter')?.value || '').toLowerCase();
    const cost = (document.getElementById('costFilter')?.value || '').toLowerCase();
    const cards = [...document.querySelectorAll('.browse-card')];
    let visible = 0;
    cards.forEach(card => {
        const match = (!query || card.dataset.title.includes(query) || card.dataset.country.includes(query)) &&
            (!country || card.dataset.country === country) && (!genre || card.dataset.genre === genre) && (!cost || card.dataset.cost === cost);
        card.hidden = !match;
        if (match) visible++;
    });
    const count = document.getElementById('resultCount');
    if (count) count.textContent = `${visible} ${visible === 1 ? 'destination' : 'destinations'}`;
    const empty = document.getElementById('noResults');
    if (empty) empty.hidden = cards.length === 0 || visible !== 0;
}
function updateFilter() { updateSearch(); }

// Initialize browse page event listeners
function initBrowsePage() {
    const searchBox = document.getElementById('searchBox');
    const countryFilter = document.getElementById('countryFilter');
    const genreFilter = document.getElementById('genreFilter');
    const costFilter = document.getElementById('costFilter');
    const resetFilters = document.getElementById('resetFilters');

    if (searchBox) {
        searchBox.addEventListener('input', updateSearch);
    }
    if (countryFilter) {
        countryFilter.addEventListener('change', updateFilter);
    }
    if (genreFilter) {
        genreFilter.addEventListener('change', updateFilter);
    }
    if (costFilter) {
        costFilter.addEventListener('change', updateFilter);
    }
    if (resetFilters) {
        resetFilters.addEventListener('click', () => {
            if (searchBox) searchBox.value = '';
            if (countryFilter) countryFilter.value = '';
            if (genreFilter) genreFilter.value = '';
            if (costFilter) costFilter.value = '';
            updateSearch();
        });
    }
    updateSearch();
}

// ============ Details Page Functions ============

// Add a comment
async function addComment(event) {
    event.preventDefault();
    const commentForm = document.getElementById('commentForm');
    const commentContent = document.getElementById('commentContent');
    const commentsList = document.getElementById('commentsList');
    if (!commentForm || !commentContent || !commentsList) return;

    const content = commentContent.value.trim();
    if (content.length === 0 || content.length > 500) {
        alert('Comment must be between 1 and 500 characters.');
        return;
    }

    const formData = new FormData(commentForm);
    try {
        const response = await fetch(`../../controller/Task4/Task4CommentController.php?action=add`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            const commentDiv = document.createElement('div');
            commentDiv.className = 'comment';
            commentDiv.setAttribute('data-comment-id', data.comment.id);
            commentDiv.innerHTML = `
                <div class="comment-header">
                    <strong>${escapeHtml(data.comment.name)}</strong>
                    <span>${escapeHtml(new Date().toLocaleString())}</span>
                    <button class="delete-comment-btn" data-comment-id="${data.comment.id}">Delete</button>
                </div>
                <div class="comment-content">
                    ${escapeHtml(data.comment.content)}
                </div>
            `;
            commentsList.appendChild(commentDiv);
            commentDiv.querySelector('.delete-comment-btn').addEventListener('click', deleteComment);
            commentContent.value = '';
        } else {
            alert(data.message || 'Unable to add comment.');
        }
    } catch (error) {
        console.error('Add comment error:', error);
        alert('An error occurred while adding the comment.');
    }
}

// Delete a comment
async function deleteComment(event) {
    const button = event.currentTarget;
    const commentId = button.getAttribute('data-comment-id');
    if (!confirm('Delete this comment?')) {
        return;
    }

    const formData = new FormData();
    formData.append('comment_id', commentId);

    try {
        const response = await fetch(`../../controller/Task4/Task4CommentController.php?action=delete`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            button.closest('.comment').remove();
        } else {
            alert(data.message || 'Unable to delete comment.');
        }
    } catch (error) {
        console.error('Delete comment error:', error);
        alert('An error occurred while deleting the comment.');
    }
}

// Attach delete listeners to all delete buttons
function attachDeleteListeners() {
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', deleteComment);
    });
}

// Calculate cost estimate
async function calculateCost() {
    const travelersInput = document.getElementById('travelers');
    const daysInput = document.getElementById('days');
    const postIdInput = document.getElementById('postId');
    const costResult = document.getElementById('costResult');
    if (!travelersInput || !daysInput || !postIdInput || !costResult) return;

    const travelers = parseInt(travelersInput.value, 10);
    const days = parseInt(daysInput.value, 10);
    const postId = postIdInput.value;

    if (travelers < 1 || travelers > 10) {
        alert('Travelers must be between 1 and 10.');
        return;
    }
    if (days < 1) {
        alert('Days must be at least 1.');
        return;
    }

    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('travelers', travelers);
    formData.append('days', days);

    try {
        const response = await fetch(`../../controller/Task4/Task4CostController.php?action=estimate`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            costResult.innerHTML = `
                <h4>Cost Estimate</h4>
                <p><strong>Base Cost:</strong> ${escapeHtml(data.currency)} ${escapeHtml(data.base_cost.toString())}</p>
                <p><strong>Travelers:</strong> ${escapeHtml(data.travelers.toString())}</p>
                <p><strong>Days:</strong> ${escapeHtml(data.days.toString())}</p>
                <p><strong>Total Estimated Cost:</strong> ${escapeHtml(data.currency)} ${escapeHtml(data.total.toString())}</p>
            `;
        } else {
            alert(data.message || 'Unable to calculate cost.');
        }
    } catch (error) {
        console.error('Cost calculation error:', error);
        alert('An error occurred while calculating the cost.');
    }
}

// Initialize details page event listeners
function initDetailsPage() {
    const commentForm = document.getElementById('commentForm');
    const calculateCostButton = document.getElementById('calculateCost');

    if (commentForm) {
        commentForm.addEventListener('submit', addComment);
    }
    attachDeleteListeners();
    if (calculateCostButton) {
        calculateCostButton.addEventListener('click', calculateCost);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initBrowsePage();
    initDetailsPage();
});
