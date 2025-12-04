document.addEventListener('DOMContentLoaded', function() {
    const countryInput = document.getElementById('country');
    const lookupCountryBtn = document.getElementById('lookup');
    const lookupCitiesBtn = document.getElementById('lookup-cities');
    const resultDiv = document.getElementById('result');

    // Helper function to perform the AJAX fetch
    function performLookup(lookupType) {
        const countryName = countryInput.value.trim();

        if (countryName === "") {
            resultDiv.innerHTML = '<p class="error-message">Please enter a country name.</p>';
            return;
        }

        // Show loading state
        resultDiv.innerHTML = '<p class="loading-message">Searching database...</p>';

        // Build the URL based on the button clicked
        let url = `world.php?country=${encodeURIComponent(countryName)}`;
        if (lookupType === 'cities') {
            url += '&lookup=cities';
        }

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                // Inject the HTML table fragment received from PHP into the result div
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                console.error('Error during fetch:', error);
                resultDiv.innerHTML = '<p class="error-message">An error occurred while fetching data.</p>';
            });
    }

    // Attach Event Listeners
    // These remain attached because we never overwrite these buttons in the HTML
    lookupCountryBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent form submission if inside a form tag
        performLookup('country');
    });

    lookupCitiesBtn.addEventListener('click', function(e) {
        e.preventDefault();
        performLookup('cities');
    });
});