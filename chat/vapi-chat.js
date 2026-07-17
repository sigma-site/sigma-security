/*
 * Universal Vapi Chat Loader
 * Version 2.0
 */

(function () {

    "use strict";
    
alert("Sunt în vapi-chat.js");
    if (!window.VAPI_CONFIG) {
        console.error("VAPI_CONFIG lipsă.");
        return;
    }

    const cfg = window.VAPI_CONFIG;

    function createWidget() {

        if (document.querySelector("vapi-widget")) {
            return;
        }

        const widget = document.createElement("vapi-widget");

        widget.setAttribute("public-key", cfg.publicKey);
        widget.setAttribute("assistant-id", cfg.assistantId);

        widget.setAttribute("mode", cfg.mode || "chat");
        widget.setAttribute("theme", cfg.theme || "light");
        widget.setAttribute("position", cfg.position || "bottom-right");
        widget.setAttribute("size", cfg.size || "full");
        widget.setAttribute("border-radius", cfg.borderRadius || "large");

        widget.setAttribute("accent-color", cfg.accentColor || "#2F9E44");
        widget.setAttribute("button-base-color", cfg.buttonBaseColor || "#2F9E44");
        widget.setAttribute("button-accent-color", cfg.buttonAccentColor || "#FFFFFF");
        widget.setAttribute("base-bg-color", cfg.baseBgColor || "#FFFFFF");

        widget.setAttribute("main-label", cfg.mainLabel || "Asistent AI");
        widget.setAttribute("chat-first-message", cfg.firstMessage || "Bună ziua!");
        widget.setAttribute("chat-placeholder", cfg.placeholder || "Scrie un mesaj...");

        widget.setAttribute(
            "voice-show-transcript",
            String(cfg.voiceShowTranscript ?? false)
        );

        document.body.appendChild(widget);
    }

    if (customElements.get("vapi-widget")) {
        createWidget();
        return;
    }

    const script = document.createElement("script");
    script.src = "https://unpkg.com/@vapi-ai/client-sdk-react/dist/embed/widget.umd.js";
    script.async = true;

    script.onload = function () {
        createWidget();
    };

    script.onerror = function () {
        console.error("Nu s-a putut încărca SDK-ul Vapi.");
    };

    document.head.appendChild(script);

})();
