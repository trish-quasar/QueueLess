var publicServices = [];
var selectedPublicService = 0;
var publicTimer = null;
var publicRequestRunning = false;

function loadPublicServices(moveNext) {
    if (publicRequestRunning) {
        return;
    }

    publicRequestRunning = true;

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?action=ajax_public_services', true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) {
            return;
        }

        publicRequestRunning = false;

        if (xhr.status !== 200) {
            return;
        }

        try {
            var data = JSON.parse(xhr.responseText);

            if (!data.success || !data.services || data.services.length === 0) {
                showNoPublicServices();
                return;
            }

            publicServices = data.services;

            if (selectedPublicService >= publicServices.length) {
                selectedPublicService = 0;
            } else if (moveNext) {
                selectedPublicService = (selectedPublicService + 1) % publicServices.length;
            }

            showPublicService(selectedPublicService);
            drawServiceButtons();
        } catch (e) {
            // Keep the last successfully loaded service visible.
        }
    };

    xhr.send();
}

function showPublicService(index) {
    if (!publicServices[index]) {
        return;
    }

    var service = publicServices[index];

    setPublicText('publicServiceName', service.name);
    setPublicText('publicServiceShort', shortServiceName(service.name));
    setPublicText('publicCurrentToken', service.current_token || '-');
    setPublicText('publicWaiting', Number(service.waiting || 0));
    setPublicText('publicCounter', service.counter_name || '-');
}

function drawServiceButtons() {
    var box = document.getElementById('publicServiceButtons');

    if (!box) {
        return;
    }

    box.innerHTML = '';

    for (var i = 0; i < publicServices.length; i++) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'service-pill' + (i === selectedPublicService ? ' active' : '');
        button.textContent = shortServiceName(publicServices[i].name);
        button.setAttribute('data-index', i);
        button.addEventListener('click', function () {
            selectPublicService(Number(this.getAttribute('data-index')));
        });
        box.appendChild(button);
    }
}

function selectPublicService(index) {
    selectedPublicService = index;
    showPublicService(index);
    drawServiceButtons();
    restartPublicTimer();
}

function restartPublicTimer() {
    if (publicTimer) {
        clearInterval(publicTimer);
    }

    publicTimer = setInterval(function () {
        loadPublicServices(true);
    }, 3000);
}

function shortServiceName(name) {
    return String(name || '')
        .replace(' Office', '')
        .replace(' Help Desk', '');
}

function setPublicText(id, value) {
    var element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

function showNoPublicServices() {
    setPublicText('publicServiceName', 'No active service');
    setPublicText('publicServiceShort', '-');
    setPublicText('publicCurrentToken', '-');
    setPublicText('publicWaiting', '0');
    setPublicText('publicCounter', '-');

    var box = document.getElementById('publicServiceButtons');
    if (box) {
        box.innerHTML = '<span class="muted">No active services right now.</span>';
    }
}

loadPublicServices(false);
restartPublicTimer();
