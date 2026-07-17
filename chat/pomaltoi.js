/*
 * Configurație Asistent Pomaltoi
 * Test fără vapi-chat.js
 */

(function () {

    "use strict";

    const cfg = {

        publicKey: "ab245358-61ea-4876-b767-8e4ad159627d",

        assistantId: "5df5fcb7-fb00-496d-b0b1-e447c8ad207a",

        mode: "chat",

        theme: "light",

        position: "bottom-right",

        size: "full",

        borderRadius: "large",

        accentColor: "#2F9E44",

        buttonBaseColor: "#2F9E44",

        buttonAccentColor: "#FFFFFF",

        baseBgColor: "#FFFFFF",

        mainLabel: "Consultant Pomaltoi AI",

        firstMessage: "Bună ziua! Sunt consultantul AI Pomaltoi. Cu ce vă pot ajuta?",

        placeholder: "Scrieți întrebarea...",

        voiceShowTranscript: false

    };

    const sdk = document.createElement("script");

    sdk.src = "https://unpkg.com/@vapi-ai/client-sdk-react/dist/embed/widget.umd.js";

    sdk.onload = function () {

        const widget = document.createElement("vapi-widget");

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

    };

    document.head.appendChild(sdk);

})();
