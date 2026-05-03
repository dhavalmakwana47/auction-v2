@extends('layout')
@section('header-script')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

@endsection
@section('content')
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#interestModal">
        I'm Interested
    </button>

    <!-- Modal -->
    <div class="modal fade" id="UserIntrestModal" tabindex="-1" role="dialog" aria-labelledby="UserIntrestModallabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="UserIntrestModallabel">Auction Participation Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#UserIntrestModal').modal('hide')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="interestForm">
                        <!-- Personal Information Section -->
                        <div class="mb-4">
                            <h5 class="text-primary">Personal Information</h5>
                            <input type="hidden" name="auction_id" value="0" id="auctionId">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter your name" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="mobile">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="Enter your mobile number" required>
                            </div>
                        </div>

                        <!-- OTP Verification Section -->
                        <div class="mb-4">
                            <h5 class="text-primary">OTP Verification</h5>
                            <div class="form-group">
                                <label for="otp">Enter OTP</label>
                                <input type="text" class="form-control" id="otp" name="otp"
                                    placeholder="Enter the OTP" oninput="this.value = this.value.slice(0, 6);" disabled>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-primary" id="sendOtpBtn">Send OTP</button>
                            <button type="button" class="btn btn-success" id="verifyOtpBtn">Verify OTP</button>
                        </div>
                    </form>
                </div>

                <style>
                    /* Additional Styling */
                    .modal-body {
                        background-color: #f8f9fa;
                        padding: 20px;
                        border-radius: 8px;
                    }

                    h5 {
                        font-weight: 600;
                        margin-bottom: 10px;
                    }

                    .form-control {
                        border-radius: 5px;
                    }

                    .btn {
                        border-radius: 5px;
                        padding: 10px 15px;
                    }
                </style>

            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="hero" class="hero section">

        <!--  <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">
                                                                                                    <div class="row gy-5 justify-content-between">
                                                                                                        <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                                                                                                            <h2><span>Unveiling Opportunities,</span><span class="accent">One Bid at a Time</span></h2>
                                                                                                            <p>Join the leading online E-auction platform in India, where you can discover exclusive deals and rare finds every day. Bid smart, win big!</p>
                                                                                                            <div class="d-flex">
                                                                                                                <a href="#about" class="btn-get-started">Get Started</a>
                                                                                                                
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-lg-5 order-1 order-lg-2">
                                                                                                            <img src="{{ asset('homepage/assets/img/hero-img.svg') }}" class="img-fluid" alt="">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div> -->

        {{-- <div class="icon-boxes position-relative" data-aos="fade-up" data-aos-delay="200">
            <div class="container position-relative">
                <div class="row gy-4 mt-5">

                    <div class="col-xl-3 col-md-6">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-easel"></i></div>
                            <h4 class="title"><a href="" class="stretched-link">Auctions Starting Today</a></h4>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-xl-3 col-md-6">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-gem"></i></div>
                            <h4 class="title"><a href="" class="stretched-link">Auctions Closing Today</a>
                            </h4>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-xl-3 col-md-6">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-geo-alt"></i></div>
                            <h4 class="title"><a href="" class="stretched-link">Bid Submission Starting
                                    Today</a></h4>
                        </div>
                    </div><!--End Icon Box -->

                    <div class="col-xl-3 col-md-6">
                        <div class="icon-box">
                            <div class="icon"><i class="bi bi-command"></i></div>
                            <h4 class="title"><a href="" class="stretched-link">Auctions Awarde</a></h4>
                        </div>
                    </div><!--End Icon Box -->

                </div>
            </div>
        </div>
         --}}

    </section><!-- /Hero Section -->
    <section class="section">
        <div class="container">
            <h2>Auctions List</h2>
            <div class="table-responsive">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-4">
                            <label for="auction-status-filter">Auction Status</label>
                            <select class="form-control" id="auction-status-filter">
                                <option value="ongoing">Ongoing</option>
                                <option value="upcoming" selected>Up Coming </option>
                            </select>
                        </div>
                    </div>
                    <!-- Prize Range Filter -->
                    <div class="col-md-3">
                        <div class="form-group mb-4">

                            <label for="min_price">Min Reserve Price:</label>
                            <input class="form-control" type="number" name="min_price" id="auction_min_price"
                                value="{{ request('min_price') }}" min="0">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-4">

                            <label for="max_price">Max Reserve Price:</label>
                            <input class="form-control" type="number" name="max_price" id="auction_max_price"
                                value="{{ request('max_price') }}" min="0">
                        </div>
                    </div>

                </div>
                <table id="users_list" class="table  yajra-datatable">
                    <thead>
                        <tr>
                            <th>Auction ID</th>
                            <th>Corporate Debtor Name</th>
                            <th>Auctioneer</th>
                            <th>Auction Title</th>
                            <th>Auction Start Date</th>
                            <th>Auction End Date</th>
                            <th>Reserve Price</th>
                            <th>EMD Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <!-- About Section -->
    <section id="about" class="about section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>About Us<br></h2>
            <p>Experience the thrill of auctions tailored for the Indian market</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-4">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <h3>Welcome to India E-auction</h3>
                    <p>
                        IBC Auction
                        India E-Auction specializes in liquidating surplus inventory, equipment, and assets for
                        businesses across various industries. With our extensive experience and expertise, we
                        assist companies in efficiently managing their excess inventory and maximizing their
                        returns through strategic liquidation solutions.
                    </p>
                    <p>
                        At www.indiaeauction.com , we offer a unique platform for buying and selling services
                        through online auctions. Whether you're a service provider looking to showcase your skills
                        or a buyer in need of professional services, our platform provides an efficient and
                        convenient solution for connecting service providers with potential clients.
                    </p>

                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="content ps-0 ps-lg-5">
                        <!-- <p class="fst-italic">
                                                                                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                                                                                                    incididunt ut labore et dolore
                                                                                                                    magna aliqua.
                                                                                                                </p> -->

                        <img src="{{ asset('homepage/assets/img/auction_small.jpg') }}" class="img-fluid rounded-4 mb-4"
                            alt="">
                        <ul>
                            <!--  <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea
                                                                                                                            commodo consequat.</span></li>
                                                                                                                    <li><i class="bi bi-check-circle-fill"></i> <span>Duis aute irure dolor in
                                                                                                                            reprehenderit in voluptate velit.</span></li>
                                                                                                                    <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea
                                                                                                                            commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta
                                                                                                                            storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li> -->
                        </ul>


                        <div class="position-relative mt-4">
                            <!-- <img src="{{ asset('homepage/assets/img/about-2.jpg') }}" class="img-fluid rounded-4"
                                                                                                                        alt="">
                                                                                                                    <a href="https://www.youtube.com/watch?v=LXb3EKWsInQ" class="glightbox play-btn"></a> -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section><!-- /About Section -->

    <!-- Clients Section -->
    <!--  <section id="clients" class="clients section">

                                                                                                <div class="container">

                                                                                                    <div class="swiper">
                                                                                                        <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 2,
                  "spaceBetween": 40
                },
                "480": {
                  "slidesPerView": 3,
                  "spaceBetween": 60
                },
                "640": {
                  "slidesPerView": 4,
                  "spaceBetween": 80
                },
                "992": {
                  "slidesPerView": 6,
                  "spaceBetween": 120
                }
              }
            }
          </script>
                                                                                                        <div class="swiper-wrapper align-items-center">
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-1.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-2.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-3.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-4.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-5.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-6.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-7.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                            <div class="swiper-slide"><img src="{{ asset('homepage/assets/img/clients/client-8.png') }}"
                                                                                                                    class="img-fluid" alt=""></div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                </div>

                                                                                            </section> --><!-- /Clients Section -->

    <!-- Stats Section -->
    <section id="stats" class="stats section">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row gy-4 align-items-center">

                <div class="col-lg-5">
                    <img src="{{ asset('homepage/assets/img/stats-img.svg') }}" alt="" class="img-fluid">
                </div>

                <div class="col-lg-7">

                    <div class="row gy-4">

                        <div class="col-lg-6">
                            <div class="stats-item d-flex">
                                <i class="bi bi-emoji-smile flex-shrink-0"></i>
                                <div>
                                    <span data-purecounter-start="0" data-purecounter-end="232"
                                        data-purecounter-duration="1" class="purecounter"></span>
                                    <p><strong>Bidder</strong> <span></span></p>
                                </div>
                            </div>
                        </div><!-- End Stats Item -->

                        <div class="col-lg-6">
                            <div class="stats-item d-flex">
                                <i class="bi bi-journal-richtext flex-shrink-0"></i>
                                <div>
                                    <span data-purecounter-start="0" data-purecounter-end="21"
                                        data-purecounter-duration="1" class="purecounter"></span>
                                    <p><strong>Action</strong> <span></span></p>
                                </div>
                            </div>
                        </div><!-- End Stats Item -->

                        <div class="col-lg-6">
                            <div class="stats-item d-flex">
                                <i class="bi bi-headset flex-shrink-0"></i>
                                <div>
                                    <span data-purecounter-start="0" data-purecounter-end="1453"
                                        data-purecounter-duration="1" class="purecounter"></span>
                                    <p><strong>Hours Of Support</strong> <span></span></p>
                                </div>
                            </div>
                        </div><!-- End Stats Item -->

                        <div class="col-lg-6">
                            <div class="stats-item d-flex">
                                <i class="bi bi-people flex-shrink-0"></i>
                                <div>
                                    <span data-purecounter-start="0" data-purecounter-end="32"
                                        data-purecounter-duration="1" class="purecounter"></span>
                                    <p><strong>Hard Workers</strong> <span></span></p>
                                </div>
                            </div>
                        </div><!-- End Stats Item -->

                    </div>

                </div>

            </div>

        </div>

    </section><!-- /Stats Section -->

    <!-- Call To Action Section -->
    <!-- <section id="call-to-action" class="call-to-action section">

                                                                                                <div class="container">
                                                                                                    <img src="{{ asset('homepage/assets/img/cta-bg.jpg') }}" alt="">
                                                                                                    <div class="content row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
                                                                                                        <div class="col-xl-10">
                                                                                                            <div class="text-center">
                                                                                                                <a href="https://www.youtube.com/watch?v=LXb3EKWsInQ" class="glightbox play-btn"></a>
                                                                                                                <h3>Call To Action</h3>
                                                                                                                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat
                                                                                                                    nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui
                                                                                                                    officia deserunt mollit anim id est laborum.</p>
                                                                                                                <a class="cta-btn" href="#">Call To Action</a>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                            </section> --><!-- /Call To Action Section -->

    <!-- Services Section -->
    <section id="services" class="services section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Our Services</h2>
            <p>Users can participate in auctions from anywhere with internet access.</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-4">

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-item  position-relative">
                        <div class="icon">
                            <i class="bi bi-activity"></i>
                        </div>
                        <h3>Forward Auction</h3>
                        <p> In a forward auction, sellers list items and buyers place progressively higher bids until
                            the auction ends, with the highest bid winning the item.</p>
                        <a href="#" class="readmore stretched-link">Read more <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div><!-- End Service Item -->

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-broadcast"></i>
                        </div>
                        <h3>Reverse Auction</h3>
                        <p>In a reverse auction, buyers post their requirements for goods or services, and sellers
                            compete to offer the lowest price. </p>
                        <a href="#" class="readmore stretched-link">Read more <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div><!-- End Service Item -->

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-easel"></i>
                        </div>
                        <h3>Dutch Auction</h3>
                        <p>In a Dutch auction, the auctioneer starts with a high asking price which is gradually lowered
                            until a participant is willing to accept the auctioneer’s price. </p>
                        <a href="#" class="readmore stretched-link">Read more <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div><!-- End Service Item -->

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-bounding-box-circles"></i>
                        </div>
                        <h3>Sealed Bid Auction</h3>
                        <p>In a sealed bid auction, all bidders submit their bids independently and without knowing the
                            other bids.</p>
                        <a href="#" class="readmore stretched-link">Read more <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div><!-- End Service Item -->

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-calendar4-week"></i>
                        </div>
                        <h3>English Auction</h3>
                        <p>An English auction is a type of forward auction where the bidding starts at a low price and
                            increases as participants place higher bids.</p>
                        <a href="#" class="readmore stretched-link">Read more <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div><!-- End Service Item -->

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-chat-square-text"></i>
                        </div>
                        <h3>Vickrey Auction</h3>
                        <p>Also known as a second-price sealed-bid auction, in a Vickrey auction, bidders submit sealed
                            bids and the highest bidder wins, but the price paid is the second-highest bid.</p>
                        <a href="#" class="readmore stretched-link">Read more <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div><!-- End Service Item -->

            </div>

        </div>

    </section><!-- /Services Section -->

    <!-- Testimonials Section -->
    <!-- /Testimonials Section -->

    <!-- Portfolio Section -->
    <!-- /Portfolio Section -->

    <!-- Team Section -->
    <!-- /Team Section -->

    <!-- Pricing Section -->
    <!-- /Pricing Section -->

    <!-- Faq Section -->

    <!-- Recent Posts Section -->
    <!-- /Recent Posts Section -->

    <!-- Contact Section -->
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
                        </div><!-- End Info Item -->

                    </div>

                </div>










                <div class="col-lg-8">
                    <!-- <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade"
                                                                                                                data-aos-delay="100">
                                                                                                                <div class="row gy-4">

                                                                                                                    <div class="col-md-6">
                                                                                                                        <input type="text" name="name" class="form-control"
                                                                                                                            placeholder="Your Name" required="">
                                                                                                                    </div>

                                                                                                                    <div class="col-md-6 ">
                                                                                                                        <input type="email" class="form-control" name="email"
                                                                                                                            placeholder="Your Email" required="">
                                                                                                                    </div>

                                                                                                                    <div class="col-md-12">
                                                                                                                        <input type="text" class="form-control" name="subject" placeholder="Subject"
                                                                                                                            required="">
                                                                                                                    </div>

                                                                                                                    <div class="col-md-12">
                                                                                                                        <textarea class="form-control" name="message" rows="8" placeholder="Message" required=""></textarea>
                                                                                                                    </div>

                                                                                                                    <div class="col-md-12 text-center">
                                                                                                                        <div class="loading">Loading</div>
                                                                                                                        <div class="error-message"></div>
                                                                                                                        <div class="sent-message">Your message has been sent. Thank you!</div>

                                                                                                                        <button type="submit">Send Message</button>
                                                                                                                    </div>

                                                                                                                </div>
                                                                                                            </form> -->
                </div><!-- End Contact Form -->

            </div>

        </div>

    </section><!-- /Contact Section -->
@endsection
@section('footer-script')
    
@endsection
