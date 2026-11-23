<?php
/**
 * Home page (converted from the original index.html).
 * @var list<array<string,mixed>> $packages
 * @var list<array<string,mixed>> $destinationOptions
 */
use App\Bookings;
use App\Controllers\BookingController;

$booking = BookingController::prefill();
?>

    <!-- home section starts  -->

    <div class="home-container">

        <section class="home" id="home">

            <div class="content">
                <h3>adventure is worthwhile</h3>
                <p>dicover new places with us, adventure awaits</p>
                <a href="#gallery" class="btn">discover more</a>
            </div>

            <div class="controls">
                <span class="vid-btn active" data-src="/images/vid-1.mp4"></span>
                <span class="vid-btn" data-src="/images/vid-2.mp4"></span>
                <span class="vid-btn" data-src="/images/vid-3.mp4"></span>
                <span class="vid-btn" data-src="/images/vid-4.mp4"></span>
                <span class="vid-btn" data-src="/images/vid-5.mp4"></span>
            </div>

            <div class="video-container">
                <video src="/images/vid-1.mp4" id="video-slider" loop autoplay muted></video>
            </div>

        </section>

    </div>

    <!-- home section ends -->

    <!-- book section starts  -->

    <section class="book" id="book">

        <h1 class="heading">
            <span>b</span>
            <span>o</span>
            <span>o</span>
            <span>k</span>
            <span class="space"></span>
            <span>n</span>
            <span>o</span>
            <span>w</span>
        </h1>

        <div class="row">

            <div class="image">
                <img src="/images/book-img.svg" alt="">
            </div>

            <form action="/bookings" method="post" class="booking-form">
                <?= csrf_field() ?>
                <input type="hidden" name="return_to" value="/#book">
                <div class="inputBox">
                    <h3>where to</h3>
                    <select name="destination_id" required>
                        <option value="">choose a destination</option>
<?php foreach ($destinationOptions as $opt): ?>
                        <option value="<?= e($opt['id']) ?>" data-price="<?= e($opt['price']) ?>"
                            <?= (string) $opt['id'] === $booking['destination_id'] ? 'selected' : '' ?>>
                            <?= e($opt['name']) ?>, <?= e($opt['country']) ?> (<?= e(money($opt['price'])) ?> / guest)
                        </option>
<?php endforeach; ?>
                    </select>
                </div>
                <div class="inputBox">
                    <h3>how many</h3>
                    <input type="number" name="guests" min="1" max="<?= Bookings::MAX_GUESTS ?>" placeholder="number of guests"
                        value="<?= e($booking['guests']) ?>" required>
                </div>
                <div class="inputBox">
                    <h3>arrivals</h3>
                    <input type="date" name="arrival" min="<?= e(today()) ?>" value="<?= e($booking['arrival']) ?>" required>
                </div>
                <div class="inputBox">
                    <h3>leaving</h3>
                    <input type="date" name="departure" min="<?= e(today()) ?>" value="<?= e($booking['departure']) ?>" required>
                </div>
                <p class="booking-total" data-booking-total hidden></p>
                <input type="submit" class="btn" value="book now">
            </form>

        </div>

    </section>

    <!-- book section ends -->

    <!-- packages section starts  -->

    <section class="packages" id="packages">

        <h1 class="heading">
            <span>p</span>
            <span>a</span>
            <span>c</span>
            <span>k</span>
            <span>a</span>
            <span>g</span>
            <span>e</span>
            <span>s</span>
        </h1>

        <div class="box-container">

<?php foreach ($packages as $d): ?>
            <?= App\View::partial('partials/package-card', ['d' => $d]) ?>
