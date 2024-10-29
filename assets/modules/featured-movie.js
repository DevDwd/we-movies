export default class FeaturedMovie {
    constructor() {
        this.initializeTrailerButtons();
    }

    initializeTrailerButtons() {
        const watchButtons = document.querySelectorAll('.watch-trailer');
        
        watchButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                const movieId = button.dataset.movieId;
                await this.loadVideo(movieId, button);
            });
        });
    }

    async loadVideo(movieId, button) {
        const videoContainer = button.closest('.featured-movie').querySelector('.video-container');
        if (!videoContainer) return;

        try { 
            const response = await fetch(`/api/movie/${movieId}/video`);
            const data = await response.json();
            
            if (data && data.key) {
                videoContainer.innerHTML = `
                    <iframe
                        width="100%"
                        height="400"
                        src="https://www.youtube.com/embed/${data.key}?autoplay=1"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                `;
                videoContainer.classList.add('active');
                button.style.display = 'none';
            } else {
                videoContainer.innerHTML = `
                    <div class="video-error">
                        Désolé, la bande-annonce n'est pas disponible.
                    </div>
                `;
                videoContainer.classList.add('active');
            }
        } catch (error) {
            console.error('Error loading video:', error);
            videoContainer.innerHTML = `
                <div class="video-error">
                    Désolé, la bande-annonce n'est pas disponible.
                </div>
            `;
            videoContainer.classList.add('active');
        }
    }
}