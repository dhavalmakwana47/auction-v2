@extends('layout')

@section('header-script')
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
   :root{
  --brand1:#0d6efd;
  --brand2:#38b6ff;
  --brandDark:#084298;
  --text:#111827;
  --muted:#6b7280;
  --bg:#f4f7fb;
  --card:#ffffff;
  --border:rgba(15,23,42,.08);
}

/* PREMIUM FONT */
body{
  background:var(--bg);
  color:var(--text);
  font-family:'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  font-size:15px;
  line-height:1.7;
  font-weight:400;
  -webkit-font-smoothing:antialiased;
  text-rendering:optimizeLegibility;
}

/* TOP BORDER */
body::before{
  content:"";
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:4px;
  background:linear-gradient(90deg,var(--brand1),var(--brand2));
  z-index:9999;
}

/* SECTION SPACING */
.section{
  padding:90px 0;
}

/* HERO */
.hero{
  position:relative;
  padding:110px 0 90px;
  background:
    radial-gradient(circle at top left, rgba(13,110,253,.15), transparent 40%),
    radial-gradient(circle at top right, rgba(56,182,255,.18), transparent 40%),
    linear-gradient(180deg,#fff,#f7fbff);
  overflow:hidden;
}

.hero h1{
  background:linear-gradient(135deg,#0f172a,#0d6efd);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}

.hero p{
  font-size:17px;
  color:var(--muted);
  max-width:620px;
}

/* BADGE */
.hero-badge{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 16px;
  border-radius:999px;
  background:#fff;
  border:1px solid var(--border);
  font-size:13px;
  font-weight:600;
  color:#475569;
  box-shadow:0 8px 30px rgba(2,6,23,.06);
}

/* BUTTONS */
.btn-brand{
  background:linear-gradient(135deg,var(--brand1),var(--brand2));
  border:none;
  border-radius:14px;
  padding:14px 24px;
  font-weight:700;
  font-size:15px;
  transition:.3s ease;
  box-shadow:0 12px 24px rgba(13,110,253,.22);
}

.btn-brand:hover{
  transform:translateY(-2px);
  box-shadow:0 16px 34px rgba(13,110,253,.28);
}

.btn-outline-soft{
  border:1px solid rgba(13,110,253,.18);
  background:#fff;
  color:var(--brandDark);
  border-radius:14px;
  padding:14px 22px;
  font-weight:700;
  transition:.3s ease;
}

.btn-outline-soft:hover{
  background:#eef5ff;
  transform:translateY(-2px);
}

/* CARDS */
.soft-card{
  background:var(--card);
  border:1px solid rgba(15,23,42,.06);
  border-radius:22px;
  box-shadow:
    0 10px 40px rgba(15,23,42,.05),
    0 2px 8px rgba(15,23,42,.03);
  transition:.3s ease;
}

.soft-card:hover{
  transform:translateY(-4px);
}

/* FEATURES */
.feature{
  padding:24px;
  height:100%;
}

.feature .icon{
  width:52px;
  height:52px;
  border-radius:16px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(135deg,#e8f1ff,#d9efff);
  color:var(--brandDark);
  font-size:20px;
  margin-bottom:18px;
}

.feature h5{
  font-size:17px;
  font-weight:800;
  margin-bottom:10px;
  color:#0f172a;
}

.feature p{
  color:var(--muted);
  font-size:14px;
  margin:0;
}

/* TITLES */
.section-title h2{
  font-size:40px;
  font-weight:900;
  letter-spacing:-1px;
  margin-bottom:12px;
  color:#0f172a;
}

.section-title p{
  color:var(--muted);
  font-size:16px;
}

/* MINI STATS */
.mini-stat{
  padding:26px;
  text-align:center;
}

.mini-stat .label{
  font-size:12px;
  font-weight:700;
  color:#94a3b8;
  text-transform:uppercase;
  letter-spacing:1px;
}

.mini-stat .value{
  font-size:26px;
  font-weight:900;
  margin-top:8px;
  color:#0f172a;
}

/* CONTACT */
.contact-card{
  padding:30px 24px;
}

.contact-card i{
  font-size:26px;
  color:var(--brandDark);
}

.contact-card .title{
  margin-top:14px;
  font-weight:800;
  font-size:18px;
}

.contact-card .text{
  color:var(--muted);
  margin-top:6px;
}

/* MODAL */
.modal-content{
  border:none;
  border-radius:24px;
  overflow:hidden;
  box-shadow:0 40px 90px rgba(15,23,42,.18);
}

.modal-header{
  background:linear-gradient(135deg,var(--brand1),var(--brand2));
  padding:22px 26px;
  border:none;
}

.modal-title{
  font-size:20px;
  font-weight:800;
}

.modal-body{
  padding:28px;
}

/* FORM */
.form-control{
  border-radius:14px;
  border:1px solid rgba(15,23,42,.12);
  min-height:50px;
  font-size:14px;
  padding:12px 14px;
}

.form-control:focus{
  border-color:rgba(13,110,253,.35);
  box-shadow:0 0 0 .2rem rgba(13,110,253,.12);
}

/* FOOTER */
.footer-note{
  padding:28px 0 50px;
  text-align:center;
  font-size:13px;
  color:#94a3b8;
  border-top:1px solid rgba(15,23,42,.06);
}

/* MOBILE */
@media(max-width:768px){

  .hero{
    padding:80px 0 60px;
    text-align:center;
  }

  .hero h1{
    font-size:40px;
  }

  .section-title h2{
    font-size:30px;
  }

  .btn-brand,
  .btn-outline-soft{
    width:100%;
    justify-content:center;
  }
}
  </style>
@endsection

@section('content')
  <section id="hero" class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <div class="hero-badge">
            <i class="fas fa-shield-alt"></i>
            Secure bidding • Transparent distribution • Real-time updates
          </div>

          <h1>Challenge Mechanism</h1>
          <p>
            A user-friendly portal to participate in the challenge process: enter category-wise amounts,
            auto-calculate the resolution total, and place bids with rule-based validation.
          </p>

          <div class="d-flex flex-wrap" style="gap:12px;">
            <button type="button" class="btn btn-brand text-white" data-toggle="modal" data-target="#UserInterestModal">
              <i class="fas fa-paper-plane mr-2"></i> I’m Interested
            </button>
            <a href="#contact" class="btn btn-outline-soft">
              <i class="fas fa-headset mr-2"></i> Contact Support
            </a>
          </div>
        </div>

        <div class="col-lg-5 mt-4 mt-lg-0">
          <div class="soft-card p-3 p-md-4">
            <div class="row">
              <div class="col-12">
                <div class="section-title mb-3">
                  <h2 style="font-size:18px;">What you can do</h2>
                  <p style="font-size:13px;">Simple steps designed for speed and clarity.</p>
                </div>
              </div>
              <div class="col-12 mb-3">
                <div class="d-flex" style="gap:12px;">
                  <div class="feature p-0" style="flex:1;">
                    <div class="icon"><i class="fas fa-calculator"></i></div>
                    <h5>Auto Total</h5>
                    <p>Resolution amount is calculated from your table inputs.</p>
                  </div>
                  <div class="feature p-0" style="flex:1;">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h5>Validations</h5>
                    <p>Increment & base rules are checked before placing the bid.</p>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex" style="gap:12px;">
                  <div class="feature p-0" style="flex:1;">
                    <div class="icon"><i class="fas fa-bolt"></i></div>
                    <h5>Live Updates</h5>
                    <p>New bids update the portal in real time.</p>
                  </div>
                  <div class="feature p-0" style="flex:1;">
                    <div class="icon"><i class="fas fa-lock"></i></div>
                    <h5>Secure</h5>
                    <p>CSRF-protected actions and controlled access.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="about" class="section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="section-title">
            <h2>About the platform</h2>
            <p> Challenge Mechanism portal focused on transparency, speed, and a consistent bidding experience.</p>
          </div>
          <div class="soft-card p-3 p-md-4">
            <div class="d-flex" style="gap:12px;">
              <div class="feature p-0" style="flex:1;">
                <div class="icon"><i class="fas fa-eye"></i></div>
                <h5>Transparent</h5>
                <p>Clear grid totals, NPV totals, and rule-based validations.</p>
              </div>
              <div class="feature p-0" style="flex:1;">
                <div class="icon"><i class="fas fa-sync-alt"></i></div>
                <h5>Real-time</h5>
                <p>Live bid updates so the minimum and base values stay current.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mt-4 mt-lg-0">
          <div class="soft-card p-3 p-md-4">
            <div class="section-title mb-2">
              <h2 style="font-size:18px;">How it works</h2>
              <p style="font-size:13px;">A simple flow aligned to auction rules.</p>
            </div>
            <div style="display:grid; gap:10px;">
              <div class="soft-card p-3" style="border-radius:14px; box-shadow:none;">
                <div style="font-weight:900;"><span style="color:var(--brandDark);">1.</span> Enter category amounts</div>
                <div style="color:var(--muted); font-size:13px; margin-top:4px;">Fill values directly in the table cells.</div>
              </div>
              <div class="soft-card p-3" style="border-radius:14px; box-shadow:none;">
                <div style="font-weight:900;"><span style="color:var(--brandDark);">2.</span> Total auto-calculates</div>
                <div style="color:var(--muted); font-size:13px; margin-top:4px;">Resolution amount becomes the table total (read-only).</div>
              </div>
              <div class="soft-card p-3" style="border-radius:14px; box-shadow:none;">
                <div style="font-weight:900;"><span style="color:var(--brandDark);">3.</span> Place a valid bid</div>
                <div style="color:var(--muted); font-size:13px; margin-top:4px;">If rules pass, you can place the bid.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="services" class="section">
    <div class="container">
      <div class="section-title text-center">
        <h2>Built for clarity and compliance</h2>
        <p>Modern UI, consistent validations, and clean user flow.</p>
      </div>

      <div class="row mt-4">
        <div class="col-md-4 mb-3">
          <div class="soft-card feature">
            <div class="icon"><i class="fas fa-table"></i></div>
            <h5>Category-wise input</h5>
            <p>Enter amounts directly in the distribution table.</p>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="soft-card feature">
            <div class="icon"><i class="fas fa-rupee-sign"></i></div>
            <h5>Resolution auto-sum</h5>
            <p>Total is auto-set on resolution amount (read-only) from the table total.</p>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="soft-card feature">
            <div class="icon"><i class="fas fa-gavel"></i></div>
            <h5>Place bid confidently</h5>
            <p>When the total is valid, you can place a bid as per existing rules.</p>
          </div>
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-md-4 mb-3">
          <div class="soft-card mini-stat">
            <div class="label">Design</div>
            <div class="value">Responsive</div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="soft-card mini-stat">
            <div class="label">UX</div>
            <div class="value">Fast Flow</div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="soft-card mini-stat">
            <div class="label">Updates</div>
            <div class="value">Real-time</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="contact" class="section" style="padding-top:0;">
    <div class="container">
      <div class="section-title text-center">
        <h2>Contact</h2>
        <p>We’re here to help you get started.</p>
      </div>

      <div class="row mt-4">
        <div class="col-lg-4 mb-3">
          <div class="soft-card contact-card text-center">
            <div style="font-size:26px;"><i class="fas fa-phone-alt"></i></div>
            <div class="title">Call Us</div>
            <p class="text">+91 7990 8223 51</p>
          </div>
        </div>
        <div class="col-lg-4 mb-3">
          <div class="soft-card contact-card text-center">
            <div style="font-size:26px;"><i class="fas fa-envelope"></i></div>
            <div class="title">Email Us</div>
            <p class="text">admin@indiaeauction.com</p>
          </div>
        </div>
        <div class="col-lg-4 mb-3">
          <div class="soft-card contact-card text-center">
            <div style="font-size:26px;"><i class="fas fa-clock"></i></div>
            <div class="title">Open Hours</div>
            <p class="text">Mon–Sat: 10:00 AM – 7:00 PM</p>
          </div>
        </div>
      </div>

      <div class="footer-note text-center mt-3">
        Powered by a secure auction workflow • Transparent bidding experience
      </div>
    </div>
  </section>

  <!-- Interested Modal -->
  <div class="modal fade" id="UserInterestModal" tabindex="-1" role="dialog" aria-labelledby="UserInterestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="UserInterestModalLabel"><i class="fas fa-paper-plane mr-2"></i> Share your interest</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="interestForm" onsubmit="return false;">
            <div class="form-group">
              <label class="mb-1" style="font-weight:800; font-size:13px;">Full Name</label>
              <input type="text" class="form-control" name="name" placeholder="Enter your name">
            </div>
            <div class="form-group">
              <label class="mb-1" style="font-weight:800; font-size:13px;">Email</label>
              <input type="email" class="form-control" name="email" placeholder="Enter your email">
            </div>
            <div class="form-group">
              <label class="mb-1" style="font-weight:800; font-size:13px;">Mobile</label>
              <input type="text" class="form-control" name="mobile" placeholder="Enter your mobile number">
            </div>
            <div class="form-group mb-0">
              <label class="mb-1" style="font-weight:800; font-size:13px;">Message</label>
              <textarea class="form-control" name="message" rows="3" placeholder="Tell us what you’re looking for"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer" style="border:0;">
          <button type="button" class="btn btn-outline-soft" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-brand text-white" id="btn-interest-submit">
            Submit
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('footer-script')
  <script>
    (function () {
      var btn = document.getElementById('btn-interest-submit');
      if (!btn) return;
      btn.addEventListener('click', function () {
        // UI-only modal for now (no backend endpoint was defined here).
        // Keep it user-friendly: close and show a simple confirmation.
        try {
          $('#UserInterestModal').modal('hide');
        } catch (e) {}
        alert('Thanks! Our team will contact you shortly.');
      });

      // Smooth-scroll for in-page anchors (works with fixed header)
      document.querySelectorAll('a[href^=\"#\"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
          var href = a.getAttribute('href');
          if (!href || href === '#') return;
          var target = document.querySelector(href);
          if (!target) return;
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      });
    })();
  </script>
@endsection