<?php endforeach; ?>

        </div>

        <div class="see-all">
            <a href="/destinations" class="btn">see all destinations</a>
        </div>

    </section>

    <!-- packages section ends -->

    <!-- services section starts  -->

    <section class="services" id="services">

        <h1 class="heading">
            <span>s</span>
            <span>e</span>
            <span>r</span>
            <span>v</span>
            <span>i</span>
            <span>c</span>
            <span>e</span>
            <span>s</span>
        </h1>

        <div class="box-container">

            <div class="box">
                <i class="fas fa-hotel"></i>
                <h3>affordable hotels</h3>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quam, incidunt?</p>
            </div>
            <div class="box">
                <i class="fas fa-utensils"></i>
                <h3>food and drinks</h3>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quam, incidunt?</p>
            </div>
            <div class="box">
                <i class="fas fa-bullhorn"></i>
                <h3>safty guide</h3>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quam, incidunt?</p>
            </div>
            <div class="box">
                <i class="fas fa-globe-asia"></i>
                <h3>around the world</h3>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quam, incidunt?</p>
            </div>
            <div class="box">
                <i class="fas fa-plane"></i>
                <h3>fastest travel</h3>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quam, incidunt?</p>
            </div>
            <div class="box">
                <i class="fas fa-hiking"></i>
                <h3>adventures</h3>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quam, incidunt?</p>
            </div>

        </div>

    </section>

    <!-- services section ends -->

    <!-- gallery section starts  -->

    <section class="gallery" id="gallery">

        <h1 class="heading">
            <span>g</span>
            <span>a</span>
            <span>l</span>
            <span>l</span>
            <span>e</span>
            <span>r</span>
            <span>y</span>
        </h1>

        <div class="box-container">

            <div class="box">
                <img src="/images/g-1.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-2.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-3.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-4.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-5.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-6.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-7.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-8.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>
            <div class="box">
                <img src="/images/g-9.jpg" alt="">
                <div class="content">
                    <h3>amazing places</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, tenetur.</p>
                    <a href="/destinations" class="btn">see more</a>
                </div>
            </div>

        </div>

    </section>

    <!-- gallery section ends -->

    <!-- review section starts  -->

    <section class="review" id="review">

        <h1 class="heading">
            <span>r</span>
            <span>e</span>
            <span>v</span>
            <span>i</span>
            <span>e</span>
            <span>w</span>
        </h1>

        <div class="swiper review-slider">

            <div class="swiper-wrapper">

                <div class="swiper-slide box">
                    <img src="/images/pic1.png" alt="">
                    <h3>john deo</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem animi nemo harum pariatur nesciunt
                        praesentium in facilis, temporibus assumenda iste?</p>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
                <div class="swiper-slide box">
                    <img src="/images/pic2.png" alt="">
                    <h3>john deo</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem animi nemo harum pariatur nesciunt
                        praesentium in facilis, temporibus assumenda iste?</p>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
                <div class="swiper-slide box">
                    <img src="/images/pic3.png" alt="">
                    <h3>john deo</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem animi nemo harum pariatur nesciunt
                        praesentium in facilis, temporibus assumenda iste?</p>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
                <div class="swiper-slide box">
                    <img src="/images/pic4.png" alt="">
                    <h3>john deo</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem animi nemo harum pariatur nesciunt
                        praesentium in facilis, temporibus assumenda iste?</p>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>

            </div>

        </div>

    </section>

    <!-- review section ends -->

    <!-- contact section starts  -->

    <section class="contact" id="contact">

        <h1 class="heading">
            <span>c</span>
            <span>o</span>
            <span>n</span>
            <span>t</span>
            <span>a</span>
            <span>c</span>
            <span>t</span>
        </h1>

        <div class="row">

            <div class="image">
                <img src="/images/contact-img.svg" alt="">
            </div>

            <form action="/contact" method="post">
                <?= csrf_field() ?>
                <div class="hp-field" aria-hidden="true">
                    <label>leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>
                <div class="inputBox">
                    <input type="text" placeholder="name" name="name" maxlength="100" value="<?= e(old('name')) ?>" required>
                    <input type="email" placeholder="email" name="email" maxlength="190" value="<?= e(old('email')) ?>" required>
                </div>
                <div class="inputBox">
                    <input type="tel" placeholder="number" name="phone" maxlength="20" value="<?= e(old('phone')) ?>">
                    <input type="text" placeholder="subject" name="subject" maxlength="150" value="<?= e(old('subject')) ?>" required>
                </div>
                <textarea placeholder="message" name="message" cols="30" rows="10" maxlength="5000" required><?= e(old('message')) ?></textarea>
                <input type="submit" class="btn" value="send message">
            </form>

        </div>

    </section>

    <!-- contact section ends -->

    <!-- brand section  -->
    <section class="brand-container">

        <div class="swiper brand-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="/images/1.jpg" alt=""></div>
                <div class="swiper-slide"><img src="/images/2.jpg" alt=""></div>
                <div class="swiper-slide"><img src="/images/3.jpg" alt=""></div>
                <div class="swiper-slide"><img src="/images/4.jpg" alt=""></div>
                <div class="swiper-slide"><img src="/images/5.jpg" alt=""></div>
                <div class="swiper-slide"><img src="/images/6.jpg" alt=""></div>
            </div>
        </div>

    </section>

