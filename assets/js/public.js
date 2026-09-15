var publicServices = [];
var selectedPublicService = 0;
var publicTimer = null;

function loadPublicServices(moveNext) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "index.php?action=ajax_public_services", true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);

            if (data.success && data.services.length > 0) {
                publicServices = data.services;

                if (selectedPublicService >= publicServices.length) {
                    selectedPublicService = 0;
                } else if (moveNext) {
                    selectedPublicService++;

                    if (selectedPublicService >= publicServices.length) {
                        selectedPublicService = 0;
                    }
                }

                showPublicService(selectedPublicService);
                drawServiceButtons();
            }
        }
    };

    xhr.send();
}

function showPublicService(index) {
    if (!publicServices[index]) {
        return;
    }

    var service = publicServices[index];

    document.getElementById("publicServiceName").innerHTML = escapePublicHtml(service.name);
    document.getElementById("publicServiceShort").innerHTML = escapePublicHtml(service.name);

    if (service.current_token) {
        document.getElementById("publicCurrentToken").innerHTML = escapePublicHtml(service.current_token);
    } else {
        document.getElementById("publicCurrentToken").innerHTML = "-";
    }

    document.getElementById("publicWaiting").innerHTML = service.waiting;

    if (service.counter_name) {
        document.getElementById("publicCounter").innerHTML = escapePublicHtml(service.counter_name);
    } else {
        document.getElementById("publicCounter").innerHTML = "-";
    }
}

function drawServiceButtons() {
    var box = document.getElementById("publicServiceButtons");

    if (!box) {
        return;
    }

    var html = "";

    for (var i = 0; i < publicServices.length; i++) {
        var active = "";

        if (i === selectedPublicService) {
            active = " active";
        }

        html += '<button type="button" class="service-pill' + active + '" ';
        html += 'onclick="selectPublicService(' + i + ')">';
        html += escapePublicHtml(shortServiceName(publicServices[i].name));
        html += '</button>';
    }

    box.innerHTML = html;
}

function selectPublicService(index) {
    selectedPublicService = index;

    showPublicService(index);
    drawServiceButtons();

    clearInterval(publicTimer);

    publicTimer = setInterval(function () {
        loadPublicServices(true);
    }, 3000);
}

function shortServiceName(name) {
    return name.replace(" Office", "").replace(" Help Desk", "");
}

function escapePublicHtml(text) {
    var div = document.createElement("div");
    div.innerText = text || "";
    return div.innerHTML;
}

loadPublicServices(false);

publicTimer = setInterval(function () {
    loadPublicServices(true);
}, 3000);
