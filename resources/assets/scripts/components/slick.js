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
    this.$slideshow.slick(this.options);
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
