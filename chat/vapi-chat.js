

/*
 * Universal Vapi Chat Loader
 * https://sigmasecurity.ro/chat/vapi-chat.js
 * Version 2.0
 */

(function () {
    "use strict";

    if (!window.VAPI_CONFIG) {
        console.error("VAPI_CONFIG nu este definit.");
        return;
    }

    if (!window.VAPI_CONFIG.publicKey) {
        console.error("Lipsește publicKey.");
        return;
    }

    if (!window.VAPI_CONFIG.assistantId) {
        console.error("Lipsește assistantId.");
        return;
    }

    function start() {

        // dacă există deja un widget, nu mai adăugăm altul
        if (document.querySelector("vapi-widget")) {
            return;
        }

        const script = document.createElement("script");
        script.src = "https://unpkg.com/@vapi-ai/client-sdk-react/dist/embed/widget.umd.js";
        script.async = true;
        document.head.appendChild(script);

        const widget = document.createElement("vapi-widget");

        widget.setAttribute("public-key", window.VAPI_CONFIG.publicKey);
        widget.setAttribute("assistant-id", window.VAPI_CONFIG.assistantId);

        widget.setAttribute("mode", window.VAPI_CONFIG.mode || "chat");
        widget.setAttribute("theme", window.VAPI_CONFIG.theme || "light");

        widget.setAttribute("base-bg-color", window.VAPI_CONFIG.baseBgColor || "#ffffff");
        widget.setAttribute("accent-color", window.VAPI_CONFIG.accentColor || "#2f9e44");
        widget.setAttribute("button-base-color", window.VAPI_CONFIG.buttonBaseColor || "#2f9e44");
        widget.setAttribute("button-accent-color", window.VAPI_CONFIG.buttonAccentColor || "#ffffff");

        widget.setAttribute("border-radius", window.VAPI_CONFIG.borderRadius || "large");
        widget.setAttribute("size", window.VAPI_CONFIG.size || "full");
        widget.setAttribute("position", window.VAPI_CONFIG.position || "bottom-right");

        widget.setAttribute("main-label", window.VAPI_CONFIG.mainLabel || "Asistent AI");
        widget.setAttribute("chat-first-message", window.VAPI_CONFIG.firstMessage || "Bună ziua!");
        widget.setAttribute("chat-placeholder", window.VAPI_CONFIG.placeholder || "Scrie un mesaj...");

        widget.setAttribute("voice-show-transcript", "false");

        document.body.appendChild(widget);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", start);
    } else {
        start();
    }

})();
