<?php

include 'config.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['register'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass'] );
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `user` WHERE name = ? AND email = ?");
   $select_user->execute([$name, $email]);

   if($select_user->rowCount() > 0){
      $message[] = 'username atau email sudah ada!';
   }else{
      if($pass != $cpass){
         $message[] = 'Konfirmasi password salah!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `user`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);
         $message[] = 'Registrasi berhasil, silahkan login!';
      }
   }

}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'Keranjang berhasil diupdate!';
}

if(isset($_GET['delete_cart_item'])){
   $delete_cart_id = $_GET['delete_cart_item'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_cart_id]);
   header('location:index.php');
}

if(isset($_GET['logout'])){
   session_unset();
   session_destroy();
   header('location:index.php');
}

if(isset($_POST['add_to_cart'])){

   if($user_id == ''){
      $message[] = 'Login terlebih dahulu untuk pesan!';
   }else{

      $pid = $_POST['pid'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $image = $_POST['image'];
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_STRING);

      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND name = ?");
      $select_cart->execute([$user_id, $name]);

      if($select_cart->rowCount() > 0){
         $message[] = 'Sudah di keranjang';
      }else{
         $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
         $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
         $message[] = 'Berhasil dimasukan keranjang!';
      }

   }

}

if(isset($_POST['order'])){

   if($user_id == ''){
      $message[] = 'Login terlebih dahulu untuk pesan!';
   }else{
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $number = $_POST['number'];
      $number = filter_var($number, FILTER_SANITIZE_STRING);
      $address = $_POST['flat'].', '.$_POST['street'].' - '.$_POST['pin_code'];
      $address = filter_var($address, FILTER_SANITIZE_STRING);
      $method = $_POST['method'];
      $method = filter_var($method, FILTER_SANITIZE_STRING);
      $total_price = $_POST['total_price'];
      $total_products = $_POST['total_products'];

      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);

      if($select_cart->rowCount() > 0){
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $method, $address, $total_products, $total_price]);
         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);
         $message[] = 'Pesanan Berhasil!';
      }else{
         $message[] = 'Keranjang masih kosong!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Kedai Kopi</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<!-- header section starts  -->

<header class="header">

   <section class="flex">

      <a href="#home" class="logo"><span>KEDAI</span> KOPI</a>

      <nav class="navbar">
         <a href="#home">HOME</a>
         <a href="#about">TENTANG KAMI</a>
         <a href="#menu">MENU</a>
         <a href="#order">PESAN</a>
         <a href="#faq">FAQ</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="order-btn" class="fas fa-sticky-note"></div>
         <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
         ?>
         <div id="cart-btn" class="fas fa-shopping-cart"><span>(<?= $total_cart_items; ?>)</span></div>
      </div>

   </section>

</header>

<!-- header section ends -->

<div class="user-account">

   <section>

      <div id="close-account"><span>close</span></div>

      <div class="user">
         <?php
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE id = ?");
            $select_user->execute([$user_id]);
            if($select_user->rowCount() > 0){
               while($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)){
                  echo '<p>Selamat Datang! <span>'.$fetch_user['name'].'</span></p>';
                  echo '<a href="index.php?logout" class="btn">logout</a>';
               }
            }else{
               echo '<p><span>Kamu belum login!</span></p>';
            }
         ?>
      </div>

      <div class="display-orders">
         <?php
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if($select_cart->rowCount() > 0){
               while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                  echo '<p>'.$fetch_cart['name'].' <span>('.$fetch_cart['price'].' x '.$fetch_cart['quantity'].')</span></p>';
               }
            }else{
               echo '<p><span>Keranjang masih kosong!</span></p>';
            }
         ?>
      </div>

      <div class="flex">

         <form action="user_login.php" method="post">
            <h3>login</h3>
            <input type="email" name="email" required class="box" placeholder="Masukan email" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="Masukan password" maxlength="20">
            <input type="submit" value="login now" name="login" class="btn">
         </form>

         <form action="" method="post">
            <h3>register</h3>
            <input type="text" name="name" oninput="this.value = this.value.replace(/\s/g, '')" required class="box" placeholder="Masukan Username" maxlength="20">
            <input type="email" name="email" required class="box" placeholder="Masukan email" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="Masukan password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" required class="box" placeholder="konfirmasi password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="register now" name="register" class="btn">
         </form>

      </div>

   </section>

</div>

<div class="my-orders">

   <section>

      <div id="close-orders"><span>close</span></div>

      <h3 class="title"> Nota Pesanan </h3>

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){   
      ?>
      <div class="box">
         <p> Dibuat pada : <span><?= $fetch_orders['placed_on']; ?></span> </p>
         <p> Nama : <span><?= $fetch_orders['name']; ?></span> </p>
         <p> Nomor telepon : <span><?= $fetch_orders['number']; ?></span> </p>
         <p> Alamat : <span><?= $fetch_orders['address']; ?></span> </p>
         <p> Metode Pembayaran : <span><?= $fetch_orders['method']; ?></span> </p>
         <p> Total Pesanan : <span><?= $fetch_orders['total_products']; ?></span> </p>
         <p> Subtotal : <span>Rp <?= $fetch_orders['total_price']; ?></span> </p>
         <p> payment status : <span style="color:<?php if($fetch_orders['payment_status'] == 'pending'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Belum pesan!</p>';
      }
      ?>

   </section>

