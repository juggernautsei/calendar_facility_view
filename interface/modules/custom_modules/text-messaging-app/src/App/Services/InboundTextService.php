<?php

namespace Juggernaut\Text\Module\App;

class InboundTextService
{

    public function injectInboundTextAlert(): string
    {
        return <<<HTML
        <script>
        // Step 1: Select the div element
        const attendantDataDiv = document.getElementById('attendantData');

        // Step 2: Check if the div exists
        if (attendantDataDiv) {
            // Step 3: Create a new button element
            const button = document.createElement('button');

            // Step 4: Set button properties
            button.textContent = 'Click Me';
            button.id = 'attendantButton'; // Optional
            button.className = 'btn btn-primary'; // Optional

            // Step 5: Append the button to the div
            attendantDataDiv.appendChild(button);
        } else {
            console.error('Div with id "attendantData" not found.');
        }
        </script>
        HTML;
    }
}
