import './bootstrap';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

// Expose ke window supaya bisa dipakai dari script inline di blade bila perlu
window.bootstrap = bootstrap;
window.Chart = Chart;
