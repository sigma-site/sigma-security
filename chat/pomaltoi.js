/*
 * Configurație Asistent Pomaltoi
 * Version: 1.0
 */

(function () {

    "use strict";

    window.VAPI_CONFIG = {

        // ==========================
        // IDENTIFICARE ASISTENT
        // ==========================

        publicKey: "AICI_PUBLIC_KEY",

        assistantId: "AICI_ASSISTANT_ID",


        // ==========================
        // CONFIGURARE CHAT
        // ==========================

        mode: "chat",

        theme: "light",

        position: "bottom-right",

        size: "full",

        borderRadius: "large",


        // ==========================
        // IDENTITATE VIZUALĂ
        // ==========================

        accentColor: "#2F9E44",

        buttonBaseColor: "#2F9E44",

        buttonAccentColor: "#FFFFFF",

        baseBgColor: "#FFFFFF",


        // ==========================
        // MESAJE
        // ==========================

        mainLabel: "Consultant Pomaltoi AI",

        firstMessage: "Bună ziua! Sunt consultantul AI Pomaltoi. Cu ce vă pot ajuta?",

        placeholder: "Scrieți întrebarea...",


        // ==========================
        // OPȚIUNI
        // ==========================

        voiceShowTranscript: false

    };


    var script = document.createElement("script");

    script.src = "https://sigmasecurity.ro/chat/vapi-chat.js";

    script.async = true;

    document.head.appendChild(script);

})();
