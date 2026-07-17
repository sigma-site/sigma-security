/*
 * Universal Vapi Chat Loader
 * Version 2.1
 */

(function () {

    "use strict";

    console.log("=== Vapi Chat Loader pornit ===");

    if (!window.VAPI_CONFIG) {
        console.error("VAPI_CONFIG lipsă.");
        return;
    }

    console.log("VAPI_CONFIG:", window.VAPI_CONFIG);

    if (!window.VAPI_CONFIG.publicKey) {
        console.error("publicKey lipsă.");
        return;
    }

    if (!window.VAPI_CONFIG.assistantId) {
        console.error("assistantId lipsă.");
        return;
    }

    if (document.querySelector("vapi-widget")) {
        console.log("Widgetul există deja.");
        return;
    }

    function createWidget() {

        console.log("createWidget() a fost apelată.");

        var cfg = window.VAPI_CONFIG;

        var widget = document.createElement("vapi-widget");

        widget.setAttribute("public-key", cfg.publicKey);
        widget.setAttribute("assistant-id", cfg.assistantId);

        widget.setAttribute("mode", cfg.mode);
        widget.setAttribute("theme", cfg.theme);

        widget.setAttribute("position", cfg.position);
        widget.setAttribute("size", cfg.size);

        widget.setAttribute("border-radius", cfg.borderRadius);

        widget.setAttribute("accent-color", cfg.accentColor);
        widget.setAttribute("button-base-color", cfg.buttonBaseColor);
        widget.setAttribute("button-accent-color", cfg.buttonAccentColor);
        widget.setAttribute("base-bg-color", cfg.baseBgColor);

        widget.setAttribute("main-label", cfg.mainLabel);
        widget.setAttribute("chat-first-message", cfg.firstMessage);
        widget.setAttribute("chat-placeholder", cfg.placeholder);

        widget.setAttribute(
            "voice-show-transcript",
            String(cfg.voiceShowTranscript)
        );

        document.body.appendChild(widget);

        console.log("Widget adăugat în pagină.");
        console.log(widget);
    }

    var script = document.createElement("script");

    script.src =
        "https://unpkg.com/@vapi-ai/client-sdk-react/dist/embed/widget.umd.js";

    script.async = true;

    script.onload = function () {

        console.log("SDK Vapi încărcat.");

        createWidget();

    };

    script.onerror = function () {

        console.error("Nu s-a putut încărca Vapi Widget SDK.");

    };

    document.head.appendChild(script);

})();
