/*
 * Vapi Chat Loader
 * https://sigmasecurity.ro/chat/vapi-chat.js
 * Version 1.0
 */

(function () {

    "use strict";

    if (!window.VAPI_CONFIG) {
        console.error("VAPI_CONFIG is missing.");
        return;
    }

    if (!window.VAPI_CONFIG.publicKey) {
        console.error("publicKey is missing.");
        return;
    }

    if (!window.VAPI_CONFIG.assistantId) {
        console.error("assistantId is missing.");
        return;
    }

    function loadWidgetSdk(callback) {

        if (customElements.get("vapi-widget")) {
            callback();
            return;
        }

        const script = document.createElement("script");

        script.src = "https://unpkg.com/@vapi-ai/client-sdk-react/dist/embed/widget.umd.js";
        script.async = true;

        script.onload = callback;

        script.onerror = function () {
            console.error("Unable to load Vapi Widget SDK.");
        };

        document.head.appendChild(script);

    }

    function createWidget() {

        if (document.querySelector("vapi-widget")) {
            return;
        }

        const widget = document.createElement("vapi-widget");

        widget.setAttribute("public-key", window.VAPI_CONFIG.publicKey);
        widget.setAttribute("assistant-id", window.VAPI_CONFIG.assistantId);

        widget.setAttribute("mode", window.VAPI_CONFIG.mode || "chat");
        widget.setAttribute("theme", window.VAPI_CONFIG.theme || "light");

        document.body.appendChild(widget);

        console.log("Vapi widget loaded.");

    }

    loadWidgetSdk(createWidget);

})();
