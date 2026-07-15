/*
 * Universal Vapi Chat Loader
 * Version 1.0
 * https://sigmasecurity.ro/chat/vapi-chat.js
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

    // Evită încărcarea dublă
    if (document.querySelector("vapi-widget")) {
        return;
    }

    function createWidget() {

        var widget = document.createElement("vapi-widget");

        widget.setAttribute("public-key", window.VAPI_CONFIG.publicKey);
        widget.setAttribute("assistant-id", window.VAPI_CONFIG.assistantId);

        widget.setAttribute("mode", "chat");
        widget.setAttribute("theme", "light");

        widget.setAttribute("base-bg-color", "#ffffff");
        widget.setAttribute("accent-color", "#2f9e44");

        widget.setAttribute("button-base-color", "#2f9e44");
        widget.setAttribute("button-accent-color", "#ffffff");

        widget.setAttribute("border-radius", "large");
        widget.setAttribute("size", "full");
        widget.setAttribute("position", "bottom-right");

        widget.setAttribute("main-label", "Asistent AI");
        widget.setAttribute("chat-first-message", "Bună ziua!");
        widget.setAttribute("chat-placeholder", "Scrie un mesaj...");

        widget.setAttribute("voice-show-transcript", "false");

        document.body.appendChild(widget);
    }

    var script = document.createElement("script");

    script.src = "https://unpkg.com/@vapi-ai/client-sdk-react/dist/embed/widget.umd.js";
    script.async = true;

    script.onload = createWidget;

    script.onerror = function () {
        console.error("Cannot load Vapi Widget SDK.");
    };

    document.head.appendChild(script);

})();
