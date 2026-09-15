function refreshAdminDashboard() {
    var tableBody = document.getElementById('adminServiceRows');

    if (!tableBody) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?action=ajax_service_stats', true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4 || xhr.status !== 200) {
            return;
        }

        try {
            var data = JSON.parse(xhr.responseText);

            if (!data.success) {
                return;
            }

            if (data.summary) {
                updateText('adminUsers', data.summary.users);
                updateText('adminWaiting', data.summary.waiting);
                updateText('adminServed', data.summary.served);
                updateText('adminOpenCounters', data.summary.open_counters);
            }

            var html = '';

            for (var i = 0; i < data.services.length; i++) {
                var service = data.services[i];
                html += '<tr>';
                html += '<td><strong>' + escapeAdminHtml(service.name) + '</strong></td>';
                html += '<td>' + Number(service.waiting || 0) + '</td>';
                html += '<td>' + escapeAdminHtml(service.current_token || '-') + '</td>';
                html += '</tr>';
            }

            tableBody.innerHTML = html;
        } catch (e) {
            // Keep the current server-rendered values if a refresh response is invalid.
        }
    };

    xhr.send();
}

function updateText(id, value) {
    var element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

function escapeAdminHtml(text) {
    var div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

setInterval(refreshAdminDashboard, 5000);
