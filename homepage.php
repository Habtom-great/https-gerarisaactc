
<?php
// Include any necessary PHP code here
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alba bistro Restaurant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="home_style.css">
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container container">
            <input type="checkbox" id="navbar-toggle">
            <div class="hamburger-lines">
                <span class="line line1"></span>
                <span class="line line2"></span>
                <span class="line line3"></span>
            </div>
            <ul class="menu-items">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#food">Category</a></li>
                <li><a href="#food-menu">Menu</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <h1 class="logo">ROHOBOT</h1>
        </div>
    </nav>

    <section class="showcase-area" id="showcase">
        <div class="showcase-container">
            <h1 class="main-title">Eat Right Food</h1>
            <p>Eat Healthy, it is good for your health.</p>
            <a href="#food-menu" class="btn btn-primary">Menu</a>
        </div>
    </section>

    <section id="about">
        <div class="about-wrapper container">
            <div class="about-text">
                <p class="small">About Us</p>
                <h2>We've been making healthy food for 8 years</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse ab eos omnis dignissimos perferendis et officia architecto fugiat possimus.</p>
            </div>
            <div class="about-img">
                <img src="images/home_images/about-photo.jpg" alt="About Us">
            </div>
        </div>
    </section>

    <section id="food">
        <h2>Types of Food</h2>
        <div class="food-container container">
            <div class="food-type fruite">
                <div class="img-container">
                    <img src="images/home_images/food1.jpg" alt="Fruits">
                    <div class="img-content">
                        <h3>Fruits</h3>
                        <a href="https://en.wikipedia.org/wiki/Fruit" class="btn btn-primary" target="_blank">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="food-type vegetable">
                <div class="img-container">
                    <img src="images/home_images/food2.jpg" alt="Vegetables">
                    <div class="img-content">
                        <h3>Vegetables</h3>
                        <a href="https://en.wikipedia.org/wiki/Vegetable" class="btn btn-primary" target="_blank">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="food-type grain">
                <div class="img-container">
                    <img src="images/home_images/food3.jpg" alt="Grains">
                    <div class="img-content">
                        <h3>Grains</h3>
                        <a href="https://en.wikipedia.org/wiki/Grain" class="btn btn-primary" target="_blank">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="food-menu">
        <h2 class="food-menu-heading">Food Menu</h2>
        <div class="food-menu-container container">
            <?php
            $menuItems = [
                ['img' => 'images/home_images/food-menu1.jpg', 'title' => 'Food Menu Item 1', 'description' => 'Lorem ipsum dolor sit amet consectetur.', 'price' => '---'],
                ['img' => 'images/home_images/food-menu2.jpg', 'title' => 'Food Menu Item 2', 'description' => 'Lorem ipsum dolor sit amet consectetur.', 'price' => '---'],
                ['img' => 'images/home_images/food-menu3.jpg', 'title' => 'Food Menu Item 3', 'description' => 'Lorem ipsum dolor sit amet consectetur.', 'price' => '---'],
                ['img' => 'images/home_images/food-menu4.jpg', 'title' => 'Food Menu Item 4', 'description' => 'Lorem ipsum dolor sit amet consectetur.', 'price' => '---'],
                ['img' => 'images/home_images/food-menu5.jpg', 'title' => 'Food Menu Item 5', 'description' => 'Lorem ipsum dolor sit amet consectetur.', 'price' => '&#8377; 250'],
                ['img' => 'images/home_images/food-menu6.jpg', 'title' => 'Food Menu Item 6', 'description' => 'Lorem ipsum dolor sit amet consectetur.', 'price' => '---']
            ];

            foreach ($menuItems as $item) {
                echo "
                <div class=\"food-menu-item\">
                    <div class=\"food-img\">
                        <img src=\"{$item['img']}\" alt=\"{$item['title']}\">
                    </div>
                    <div class=\"food-description\">
                        <h2 class=\"food-title\">{$item['title']}</h2>
                        <p>{$item['description']}</p>
                        <p class=\"food-price\">Price: {$item['price']}</p>
                    </div>
                </div>
                ";
            }
            ?>
        </div>
    </section>

    <section id="testimonials">
        <h2 class="testimonial-title">What Our Customers Say</h2>
        <div class="testimonial-container container">
            <div class="testimonial-box">
                <div class="customer-detail">
                    <div class="customer-photo">
                        <img src="images/home_images/male-photo1.jpg" alt="Andrew Lesito">
                        <p class="customer-name">Andrew Lesito</p>
                    </div>
                </div>
                <div class="star-rating">
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                </div>
                <p class="testimonial-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Impedit voluptas cupiditate aspernatur odit doloribus non.</p>
            </div>
            <div class="testimonial-box">
                <div class="customer-detail">
                    <div class="customer-photo">
                        <img src="images/home_images/female-photo1.jpg" alt="Semira Esmael">
                        <p class="customer-name">Semira Esmael</p>
                    </div>
                </div>
                <div class="star-rating">
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                </div>
                <p class="testimonial-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Impedit voluptas cupiditate aspernatur odit doloribus non.</p>
            </div>
            <div class="testimonial-box">
                <div class="customer-detail">
                    <div class="customer-photo">
                        <img src="images/home_images/male-photo3.jpg" alt="Jack Mickiel">
                        <p class="customer-name">Jack Mickiel</p>
                    </div>
                </div>
                <div class="star-rating">
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                </div>
                <p class="testimonial-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Impedit voluptas cupiditate aspernatur odit doloribus non.</p>
            </div>
        </div>
    </section>

            <section id="contact">
        <div class="contact-container container">
            <div class="contact-img"> <img src="images/home_images/restraunt2.jpg" alt="" /> </div>
            <div class="form-container">
                <h2>Contact Us</h2> <input type="text" placeholder="Your Name" /> <input type="email" placeholder="E-Mail" /> <textarea cols="30" rows="6" placeholder="Type Your Message"></textarea> <a href="#" class="btn btn-primary">Submit</a>
            </div>
        </div>
    </section>

    <footer id="footer">
        <h2>ROHOBOT Restaurant  <br>
            <a href="https://facebook.com/" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a></h2>
        <div class="footer-social">
             <h2> <div class="customer-photo"> <img src="images/home_images/IMG_20160114_152734-1.jpg" alt="" />Web Developer Habtom Araya-ACCA  &copy; all rights reserved</h2>
            
        </div>
      
    </footer>

</body>

</html>

kkkkkkkkkkkkkkkkk
