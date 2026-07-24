<?php
/* =====================================================================
   SHARED SITE HEADER
   Contains: <head>, top navigation bar, and the opening page wrappers.

   A page may OPTIONALLY set these variables BEFORE including this file:
     $pageTitle   - text for the <title> tag        (default: "Surefix")
     $extraHead   - extra markup for <head>         (page-specific <style>, etc.)
     $hideEnquiry - true hides the "Enquire Now" button + enquiry modal
                    (use on pages that already have their own form, e.g. careers/contact)

   Usage in a page:
     <?php $pageTitle = 'About Us'; include 'header.php'; ?>
   ===================================================================== */
$pageTitle   = isset($pageTitle)   ? $pageTitle   : 'Surefix';
$extraHead   = isset($extraHead)   ? $extraHead   : '';
$hideEnquiry = isset($hideEnquiry) ? $hideEnquiry : false;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/home/favicon.png" sizes="32x32" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/effect.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/media.css">

    <!-- ===== SHARED ENQUIRY / FORM STYLES ===== -->
    <style>
      #enquiryModal .modal-body { padding: 20px 15px; }
      #enquiryModal .form-group { margin-bottom: 15px; }
      #enquiryModal label { font-weight: 600; font-size: 13px; margin-bottom: 5px; display: block; }
      #enquiryModal .form-control { height: 42px; border-radius: 4px; box-shadow: none; }
      #enquiryModal textarea.form-control { height: auto; }
      #enquiryForm { overflow: hidden; }
      .err-msg { color:#d9534f; font-size:12px; display:block; margin-top:4px; min-height:15px; }
      .input-error { border:1px solid #d9534f !important; }
      #formAlert { margin: 0 0 15px; padding: 10px 14px; border-radius: 4px; font-size: 14px; }
      #formAlert.alert-success { background:#dff0d8; color:#3c763d; border:1px solid #d6e9c6; }
      #formAlert.alert-danger  { background:#f2dede; color:#a94442; border:1px solid #ebccd1; }
      .captcha-wrap { display:flex; justify-content:center; margin-bottom:5px; }
      .g-recaptcha { transform-origin: center; }
      @media (max-width: 480px) {
        .g-recaptcha { transform: scale(0.85); transform-origin: center; }
      }
    </style>
    <?php echo $extraHead; ?>
  </head>
  <body>
    <header>
      <section class="main_menu">
        <div class="container">
          <div class="row v-center">
            <div class="header-item item-left">
              <div class="logo">
                <a href="index.html"><img src="images/home/logo.webp"></a>
              </div>
            </div>
            <!-- menu start here -->
            <div class="header-item item-center">
              <div class="menu-overlay"></div>
              <nav class="menu">
                <div class="mobile-menu-head">
                  <div class="go-back"><i class="fa fa-angle-left"></i></div>
                  <div class="current-menu-title"></div>
                  <div class="mobile-menu-close">×</div>
                </div>
                <ul class="menu-main">
                  <!--<li><a href="#">Home</a></li>-->
                  <li><a href="about-us.html">About Us</a></li>
                  <li class="menu-item-has-children">
                    <a href="#">Products <i class="fa fa-angle-down"></i></a>
                    <div class="sub-menu mega-menu row mega-menu-column-4 scrollbar" id="style-3">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-3 list-item border-right-one">
                              <span class="h3tag"><a href="#">Health Care</a></span>
                              <ul>
                                <li><a href="drape-construction-tape.html">Drape Construction Tapes</a></li>
                                <li><a href="incision-application-films.html">Incision Application Films</a></li>
                                <li><a href="re-inforcement-tape.html">Re-inforcement Tape</a></li>
                                <li><a href="ds-pouch-holding-tape.html">D/S Pouch Holding Tape </a></li>
                                <li><a href="seam-sealing-tape-for-gowns.html">Seam Sealing Tape for Gowns </a></li>
                                <li><a href="wrap-closure-tape.html">Wrap Closure Tape </a></li>
                                <li><a href="#">Blue Non-Woven </a></li>
                                <li><a href="eye-bandage.html">Eye Bandage </a></li>
                                <li><a href="white-non-woven.html">White Non-Woven </a></li>
                                <li><a href="back-neck-tape.html">Back Neck Tape</a></li>
                              </ul>
                            </div>
                            <div class="col-md-3 list-item border-right-one">
                              <span class="h3tag"><a href="furnishing-fabrics.html">Furnishing Fabrics</a></span>
                              <ul>
                                <li><a href="furnishing-fabrics.html">Anit-Fray Tapes</a></li>
                                <li><a href="furnishing-fabrics.html">Double sided tissue tape</a></li>
                              </ul>
                              <div class="menu-divider"></div>
                              <span class="h3tag"><a href="automotive.html">Automotive</a></span>
                              <ul>
                                <li><a href="automotive.html">Double Sided Panel Fixing Tape</a></li>
                              </ul>
                              <div class="menu-divider"></div>
                              <span class="h3tag"><a href="tarpaulin-fibc.html">Tarpaulin / FIBC</a></span>
                              <ul>
                                <li><a href="tarpaulin-fibc.html">HDPE Tapes</a></li>
                              </ul>
                              <div class="menu-divider"></div>
                              <span class="h3tag"><a href="fibc.html">FIBC</a></span>
                            </div>
                            <div class="col-md-3 list-item border-right-one">
                              <span class="h3tag"><a href="paper-fabric-and-nonwoven-converting-industry.html">Paper, fabric & nonwoven converting industry</a></span>
                              <ul>
                                <li><a href="paper-fabric-and-nonwoven-converting-industry.html">Splicing application</a></li>
                              </ul>
                              <div class="menu-divider"></div>
                              <span class="h3tag"><a href="printing-packaging-industry.html">Printing & Packaging industry</a></span>
                              <ul>
                                <li><a href="printing-packaging-industry.html">Bonding and holding application</a></li>
                              </ul>
                              <div class="menu-divider"></div>
                              <span class="h3tag"><a href="export-hygroscopic-packaging.html">Export - hygroscopic packaging</a></span>
                              <ul>
                                <li><a href="export-hygroscopic-packaging.html">Valve closure labels (3640 Poly)</a></li>
                              </ul>
                            </div>

                            <div class="col-md-3 list-item">
                              <span class="h3tag"><a href="pre-engineered-building.html">Pre Engineered Building</a></span>
                              <ul>
                                <li><a href="pre-engineered-building.html">Overlap sealing tape (ST 93)</a></li>
                                <li><a href="pre-engineered-building.html">Screw area protection (201)</a></li>
                                <li><a href="pre-engineered-building.html">Damaged sheet protector (SPP 120)</a></li>
                                <li><a href="pre-engineered-building.html">Double side tape for splicing under the roof insulation (PP 4077)</a></li>
                                <li><a href="pre-engineered-building.html">Double sided foam tape for overlap joints (6060 Black)</a></li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li><a href="careers.html">Careers</a></li>
                  <li><a href="events.html">Events</a></li>
                  <!--<li><a href="#">Blog</a></li>-->
                  <li><a href="contact-us.html">Contact Us</a></li>
                </ul>
              </nav>
            </div><!-- menu end here -->
            <div class="header-item header-right-item item-right">
              <?php if (!$hideEnquiry): ?>
              <ul class="nav-icon">
                <li class="hvr-icon-push nav-search">
                  <div class="cnt-about-btn-box tp_fade_anim" data-delay=".5" data-fade-from="top" data-ease="bounce">
                    <a class="upd-btn-black-square cnt-btn-style style-2" data-toggle="modal" data-target="#enquiryModal">
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
                        <span class="text-1">Enquire Now</span>
                        <span class="text-2">Enquire Now</span>
                      </span>
                    </a>
                  </div>
                </li>
              </ul>
              <?php endif; ?>
              <!-- mobile menu trigger -->
              <div class="mobile-menu-trigger">
                <span></span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </header>

    <div id="smooth-wrapper">
      <div id="smooth-content">
