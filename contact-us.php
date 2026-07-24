<?php
$pageTitle   = 'Contact Us';
$extraHead   = <<<'HTML'
<style>
  #contactForm .form-group { margin-bottom: 18px; }
  #contactAlert { margin: 0 0 18px; padding: 12px 16px; border-radius: 4px; font-size: 14px; }
  #contactAlert.alert-success { background:#dff0d8; color:#3c763d; border:1px solid #d6e9c6; }
  #contactAlert.alert-danger  { background:#f2dede; color:#a94442; border:1px solid #ebccd1; }
</style>
HTML;
include 'header.php';
?>

      <section class="sf-breadcrumb-sec">
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <div class="breadcrumb-box">
                <h2>Contact Us</h2>
              </div>
              <div class="breadcrumb-list">
                <ul>
                  <li><a href="/">Home</a></li>
                  <li class="active">Contact Us</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="contact-sec">
        <div class="container">
          <!-- Contact Details -->
          <div class="row contact-info-sec">
            <div class="col-md-12">
              <div class="career-heading">
                <span class="careers-subtitle">Get In Touch</span>
                <h2>Contact Us</h2>
                <p>We're here to answer your questions and assist you.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="contact-card">
                <div class="icon">
                  <img src="images/icons/mail.svg">
                  <h4>Email</h4>
                </div>
                <div class="text">
                  <a href="mailto:sales@surefix.co.in">sales@surefix.co.in</a>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="contact-card">
                <div class="icon">
                  <img src="images/icons/phone.svg">
                  <h4>Phone</h4>
                </div>
                <div class="text">
                  <a href="tel:+919579726091">+91 95797 26091</a>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="contact-card">
                <div class="icon">
                  <img src="images/icons/pin.svg">
                  <h4>Office</h4>
                </div>
                <div class="text">
                  <a href="https://maps.app.goo.gl/BiXJq8DbzcponoRw9?g_st=aw" target="_blank">S Doshi Papers Industries Private Limited - CREC-WH 11 (1), Plot 11, SY 137/1, Sancoale, Mormugao, near Golden Marble, South Goa, Goa, 403726.</a>
                </div>
              </div>
            </div>
          </div>
        <!-- Contact Form -->
<div class="row contact-form-sec">
  <div class="col-md-12">
    <div class="career-heading">
      <span class="careers-subtitle">Contact Form</span>
      <h2>Fill out the form below.</h2>
    </div>
  </div>
  <div class="col-md-12">

    <div id="contactAlert" style="display:none;"></div>

    <form id="contactForm" novalidate onsubmit="return false;">
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>Full Name <span>*</span></label>
            <input type="text" name="full_name" id="full_name" class="form-control" maxlength="50" autocomplete="off">
            <small class="err-msg" data-for="full_name"></small>
          </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>Company Name</label>
            <input type="text" name="company_name" id="company_name" class="form-control" maxlength="100" autocomplete="off">
            <small class="err-msg" data-for="company_name"></small>
          </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>City <span>*</span></label>
            <input type="text" name="city" id="city" class="form-control" maxlength="50" autocomplete="off">
            <small class="err-msg" data-for="city"></small>
          </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>Mobile Number <span>*</span></label>
            <input type="tel" name="mobile" id="mobile" class="form-control" maxlength="15" placeholder="10-15 digits" autocomplete="off">
            <small class="err-msg" data-for="mobile"></small>
          </div>
        </div>

        <div class="col-md-12 col-xs-12">
          <div class="form-group">
            <label>Email Address <span>*</span></label>
            <input type="email" name="email" id="email" class="form-control" maxlength="150" autocomplete="off">
            <small class="err-msg" data-for="email"></small>
          </div>
        </div>

        <div class="col-md-12 col-xs-12">
          <div class="form-group">
            <div class="captcha-wrap">
              <div class="g-recaptcha"
                   id="contactRecaptcha"
                   data-sitekey="6Lf4cWEtAAAAAKgO5ByEq_lVo5R6zBOo93AGh2jg"
                   data-callback="onContactCaptchaSuccess"
                   data-expired-callback="onContactCaptchaExpired"></div>
            </div>
            <small class="err-msg" data-for="recaptcha" style="text-align:center;"></small>
          </div>
        </div>

        <div class="col-md-12 col-xs-12">
          <div class="cnt-about-btn-box text-center tp_fade_anim" data-delay=".5" data-fade-from="top" data-ease="bounce">
            <a class="upd-btn-black-square cnt-btn-style style-2" href="javascript:void(0)" id="submitContact">
              <i>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1 11L11 1" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M1 1H11V11" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1 11L11 1" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M1 1H11V11" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </i>
              <span>
                <span class="text-1">Submit</span>
                <span class="text-2">Submit</span>
              </span>
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
          <!-- Map -->
          <div class="row contact-map-sec">
            <div class="col-md-12">
              <div class="career-heading">
                <span class="careers-subtitle">Find Us</span>
                <h2>Our Location</h2>
              </div>
              <div class="map-box">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2729.923223844282!2d73.87321187319267!3d15.379861057527615!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bbfb9040c1e27f1%3A0xe87096ed943e23d6!2sS%20Doshi%20Papers%20Industries%20Private%20Limited!5e1!3m2!1sen!2sin!4v1784712129412!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
              </div>
            </div>
          </div>
        </div>
      </section>



