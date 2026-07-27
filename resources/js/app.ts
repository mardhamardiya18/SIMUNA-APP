import { createInertiaApp, router } from '@inertiajs/vue3';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Ensure page always scrolls to top (0, 0) on every page transition
router.on('navigate', () => {
    window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#0d9488',
    },
});
