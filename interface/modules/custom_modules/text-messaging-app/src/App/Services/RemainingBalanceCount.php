<?php

namespace Juggernaut\Text\Module\App\Services;

class RemainingBalanceCount
{
    public function scriptRemainingBalanceCount()
    {
        ?>
        <script>
            // Function to fetch the count from the server
            function fetchCount() {
                fetch('../../modules/custom_modules/text-messaging-app/resources/fetchCount.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network response was not ok " + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.count !== undefined) {
                            // Update the Knockout observable
                            app_view_model.textbeltData().remainingText(`Remaining Text: ${data.count}`);
                        } else {
                            console.error("Count not found in response", data);
                        }
                    })
                    .catch(error => {
                        console.error("There was a problem with the fetch operation:", error);
                        app_view_model.textbeltData().remainingText("Error fetching count");
                    });
            }

            // Call fetchCount on page load
            fetchCount();

            // Optionally, refresh the count periodically
            setInterval(fetchCount, 300000); // Refresh every 5 minutes
        </script>
<?php
    }
}
