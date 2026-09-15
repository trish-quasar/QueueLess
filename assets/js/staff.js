function refreshStaffQueue() {
    var waitingBox = document.getElementById("staffWaiting");

    if (!waitingBox) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "index.php?action=ajax_staff_queue", true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);

            if (!data.success) {
                return;
            }

            var html = "";

            for (var i = 0; i < data.waiting.length; i++) {
                html += '<div class="list-row">';
                html += '<div>';
                html += '<b>' + escapeHtml(data.waiting[i].token_number) + '</b>';
                html += '<small>' + escapeHtml(data.waiting[i].full_name) + '</small>';
                html += '</div>';
                html += '<span>Waiting</span>';
                html += '</div>';
            }

            if (html === "") {
                html = '<div class="empty-state compact">No customers are waiting.</div>';
            }

            waitingBox.innerHTML = html;

            var currentBox = document.getElementById("staffCurrent");

            if (currentBox) {
                if (data.current) {
                    var currentHtml = '';
                    currentHtml += '<div class="big-token">';
                    currentHtml += escapeHtml(data.current.token_number);
                    currentHtml += '</div>';
                    currentHtml += '<p>';
                    currentHtml += escapeHtml(data.current.full_name);
                    currentHtml += ' &middot; ';
                    currentHtml += escapeHtml(data.current.status);
                    currentHtml += '</p>';

                    currentBox.innerHTML = currentHtml;
                } else {
                    currentBox.innerHTML = '<div class="empty-state compact">No token is currently called.</div>';
                }
            }

            var statusBox = document.getElementById("counterStatus");

            if (statusBox) {
                statusBox.innerHTML = escapeHtml(data.counter_status);
            }
        }
    };

    xhr.send();
}

function escapeHtml(text) {
    var div = document.createElement("div");
    div.innerText = text || "";
    return div.innerHTML;
}

setInterval(refreshStaffQueue, 5000);
