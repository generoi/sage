import $ from 'jquery';

/* eslint-disable no-unused-vars */
import {
  MediaQuery,
  // Abide,
  Accordion,
  // AccordionMenu,
  Drilldown,
  Dropdown,
  DropdownMenu,
  // Equalizer,
  ResponsiveMenu,
  ResponsiveToggle,
  Reveal,
  // Slider,
  SmoothScroll,
  // Sticky,
  Tabs,
  Toggler,
  Foundation,
  // Tooltip,
  // ResponsiveAccordionTabs,
} from 'foundation-sites';
/* eslint-enable no-unused-vars */

window.Foundation = Foundation;

export function init() {
  const mqString = $('.foundation-mq').css('font-family');
  if (mqString && mqString.indexOf('small=0em') !== -1) {
    $(document).foundation();
  } else {
    $(window).on('load', () => $(document).foundation());
  }
}
