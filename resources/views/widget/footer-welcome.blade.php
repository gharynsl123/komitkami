<footer class="bg-dark text-light pt-5 pb-3" id="contact" style="background: linear-gradient(180deg, #161c24 0%, #0c1015 100%);">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Company Info Column -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('images/KING-DATAR.png') }}" alt="Komitkami Logo" height="38"
                        style="filter: brightness(0) invert(1); max-height: 40px; width: auto;" class="me-2">
                </div>
                <p class="text-secondary small lh-base mb-3">
                    @if(app()->getLocale() === 'en')
                        Trusted medical device manufacturing company dedicated to high quality, innovation, and sustainable quality assurance in healthcare.
                    @else
                        Perusahaan manufaktur alat kesehatan terpercaya yang berfokus pada kualitas tinggi, inovasi produk, dan jaminan mutu berkelanjutan di industri kesehatan.
                    @endif
                </p>
                <div class="d-flex gap-2">
                    <a href="https://www.youtube.com/@KOMITKAMI-Official" target="_blank"
                       class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
                       style="width: 36px; height: 36px;" aria-label="YouTube">
                        <i class="bi bi-youtube text-danger"></i>
                    </a>
                    <a href="https://wa.me/6281291238909" target="_blank"
                       class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
                       style="width: 36px; height: 36px;" aria-label="WhatsApp">
                        <i class="bi bi-whatsapp text-success"></i>
                    </a>
                    <a href="mailto:komitkami@gmail.com"
                       class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
                       style="width: 36px; height: 36px;" aria-label="Email">
                        <i class="bi bi-envelope-fill text-warning"></i>
                    </a>
                </div>
            </div>

            <!-- Contact Details Column -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-white mb-3 position-relative pb-2" style="letter-spacing: 0.5px;">
                    @if(app()->getLocale() === 'en') Contact Us @else Kontak Kami @endif
                    <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 2px; background: #0d6efd;"></span>
                </h5>
                <ul class="list-unstyled text-secondary small mb-0">
                    <li class="d-flex align-items-start mb-2">
                        <i class="bi bi-geo-alt-fill text-primary me-2 mt-1 fs-6"></i>
                        <span>
                            Komplek Pergudangan Bizpark 3, Jl. Sultan Agung, RT.003/RW.005,
                            Kali Baru, Medan Satria, Kota Bekasi, Jawa Barat 17133
                        </span>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="bi bi-telephone-fill text-primary me-2 fs-6"></i>
                        <a href="tel:02189463330" class="text-secondary text-decoration-none">021 8946 3330</a>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="bi bi-whatsapp text-success me-2 fs-6"></i>
                        <a href="https://wa.me/6281291238909" target="_blank" class="text-secondary text-decoration-none">+62 812-9123-8909</a>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="bi bi-envelope-fill text-primary me-2 fs-6"></i>
                        <a href="mailto:komitkami@gmail.com" class="text-secondary text-decoration-none">komitkami@gmail.com</a>
                    </li>
                </ul>
            </div>

            <!-- Google Map Column -->
            <div class="col-lg-4 col-md-12">
                <h5 class="fw-bold text-white mb-3 position-relative pb-2" style="letter-spacing: 0.5px;">
                    @if(app()->getLocale() === 'en') Location Map @else Lokasi Pabrik @endif
                    <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 2px; background: #0d6efd;"></span>
                </h5>
                <div class="shadow rounded-3 overflow-hidden border border-secondary" style="height: 180px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.362750210472!2d106.97708617607!3d-6.215800393772137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698d9f9fdd862d%3A0x77fb58db5bbebc12!2sPT%20Komitkami%20Intinusa%20Gemilang!5e0!3m2!1sen!2sid!4v1716789894607!5m2!1sen!2sid"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>

        <hr class="border-secondary opacity-25 mt-4 mb-3">

        <!-- Bottom Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-secondary small m-0">
                    &copy; {{ date('Y') }} <strong>PT Komitkami Intinusa Gemilang</strong>. All Rights Reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                <span class="badge bg-secondary opacity-75 fw-normal small px-2 py-1">ISO & CPAKB Standard</span>
            </div>
        </div>
    </div>
</footer>
