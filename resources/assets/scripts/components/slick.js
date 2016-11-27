/**
 * Slick
 * @see http://kenwheeler.github.io/slick/
 */

export default {
  init(options = {}) {
    this.options = Object.assign({}, options);
    this.$slideshows = $('.slick').filter(':not(.slick--no-slider)');

    this.$slideshows.on('afterChange', this.autoPlayVideo);
    this.$slideshows.on('init', this.autoPlayVideo);
    this.$slideshows.slick(this.options);
  },

  autoPlayVideo(e, slick) {
    const $slide = slick.$slides.eq(slick.currentSlide);
    const $video = $slide.find('video');
    if ($video.length) {
      $video[0].play();
    }
  },
};
