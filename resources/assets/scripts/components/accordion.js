class AccordionComponent {
  constructor(el, options = {}) {
    this.options = Object.assign({}, options);
    this.wrapper = el;
    this.items = this.wrapper.querySelectorAll(options.itemSelector);
    this.activeItem = null;
    this.wrapper.addEventListener('click', this.onClick.bind(this));
  }

  onClick(e) {
    if (e.target.matches(this.options.itemSelector)) {
      this.activeItem = e.target;
    } else {
      const clickedTitle = e.target.closest(this.options.titleSelector);
      if (!clickedTitle) {
        return;
      }
      this.activeItem = clickedTitle.closest(this.options.itemSelector);
    }

    if (!this.activeItem) {
      return;
    }

    if (this.activeItem.classList.contains('is-active')) {
      this.activeItem.classList.remove('is-active');
    } else {
      this.activeItem.classList.add('is-active');
      for (let i = 0; i < this.items.length; i++) {
        if (this.items[i] === this.activeItem) {
          continue;
        }
        this.items[i].classList.remove('is-active');
      }
    }
  }
}

export function init(selector = '.schema-faq', options = {}) {
  const elements = document.querySelectorAll(selector);
  for (let i = 0; i < elements.length; i++) {
    elements[i]._accordion = new AccordionComponent(elements[i], options);
  }
}