<?php
$extraScripts = <<<'HTML'
<!-- ===== CONTACT FORM SCRIPT ===== -->
<script>
function onContactCaptchaSuccess() {
  jQuery('.err-msg[data-for="recaptcha"]').text('');
}
function onContactCaptchaExpired() {
  jQuery('.err-msg[data-for="recaptcha"]').text('Verification expired. Please verify again.');
}

jQuery(function ($) {

  var ENDPOINT = 'contact.php';

  /* Hard block on native form submission */
  $('#contactForm').on('submit', function (e) {
    e.preventDefault();
    return false;
  });

  function setError(field, msg) {
    $('.err-msg[data-for="' + field + '"]').text(msg);
    if (field !== 'recaptcha') $('#' + field).addClass('input-error');
  }

  function clearErrors() {
    $('#contactForm .err-msg').text('');
    $('#contactForm .form-control').removeClass('input-error');
    $('#contactAlert').hide().removeClass('alert-success alert-danger');
  }

  /* ---- Live input restrictions ---- */
  $('#full_name, #city').on('input', function () {
    this.value = this.value.replace(/[^A-Za-z\s.'-]/g, '');
  });
  $('#company_name').on('input', function () {
    this.value = this.value.replace(/[^A-Za-z0-9\s.,&'-]/g, '');
  });
  $('#mobile').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  /* ---- Validation ---- */
  function validate() {
    clearErrors();
    var ok = true;

    var fullName = $('#full_name').val().trim();
    var company  = $('#company_name').val().trim();
    var city     = $('#city').val().trim();
    var mobile   = $('#mobile').val().trim();
    var email    = $('#email').val().trim();

    if (!fullName) { setError('full_name', 'Full name is required.'); ok = false; }
    else if (!/^[A-Za-z\s.'-]{2,50}$/.test(fullName)) { setError('full_name', 'Only letters allowed (2-50 characters).'); ok = false; }

    if (company && !/^[A-Za-z0-9\s.,&'-]{2,100}$/.test(company)) { setError('company_name', 'Enter a valid company name.'); ok = false; }

    if (!city) { setError('city', 'City is required.'); ok = false; }
    else if (!/^[A-Za-z\s.,'-]{2,50}$/.test(city)) { setError('city', 'Only letters allowed.'); ok = false; }

    if (!mobile) { setError('mobile', 'Mobile number is required.'); ok = false; }
    else if (!/^[0-9]{10,15}$/.test(mobile)) { setError('mobile', 'Mobile must be 10 to 15 digits.'); ok = false; }

    if (!email) { setError('email', 'Email address is required.'); ok = false; }
    else if (!/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/.test(email)) { setError('email', 'Enter a valid email address.'); ok = false; }

    if (typeof grecaptcha === 'undefined' || grecaptcha.getResponse() === '') {
      setError('recaptcha', 'Please verify that you are not a robot.');
      ok = false;
    }

    return ok;
  }

  $('#full_name, #company_name, #city, #mobile, #email').on('blur', function () {
    if ($(this).val().trim() !== '') validate();
  });

  /* ---- Submit via AJAX ---- */
  $('#submitContact').on('click', function (e) {
    e.preventDefault();

    if (!validate()) return;

    var $btn = $(this);
    $btn.css({ 'pointer-events': 'none', 'opacity': '0.6' })
        .find('.text-1, .text-2').text('Sending...');

    var payload = $('#contactForm').serialize()
                + '&g-recaptcha-response=' + encodeURIComponent(grecaptcha.getResponse());

    $.ajax({
      url: ENDPOINT,
      type: 'POST',
      dataType: 'json',
      data: payload,
      success: function (res) {
        clearErrors();
        if (res.status === 'success') {
          $('#contactForm')[0].reset();
          if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
          window.location.href = 'thank-you';
        } else {
          if (res.errors) {
            $.each(res.errors, function (k, v) { setError(k, v); });
          } else {
            $('#contactAlert').addClass('alert-danger').text(res.message || 'Something went wrong.').show();
          }
          if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
        }
      },
      error: function (xhr) {
        $('#contactAlert').addClass('alert-danger')
          .text('Server error (' + xhr.status + '). Please try again later.').show();
        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
      },
      complete: function () {
        $btn.css({ 'pointer-events': 'auto', 'opacity': '1' })
            .find('.text-1, .text-2').text('Submit');
      }
    });
  });

});
</script>
HTML;
include 'footer.php';
?>
