// assets/modules/modal.js
export default class MovieModal {
    constructor() {
        this.modal = document.getElementById('movieModal');
        this.closeBtn = this.modal.querySelector('.close');
        this.currentMovieId = null;
        this.currentRating = 0;
        this.init();
    }

    init() {
        // Fermeture de la modal
        this.closeBtn.addEventListener('click', () => this.hide());
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) this.hide();
        });

        // Gestion des étoiles
        const starsContainer = this.modal.querySelector('.stars');
        const stars = starsContainer.querySelectorAll('.star');

        // Hover effect
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

        // Reset hover
        starsContainer.addEventListener('mouseleave', () => {
            this.highlightStars(this.currentRating);
        });
    }

    async show(movieId) {
        this.currentMovieId = movieId;
        this.currentRating = 0; // Reset rating
        
        try {
            const response = await fetch(`/movie/${movieId}`);
            const movie = await response.json();
            
            // Mise à jour du contenu
            this.updateContent(movie);
            
            // Affichage de la modal
            this.modal.style.display = 'flex';
            
            // Reset des étoiles
            this.highlightStars(0);
        } catch (error) {
            console.error('Error loading movie:', error);
        }
    }

    updateContent(movie) {
        this.modal.querySelector('#modal-title').textContent = movie.title;
        this.modal.querySelector('#modal-year').textContent = new Date(movie.releaseDate).getFullYear();
        this.modal.querySelector('#modal-overview').textContent = movie.overview;
        this.modal.querySelector('#modal-genres').textContent = movie.genres.map(g => g.name).join(', ');
        
        const posterImg = this.modal.querySelector('#modal-poster');
        if (movie.posterPath) {
            posterImg.src = `https://image.tmdb.org/t/p/w500${movie.posterPath}`;
            posterImg.style.display = 'block';
        } else {
            posterImg.style.display = 'none';
        }
    }

    hide() {
        this.modal.style.display = 'none';
        this.currentMovieId = null;
        this.currentRating = 0;
    }

    async submitRating(rating) {
        if (!this.currentMovieId) return;

        try {
            const response = await fetch(`/movie/${this.currentMovieId}/rate`, {
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
        const container = this.modal.querySelector('.rating-section');
        const message = document.createElement('div');
        message.className = 'rating-confirmation';
        message.textContent = 'Note enregistrée !';
        
        // Supprimer l'ancien message
        const oldMessage = container.querySelector('.rating-confirmation');
        if (oldMessage) {
            oldMessage.remove();
        }
        
        container.appendChild(message);
        setTimeout(() => message.remove(), 3000);
    }

    showRatingError() {
        const container = this.modal.querySelector('.rating-section');
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