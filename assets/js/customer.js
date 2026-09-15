function refreshQueueStatus() {
    var box = document.getElementById('liveToken');

    if (!box) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?action=ajax_queue_status', true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4 || xhr.status !== 200) {
            return;
        }

        try {
            var data = JSON.parse(xhr.responseText);

            if (data.success && data.queue) {
                setCustomerText('peopleAhead', data.queue.people_ahead);
                setCustomerText('currentToken', data.queue.currently_serving || '-');
                setCustomerText('yourCounter', data.queue.counter_name || '-');
                setCustomerText('tokenStatus', data.queue.status);
            } else if (
                data.success &&
                !data.queue &&
                box.getAttribute('data-has-token') === '1'
            ) {
                window.location.reload();
            }
        } catch (e) {
            // Keep the last rendered values if a refresh response cannot be parsed.
        }
    };

    xhr.send();
}

function refreshServiceStats() {
    var counters = document.querySelectorAll('.service-waiting[data-service-id]');

    if (!counters.length) {
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
            if (!data.success) return;

            var map = {};
            for (var i = 0; i < data.services.length; i++) {
                map[String(data.services[i].service_id)] = data.services[i].waiting;
            }

            for (var j = 0; j < counters.length; j++) {
                var id = counters[j].getAttribute('data-service-id');
                if (Object.prototype.hasOwnProperty.call(map, id)) {
                    counters[j].textContent = map[id];
                }
            }
        } catch (e) {
            // Ignore a failed background refresh and keep current values.
        }
    };

    xhr.send();
}

function setCustomerText(id, value) {
    var element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

refreshQueueStatus();
refreshServiceStats();
setInterval(refreshQueueStatus, 5000);
setInterval(refreshServiceStats, 5000);
