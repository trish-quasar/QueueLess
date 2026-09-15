function refreshStaffQueue() {
    var waitingBox = document.getElementById('staffWaiting');

    if (!waitingBox) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?action=ajax_staff_queue', true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4 || xhr.status !== 200) {
            return;
        }

        try {
            var data = JSON.parse(xhr.responseText);

            if (!data.success) {
                return;
            }

            var html = '';

            for (var i = 0; i < data.waiting.length; i++) {
                html += '<div class="list-row">';
                html += '<div><b>' + escapeStaffHtml(data.waiting[i].token_number) + '</b>';
                html += '<small>' + escapeStaffHtml(data.waiting[i].full_name) + '</small></div>';
                html += '<span class="status-pill neutral">Waiting</span>';
                html += '</div>';
            }

            if (html === '') {
                html = '<div class="empty-state compact">No customers are waiting.</div>';
            }

            waitingBox.innerHTML = html;

            var currentBox = document.getElementById('staffCurrent');
            if (currentBox) {
                if (data.current) {
                    currentBox.innerHTML =
                        '<div class="big-token">' + escapeStaffHtml(data.current.token_number) + '</div>' +
                        '<p>' + escapeStaffHtml(data.current.full_name) + ' &middot; ' +
                        '<span class="status-pill">' + escapeStaffHtml(data.current.status) + '</span></p>';
                } else {
                    currentBox.innerHTML = '<div class="empty-state compact">No token is currently called.</div>';
                }
            }

            var statusBox = document.getElementById('counterStatus');
            if (statusBox) {
                statusBox.textContent = data.counter_status;
                statusBox.classList.remove('open', 'closed');
                statusBox.classList.add(data.counter_status === 'Open' ? 'open' : 'closed');
            }
        } catch (e) {
            // Leave the current queue visible if a background refresh fails.
        }
    };

    xhr.send();
}

function escapeStaffHtml(text) {
    var div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

refreshStaffQueue();
setInterval(refreshStaffQueue, 5000);