</div>

<div class="shopping-cart">

   <section>

      <div id="close-cart"><span>close</span></div>

      <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
              $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
              $grand_total += $sub_total; 
      ?>
      <div class="box">
         <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this cart item?');"></a>
         <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
         <div class="content">
          <p> <?= $fetch_cart['name']; ?> <span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
          <form action="" method="post">
             <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
             <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" onkeypress="if(this.value.length == 2) return false;">
               <button type="submit" class="fas fa-edit" name="update_qty"></button>
          </form>
         </div>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty"><span>Keranjang masih kosong!</span></p>';
      }
      ?>

      <div class="cart-total"> Subtotal : <span>Rp <?= $grand_total; ?></span></div>

      <a href="#order" class="btn">Pesan sekarang</a>

   </section>

</div>

<div class="home-bg">

   <section class="home" id="home">

      <div class="slide-container">

         <div class="slide active">
            <div class="image">
               <img src="images/latte.png" alt="">
            </div>
            <div class="content">
               <h3>Coffee Latte</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>

         <div class="slide">
            <div class="image">
               <img src="images/american.png" alt="">
            </div>
            <div class="content">
               <h3>Authentic americano</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>

         <div class="slide">
            <div class="image">
               <img src="images/Mochacino.png" alt="">
            </div>
            <div class="content">
               <h3>Mochacino</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>

         <div class="slide">
            <div class="image">
               <img src="images/chocolate-coffe.png" alt="">
            </div>
            <div class="content">
               <h3>Chocolate Coffee</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>

      </div>

   </section>

</div>

<!-- about section starts  -->

<section class="about" id="about">

   <h1 class="heading">Tentang Kami</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/barista3.jpg" alt="">
         <h3>Made With Love</h3>
         <p>Kami bangga menyajikan kopi dengan kualitas terbaik, yang dibuat dengan hati dan keahlian para barista kami. Di Kedai Kopi, kami tidak hanya menjual minuman, tetapi juga memberikan secangkir kebahagiaan yang dibuat dengan cinta.</p>
         <a href="#menu" class="btn">Menu</a>
      </div>

      <div class="box">
         <img src="images/delivery5.jpg" alt="">
         <h3>Fast Delivery</h3>
         <p>kami berkomitmen untuk memberikan minuman berkualitas ke tangan Anda secepat mungkin. Nikmati kenyamanan memesan dari rumah atau kantor Anda dan biarkan kami yang mengantarkan secangkir kebahagiaan langsung ke tempat Anda berada.</p>
         <a href="#menu" class="btn">Menu</a>
      </div>

      <div class="box">
         <img src="images/coffee3.png" alt="">
         <h3>Authentic Taste</h3>
         <p>Di Kedai Kopi, kami mengutamakan cita rasa otentik yang sesungguhnya. Proses roasting dan penyeduhan kami dilakukan dengan teliti untuk menjaga kualitas dan keaslian rasa kopi. Apapun pilihan kopi Anda, kami menjamin setiap tegukannya memiliki cita rasa yang sejati.</p>
         <a href="#menu" class="btn">Menu</a>
      </div>

   </div>

</section>

<!-- about section ends -->

<!-- menu section starts  -->

<section id="menu" class="menu">

   <h1 class="heading">Menu</h1>

   <div class="box-container">

      <?php
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){    
      ?>
      <div class="box">
         <div class="price">Rp <?= $fetch_products['price'] ?></div>
         <img src="uploaded_img/<?= $fetch_products['image'] ?>" alt="">
         <div class="name"><?= $fetch_products['name'] ?></div>
         <form action="" method="post">
            <input type="hidden" name="pid" value="<?= $fetch_products['id'] ?>">
            <input type="hidden" name="name" value="<?= $fetch_products['name'] ?>">
            <input type="hidden" name="price" value="<?= $fetch_products['price'] ?>">
            <input type="hidden" name="image" value="<?= $fetch_products['image'] ?>">
            <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
            <input type="submit" class="btn" name="add_to_cart" value="Masukan Keranjang">
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Belum ada produk yang ditambahkan!</p>';
      }
      ?>

   </div>

</section>

<!-- menu section ends -->

<!-- order section starts  -->

