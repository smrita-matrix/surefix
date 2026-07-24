<?php
/* =====================================================================
   SHARED SITE FOOTER
   Contains: <footer>, closing page wrappers, the (working) enquiry modal,
   shared JS libraries, and the enquiry-form AJAX script.

   A page may OPTIONALLY set these variables BEFORE including this file:
     $enableSmoother - true loads GSAP ScrollTrigger + ScrollSmoother
                       (only the home page uses smooth scrolling)
     $extraScripts   - extra markup output just before </body>
                       (page-specific <script> blocks, e.g. careers/contact forms)
     $hideEnquiry    - true removes the enquiry modal + its script
                       (pages with their own captcha form, e.g. careers/contact)
   ===================================================================== */
$enableSmoother = isset($enableSmoother) ? $enableSmoother : false;
$extraScripts   = isset($extraScripts)   ? $extraScripts   : '';
$hideEnquiry    = isset($hideEnquiry)    ? $hideEnquiry    : false;
?>
        <footer class="bg-dark text-light">
          <div class="container">
            <div class="f-items">
              <div class="row">
                <div class="col-lg-12"  data-aos="fade-up" data-aos-duration="1200">
                  <div class="footer-header">
                    <div class="section-title dark-section">
                      <h2 class="text-anime-style-2" data-cursor="-opaque">Ready to build reliable solutions?</h2>
                      <!--<p>Partner with us for performance-driven adhesive expertise.</p>-->
                    </div>

                    <a href="contact-us" class="footer-contact-circle">
                      <img src="images/icons/contact-now-circle.svg" alt="">
                    </a>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4 col-md-5 col-sm-12 footer-item">
                  <div class="f-item about">
                    <img class="logo" src="images/home/logo-footer.webp" alt="Logo">
                    <p>Established in 1984, S Doshi Papers Industries Private Limited is an Indian manufacturer of self-adhesive tape solutions, operating under the brand name SureFix.</p>
                  </div>
                  <ul class="footer-social">
                    <h6>Follow Us</h6>
                    <li>
                      <a href="https://www.instagram.com/surefixindia?igsh=OXB6YzNvamRtZ3gz" target="_blank">
                      <i class="fa fa-instagram"></i>
                      </a>
                    </li>
                    <li>
                      <a href="https://www.linkedin.com/company/surefixmedicaltapes/posts/?feedView=all" target="_blank">
                      <i class="fa fa-linkedin"></i>
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 footer-item">
                  <div class="col-lg-12 no-padding col-md-12 footer-item">
                    <div class="f-item link">
                      <h4 class="widget-title">Quick Links</h4>
                      <ul class="foot">
                        <li><a href="/">Home</a></li>
                        <li><a href="about-us">About Us</a></li>
                        <li><a href="careers">Careers</a></li>
                        <li><a href="events">Events</a></li>
                        <!--<li><a href="#">Blog</a></li>-->
                        <li><a href="contact-us">Contact Us</a></li>
                        <!-- <li><a href="#" class="special-btn">Shop Now</a></li> -->
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 footer-item">
                  <h4 class="widget-title">Contact Us</h4>
                  <div class="opening-hours">
                    <li>
                      <a href="mailto:sales@surefix.co.in">
                        <div class="fot-box">
                          <img src="images/icons/mail.svg" alt="email"/>
                          <span>sales@surefix.co.in</span>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="fot-box">
                        <img src="images/icons/phone.svg" alt="phone"/>
                        <div class="many-numbers">
                            <a href="tel:+919579726091">+91 95797 26091</a>
                          <!--<a href="tel:+91 98203 79703">+91 98203 79703</a>-->
                          <!--<a href="tel:+91 22 2847 2588">+91 22 2847 2588</a>-->
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="fot-box">
                        <img src="images/icons/pin.svg" alt="location" style="margin-top: 10px;"/>
                        <a href="https://maps.app.goo.gl/BiXJq8DbzcponoRw9?g_st=aw" target="_blank">S Doshi Papers Industries Private Limited - CREC-WH 11 (1), Plot 11, SY 137/1, Sancoale, Mormugao, near Golden Marble, South Goa, Goa, 403726.
                        </a>
                      </div>
                    </li>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 footer-item">
                    <h4 class="widget-title text-center certif-title-sec">Certification</h4>
                    <div class="footer-certifi-img-sec">
                        <img class="footer-certifi-img-custom" src="images/home/iso-certifica-img.webp" alt="Iso Certification Logo">
                    </div>
                </div>
              </div>
            </div>
          </div>
          <div class="footer-bottom">
            <div class="container">
              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <p>Copyright © 2026 Surefix. All rights reserved. Designed By <a target="_blank"
                  href="https://www.matrixbricks.com/in/">Matrix Bricks</a></p>
                </div>
              </div>
            </div>
          </div>
        </footer>

      </div>
    </div>

    <?php if (!$hideEnquiry): ?>
    <!-- ===================== ENQUIRY MODAL ===================== -->
    <div class="modal fade" role="dialog" id="enquiryModal" tabindex="-1">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="enquiryModalLabel">Enquiry Form</h4>
          </div>
          <div class="modal-body">

            <div id="formAlert" style="display:none;"></div>

            <form id="enquiryForm" novalidate onsubmit="return false;">
              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Name <span style="color:red">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" maxlength="50" autocomplete="off">
                    <small class="err-msg" data-for="name"></small>
                  </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Email <span style="color:red">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email Address" maxlength="150" autocomplete="off">
                    <small class="err-msg" data-for="email"></small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Phone <span style="color:red">*</span></label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="10-15 digits" maxlength="15" autocomplete="off">
                    <small class="err-msg" data-for="phone"></small>
                  </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Location <span style="color:red">*</span></label>
                    <input type="text" name="location" id="location" class="form-control" placeholder="City" maxlength="50" autocomplete="off">
                    <small class="err-msg" data-for="location"></small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 col-xs-12">
                  <div class="form-group">
                    <label>Message <span style="color:red">*</span></label>
                    <textarea class="form-control" name="message" id="message" rows="4" placeholder="Your Message" maxlength="1000"></textarea>
                    <small class="err-msg" data-for="message"></small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 col-xs-12">
                  <div class="form-group">
                    <div class="captcha-wrap">
                      <div class="g-recaptcha"
                           id="recaptchaBox"
                           data-sitekey="6Lf4cWEtAAAAAKgO5ByEq_lVo5R6zBOo93AGh2jg"
                           data-callback="onRecaptchaSuccess"
                           data-expired-callback="onRecaptchaExpired"></div>
                    </div>
                    <small class="err-msg" data-for="recaptcha" style="text-align:center;"></small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 col-xs-12 text-center">
                  <a class="upd-btn-black-square cnt-btn-style style-2" href="javascript:void(0)" id="submitEnquiry">
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
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- =================== END ENQUIRY MODAL =================== -->
    <?php endif; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.0/jquery.waypoints.min.js" defer type="text/javascript"></script>
    <script src="https://ciromattia.github.io/jquery.counterup/jquery.counterup.js" defer type="text/javascript"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"></script>
    <?php if ($enableSmoother): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollSmoother.min.js"></script>
    <?php endif; ?>

    <script src="js/wow.min.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/custom.js"></script>

    <!-- ===== GOOGLE reCAPTCHA ===== -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <?php if (!$hideEnquiry): ?>
    <!-- ===== ENQUIRY FORM SCRIPT ===== -->
    <script>
    function onRecaptchaSuccess() {
      jQuery('.err-msg[data-for="recaptcha"]').text('');
    }
    function onRecaptchaExpired() {
      jQuery('.err-msg[data-for="recaptcha"]').text('Verification expired. Please verify again.');
    }

    jQuery(function ($) {

      var ENDPOINT = 'enquiry.php';

      /* This modal's own reCAPTCHA widget id. Works even when the page has a
         second captcha (e.g. the careers/contact form): reCAPTCHA numbers
         widgets in DOM order, so the index of the enquiry .g-recaptcha == its id. */
      function enqWidget() {
        return $('.g-recaptcha').index($('#enquiryModal .g-recaptcha'));
      }

      /* Hard block on native form submission */
      $('#enquiryForm').on('submit', function (e) {
        e.preventDefault();
        return false;
      });

      function setError(field, msg) {
        $('.err-msg[data-for="' + field + '"]').text(msg);
        if (field !== 'recaptcha') $('#' + field).addClass('input-error');
      }

      function clearErrors() {
        $('.err-msg').text('');
        $('#enquiryForm .form-control').removeClass('input-error');
        $('#formAlert').hide().removeClass('alert-success alert-danger');
      }

      /* ---- Live input restrictions ---- */
      $('#name, #location').on('input', function () {
        this.value = this.value.replace(/[^A-Za-z\s.'-]/g, '');
      });
      $('#phone').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
      });

      /* ---- Validation ---- */
      function validate() {
        clearErrors();
        var ok = true;

        var name  = $('#name').val().trim();
        var email = $('#email').val().trim();
        var phone = $('#phone').val().trim();
        var loc   = $('#location').val().trim();
        var msg   = $('#message').val().trim();

        if (!name) { setError('name', 'Name is required.'); ok = false; }
        else if (!/^[A-Za-z\s.'-]{2,50}$/.test(name)) { setError('name', 'Only letters allowed (2-50 characters).'); ok = false; }

        if (!email) { setError('email', 'Email is required.'); ok = false; }
        else if (!/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/.test(email)) { setError('email', 'Enter a valid email address.'); ok = false; }

        if (!phone) { setError('phone', 'Phone number is required.'); ok = false; }
        else if (!/^[0-9]{10,15}$/.test(phone)) { setError('phone', 'Phone must be 10 to 15 digits.'); ok = false; }

        if (!loc) { setError('location', 'Location is required.'); ok = false; }
        else if (!/^[A-Za-z\s.,'-]{2,50}$/.test(loc)) { setError('location', 'Only letters allowed.'); ok = false; }

        if (!msg) { setError('message', 'Message is required.'); ok = false; }
        else if (msg.length < 10) { setError('message', 'Message must be at least 10 characters.'); ok = false; }

        if (typeof grecaptcha === 'undefined' || grecaptcha.getResponse(enqWidget()) === '') {
          setError('recaptcha', 'Please verify that you are not a robot.');
          ok = false;
        }

        return ok;
      }

      $('#name, #email, #phone, #location, #message').on('blur', function () {
        if ($(this).val().trim() !== '') validate();
      });

      /* ---- Submit via AJAX ---- */
      $('#submitEnquiry').on('click', function (e) {
        e.preventDefault();

        if (!validate()) return;

        var $btn = $(this);
        $btn.css({ 'pointer-events': 'none', 'opacity': '0.6' })
            .find('.text-1, .text-2').text('Sending...');

        var payload = $('#enquiryForm').serialize()
                    + '&g-recaptcha-response=' + encodeURIComponent(grecaptcha.getResponse(enqWidget()));

        $.ajax({
          url: ENDPOINT,
          type: 'POST',
          dataType: 'json',
          data: payload,
          success: function (res) {
            clearErrors();
            if (res.status === 'success') {
              $('#enquiryForm')[0].reset();
              if (typeof grecaptcha !== 'undefined') grecaptcha.reset(enqWidget());
              window.location.href = 'thank-you';
            } else {
              if (res.errors) {
                $.each(res.errors, function (k, v) { setError(k, v); });
              } else {
                $('#formAlert').addClass('alert-danger').text(res.message || 'Something went wrong.').show();
              }
              if (typeof grecaptcha !== 'undefined') grecaptcha.reset(enqWidget());
            }
          },
          error: function () {
            $('#formAlert').addClass('alert-danger').text('Server error. Please try again later.').show();
            if (typeof grecaptcha !== 'undefined') grecaptcha.reset(enqWidget());
          },
          complete: function () {
            $btn.css({ 'pointer-events': 'auto', 'opacity': '1' })
                .find('.text-1, .text-2').text('Submit');
          }
        });
      });

      /* ---- Reset form when modal closes ---- */
      $('#enquiryModal').on('hidden.bs.modal', function () {
        $('#enquiryForm')[0].reset();
        clearErrors();
        if (typeof grecaptcha !== 'undefined') grecaptcha.reset(enqWidget());
      });

    });
    </script>
    <?php endif; ?>

    <?php echo $extraScripts; ?>

  </body>
</html>
