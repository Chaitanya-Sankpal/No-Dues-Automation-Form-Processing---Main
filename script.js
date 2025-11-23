document.addEventListener('DOMContentLoaded', function() {
    const loginBox = document.querySelector('.login-box');
    const container = document.querySelector('.container');
    let isMoving = false;

    // Function to handle box movement
    function moveBox(direction) {
        if (isMoving) return;
        isMoving = true;

        // Remove all movement classes
        loginBox.classList.remove('move-right', 'move-left', 'move-up', 'move-down', 'reset');

        // Add the new movement class
        loginBox.classList.add(direction);

        // Reset after animation
        setTimeout(() => {
            loginBox.classList.add('reset');
            setTimeout(() => {
                loginBox.classList.remove('reset', direction);
                isMoving = false;
            }, 600);
        }, 600);
    }

    // Handle click events on the container
    container.addEventListener('click', function(e) {
        const rect = loginBox.getBoundingClientRect();
        const clickX = e.clientX;
        const clickY = e.clientY;
        const boxCenterX = rect.left + rect.width / 2;
        const boxCenterY = rect.top + rect.height / 2;

        // Calculate click position relative to box center
        const relativeX = clickX - boxCenterX;
        const relativeY = clickY - boxCenterY;

        // Determine direction based on click position
        if (Math.abs(relativeX) > Math.abs(relativeY)) {
            // Horizontal movement
            if (relativeX > 0) {
                moveBox('move-right');
            } else {
                moveBox('move-left');
            }
        } else {
            // Vertical movement
            if (relativeY > 0) {
                moveBox('move-down');
            } else {
                moveBox('move-up');
            }
        }
    });

    // Add hover effect for the login box
    loginBox.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.02)';
    });

    loginBox.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
}); 