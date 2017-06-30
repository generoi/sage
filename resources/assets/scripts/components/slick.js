/**
 * Slick
 * @see http://kenwheeler.github.io/slick/
 */

class SlickComponent {
  constructor(el, options = {}) {
    this.options = Object.assign({}, options);
    this.$slideshow = $(el);

    this.$slideshow.on('afterChange', this.autoPlayVideo);
    this.$slideshow.on('init', this.autoPlayVideo);

    if (this.checkVisibility()) {
      this.$slideshow.slick(this.options);
    }

    $(window).on('changed.zf.mediaquery', () => {
      if (!this.isVisible() && this.checkVisibility()) {
        this.$slideshow.slick(this.options);
      }
    });
  }

  isVisible() {
    return this._isVisible;
  }

  checkVisibility() {
    this._isVisible = this.$slideshow.is(':visible');
    return this.isVisible();
  }

  autoPlayVideo(e, slick) {
    const $slide = slick.$slides.eq(slick.currentSlide);
    const $video = $slide.find('video');
    if ($video.length) {
      $video[0].play();
    }
  }
}

export default function (selector = '.slick', options = {}) {
  $(selector)
    .not('.slick--no-slider')
    .each((i, el) => new SlickComponent(el, options));
}