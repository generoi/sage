/**
 * Required with Genero\Sage\GravityFormTwig().
 * @see src/custom/gravityform.php
 */
import 'jquery.scrollto';

export default {
  init() {
    const $document = $(document);
    // Automatically scroll to the confirmation message when submitting
    // a Gravity Form with AJAX.
    $document.on('gform_confirmation_loaded', this.scrollToConfirmation.bind(this));
    // Support radio other field.
    $document.on('ready', this.initRadioOtherField.bind(this));
  },

  initRadioOtherField() {
    const $document = $(document);
    $document.find('[data-gform-other-choice]').on('focus', this.focusOtherField.bind(this));
    $document.find('[data-gform-other-field]').on('focus', this.checkOtherBox.bind(this));
  },

  checkOtherBox(e) {
    const $choice = $(`[data-gform-other-choice="${e.target.id}"]`);
    if ($choice.length) {
      $choice.prop('checked', true);
    }
  },

  focusOtherField(e) {
    const field = $(e.target).data('gformOtherChoice');
    const $field = $(`#${field}`);
    if ($field.length) {
      $field.trigger('focus');
    }
  },

  scrollToConfirmation() {
    $.scrollTo('.gform_confirmation_wrapper', {
      offset: { top: -150, left: 0 },
      duration: 200,
    });
  },
};
