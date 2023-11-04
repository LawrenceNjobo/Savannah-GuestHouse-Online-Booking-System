<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'room booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Savannah GuestHouse</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body class="gradient-background">
  

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/home-img-1.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Rooms</h3>
               <a href="#availability" class="btn">Check Availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>BreakFast</h3>
               <a href="#reservation" class="btn">Make Reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>Family Rooms</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Check In <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check Out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
         <div class="box">
            <p>Rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<section class="about" id="about">

  <div class="row">
    <div class="image">
      <img src="images/about-img-1.jpg" alt="">
    </div>
    <div class="content">
      <h3>Amazing Staff</h3>
      <p>Our staff is the heart and soul of Savannah GuestHouse. We are passionate about providing our guests with the best possible experience. We are always happy to help you with anything you need, from booking tours to recommending restaurants. We are here to make your stay as enjoyable as possible.</p>
    </div>
      <a href="#reservation" class="btn">Make a Reservation</a>
    </div>
  </div>

  <div class="row revers">
    <div class="image">
      <img src="images/about-img-2.jpg" alt="">
    </div>
    <div class="content">
      <h3>Delicious Meals</h3>
      <p>Our restaurant offers a variety of delicious meals, all made with fresh, local ingredients. Our menu changes seasonally to reflect the freshest produce available. We also offer a variety of vegetarian and vegan options. Whether you're looking for a quick bite or a leisurely meal, we have something to satisfy everyone.</p>
      
      <a href="#contact" class="btn">Contact Us</a>
    </div>
  </div>

  <div class="row">
    <div class="image">
      <img src="images/about-img-3.jpg" alt="">
    </div>
    <div class="content">
      <h3>Garden</h3>
      <p>Our beautiful garden is the perfect place to relax and unwind after a long day of exploring Savannah. The garden features a variety of plants and flowers, as well as a fountain and a pond. Guests can enjoy a cup of coffee or tea in the garden, or simply relax and enjoy the peace and quiet.</p>

      <a href="#availability" class="btn">Check Availability</a>
    </div>
  </div>

</section>


<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

  <div class="box-container">

    <div class="box">
      <i class="fa fa-cutlery"></i>
      <h3>Breakfast</h3>
      <p>Start your day off right with a delicious breakfast at Savannah GuestHouse. We offer a variety of options to choose from, including continental breakfast, full English breakfast, and more.</p>
    </div>

    <div class="box">
      <i class="fa fa-car"></i>
      <h3>Free Parking</h3>
      <p>Our guests enjoy free parking on-site. This is a great convenience for travelers who are coming to Savannah by car.</p>
    </div>

    <div class="box">
      <i class="fa fa-tree"></i>
      <h3>Garden</h3>
      <p>Savannah GuestHouse has a beautiful garden where guests can relax and enjoy the fresh air. The garden is also a great place to take photos or simply enjoy a quiet moment.</p>
    </div>

    <div class="box">
      <i class="fa fa-puzzle-piece"></i>
      <h3>Puzzles/Board Games</h3>
      <p>We have a variety of puzzles and board games available for our guests to enjoy. This is a great way to relax and unwind after a long day of exploring Savannah.</p>
    </div>

    <div class="box">
      <i class="fa fa-bell"></i>
      <h3>Room Service</h3>
      <p>If you're traveling with children, you can have room service clean up after them so you don't have to worry about it.If you're on a romantic vacation, you can have room service deliver a special meal to your room so you can enjoy a private dinner together.</p>
    </div>

    <div class="box">
      <i class="fa fa-map-o"></i>
      <h3>Tour Desk</h3>
      <p>Our tour desk can help you book tours and activities in Savannah. We offer a variety of tours to choose from, including city tours, historical tours, and more.</p>
    </div>

  </div>

</section>



<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>Make Reservation</h3>
      <div class="flex">
         <div class="box">
            <p>Enter Name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>Enter Email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>Enter Telephone <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>Room <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
         <div class="box">
            <p>Check In <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check Out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/room1.jpg" class="swiper-slide" alt="">
         <img src="images/room2.jpg" class="swiper-slide" alt="">
         <img src="images/room3.jpg" class="swiper-slide" alt="">
         <img src="images/room4.jpg" class="swiper-slide" alt="">
         <img src="images/room5.jpg" class="swiper-slide" alt="">
         <img src="images/room6.jpg" class="swiper-slide" alt="">
         <img src="images/room7.jpg" class="swiper-slide" alt="">
         <img src="images/room8.jpg" class="swiper-slide" alt="">
         <img src="images/room9.jpg" class="swiper-slide" alt="">
         <img src="images/room10.jpg" class="swiper-slide" alt="">
         <img src="images/room11.jpg" class="swiper-slide" alt="">
         <img src="images/room12.jpg" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>Send us messages/ FeedBack</h3>
         <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder= "Enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="Enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
  <h3 class="title">Frequently asked Questions</h3>
  <div class="box active">
    <h3>How to cancel?</h3>
    <p>To cancel your reservation, please contact us at 078 985 6228/077 258 2504 email us at savannahguesthouse@outlook.com. Please note that there is a cancellation fee of 24 hours prior to your arrival date.</p>
  </div>
  <div class="box">
    <h3>Is there any vacancy?</h3>
    <p>Yes, we have vacancies available. Please check our availability calendar on our website to see which dates are available.</p>
  </div>
  <div class="box">
    <h3>What are payment methods?</h3>
    <p>We accept all major credit cards, as well as PayPal and Venmo.</p>
  </div>
  <div class="box">
    <h3>How to claim coupons codes?</h3>
    <p>To claim a coupon code, please enter the code in the "Coupon Code" field on our checkout page.</p>
  </div>
  <div class="box">
    <h3>What are the age requirements?</h3>
    <p>Guests must be at least 18 years old to check in without a parent or guardian.</p>
  </div>
</div>


   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/reviews2.jpg" alt="">
            <h3>SHEPHERD CHIDAMBAEH</h3>
            <p>Great affordable place... Stayed there for my honeymoon... My wife Mazvita loved it"</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/reviews1.jpg" alt="">
            <h3>Mike Chapisa</h3>
            <p>"Affordable, very clean, respectful staff, everything about this place is just so good, i highly recommend it to just about anyone who might be in need of accomodation. You wont regret"</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>Alistas N</h3>
            <p>"Excellent service, had an amazing time there, Host and hostess are really great people, very friendly and welcoming, you feel right at home when you are there"</p>
         </div>
         
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
<script src="https://kit.fontawesome.com/032d11eac3.js" crossorigin="anonymous"></script>
</html>