<section class="order" id="order">

   <h1 class="heading">Pesan Sekarang</h1>

   <form action="" method="post">

   <div class="display-orders">

   <?php
         $grand_total = 0;
         $cart_item[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
              $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
              $grand_total += $sub_total; 
              $cart_item[] = $fetch_cart['name'].' ( '.$fetch_cart['price'].' x '.$fetch_cart['quantity'].' ) - ';
              $total_products = implode($cart_item);
              echo '<p>'.$fetch_cart['name'].' <span>('.$fetch_cart['price'].' x '.$fetch_cart['quantity'].')</span></p>';
            }
         }else{
            echo '<p class="empty"><span>Keranjang anda kosong!</span></p>';
         }
      ?>

   </div>

      <div class="grand-total"> Total Pesanan : <span>Rp <?= $grand_total; ?></span></div>

      <input type="hidden" name="total_products" value="<?= $total_products; ?>">
      <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

      <div class="flex">
         <div class="inputBox">
            <span>Nama Anda :</span>
            <input type="text" name="name" class="box" required placeholder="masukan nama anda" maxlength="20">
         </div>
         <div class="inputBox">
            <span>Nomor telepon :</span>
            <input type="number" name="number" class="box" required placeholder="masukan nomor telepon"  onkeypress="if(this.value.length == 20) return false;">
         </div>
         <div class="inputBox">
            <span>Metode Pembayaran</span>
            <select name="method" class="box">
               <option value="cash on delivery">Cash on delivery</option>
               <option value="credit card">Credit card</option>
               <option value="Qris">Qris</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Alamat :</span>
            <input type="text" name="flat" class="box" required placeholder="cth.  perumahan blok.A" maxlength="50">
         </div>
         <div class="inputBox">
            <span>Patokan (ciri ciri rumah) :</span>
            <input type="text" name="street" class="box" required placeholder="cth.  pagar putih samping masjid" maxlength="50">
         </div>
         <div class="inputBox">
            <span>Kode pos :</span>
            <input type="number" name="pin_code" class="box" required placeholder="cth.  123456" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;">
         </div>
      </div>

      <input type="submit" value="Pesan sekarang" class="btn" name="order">

   </form>

</section>

<!-- order section ends -->

<!-- faq section starts  -->

<section class="faq" id="faq">

   <h1 class="heading">FAQ</h1>

   <div class="accordion-container">

      <div class="accordion active">
         <div class="accordion-heading">
            <span>Apa itu Kedai Kopi?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
         Kedai Kopi adalah kafe yang berdedikasi menyajikan berbagai jenis minuman kopi berkualitas tinggi. Kami memadukan biji kopi pilihan dengan teknik penyeduhan yang tepat untuk menghadirkan cita rasa otentik dalam setiap cangkir. Selain kopi, kami juga menawarkan berbagai minuman lain dan makanan pendamping untuk melengkapi pengalaman Anda. Kami berkomitmen untuk memberikan pelayanan terbaik dan menciptakan tempat yang nyaman bagi para pecinta kopi untuk berkumpul dan menikmati waktu.
         </p>
      </div>

      <div class="accordion">
         <div class="accordion-heading">
            <span>Berapa lama pesanan akan sampai?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
         Kami memahami bahwa kecepatan adalah faktor penting bagi Anda. Setelah Anda melakukan pemesanan, kami akan segera memproses dan mengirimkannya. Waktu pengiriman biasanya berkisar antara 15 hingga 30 menit, tergantung pada jarak lokasi pengiriman dan kondisi lalu lintas. Kami berupaya untuk memastikan pesanan Anda tiba secepat mungkin dalam kondisi yang masih segar dan nikmat.
         </p>
      </div>

      <div class="accordion">
         <div class="accordion-heading">
            <span>Bisa kah pesan dalam jumlah besar?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
         Tentu saja! Kedai Kopi dengan senang hati menerima pesanan dalam jumlah besar, baik untuk acara kantor, pertemuan keluarga, atau acara khusus lainnya. Kami menawarkan berbagai paket dan pilihan untuk memenuhi kebutuhan Anda. Untuk memastikan kami dapat memberikan pelayanan terbaik, silakan hubungi kami terlebih dahulu untuk mendiskusikan detail pesanan besar Anda, termasuk jumlah, jenis minuman, dan waktu pengiriman yang diinginkan. Kami akan bekerja sama dengan Anda untuk memastikan semua kebutuhan terpenuhi dengan sempurna.
         </p>
      </div>

   </div>

</section>

<!-- faq section ends -->

<!-- footer section starts  -->

<section class="footer">

   <div class="box-container">

      <div class="box">
         <i class="fas fa-phone"></i>
         <h3>Kontak Kami</h3>
         <p>+6281294311076</p>
      </div>

      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>Alamat Kami</h3>
         <p>Bekasi, Harapan Indah</p>
      </div>

      <div class="box">
         <i class="fas fa-clock"></i>
         <h3>Jam Operasional</h3>
         <p>09:00am to 10:00pm</p>
      </div>

      <div class="box">
         <i class="fas fa-envelope"></i>
         <h3>Email Kami</h3>
         <p>KedaiKopi@gmail.com</p>
      </div>

   </div>


</section>


<!-- footer section ends -->



















<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>