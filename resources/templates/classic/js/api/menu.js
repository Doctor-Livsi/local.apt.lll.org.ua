import axios from 'axios';

export default {
    getMenu() {
        return axios.get('/api/menu');
    },
};