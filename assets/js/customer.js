function refreshQueueStatus() {
    var box = document.getElementById("liveToken");

    if (!box) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "index.php?action=ajax_queue_status", true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);

            if (data.success && data.queue) {
                var ahead = document.getElementById("peopleAhead");
                var current = document.getElementById("currentToken");
                var counter = document.getElementById("yourCounter");
                var status = document.getElementById("tokenStatus");

                if (ahead) {
                    ahead.innerHTML = data.queue.people_ahead;
                }

                if (current) {
                    current.innerHTML = data.queue.currently_serving;
                }

                if (counter) {
                    if (data.queue.counter_name) {
                        counter.innerHTML = data.queue.counter_name;
                    } else {
                        counter.innerHTML = "-";
                    }
                }

                if (status) {
                    status.innerHTML = data.queue.status;
                }

            } else if (
                data.success &&
                !data.queue &&
                box.getAttribute("data-has-token") === "1"
            ) {
                window.location.reload();
            }
        }
    };

    xhr.send();
}

setInterval(refreshQueueStatus, 5000);
