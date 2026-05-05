@extends('layout')
@section('header-script')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

@endsection
@section('content')
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#UserIntrestModal">
        I'm Interested
    </button>

    <!-- Modal -->
    <div class="modal fade" id="UserIntrestModal" tabindex="-1" role="dialog" aria-labelledby="UserIntrestModallabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                
                
                       
                    </form>
                </div>

                <style>

/* ===== INDIAEAUCTION PREMIUM THEME ===== */

:root{
  --primary1:#1FA971;
  --primary2:#2CCB83;
  --primaryDark:#138A63;
  --textDark:#243238;
  --muted:#6c757d;
  --bg:#F4F7F8;
}

/* Global */
body{
    background:var(--bg);
    color:var(--textDark);
    font-family: 'Segoe UI',sans-serif;
}

.section{
    padding:90px 0;
}

.section-title h2{
    font-weight:800;
    letter-spacing:.5px;
    margin-bottom:10px;
}

.section-title p{
    color:var(--muted);
}

/* ===== GRADIENT TOP STRIP ===== */
body::before{
    content:"";
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:6px;
    background:linear-gradient(90deg,var(--primary1),var(--primary2));
    z-index:9999;
}

/* ===== BUTTONS ===== */
.btn-primary{
    background:linear-gradient(90deg,var(--primary1),var(--primary2));
    border:none;
    padding:12px 26px;
    font-weight:600;
    border-radius:8px;
    box-shadow:0 6px 18px rgba(0,0,0,.15);
}

.btn-primary:hover{
    background:var(--primaryDark);
}

/* ===== MODAL ===== */
.modal-content{
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 25px 60px rgba(0,0,0,.2);
}

.modal-header{
    background:linear-gradient(90deg,var(--primary1),var(--primary2));
    color:#fff;
}

/* ===== ABOUT SECTION ===== */
#about img{
    border-radius:18px;
    box-shadow:0 25px 50px rgba(0,0,0,.15);
}

/* ===== STATS SECTION ===== */
.stats{
    
}

.stats-item{
    
    padding:25px;
    border-radius:16px;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
    transition:.3s;
}

.stats-item:hover{
    transform:translateY(-6px);
}

/* ===== SERVICES ===== */
.services{
    background:linear-gradient(180deg,#ffffff,#f5f8f7);
}

.service-item{
   
    padding:35px 30px;
    border-radius:18px;
    box-shadow:0 20px 45px rgba(0,0,0,.07);
    transition:.3s;
    border-top:4px solid transparent;
}

.service-item:hover{
    transform:translateY(-8px);
    border-top:4px solid var(--primary1);
}

.service-item .icon{
    color:var(--primary1);
    font-size:32px;
    margin-bottom:15px;
}

/* ===== CONTACT ===== */
.contact{
   
}

.info-container{
    
    padding:30px;
    border-radius:18px;
    box-shadow:0 20px 45px rgba(0,0,0,.07);
    transition:.3s;
}

.info-container:hover{
    transform:translateY(-6px);
}

/* ===== HEADINGS STYLE ===== */
h3{
    font-weight:700;
}

p{
    color:#55636b;
    line-height:1.7;
}

</style>

            </div>
        </div>
    </div>

    <!-- Hero Section -->
   
    <!-- About Section -->
    <section style="background:linear-gradient(90deg,#1FA971,#2CCB83);padding:70px 0;color:white;">
    <div class="container text-center">
        <h1 style="font-weight:800;">NPV CHALLENGE MECHANISM</h1>
        <p style="opacity:.9;font-size:18px;">Transparent • Secure • Nationwide Auction Marketplace</p>
        
    </div>
</section><!-- /About Section -->

   
   
   
    <section id="contact" class="contact section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Contact</h2>
            <!-- <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p> -->
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row gx-lg-0 gy-4">

                <div class="col-lg-4">
                    <div class="info-container d-flex flex-column align-items-center justify-content-center">
         
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>Call Us</h3>
                                <p>+91 7990 8223 51</p>
                            </div>
                        </div><!-- End Info Item -->

              

                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="info-container d-flex flex-column align-items-center justify-content-center">
         
             

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-envelope flex-shrink-0"></i>
                            <div>
                                <h3>Email Us</h3>
                                <p>admin@indiaeauction.com</p>
                            </div>
                        </div><!-- End Info Item -->



                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="info-container d-flex flex-column align-items-center justify-content-center">
         
 
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
                            <i class="bi bi-clock flex-shrink-0"></i>
                            <div>
                                <h3>Open Hours:</h3>
                                <p>Mon-Sat: 10:00 AM - 7:00 PM</p>
                            </div>
                        </div>

                    </div>

                </div>


    </section>
@endsection
@section('footer-script')
    
@endsection
