import axios from 'axios';

declare global {
    interface Window {
        axios: typeof axios;
    }
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

if (typeof window !== 'undefined' && typeof document !== 'undefined') {
    
    window.axios = axios;
    const token = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');

    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
}