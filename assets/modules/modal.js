export default class MovieModal {
    constructor() {
        if (MovieModal.instance) {
            return MovieModal.instance;
        }

        this.modal = document.getElementById('movieModal');
        if (!this.modal) {
            console.error('Modal element not found');
            return;  // Sort du constructeur si pas de modal
        }
        this.closeBtn = this.modal.querySelector('.modal-close');
        this.currentMovieId = null;
        this.currentRating = 0;
        this.init();

        MovieModal.instance = this;
    }

    init() {
        if (!this.modal) return;
        
        // Fermeture de la modal
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.hide());
        }
        
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) this.hide();
        });

        // Gestion des étoiles - Mise à jour du sélecteur
        const starsContainer = this.modal.querySelector('.rating-stars');  // Changé de .stars à .rating-stars
        if (starsContainer) {
            const stars = starsContainer.querySelectorAll('.star');

            stars.forEach(star => {
                star.addEventListener('mouseover', () => {
                    const rating = parseInt(star.dataset.value);
                    this.highlightStars(rating);
                });

                star.addEventListener('click', () => {
                    const rating = parseInt(star.dataset.value);
                    this.submitRating(rating);
                });
            });

            starsContainer.addEventListener('mouseleave', () => {
                this.highlightStars(this.currentRating);
            });
        }
    }

    async show(movie) {
        // Vérifier qu'on a bien un objet movie 
        console.log(movie);
        if (!movie || typeof movie !== 'object') {
            console.error('Invalid movie data:', movie);
            return;
        }

        console.log('Données reçues dans la modal:', movie);
        this.currentMovieId = movie.id;

        // Remplir les infos
        const elements = {
            title: this.modal.querySelector('.modal-title'),
            year: this.modal.querySelector('.modal-year'),
            rating: this.modal.querySelector('.modal-rating'),
            overview: this.modal.querySelector('.modal-overview'),
            poster: this.modal.querySelector('#modal-poster')
        };

        if (elements.title) {
            elements.title.textContent = movie.title;
        }

        if (elements.year && movie.release_date) {
            elements.year.textContent = new Date(movie.release_date).getFullYear();
        }
 
        if (elements.rating && movie.vote_average) {
            elements.rating.textContent = `★ ${movie.vote_average.toFixed(1)}`;
        }

        if (elements.overview) {
            elements.overview.textContent = movie.overview || 'Aucune description disponible.';
        }

        if (elements.poster) {
            elements.poster.src = movie.poster_path 
                ? `https://image.tmdb.org/t/p/w300${movie.poster_path}`
                : '/build/images/no-poster.jpg';  // Modifier ce chemin selon votre structure
            elements.poster.alt = movie.title;
        }

        // Nettoyer et réinitialiser le bouton de bande-annonce
        const trailerContainer = this.modal.querySelector('.modal-trailer-btn');
        if (trailerContainer) {
            const newTrailerBtn = trailerContainer.cloneNode(true);
            trailerContainer.parentNode.replaceChild(newTrailerBtn, trailerContainer);
            newTrailerBtn.addEventListener('click', () => this.loadVideo(movie.id));
        }

        // Afficher la modal
        this.modal.classList.add('show');
    }

    async loadVideo(movieId) {
        try {
            const response = await fetch(`/api/movie/${movieId}/video`);
            const data = await response.json();
            
            const videoContainer = this.modal.querySelector('.modal-video-container');
            
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
                videoContainer.classList.add('show');
            } else {
                videoContainer.innerHTML = `
                    <div class="video-error">
                        Désolé, la bande-annonce n'est pas disponible.
                    </div>
                `;
                videoContainer.classList.add('show');
            }
        } catch (error) {
            console.error('Error loading video:', error);
            const videoContainer = this.modal.querySelector('.modal-video-container');
            videoContainer.innerHTML = `
                <div class="video-error">
                    Désolé, la bande-annonce n'est pas disponible.
                </div>
            `;
            videoContainer.classList.add('show');
        }
    }

    hide() {
        this.modal.classList.remove('show');
        const videoContainer = this.modal.querySelector('.modal-video-container');
        videoContainer.classList.remove('show');
        videoContainer.innerHTML = '';
    }

    async submitRating(rating) {
        if (!this.currentMovieId) return;

        try {
            const response = await fetch(`/api/movie/${this.currentMovieId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ rating })
            });

            const data = await response.json();

            if (response.ok) {
                this.currentRating = rating;
                this.highlightStars(rating);
                
                // Afficher un message de confirmation
                this.showRatingConfirmation();
            } else {
                throw new Error(data.error || 'Error submitting rating');
            }
        } catch (error) {
            console.error('Rating error:', error);
            this.showRatingError();
        }
    }

    highlightStars(rating) {
        this.modal.querySelectorAll('.star').forEach(star => {
            const value = parseInt(star.dataset.value);
            if (value <= rating) {
                star.textContent = '★';
                star.classList.add('active');
            } else {
                star.textContent = '☆';
                star.classList.remove('active');
            }
        });
    }

    showRatingConfirmation() {
        // Mise à jour du sélecteur
        const container = this.modal.querySelector('.modal-rating-section');  // Changé de .rating-section
        const message = document.createElement('div');
        message.className = 'rating-confirmation';
        message.textContent = 'Note enregistrée !';
        
        const oldMessage = container.querySelector('.rating-confirmation');
        if (oldMessage) {
            oldMessage.remove();
        }
        
        container.appendChild(message);
        setTimeout(() => message.remove(), 3000);
    }

    showRatingError() {
        // Mise à jour du sélecteur
        const container = this.modal.querySelector('.modal-rating-section');  // Changé de .rating-section
        const message = document.createElement('div');
        message.className = 'rating-error';
        message.textContent = 'Erreur lors de l\'enregistrement de la note';
        
        const oldMessage = container.querySelector('.rating-error');
        if (oldMessage) {
            oldMessage.remove();
        }
        
        container.appendChild(message);
        setTimeout(() => message.remove(), 3000);
    }
}