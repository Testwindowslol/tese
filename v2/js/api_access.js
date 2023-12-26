(() => {
    let yesButton = document.getElementById("alert-button-yes");
    let noButton = document.getElementById("alert-button-no");

    let copyRequestLinkButton = document.getElementById("button-copy-request-link");
    let copyApiKeyButton = document.getElementById("button-copy-api-key");
    let regenerateApiKeyButton = document.getElementById("button-regenerate-api-key");

    let apiRequestLink = document.getElementById("api-request-link");
    let apiKey = document.getElementById("api-key");

    let backgroundCover = document.getElementById("background-cover");
    let alertPopup = document.getElementById("alert-popup");

    let notificationPopup = document.getElementById("notification-popup");
    let notificationPopupIconSuccess = document.getElementById("notification-popup-icon-success");
    let notificationPopupIconError = document.getElementById("notification-popup-icon-error");
    let notificationPopupMessage = document.getElementById("notification-popup-message");

    function showAlertPopup() {
        backgroundCover.classList.add("visible");
        alertPopup.classList.add("visible");
    }
    
    function hideAlertPopup() {
        backgroundCover.classList.remove("visible");
        alertPopup.classList.remove("visible");
    }

    function showNotificationPopup(type, message) {
        if (type === "success") {
            notificationPopupIconSuccess.classList.add("visible");
            notificationPopupIconError.classList.remove("visible");
        } else if (type === "error") {
            notificationPopupIconError.classList.add("visible");
            notificationPopupIconSuccess.classList.remove("visible");
        } else {
            console.error("Unknown notification popup type : " + type + ".");
            return;
        }

        notificationPopup.classList.remove("success");
        notificationPopup.classList.remove("error");

        notificationPopup.classList.add(type);
        notificationPopup.classList.add("visible");

        notificationPopupMessage.innerHTML = message;

        setTimeout(hideNotificationPopup, 2000);
    }

    function hideNotificationPopup() {
        notificationPopup.classList.remove("visible");
    }

    async function copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            showNotificationPopup("success", "Copied !");
        } catch (err) {
            showNotificationPopup("error", "Failed !");
        }
    }

    // Listeners
    yesButton.onclick = (() => {
        hideAlertPopup();

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === this.DONE) {
                let data = JSON.parse(this.responseText);

                if (this.status === 200) {
                    showNotificationPopup("success", "Success !");
                    apiKey.value = data["new_api_key"];
                } else {
                    showNotificationPopup("error", "Error !");
                    console.error(data["error"] + " : " + data["error_message"]);
                }
            }
        };
        xhr.open("GET", window.location.origin + "/v2/ajax/api_key.php?regenerate");
        xhr.send();
    });

    noButton.onclick = (() => {
        hideAlertPopup();
    });

    copyRequestLinkButton.onclick = (() => {
        copyToClipboard(apiRequestLink.value);
    });

    copyApiKeyButton.onclick = (() => {
        copyToClipboard(apiKey.value);
    });

    regenerateApiKeyButton.onclick = (() => {
        showAlertPopup();
    });
})();
