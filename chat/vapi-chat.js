/*
 * Universal Vapi Chat Loader
 * Version 2.0
 */

(function () {

    "use strict";

    alert("0 - Sunt în vapi-chat.js");

    if (!window.VAPI_CONFIG) {
        alert("VAPI_CONFIG lipsește");
        console.error("VAPI_CONFIG lipsă.");
        return;
    }

    alert("1 - VAPI_CONFIG există");

    const cfg = window.VAPI_CONFIG;

    alert("2 - cfg creat");

    function createWidget() {

        alert("3 - createWidget()");

        if (document.querySelector("vapi-widget")) {
            alert("4 - Widget există deja");
            return;
        }

        alert("5 - Creez widget");

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

        alert("6 - Înainte de appendChild");

        document.body.appendChild(widget);

        alert("7 - După appendChild");
    }

    alert("8 - Verific customElements");

    if (customElements.get("vapi-widget")) {

        alert("9 - Widget deja înregistrat");

        createWidget();

        return;
    }

    alert("10 - Încarc SDK");

    const script = document.createElement("script");

    script.src = "https://unpkg.com/@vapi-ai/client-sdk-react/dist/embed/widget.umd.js";

    script.async = true;

    script.onload = function () {

        alert("11 - SDK încărcat");

        createWidget();
    };

    script.onerror = function () {

        alert("EROARE la încărcarea SDK");

        console.error("Nu s-a putut încărca SDK-ul Vapi.");
    };

    document.head.appendChild(script);

    alert("12 - Script adăugat în HEAD");

})();
