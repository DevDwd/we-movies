export class VideoService {
    static async fetchVideo(movieId) {
        try {
            const response = await fetch(`/api/movie/${movieId}/video`);
            if (!response.ok) {
                throw new Error('Video not found');
            }
            const data = await response.json(); 
            return data;
        } catch (error) {
            console.error('Error fetching video:', error);
            return null;
        }
    }

    static createVideoFrame(videoKey) {
        return `
            <iframe
                width="100%"
                height="400"
                src="https://www.youtube.com/embed/${videoKey}?autoplay=1"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
        `;
    }

    static showError() {
        return `
            <div class="video-error">
                Désolé, la bande-annonce n'est pas disponible.
            </div>
        `;
    }
}