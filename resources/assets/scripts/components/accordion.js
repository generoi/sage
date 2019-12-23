export class Accordion {
  constructor(el, options = {}) {
    this.options = Object.assign({}, options);
    this.$wrapper = $(el);
    this.$list = this.$wrapper.children();
    this.$activeItem = null;
    this.$wrapper.on('click', this.options.titleSelector, this.onClick.bind(this));
  }

  onClick(e) {
    this.$activeItem = $(e.currentTarget).parent();
    if (this.$activeItem.hasClass('is-active')) {
      this.$activeItem.removeClass('is-active');
    } else {
      this.$list.removeClass('is-active');
      this.$activeItem.addClass('is-active');
    }
  }
}

export function accordion(selector = '.schema-faq', options = {}) {
  $(selector)
    .each((i, el) => new Accordion(el, options));
}
