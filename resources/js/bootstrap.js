// Mengimpor library Axios untuk melakukan HTTP request (AJAX) dari sisi client dan menempelkannya ke objek global window
import axios from 'axios';
window.axios = axios;

// Menambahkan header default pada setiap request Axios agar Laravel mengenalinya sebagai request AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
