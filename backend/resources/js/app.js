import './bootstrap';

import Alpine from 'alpinejs';
import accordion from './plugins/accordion';
import collapse from './plugins/collapse';
import dropdownMenu from './plugins/dropdown-menu';
import tooltip from './plugins/tooltip';
import form from './plugins/form';
import hoverCard from './plugins/hover-card';
import menubar from './plugins/menubar';

window.Alpine = Alpine;

Alpine.plugin(accordion);
Alpine.plugin(collapse);
Alpine.plugin(dropdownMenu);
Alpine.plugin(tooltip);
Alpine.plugin(form);
Alpine.plugin(hoverCard);
Alpine.plugin(menubar);

Alpine.start();
