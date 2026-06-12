<script>
    // Create stars
    function createStars() {
        const stars = document.querySelector('.stars');
        const numStars = 200;

        for (let i = 0; i < numStars; i++) {
            const star = document.createElement('div');
            star.className = 'star';

            // Random position
            const x = Math.random() * 100;
            const y = Math.random() * 100;

            // Random size
            const size = Math.random() * 3;

            star.style.left = `${x}%`;
            star.style.top = `${y}%`;
            star.style.width = `${size}px`;
            star.style.height = `${size}px`;

            // Random animation delay
            star.style.animationDelay = `${Math.random() * 1}s`;

            stars.appendChild(star);
        }
    }

    createStars();
</script>
