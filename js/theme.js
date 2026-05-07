document.addEventListener('DOMContentLoaded', () => {
    // Get the toggle checkbox element by ID
    var themeSwitch = document.getElementById('themeSwitch');

    // Define the logo paths
    const logoLight = "./img/logo.png";
    const logoDark = "./img/logo-dark.png";

    if (themeSwitch) {
        // 1. Initial Setup on DOM Load
        initTheme();

        // 2. Toggle Handler: Listen for the change event on the checkbox
        themeSwitch.addEventListener('change', function () {
            resetTheme(); // update color theme and logo
        });
    }

    // Called once when the page loads
    function initTheme() {
        // Use the same key used in the initial <head> script
        var currentTheme = localStorage.getItem('themeSwitch');

        // 1. Set the initial state of the checkbox
        // If theme is 'dark' or null (default), check the box.
        themeSwitch.checked = (currentTheme === 'dark' || currentTheme === null);

        // 2. Apply theme and logo based on the final state
        // If localStorage is null, we assume 'dark' (as set in <head>)
        updateTheme(currentTheme || 'dark');
    };

    // Called when the user clicks the toggle
    function resetTheme() {
        // Determine the new theme based on the checkbox state
        var newTheme = themeSwitch.checked ? 'dark' : 'light';

        // Save the new theme to localStorage
        localStorage.setItem('themeSwitch', newTheme);

        // Apply the new theme and logo
        updateTheme(newTheme);
    };

    function updateTheme(theme) {
        // *** Always target the <html> element (documentElement) ***
        var htmlElement = document.documentElement;
        var logoElement = document.getElementById("imglogo");

        if (theme === 'dark') {
            htmlElement.setAttribute('data-theme', 'dark');
            // Check if the element exists before trying to set its src
            if (logoElement) {
                // Set logo SRC to the dark version
                logoElement.src = logoDark;
            }
        } else {
            htmlElement.setAttribute('data-theme', 'light');
            if (logoElement) {
                // Set logo SRC to the light version
                logoElement.src = logoLight;
            }
        }
    }
});