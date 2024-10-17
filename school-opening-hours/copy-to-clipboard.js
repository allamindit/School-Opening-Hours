document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.copy-code-btn');

    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const code = button.getAttribute('data-clipboard-text');

            // Create a temporary textarea element to copy text from
            const textarea = document.createElement('textarea');
            textarea.value = code;
            document.body.appendChild(textarea);

            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            // Optionally, provide user feedback
            button.textContent = 'Copied!';
            setTimeout(function() {
                button.textContent = 'Copy code';
            }, 2000);
        });
    });
});
