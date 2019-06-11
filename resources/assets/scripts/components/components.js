import Vue from 'vue';
import Accordion from './Accordion.vue';
import AccordionList from './AccordionList.vue';

export function addComponents(element) {
  new Vue({
    el: element,
    components: {
      Accordion,
      AccordionList,
    },
  });
}
