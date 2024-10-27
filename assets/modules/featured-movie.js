// assets/modules/featured-movie.js

export default class FeaturedMovie {
    constructor() {
        // Initialisation immédiate
        this.initializeTrailerButtons();
    }

    initializeTrailerButtons() {
        const watchButtons = document.querySelectorAll('.watch-trailer');
        console.log('Found watch buttons:', watchButtons.length);

        watchButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                const movieId = button.dataset.movieId;
                console.log('Loading trailer for movie:', movieId);
                await this.loadVideo(movieId, button);
            });
        });
    }

    async loadVideo(movieId, button) {
        try {
            // Trouver le container vidéo associé au bouton
            const videoContainer = button.closest('.featured-movie').querySelector('.video-container');
            if (!videoContainer) return;

            console.log('Fetching video for movie:', movieId);
            const response = await fetch(`/movie/${movieId}/video`);
            const data = await response.json();
            
            if (data && data.key) {
                console.log('Found video key:', data.key);
                this.showVideo(data.key, videoContainer);
                button.style.display = 'none'; // Cache le bouton une fois la vidéo chargée
            } else {
                console.log('No video found');
                this.showError(videoContainer);
            }
        } catch (error) {
            console.error('Error loading video:', error);
            if (videoContainer) {
                this.showError(videoContainer);
            }
        }
    }

    showVideo(videoKey, container) {
        container.innerHTML = `
            <iframe
                width="100%"
                height="400"
                src="https://www.youtube.com/embed/${videoKey}?autoplay=1"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
        `;
        container.classList.add('active');
    }

    showError(container) {
        container.innerHTML = `
            <div class="video-error">
                Désolé, la bande-annonce n'est pas disponible.
            </div>
        `;
        container.classList.add('active');
    }
}