/*
 * Vapi Chat Loader
 * Version: 0.1
 * https://sigmasecurity.ro/chat/vapi-chat.js
 */

(function () {
    "use strict";

    // Verifică dacă există configurația
    if (!window.VAPI_CONFIG) {
        console.error("VAPI_CONFIG nu este definit.");
        return;
    }

    console.log("Vapi Chat Loader");
    console.log(window.VAPI_CONFIG);

})();
