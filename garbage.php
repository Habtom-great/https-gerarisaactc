<?php

class bmp {

var $mpdf = null;

function bmp(&$mpdf) {
	$this->mpdf = $mpdf;
}


function _getBMPimage($data, $file) {
	$info = array();
		// Adapted from script by Valentin Schmidt
		// http://staff.dasdeck.de/valentin/fpdf/fpdf_bmp/
		$bfOffBits=$this->_fourbytes2int_le(substr($data,10,4));
		$width=$this->_fourbytes2int_le(substr($data,18,4));
		$height=$this->_fourbytes2int_le(substr($data,22,4));
		$flip = ($height<0);
		if ($flip) $height =-$height;
		$biBitCount=$this->_twobytes2int_le(substr($data,28,2));
		$biCompression=$this->_fourbytes2int_le(substr($data,30,4)); 
		$info = array('w'=>$width, 'h'=>$height);
		if ($biBitCount<16){
			$info['cs'] = 'Indexed';
			$info['bpc'] = $biBitCount;
			$palStr = substr($data,54,($bfOffBits-54));
			$pal = '';
			$cnt = strlen($palStr)/4;
			for ($i=0;$i<$cnt;$i++){
				$n = 4*$i;
				$pal .= $palStr[$n+2].$palStr[$n+1].$palStr[$n];
			}
			$info['pal'] = $pal;
		}
		else{
			$info['cs'] = 'DeviceRGB';
			$info['bpc'] = 8;
		}

		if ($this->mpdf->restrictColorSpace==1 || $this->mpdf->PDFX || $this->mpdf->restrictColorSpace==3) {
			if (($this->mpdf->PDFA && !$this->mpdf->PDFAauto) || ($this->mpdf->PDFX && !$this->mpdf->PDFXauto)) { $this->mpdf->PDFAXwarnings[] = "Image cannot be converted to suitable colour space for PDFA or PDFX file - ".$file." - (Image replaced by 'no-image'.)"; }
			return array('error' => "BMP Image cannot be converted to suitable colour space - ".$file." - (Image replaced by 'no-image'.)"); 
		}

		$biXPelsPerMeter=$this->_fourbytes2int_le(substr($data,38,4));	// horizontal pixels per meter, usually set to zero
		//$biYPelsPerMeter=$this->_fourbytes2int_le(substr($data,42,4));	// vertical pixels per meter, usually set to zero
		$biXPelsPerMeter=round($biXPelsPerMeter/1000 *25.4);
		//$biYPelsPerMeter=round($biYPelsPerMeter/1000 *25.4);
		$info['set-dpi'] = $biXPelsPerMeter; 

		switch ($biCompression){
		  case 0:
			$str = substr($data,$bfOffBits);
			break;
		  case 1: # BI_RLE8
			$str = $this->rle8_decode(substr($data,$bfOffBits), $width);
			break;
		  case 2: # BI_RLE4
			$str = $this->rle4_decode(substr($data,$bfOffBits), $width);
			break;
		}
		$bmpdata = '';
		$padCnt = (4-ceil(($width/(8/$biBitCount)))%4)%4;
		switch ($biBitCount){
		  case 1:
		  case 4:
		  case 8:
			$w = floor($width/(8/$biBitCount)) + ($width%(8/$biBitCount)?1:0);
			$w_row = $w + $padCnt;
			if ($flip){
				for ($y=0;$y<$height;$y++){
					$y0 = $y*$w_row;
					for ($x=0;$x<$w;$x++)
						$bmpdata .= $str[$y0+$x];
				}
			}else{
				for ($y=$height-1;$y>=0;$y--){
					$y0 = $y*$w_row;
					for ($x=0;$x<$w;$x++)
						$bmpdata .= $str[$y0+$x];
				}
			}
			break;

		  case 16:
			$w_row = $width*2 + $padCnt;
			if ($flip){
				for ($y=0;$y<$height;$y++){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$n = (ord( $str[$y0 + 2*$x + 1])*256 +    ord( $str[$y0 + 2*$x]));
						$b = ($n & 31)<<3; $g = ($n & 992)>>2; $r = ($n & 31744)>>7128;
						$bmpdata .= chr($r) . chr($g) . chr($b);
					}
				}
			}else{
				for ($y=$height-1;$y>=0;$y--){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$n = (ord( $str[$y0 + 2*$x + 1])*256 +    ord( $str[$y0 + 2*$x]));
						$b = ($n & 31)<<3; $g = ($n & 992)>>2; $r = ($n & 31744)>>7;
						$bmpdata .= chr($r) . chr($g) . chr($b);
					}
				}
			}
			break;

		  case 24:
		  case 32:
			$byteCnt = $biBitCount/8;
			$w_row = $width*$byteCnt + $padCnt;

			if ($flip){
				for ($y=0;$y<$height;$y++){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$i = $y0 + $x*$byteCnt ; # + 1
						$bmpdata .= $str[$i+2].$str[$i+1].$str[$i];
					}
				}
			}else{
				for ($y=$height-1;$y>=0;$y--){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$i = $y0 + $x*$byteCnt ; # + 1
						$bmpdata .= $str[$i+2].$str[$i+1].$str[$i];
					}
				}
			}
			break;

		  default:
			return array('error' => 'Error parsing BMP image - Unsupported image biBitCount'); 
		}
		if ($this->mpdf->compress) {
			$bmpdata=gzcompress($bmpdata);
			$info['f']='FlateDecode';
		} 
		$info['data']=$bmpdata;
		$info['type']='bmp';
		return $info;
}

function _fourbytes2int_le($s) {
	//Read a 4-byte integer from string
	return (ord($s[3])<<24) + (ord($s[2])<<16) + (ord($s[1])<<8) + ord($s[0]);
}

function _twobytes2int_le($s) {
	//Read a 2-byte integer from string
	return (ord(substr($s, 1, 1))<<8) + ord(substr($s, 0, 1));
}


# Decoder for RLE8 compression in windows bitmaps
# see http://msdn.microsoft.com/library/default.asp?url=/library/en-us/gdi/bitmaps_6x0u.asp
function rle8_decode ($str, $width){
    $lineWidth = $width + (3 - ($width-1) % 4);
    $out = '';
    $cnt = strlen($str);
    for ($i=0;$i<$cnt;$i++){
        $o = ord($str[$i]);
        switch ($o){
            case 0: # ESCAPE
                $i++;
                switch (ord($str[$i])){
                    case 0: # NEW LINE
                         $padCnt = $lineWidth - strlen($out)%$lineWidth;
                        if ($padCnt<$lineWidth) $out .= str_repeat(chr(0), $padCnt); # pad line
                        break;
                    case 1: # END OF FILE
                        $padCnt = $lineWidth - strlen($out)%$lineWidth;
                        if ($padCnt<$lineWidth) $out .= str_repeat(chr(0), $padCnt); # pad line
                         break 3;
                    case 2: # DELTA
                        $i += 2;
                        break;
                    default: # ABSOLUTE MODE
                        $num = ord($str[$i]);
                        for ($j=0;$j<$num;$j++)
                            $out .= $str[++$i];
                        if ($num % 2) $i++;
             }
                break;
            default:
                $out .= str_repeat($str[++$i], $o);
        }
    }
    return $out;
}

# Decoder for RLE4 compression in windows bitmaps
# see http://msdn.microsoft.com/library/default.asp?url=/library/en-us/gdi/bitmaps_6x0u.asp
function rle4_decode ($str, $width){
    $w = floor($width/2) + ($width % 2);
    $lineWidth = $w + (3 - ( ($width-1) / 2) % 4);    
    $pixels = array();
    $cnt = strlen($str);
    for ($i=0;$i<$cnt;$i++){
        $o = ord($str[$i]);
        switch ($o){
            case 0: # ESCAPE
                $i++;
                switch (ord($str[$i])){
                    case 0: # NEW LINE                        
                        while (count($pixels)%$lineWidth!=0)
                            $pixels[]=0;
                        break;
                    case 1: # END OF FILE
                        while (count($pixels)%$lineWidth!=0)
                            $pixels[]=0;
                        break 3;
                    case 2: # DELTA
                        $i += 2;
                        break;
                    default: # ABSOLUTE MODE
                        $num = ord($str[$i]);
                        for ($j=0;$j<$num;$j++){
                            if ($j%2==0){
                                $c = ord($str[++$i]);
                              $pixels[] = ($c & 240)>>4;
                             } else
                              $pixels[] = $c & 15;
                        }
                        if ($num % 2) $i++;
             }
                break;
            default:
                $c = ord($str[++$i]);
                for ($j=0;$j<$o;$j++)
                    $pixels[] = ($j%2==0 ? ($c & 240)>>4 : $c & 15);
        }
    }
    
    $out = '';
    if (count($pixels)%2) $pixels[]=0;
    $cnt = count($pixels)/2;
    for ($i=0;$i<$cnt;$i++)
        $out .= chr(16*$pixels[2*$i] + $pixels[2*$i+1]);
    return $out;
} 



}

?>
kkkk
home 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac Training Center</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Fixed Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Content Margin after fixed navbar */
        body {
            padding-top: 90px;
            background-color: #f4f4f4;
        }

        /* Top section for Clock, Weather */
        .top-info-container {
            background-color: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-info-container .info-section {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .top-info-container .info-section h5 {
            margin-bottom: 0;
            margin-right: 10px;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 160px;
            left: 0;
            width: 200px;
            height: 100%;
            background-color: #343a40;
            padding: 20px;
            border-right: 3px solid #007bff;
            color: white;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #495057;
            text-align: center;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        /* Content Section */
        .section {
            margin-left: 230px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Analog Clock Styling */
        .analog-clock {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 10px auto;
            border: 5px solid #333;
            border-radius: 50%;
            background: white;
        }

        .hand {
            position: absolute;
            background: #333;
            border-radius: 10px;
            transform-origin: bottom;
            bottom: 50%;
            left: 50%;
            transform: translateX(-50%);
        }

        .hour-hand {
            width: 6px;
            height: 30px;
            background: black;
        }

        .minute-hand {
            width: 4px;
            height: 40px;
            background: gray;
        }

        .second-hand {
            width: 2px;
            height: 45px;
            background: red;
        }

        .center {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #333;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .number {
            position: absolute;
            font-weight: bold;
            font-size: 12px;
            color: black;
            transform: translate(-50%, -50%);
        }

        .weather-info {
            text-align: center;
            margin-top: 20px;
            color: #007bff;
        }

        .carousel-item img {
            max-height: 500px; /* Ensures clear display of images */
            object-fit: cover;
        }
    </style>
</head>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <img src="assets/logos/1.png-2.png" alt="GITC Logo" style="max-height: 40px;"> Gerar Isaac Training Center (GITC)
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" data-toggle="dropdown">Language</a>
                    <div class="dropdown-menu" aria-labelledby="languageDropdown">
                        <a class="dropdown-item" href="?lang=en">English</a>
                        <a class="dropdown-item" href="?lang=am">Amharic</a>
                        <a class="dropdown-item" href="?lang=ti">Tigrinya</a>
                    </div>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#profile">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Navigation</h4>
    <a href="#home">Home</a>
    <a href="#About Us">About Us</a>
    <a href="#courses">Courses</a>
    <a href="#testimonials">Testimonials</a>
    <a href="#contact">Contact Us</a>
</div>
<body>
<!-- Top Info Section -->
<div class="top-info-container">
    <!-- Washington DC Time -->
    <div class="info-section">
        <h5><span class="clock-icon"></span> Washington DC Time:</h5>
        <span id="dcClock">--:--:-- AM</span>
        <div class="analog-clock">
            <div class="hand hour-hand" id="hourHandDC"></div>
            <div class="hand minute-hand" id="minuteHandDC"></div>
            <div class="hand second-hand" id="secondHandDC"></div>
            <div class="center"></div>
            <div class="number" style="top: 5%; left: 50%;">12</div>
            <div class="number" style="top: 50%; left: 95%;">3</div>
            <div class="number" style="top: 95%; left: 50%;">6</div>
            <div class="number" style="top: 50%; left: 5%;">9</div>
        </div>
    </div>

    <!-- Canada Time -->
    <div class="info-section">
        <h5><span class="clock-icon"></span> Canada Time:</h5>
        <span id="canadaClock">--:--:-- AM</span>
        <div class="analog-clock">
            <div class="hand hour-hand" id="hourHandCA"></div>
            <div class="hand minute-hand" id="minuteHandCA"></div>
            <div class="hand second-hand" id="secondHandCA"></div>
            <div class="center"></div>
            <div class="number" style="top: 5%; left: 50%;">12</div>
            <div class="number" style="top: 50%; left: 95%;">3</div>
            <div class="number" style="top: 95%; left: 50%;">6</div>
            <div class="number" style="top: 50%; left: 5%;">9</div>
        </div>
    </div>

    <!-- Ethiopia Time -->
    <div class="info-section">
        <h5><span class="clock-icon"></span> Ethiopia Time:</h5>
        <span id="ethiopiaClock">--:--:-- AM</span>
        <div class="analog-clock">
            <div class="hand hour-hand" id="hourHandET"></div>
            <div class="hand minute-hand" id="minuteHandET"></div>
            <div class="hand second-hand" id="secondHandET"></div>
            <div class="center"></div>
            <div class="number" style="top: 5%; left: 50%;">12</div>
            <div class="number" style="top: 50%; left: 95%;">3</div>
            <div class="number" style="top: 95%; left: 50%;">6</div>
            <div class="number" style="top: 50%; left: 5%;">9</div>
        </div>
    </div>
</div>

<!-- Weather Information -->
<div class="weather-info">
    <h5>Weather Information:</h5>
    <div id="weatherDC">Washington DC: Loading...</div>
    <div id="weatherCA">Canada: Loading...</div>
    <div id="weatherET">Ethiopia: Loading...</div>
</div>

<!-- Main Content Section -->
<div class="section">
    <div id="home" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images_courses/first round-final exam.jpg" class="d-block w-100" alt="First slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images_courses/2nd round graduating  students peach tree exam ..jpg" class="d-block w-100" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images_courses/3rd round   students training session .jpg" class="d-block w-100" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <h2>Welcome to Gerar Isaac Training Center</h2>
    <p>Explore our diverse courses and training sessions tailored to equip you with essential skills.</p>
</div>

<script>
    // Clock Functionality
    function updateClocks() {
        const nowDC = new Date().toLocaleString("en-US", {timeZone: "America/New_York"});
        const nowCA = new Date().toLocaleString("en-US", {timeZone: "America/Toronto"});
        const nowET = new Date().toLocaleString("en-US", {timeZone: "Africa/Addis_Ababa"});

        // Update digital clocks
        document.getElementById("dcClock").innerText = nowDC;
        document.getElementById("canadaClock").innerText = nowCA;
        document.getElementById("ethiopiaClock").innerText = nowET;

        // Update analog clocks
        const [hoursDC, minutesDC, secondsDC] = nowDC.split(/:| /).map(Number);
        const [hoursCA, minutesCA, secondsCA] = nowCA.split(/:| /).map(Number);
        const [hoursET, minutesET, secondsET] = nowET.split(/:| /).map(Number);

        document.getElementById("hourHandDC").style.transform = `translateX(-50%) rotate(${(hoursDC % 12) * 30 + minutesDC * 0.5}deg)`;
        document.getElementById("minuteHandDC").style.transform = `translateX(-50%) rotate(${minutesDC * 6}deg)`;
        document.getElementById("secondHandDC").style.transform = `translateX(-50%) rotate(${secondsDC * 6}deg)`;

        document.getElementById("hourHandCA").style.transform = `translateX(-50%) rotate(${(hoursCA % 12) * 30 + minutesCA * 0.5}deg)`;
        document.getElementById("minuteHandCA").style.transform = `translateX(-50%) rotate(${minutesCA * 6}deg)`;
        document.getElementById("secondHandCA").style.transform = `translateX(-50%) rotate(${secondsCA * 6}deg)`;

        document.getElementById("hourHandET").style.transform = `translateX(-50%) rotate(${(hoursET % 12) * 30 + minutesET * 0.5}deg)`;
        document.getElementById("minuteHandET").style.transform = `translateX(-50%) rotate(${minutesET * 6}deg)`;
        document.getElementById("secondHandET").style.transform = `translateX(-50%) rotate(${secondsET * 6}deg)`;
    }

    // Weather Fetching
    async function fetchWeather() {
        const apiKey = 'YOUR_API_KEY'; // Replace with your actual API key
        const locations = {
            dc: "Washington,DC",
            ca: "Toronto,CA",
            et: "Addis Ababa,ET"
        };

        for (const [key, location] of Object.entries(locations)) {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${location}&appid=${apiKey}&units=metric`);
            const data = await response.json();
            document.getElementById(`weather${key.toUpperCase()}`).innerText = `${data.name}: ${data.weather[0].description}, ${data.main.temp}°C`;
        }
    }

    // Initialize Clocks and Weather
    setInterval(updateClocks, 1000);
    fetchWeather();
</script>

<!-- About Us Section -->
<section id="About Us" class="section">
    <div class="container">
        <h2>About Us</h2>
        <p>At Gerar Isaac Training Center (GITC), we are committed to providing high-quality<br>
         education in various fields, helping students achieve their academic and professional goals.
        
We are in class/Online Courses, your one-stop solution for learning. Our platform offers a wide range of comprehensive courses designed to help you master the principles and practices of accounting.

we understand the importance of quality education, which is why our courses are taught by experienced professionals in the field. Whether you are a beginner or an experienced accountant looking to enhance your skills, we have courses tailored to meet your needs.

Our mission is to make accounting,basic electrical engineering and web development education accessible to everyone, regardless of their location or background. With our user-friendly platform and flexible learning options, you can learn at your own pace and from anywhere in the world.

Join us today and take the first step towards advancing your career!
        </p>
    </div>
</section>


<style>
    /* Container and Section Styling */
    #courses {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    /* Card Styling */
    .card {
        transition: transform 0.3s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
    }

    /* Hover Effects */
    .card:hover {
        transform: scale(1.05);
    }

    /* Logo Styling */
    .logo-container {
        position: relative;
        display: flex;
        justify-content: center;
    }

    .logo-container img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Add Animation */
    .card {
    animation: rolling 4s forwards; /* Use 'forwards' to maintain the final state after animation */
}

@keyframes rolling {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}


    /* Button Styling */
    .btn {
        font-weight: bold;
        letter-spacing: 1px;
        padding: 8px 20px;
    }

    /* Title and Text Styling */
    .card-title {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .card-text {
        font-size: 0.9rem;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        .card {
            margin-bottom: 30px;
        }
    }
</style>

<!-- Courses Section -->
<section id="courses" class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <h2 class="text-center mb-5" style="font-size: 2rem; color: #007bff; font-weight: bold;">Our Course Offers</h2>
        <div class="row justify-content-center">

            <!-- Accounting Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Accounting Image.png" class="rounded-circle" alt="Accounting">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-primary">Accounting</h5>
                        <p class="card-text">Explore a range of accounting courses including:</p>
                        <ul class="list-unstyled">
                            <li>Principles of Accounting</li>
                            <li>Cost Accounting</li>
                            <li>Financial Accounting</li>
                        </ul>
                        <a href="accounting_courses.php" class="btn btn-primary btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Accounting Software -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Peachtree-logo.jpg" class="rounded-circle" alt="Accounting Software">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-success">Accounting Software</h5>
                        <p class="card-text">Get hands-on experience with:</p>
                        <ul class="list-unstyled">
                            <li>Peach Tree</li>
                            <li>Tally</li>
                            <li>QuickBooks</li>
                        </ul>
                        <a href="accounting_software.php" class="btn btn-success btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Electrical Engineering -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/electrical-engineering-logo.avif" class="rounded-circle" alt="Electrical Engineering">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-warning">Electrical Engineering</h5>
                        <p class="card-text">Courses include:</p>
                        <ul class="list-unstyled">
                            <li>Basic Electricity</li>
                            <li>Basic Electronics</li>
                        </ul>
                        <a href="electrical_engineering.php" class="btn btn-warning btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Programming Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/html,css,js.webp" class="rounded-circle" alt="Programming">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-info">Programming</h5>
                        <p class="card-text">Learn the essentials of:</p>
                        <ul class="list-unstyled">
                            <li>Website Development (HTML, CSS, JS)</li>
                            <li>PHP Tutorial</li>
                            <li>Database Management</li>
                        </ul>
                        <a href="programming_courses.php" class="btn btn-info btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- Include Bootstrap CSS and any custom CSS file -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<!-- Testimonials Section -->
<div id="testimonials" class="section text-center bordered">
   
    <div class="container">
      
    <h2>What Our Graduates Say</h2>
   <!-- Testimonials Section -->

        
        <!-- First Round Accounting Graduates -->
          <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">First Round Accounting Graduates</h3>
        <div class="row">
        
            <!-- Testimonial 1 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Esaias Amanuel Zeregabir Acc 1st R-3.jpeg" class="rounded-circle mb-3" alt="Eseas Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Eseas Amanuel</h5>
                    <p class="text-muted">"The course deepened my understanding of accounting principles. I’m confident in my skills now!"</p>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Feven Gebremichael" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Feven Gebremichael</h5>
                    <p class="text-muted">"The accounting course is very detailed and easy to understand. Highly recommended!"</p>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Fireselam  bokre Gebresilasia Acc 1st R-2.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Fireselam Bokre</h5>
                    <p class="text-muted">"Clear, concise, and practical. This course gave me the confidence to pursue my accounting career."</p>
                </div>
            </div>

            <!-- Testimonial 4 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Lidia weldu Hayelom Acc 1st R-4.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Lidia weldu</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                </div>
            </div>
        </div>

        <!-- Second Type Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Second Round Accounting Graduates</h3>
        <div class="row">
            <!-- Testimonial 5 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Betelihem.jpg " class="rounded-circle mb-3" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                </div>
            </div>

            <!-- Testimonial 6 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Tsegay.jpg" class="rounded-circle mb-3" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                </div>
            </div>

            <!-- Testimonial 7 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg" class="rounded-circle mb-3" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #ff5733;">Helen Tesfaye</h5>
                    <p class="text-muted">"A fantastic course that greatly improved my accounting knowledge."</p>
                </div>
            </div>

            <!-- Testimonial 8 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg " class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
        </div>
    </div>

 
            <!-- Testimonial 8 -->
            <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Third Round Accounting Graduates</h3>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2aac503c7e.jpg" class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <!-- Testimonial 9 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2beac03e52.jpg" class="rounded-circle mb-3" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Tesfay</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <!-- Testimonial 10 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
 
  
      
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Betelihem</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                  
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-danger" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                  
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-purple" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                  
                </div>
            </div>
        </div>


        <!-- Fourth Round Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Fourth Round Accounting Graduates</h3>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .testimonial-box:hover {
        background-color: #f0f0f0;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(-10px);
    }
</style>

    

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 text-center bg-light">
        <div class="container">
            <h2 class="mb-5">Testimonials</h2>
            <div class="row">
             
                <div class="main-content container mt-5">
    <!-- Testimonials Section -->
    <section id="testimonials">
        <h2 class="text-center mb-5" style="color: #333;">2nd Round Accounting Graduates</h2>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-success" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Betelihem</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-danger" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-purple" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
        </div>

        <!-- Fifth Round Accounting Graduates -->
        <h2 class="text-center mb-5" style="color: #333;">Fifth Round Accounting Graduates</h2>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
        </div>
    </section>
    
                <!-- More Testimonials... -->
            </div>
        </div>
    </section>
    <?php


// Initialize variables
$success_message = $error_message = '';
$name = $telephone = $email = $message = ''; // Initialize all variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and trim input
    $name = trim($_POST['name']);
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Form validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (empty($name) || empty($telephone) || empty($message)) {
        $error_message = "Name, telephone, and message fields are required.";
    } else {
        // Database connection using PDO
        $servername = 'localhost';
        $username = 'root'; // Replace with your MySQL username
        $password = ""; // Replace with your MySQL password
        $dbname = 'accounting_course'; // Replace with your database name

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Set the PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare the SQL statement to insert the data
            $stmt = $pdo->prepare("INSERT INTO contact (name, telephone, email, message) VALUES (:name, :telephone, :email, :message)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Your message has been sent successfully!";
                // Clear the form fields after successful submission
                $name = $telephone = $email = $message = '';
            } else {
                $error_message = "There was an error sending your message. Please try again.";
            }

        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>


    <style>
        body {
            background-color: #f8f9fa;
            /* Add padding at the bottom to prevent footer overlap */
            padding-bottom: 70px; /* Adjust this value if needed */
        }
        .contact-section {
            padding: 50px 0;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .form-section, .address-section {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
            flex: 1 1 45%;
        }
        .address-section h4 {
            color: #007bff;
        }
        .map-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            margin-top: 20px;
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 100;
        }
        @media (max-width: 768px) {
            .contact-container {
                flex-direction: column;
            }
            .form-section, .address-section {
                flex: 1 1 100%;
                margin-right: 0;
            }
        }
        
  
        body {
            background-color: #f8f9fa;
            padding-bottom: 150px; /* Increased padding to avoid footer overlap */
        }

        .contact-section {
            padding: 50px 0;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .form-section, .address-section {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
        }

        .form-section {
            margin-right: 30px;
        }

        .address-section h4 {
            color: #007bff;
        }

        .map-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Adjust padding and gap between sections */
        @media (max-width: 768px) {
            .form-section {
                margin-right: 0;
                margin-bottom: 30px;
            }
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: relative;
            width: 100%;
            bottom: 0;
        }
    </style>

   
</head>
<body>

<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container">
        <h2 class="contact-header">Contact Us</h2>
        <div class="contact-container">
            <!-- Contact Form -->
            <div class="form-section">
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="form-group">
                        <label for="telephone">Telephone<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" id="telephone" name="telephone" required value="<?php echo htmlspecialchars($telephone); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email<span style="color:red;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message<span style="color:red;">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary btn-block">Send Message</button>
                </form>
            </div>

            <!-- Address and Map Section -->
            <div class="address-section">
                <h4>Our Address</h4>
                <p><strong>Gerar Isaac Training Center</strong></p>
                <p>Meskel Flower, Bahgmer Building</p>
                <p>Addis Ababa, Ethiopia</p>
                <p><strong>Phone:</strong> +251 911 123456</p>
                <p><strong>Email:</strong> info@gerarisaac.com</p>

                <div class="map-container">
                    <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3942.221978156433!2d38.76136887493415!3d9.005601393496469!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b857f7dbe2cf3%3A0xdfb4b7d08319f7c6!2sMeskel%20Flower!5e0!3m2!1sen!2set!4v1694106543321!5m2!1sen!2set"
                        width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                        
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



<?php
// Include footer
include('footer.php');
?>
</body>
</html>
kkkkkkkkkkkkkkk


<?php
// Include footer
include('footer.php');
?>
</body>
</html>
lllll
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac Training Center</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Fixed Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Content Margin after fixed navbar */
        body {
            padding-top: 90px;
            background-color: #f4f4f4;
        }

        /* Top section for Clock, Weather */
        .top-info-container {
            background-color: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-info-container .info-section {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .top-info-container .info-section h5 {
            margin-bottom: 0;
            margin-right: 10px;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 160px;
            left: 0;
            width: 200px;
            height: 100%;
            background-color: #343a40;
            padding: 20px;
            border-right: 3px solid #007bff;
            color: white;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #495057;
            text-align: center;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        /* Content Section */
        .section {
            margin-left: 230px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }


        .center {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #333;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .number {
            position: absolute;
            font-weight: bold;
            font-size: 12px;
            color: black;
            transform: translate(-50%, -50%);
        }

        .weather-info {
            text-align: center;
            margin-top: 20px;
            color: #007bff;
        }

        .carousel-item img {
            max-height: 500px; /* Ensures clear display of images */
            object-fit: cover;
        }
    </style>
</head>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <img src="assets/logos/1.png-2.png" alt="GITC Logo" style="max-height: 40px;"> Gerar Isaac Training Center (GITC)
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" data-toggle="dropdown">Language</a>
                    <div class="dropdown-menu" aria-labelledby="languageDropdown">
                        <a class="dropdown-item" href="?lang=en">English</a>
                        <a class="dropdown-item" href="?lang=am">Amharic</a>
                        <a class="dropdown-item" href="?lang=ti">Tigrinya</a>
                    </div>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#profile">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Navigation</h4>
    <a href="#home">Home</a>
    <a href="#About Us">About Us</a>
    <a href="#courses">Courses</a>
    <a href="#testimonials">Testimonials</a>
    <a href="#contact">Contact Us</a>
</div>
<body>
<!-- Top Info Section -->
<div class="top-info-container">
    <!-- Washington DC Time -->
    <div class="info-section">
        <h5><span class="clock-icon"></span> Washington DC Time:</h5>
        <span id="dcClock">--:--:-- AM</span>
        <div class="analog-clock">
            <div class="hand hour-hand" id="hourHandDC"></div>
            <div class="hand minute-hand" id="minuteHandDC"></div>
            <div class="hand second-hand" id="secondHandDC"></div>
            <div class="center"></div>
            <div class="number" style="top: 5%; left: 50%;">12</div>
            <div class="number" style="top: 50%; left: 95%;">3</div>
            <div class="number" style="top: 95%; left: 50%;">6</div>
            <div class="number" style="top: 50%; left: 5%;">9</div>
        </div>
    </div>

    <!-- Canada Time -->
    <div class="info-section">
        <h5><span class="clock-icon"></span> Canada Time:</h5>
        <span id="canadaClock">--:--:-- AM</span>
        <div class="analog-clock">
            <div class="hand hour-hand" id="hourHandCA"></div>
            <div class="hand minute-hand" id="minuteHandCA"></div>
            <div class="hand second-hand" id="secondHandCA"></div>
            <div class="center"></div>
            <div class="number" style="top: 5%; left: 50%;">12</div>
            <div class="number" style="top: 50%; left: 95%;">3</div>
            <div class="number" style="top: 95%; left: 50%;">6</div>
            <div class="number" style="top: 50%; left: 5%;">9</div>
        </div>
    </div>

    <!-- Ethiopia Time -->
    <div class="info-section">
        <h5><span class="clock-icon"></span> Ethiopia Time:</h5>
        <span id="ethiopiaClock">--:--:-- AM</span>
        <div class="analog-clock">
            <div class="hand hour-hand" id="hourHandET"></div>
            <div class="hand minute-hand" id="minuteHandET"></div>
            <div class="hand second-hand" id="secondHandET"></div>
            <div class="center"></div>
            <div class="number" style="top: 5%; left: 50%;">12</div>
            <div class="number" style="top: 50%; left: 95%;">3</div>
            <div class="number" style="top: 95%; left: 50%;">6</div>
            <div class="number" style="top: 50%; left: 5%;">9</div>
        </div>
    </div>
</div>

<!-- Weather Information -->
<div class="weather-info">
    <h5>Weather Information:</h5>
    <div id="weatherDC">Washington DC: Loading...</div>
    <div id="weatherCA">Canada: Loading...</div>
    <div id="weatherET">Ethiopia: Loading...</div>
</div>

<!-- Main Content Section -->
<div class="section">
    <div id="home" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images_courses/first round-final exam.jpg" class="d-block w-100" alt="First slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images_courses/2nd round graduating  students peach tree exam ..jpg" class="d-block w-100" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images_courses/3rd round   students training session .jpg" class="d-block w-100" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <h2>Welcome to Gerar Isaac Training Center</h2>
    <p>Explore our diverse courses and training sessions tailored to equip you with essential skills.</p>
</div>

<script>
    // Clock Functionality
    function updateClocks() {
        const nowDC = new Date().toLocaleString("en-US", {timeZone: "America/New_York"});
        const nowCA = new Date().toLocaleString("en-US", {timeZone: "America/Toronto"});
        const nowET = new Date().toLocaleString("en-US", {timeZone: "Africa/Addis_Ababa"});

        // Update digital clocks
        document.getElementById("dcClock").innerText = nowDC;
        document.getElementById("canadaClock").innerText = nowCA;
        document.getElementById("ethiopiaClock").innerText = nowET;

        // Update analog clocks
        const [hoursDC, minutesDC, secondsDC] = nowDC.split(/:| /).map(Number);
        const [hoursCA, minutesCA, secondsCA] = nowCA.split(/:| /).map(Number);
        const [hoursET, minutesET, secondsET] = nowET.split(/:| /).map(Number);

        document.getElementById("hourHandDC").style.transform = `translateX(-50%) rotate(${(hoursDC % 12) * 30 + minutesDC * 0.5}deg)`;
        document.getElementById("minuteHandDC").style.transform = `translateX(-50%) rotate(${minutesDC * 6}deg)`;
        document.getElementById("secondHandDC").style.transform = `translateX(-50%) rotate(${secondsDC * 6}deg)`;

        document.getElementById("hourHandCA").style.transform = `translateX(-50%) rotate(${(hoursCA % 12) * 30 + minutesCA * 0.5}deg)`;
        document.getElementById("minuteHandCA").style.transform = `translateX(-50%) rotate(${minutesCA * 6}deg)`;
        document.getElementById("secondHandCA").style.transform = `translateX(-50%) rotate(${secondsCA * 6}deg)`;

        document.getElementById("hourHandET").style.transform = `translateX(-50%) rotate(${(hoursET % 12) * 30 + minutesET * 0.5}deg)`;
        document.getElementById("minuteHandET").style.transform = `translateX(-50%) rotate(${minutesET * 6}deg)`;
        document.getElementById("secondHandET").style.transform = `translateX(-50%) rotate(${secondsET * 6}deg)`;
    }

    // Weather Fetching
    async function fetchWeather() {
        const apiKey = 'YOUR_API_KEY'; // Replace with your actual API key
        const locations = {
            dc: "Washington,DC",
            ca: "Toronto,CA",
            et: "Addis Ababa,ET"
        };

        for (const [key, location] of Object.entries(locations)) {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${location}&appid=${apiKey}&units=metric`);
            const data = await response.json();
            document.getElementById(`weather${key.toUpperCase()}`).innerText = `${data.name}: ${data.weather[0].description}, ${data.main.temp}°C`;
        }
    }

    // Initialize Clocks and Weather
    setInterval(updateClocks, 1000);
    fetchWeather();
</script>

<!-- About Us Section -->
section id="About Us" class="section">
    <div class="container">
        <h2>About Us</h2>
        <p>At Gerar Isaac Training Center (GITC), we are committed to providing high-quality<br>
         education in various fields, helping students achieve their academic and professional goals.
        
We are in class/Online Courses, your one-stop solution for learning. Our platform offers a wide range of comprehensive courses designed to help you master the principles and practices of accounting.

we understand the importance of quality education, which is why our courses are taught by experienced professionals in the field. Whether you are a beginner or an experienced accountant looking to enhance your skills, we have courses tailored to meet your needs.

Our mission is to make accounting,basic electrical engineering and web development education accessible to everyone, regardless of their location or background. With our user-friendly platform and flexible learning options, you can learn at your own pace and from anywhere in the world.

Join us today and take the first step towards advancing your career!
        </p>
    </div>
</section>


<style>
    /* Container and Section Styling */
    #courses {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    /* Card Styling */
    .card {
        transition: transform 0.3s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
    }

    /* Hover Effects */
    .card:hover {
        transform: scale(1.05);
    }

    /* Logo Styling */
    .logo-container {
        position: relative;
        display: flex;
        justify-content: center;
    }

    .logo-container img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Add Animation */
    .card {
    animation: rolling 4s forwards; /* Use 'forwards' to maintain the final state after animation */
}

@keyframes rolling {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}


    /* Button Styling */
    .btn {
        font-weight: bold;
        letter-spacing: 1px;
        padding: 8px 20px;
    }

    /* Title and Text Styling */
    .card-title {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .card-text {
        font-size: 0.9rem;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        .card {
            margin-bottom: 30px;
        }
    }
</style>

<!-- Courses Section -->
<section id="courses" class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <h2 class="text-center mb-5" style="font-size: 2rem; color: #007bff; font-weight: bold;">Our Course Offers</h2>
        <div class="row justify-content-center">

            <!-- Accounting Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Accounting Image.png" class="rounded-circle" alt="Accounting">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-primary">Accounting</h5>
                        <p class="card-text">Explore a range of accounting courses including:</p>
                        <ul class="list-unstyled">
                            <li>Principles of Accounting</li>
                            <li>Cost Accounting</li>
                            <li>Financial Accounting</li>
                        </ul>
                        <a href="accounting_courses.php" class="btn btn-primary btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Accounting Software -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Peachtree-logo.jpg" class="rounded-circle" alt="Accounting Software">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-success">Accounting Software</h5>
                        <p class="card-text">Get hands-on experience with:</p>
                        <ul class="list-unstyled">
                            <li>Peach Tree</li>
                            <li>Tally</li>
                            <li>QuickBooks</li>
                        </ul>
                        <a href="accounting_software.php" class="btn btn-success btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Electrical Engineering -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/electrical-engineering-logo.avif" class="rounded-circle" alt="Electrical Engineering">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-warning">Electrical Engineering</h5>
                        <p class="card-text">Courses include:</p>
                        <ul class="list-unstyled">
                            <li>Basic Electricity</li>
                            <li>Basic Electronics</li>
                        </ul>
                        <a href="electrical_engineering.php" class="btn btn-warning btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Programming Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/html,css,js.webp" class="rounded-circle" alt="Programming">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-info">Programming</h5>
                        <p class="card-text">Learn the essentials of:</p>
                        <ul class="list-unstyled">
                            <li>Website Development (HTML, CSS, JS)</li>
                            <li>PHP Tutorial</li>
                            <li>Database Management</li>
                        </ul>
                        <a href="programming_courses.php" class="btn btn-info btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- Include Bootstrap CSS and any custom CSS file -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<!-- Testimonials Section -->
<div id="testimonials" class="section text-center bordered">
   
    <div class="container">
      
    <h2>What Our Graduates Say</h2>
   <!-- Testimonials Section -->

        
        <!-- First Round Accounting Graduates -->
          <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">First Round Accounting Graduates</h3>
        <div class="row">
        
            <!-- Testimonial 1 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Esaias Amanuel Zeregabir Acc 1st R-3.jpeg" class="rounded-circle mb-3" alt="Eseas Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Eseas Amanuel</h5>
                    <p class="text-muted">"The course deepened my understanding of accounting principles. I’m confident in my skills now!"</p>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Feven Gebremichael" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Feven Gebremichael</h5>
                    <p class="text-muted">"The accounting course is very detailed and easy to understand. Highly recommended!"</p>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Fireselam  bokre Gebresilasia Acc 1st R-2.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Fireselam Bokre</h5>
                    <p class="text-muted">"Clear, concise, and practical. This course gave me the confidence to pursue my accounting career."</p>
                </div>
            </div>

            <!-- Testimonial 4 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Lidia weldu Hayelom Acc 1st R-4.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Lidia weldu</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                </div>
            </div>
        </div>

        <!-- Second Type Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Second Round Accounting Graduates</h3>
        <div class="row">
            <!-- Testimonial 5 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Betelihem.jpg " class="rounded-circle mb-3" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                </div>
            </div>

            <!-- Testimonial 6 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Tsegay.jpg" class="rounded-circle mb-3" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                </div>
            </div>

            <!-- Testimonial 7 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg" class="rounded-circle mb-3" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #ff5733;">Helen Tesfaye</h5>
                    <p class="text-muted">"A fantastic course that greatly improved my accounting knowledge."</p>
                </div>
            </div>

            <!-- Testimonial 8 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg " class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
        </div>
    </div>

 
            <!-- Testimonial 8 -->
            <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Third Round Accounting Graduates</h3>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2aac503c7e.jpg" class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <!-- Testimonial 9 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2beac03e52.jpg" class="rounded-circle mb-3" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Tesfay</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <!-- Testimonial 10 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
 
  
      
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Betelihem</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                  
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-danger" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                  
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-purple" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                  
                </div>
            </div>
        </div>


        <!-- Fourth Round Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Fourth Round Accounting Graduates</h3>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .testimonial-box:hover {
        background-color: #f0f0f0;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(-10px);
    }
</style>

    

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 text-center bg-light">
        <div class="container">
            <h2 class="mb-5">Testimonials</h2>
            <div class="row">
             
                <div class="main-content container mt-5">
    <!-- Testimonials Section -->
    <section id="testimonials">
        <h2 class="text-center mb-5" style="color: #333;">2nd Round Accounting Graduates</h2>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-success" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Betelihem</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-danger" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-purple" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
        </div>

        <!-- Fifth Round Accounting Graduates -->
        <h2 class="text-center mb-5" style="color: #333;">Fifth Round Accounting Graduates</h2>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
        </div>
    </section>
    
                <!-- More Testimonials... -->
            </div>
        </div>
    </section>
    <?php


// Initialize variables
$success_message = $error_message = '';
$name = $telephone = $email = $message = ''; // Initialize all variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and trim input
    $name = trim($_POST['name']);
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Form validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (empty($name) || empty($telephone) || empty($message)) {
        $error_message = "Name, telephone, and message fields are required.";
    } else {
        // Database connection using PDO
        $servername = 'localhost';
        $username = 'root'; // Replace with your MySQL username
        $password = ""; // Replace with your MySQL password
        $dbname = 'accounting_course'; // Replace with your database name

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Set the PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare the SQL statement to insert the data
            $stmt = $pdo->prepare("INSERT INTO contact (name, telephone, email, message) VALUES (:name, :telephone, :email, :message)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Your message has been sent successfully!";
                // Clear the form fields after successful submission
                $name = $telephone = $email = $message = '';
            } else {
                $error_message = "There was an error sending your message. Please try again.";
            }

        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>


    <style>
        body {
            background-color: #f8f9fa;
            /* Add padding at the bottom to prevent footer overlap */
            padding-bottom: 70px; /* Adjust this value if needed */
        }
        .contact-section {
            padding: 50px 0;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .form-section, .address-section {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
            flex: 1 1 45%;
        }
        .address-section h4 {
            color: #007bff;
        }
        .map-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            margin-top: 20px;
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 100;
        }
        @media (max-width: 768px) {
            .contact-container {
                flex-direction: column;
            }
            .form-section, .address-section {
                flex: 1 1 100%;
                margin-right: 0;
            }
        }
        
  
        body {
            background-color: #f8f9fa;
            padding-bottom: 150px; /* Increased padding to avoid footer overlap */
        }

        .contact-section {
            padding: 50px 0;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .form-section, .address-section {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
        }

        .form-section {
            margin-right: 30px;
        }

        .address-section h4 {
            color: #007bff;
        }

        .map-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Adjust padding and gap between sections */
        @media (max-width: 768px) {
            .form-section {
                margin-right: 0;
                margin-bottom: 30px;
            }
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: relative;
            width: 100%;
            bottom: 0;
        }
    </style>

   
</head>
<body>

<!-- Contact Section -->
section id="contact" class="contact-section">
    <div class="container">
        <h2 class="contact-header">Contact Us</h2>
        <div class="contact-container">
            <!-- Contact Form -->
            <div class="form-section">
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="form-group">
                        <label for="telephone">Telephone<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" id="telephone" name="telephone" required value="<?php echo htmlspecialchars($telephone); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email<span style="color:red;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message<span style="color:red;">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary btn-block">Send Message</button>
                </form>
            </div>

            <!-- Address and Map Section -->
            <div class="address-section">
                <h4>Our Address</h4>
                <p><strong>Gerar Isaac Training Center</strong></p>
                <p>Meskel Flower, Bahgmer Building</p>
                <p>Addis Ababa, Ethiopia</p>
                <p><strong>Phone:</strong> +251 911 123456</p>
                <p><strong>Email:</strong> info@gerarisaac.com</p>

                <div class="map-container">
                    <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3942.221978156433!2d38.76136887493415!3d9.005601393496469!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b857f7dbe2cf3%3A0xdfb4b7d08319f7c6!2sMeskel%20Flower!5e0!3m2!1sen!2set!4v1694106543321!5m2!1sen!2set"
                        width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                        
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



<?php
// Include footer
include('footer.php');
?>
</body>
</html>

kkkkkkkkkkk
home 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac Training Center</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Fixed Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Content Margin after fixed navbar */
        body {
            padding-top: 120px;
            background-color: #f4f4f4;
        }

        /* Top section for Clock, Weather, Currency */
        .top-info-container {
            background-color: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-info-container .info-section {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .top-info-container .info-section h5 {
            margin-bottom: 0;
            margin-right: 10px;
        }

        .top-info-container .info-section img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 140px; /* Adjusted to fit the new top section */
            left: 0;
            width: 200px;
            height: 100%;
            background-color: #343a40;
            padding: 20px;
            border-right: 3px solid #007bff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        /* Sidebar links */
        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #495057;
            text-align: center;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        /* Content Section */
        .section {
            margin-left: 230px;
            padding: 30px;
            margin-bottom: 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
                margin-bottom: 20px;
            }

            .section {
                margin-left: 0;
            }

            .navbar {
                position: relative;
            }

            .top-info-container {
                flex-direction: column;
            }

            .top-info-container .info-section {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Top Info Container -->
<div class="top-info-container">
    <div class="info-section">
        <h5>Washington DC Time:</h5>
        <span id="dcClock">--:-- AM</span>
        <div class="analog-clock">
            <div class="clock">
                <div class="hand hour-hand" id="hourHandDC"></div>
                <div class="hand minute-hand" id="minuteHandDC"></div>
                <div class="hand second-hand" id="secondHandDC"></div>
                <div class="center"></div>
                <div class="number" style="top: 5%; left: 50%;">12</div>
                <div class="number" style="top: 50%; left: 95%;">3</div>
                <div class="number" style="top: 95%; left: 50%;">6</div>
                <div class="number" style="top: 50%; left: 5%;">9</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h5>Canada Time:</h5>
        <span id="canadaClock">--:-- AM</span>
        <div class="analog-clock">
            <div class="clock">
                <div class="hand hour-hand" id="hourHandCA"></div>
                <div class="hand minute-hand" id="minuteHandCA"></div>
                <div class="hand second-hand" id="secondHandCA"></div>
                <div class="center"></div>
                <div class="number" style="top: 5%; left: 50%;">12</div>
                <div class="number" style="top: 50%; left: 95%;">3</div>
                <div class="number" style="top: 95%; left: 50%;">6</div>
                <div class="number" style="top: 50%; left: 5%;">9</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h5>Ethiopia Time:</h5>
        <span id="ethiopiaClock">--:-- AM</span>
        <div class="analog-clock">
            <div class="clock">
                <div class="hand hour-hand" id="hourHandET"></div>
                <div class="hand minute-hand" id="minuteHandET"></div>
                <div class="hand second-hand" id="secondHandET"></div>
                <div class="center"></div>
                <div class="number" style="top: 5%; left: 50%;">12</div>
                <div class="number" style="top: 50%; left: 95%;">3</div>
                <div class="number" style="top: 95%; left: 50%;">6</div>
                <div class="number" style="top: 50%; left: 5%;">9</div>
            </div>
        </div>
    </div>
</div>

<!-- Weather Section -->
<div class="weather-info">
    <h5>Weather Information:</h5>
    <div id="weatherDC">Washington DC: Loading...</div>
    <div id="weatherCA">Canada: Loading...</div>
    <div id="weatherET">Ethiopia: Loading...</div>
</div>

<style>
    .analog-clock {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 10px auto;
        border: 5px solid #333;
        border-radius: 50%;
        background: white;
    }

    .hand {
        position: absolute;
        background: #333;
        border-radius: 10px;
        transform-origin: bottom;
        bottom: 50%;
        left: 50%;
        transform: translateX(-50%);
    }

    .hour-hand {
        width: 6px;
        height: 30px;
        background: black;
    }

    .minute-hand {
        width: 4px;
        height: 40px;
        background: gray;
    }

    .second-hand {
        width: 2px;
        height: 45px;
        background: red;
    }

    .center {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #333;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .number {
        position: absolute;
        font-weight: bold;
        font-size: 12px;
        color: black;
        transform: translate(-50%, -50%);
    }

    .weather-info {
        text-align: center;
        margin-top: 20px;
    }
</style>

<script>
    function updateClock(clockId, hourHandId, minuteHandId, timezone) {
        const now = new Date();
        const localTime = now.toLocaleString('en-US', { timeZone: timezone, hour: 'numeric', minute: 'numeric', hour12: true });
        document.getElementById(clockId).innerText = localTime;

        const hours = now.toLocaleString('en-US', { timeZone: timezone, hour: 'numeric' });
        const minutes = now.getMinutes();

        const hourDeg = (hours % 12) * 30 + (minutes / 60) * 30;
        const minuteDeg = minutes * 6;

        document.getElementById(hourHandId).style.transform = `translateX(-50%) rotate(${hourDeg}deg)`;
        document.getElementById(minuteHandId).style.transform = `translateX(-50%) rotate(${minuteDeg}deg)`;
    }

    setInterval(() => {
        updateClock('dcClock', 'hourHandDC', 'minuteHandDC', 'America/New_York');
        updateClock('canadaClock', 'hourHandCA', 'minuteHandCA', 'America/Toronto');
        updateClock('ethiopiaClock', 'hourHandET', 'minuteHandET', 'Africa/Addis_Ababa');
    }, 1000);

    // Dummy weather information (API integration can be added)
    document.getElementById('weatherDC').innerText = 'Washington DC: 22°C, Clear';
    document.getElementById('weatherCA').innerText = 'Canada: 18°C, Cloudy';
    document.getElementById('weatherET').innerText = 'Ethiopia: 25°C, Sunny';
</script>

</body>
</html>


<div class="weather-info">
    <h5>Weather Information:</h5>
    <div id="weatherDC">Washington DC: Loading...</div>
    <div id="weatherCA">Canada: Loading...</div>
    <div id="weatherET">Ethiopia: Loading...</div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <img src="assets/logos/1.png" alt="GITC Logo" style="max-height: 40px;"> Gerar Isaac Training Center (GITC)
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="d-flex">
    <div class="sidebar">
        <a href="home.php">Home</a>
        <a href="courses.php">Courses</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact Us</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </div>

<!-- Main Content Section -->
<div class="section">
    <div id="carouselExample" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/image1.jpg" class="d-block w-100" alt="First slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images/image2.jpg" class="d-block w-100" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images/image3.jpg" class="d-block w-100" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <h2>Welcome to Gerar Isaac Training Center</h2>
    <p>Explore our diverse courses and training sessions tailored to equip you with essential skills.</p>
</div>

<script>
    // Clock Functionality
    function updateClocks() {
        const nowDC = new Date().toLocaleString("en-US", {timeZone: "America/New_York"});
        const nowCA = new Date().toLocaleString("en-US", {timeZone: "America/Toronto"});
        const nowET = new Date().toLocaleString("en-US", {timeZone: "Africa/Addis_Ababa"});

        // Update digital clocks
        document.getElementById("dcClock").innerText = nowDC.split(' ')[1]; // Only time
        document.getElementById("canadaClock").innerText = nowCA.split(' ')[1]; // Only time
        document.getElementById("ethiopiaClock").innerText = nowET.split(' ')[1]; // Only time

        // Update analog clocks
        const [hoursDC, minutesDC, secondsDC] = nowDC.split(/:| /).map(Number);
        const [hoursCA, minutesCA, secondsCA] = nowCA.split(/:| /).map(Number);
        const [hoursET, minutesET, secondsET] = nowET.split(/:| /).map(Number);

        document.getElementById("hourHandDC").style.transform = `translateX(-50%) rotate(${(hoursDC % 12) * 30 + minutesDC * 0.5}deg)`;
        document.getElementById("minuteHandDC").style.transform = `translateX(-50%) rotate(${minutesDC * 6}deg)`;
        document.getElementById("secondHandDC").style.transform = `translateX(-50%) rotate(${secondsDC * 6}deg)`;

        document.getElementById("hourHandCA").style.transform = `translateX(-50%) rotate(${(hoursCA % 12) * 30 + minutesCA * 0.5}deg)`;
        document.getElementById("minuteHandCA").style.transform = `translateX(-50%) rotate(${minutesCA * 6}deg)`;
        document.getElementById("secondHandCA").style.transform = `translateX(-50%) rotate(${secondsCA * 6}deg)`;

        document.getElementById("hourHandET").style.transform = `translateX(-50%) rotate(${(hoursET % 12) * 30 + minutesET * 0.5}deg)`;
        document.getElementById("minuteHandET").style.transform = `translateX(-50%) rotate(${minutesET * 6}deg)`;
        document.getElementById("secondHandET").style.transform = `translateX(-50%) rotate(${secondsET * 6}deg)`;
    }

    // Weather Fetching
    async function fetchWeather() {
        const apiKey = 'YOUR_API_KEY'; // Replace with your actual API key
        const locations = {
            dc: "Washington,DC",
            ca: "Toronto,CA",
            et: "Addis Ababa,ET"
        };

        for (const [key, location] of Object.entries(locations)) {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${location}&appid=${apiKey}&units=metric`);
            const data = await response.json();
            document.getElementById(`weather${key.toUpperCase()}`).innerText = `${data.name}: ${data.weather[0].description}, ${data.main.temp}°C`;
        }
    }

    // Initialize Clocks and Weather
    setInterval(updateClocks, 1000);
    fetchWeather();
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac Training Center</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .header {
            background-color: #004080;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        .sidebar {
            background-color: #f8f9fa;
            padding: 20px;
            height: 100vh;
            border-right: 1px solid #dee2e6;
        }

        .sidebar a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .sidebar a:hover {
            background-color: #e0e0e0;
            border-radius: 5px;
        }

        .section {
            margin-left: 220px;
            padding: 20px;
        }

        .small-info {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 10px;
            font-size: 12px;
            color: #666;
        }

        .weather, .clock {
            text-align: center;
            padding: 5px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .clock-box {
            display: inline-block;
            padding: 5px;
        }

        /* Clock Hands Styling */
        .clock-hand {
            position: absolute;
            transform-origin: 50% 100%;
            background-color: #000;
        }

        .hour {
            width: 4px;
            height: 40px;
        }

        .minute {
            width: 3px;
            height: 60px;
        }

        .second {
            width: 2px;
            height: 70px;
            background-color: red;
        }

        .carousel {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Gerar Isaac Training Center</h1>
    <div class="small-info">
        <div class="clock" id="dcClock"></div>
        <div class="clock" id="canadaClock"></div>
        <div class="clock" id="ethiopiaClock"></div>
        <div class="weather" id="weatherDC"></div>
        <div class="weather" id="weatherCA"></div>
        <div class="weather" id="weatherET"></div>
    </div>
</div>

<div class="d-flex">
    <div class="sidebar">
        <a href="home.php">Home</a>
        <a href="courses.php">Courses</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact Us</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </div>

    <div class="section">
        <div id="carouselExample" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/images/image1.jpg" class="d-block w-100" alt="First slide">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/image2.jpg" class="d-block w-100" alt="Second slide">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/image3.jpg" class="d-block w-100" alt="Third slide">
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

        <h2>Welcome to Gerar Isaac Training Center</h2>
        <p>Explore our diverse courses and training sessions tailored to equip you with essential skills.</p>
    </div>
</div>

<script>
    // Clock Functionality
    function updateClocks() {
        const nowDC = new Date().toLocaleString("en-US", {timeZone: "America/New_York"});
        const nowCA = new Date().toLocaleString("en-US", {timeZone: "America/Toronto"});
        const nowET = new Date().toLocaleString("en-US", {timeZone: "Africa/Addis_Ababa"});

        document.getElementById("dcClock").innerText = `DC Time: ${nowDC.split(' ')[1]}`;
        document.getElementById("canadaClock").innerText = `Canada Time: ${nowCA.split(' ')[1]}`;
        document.getElementById("ethiopiaClock").innerText = `Ethiopia Time: ${nowET.split(' ')[1]}`;
    }

    // Weather Fetching
    async function fetchWeather() {
        const apiKey = 'YOUR_API_KEY'; // Replace with your actual API key
        const locations = {
            dc: "Washington,DC",
            ca: "Toronto,CA",
            et: "Addis Ababa,ET"
        };

        for (const [key, location] of Object.entries(locations)) {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${location}&appid=${apiKey}&units=metric`);
            const data = await response.json();
            document.getElementById(`weather${key.toUpperCase()}`).innerText = `${data.name}: ${data.weather[0].description}, ${data.main.temp}°C`;
        }
    }

    // Initialize Clocks and Weather
    setInterval(updateClocks, 1000);
    fetchWeather();

<!-- About Us Section -->
<section id="about" class="section">
    <div class="container">
        <h2>About Us</h2>
        <p>At Gerar Isaac Training Center (GITC), we are committed to providing high-quality<br>
         education in various fields, helping students achieve their academic and professional goals.
        
We are in class/Online Courses, your one-stop solution for learning. Our platform offers a wide range of comprehensive courses designed to help you master the principles and practices of accounting.

we understand the importance of quality education, which is why our courses are taught by experienced professionals in the field. Whether you are a beginner or an experienced accountant looking to enhance your skills, we have courses tailored to meet your needs.

Our mission is to make accounting,basic electrical engineering and web development education accessible to everyone, regardless of their location or background. With our user-friendly platform and flexible learning options, you can learn at your own pace and from anywhere in the world.

Join us today and take the first step towards advancing your career!
        </p>
    </div>
</section>

<!-- Include Bootstrap CSS and any custom CSS file -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    /* Container and Section Styling */
    #courses {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    /* Card Styling */
    .card {
        transition: transform 0.3s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
    }

    /* Hover Effects */
    .card:hover {
        transform: scale(1.05);
    }

    /* Logo Styling */
    .logo-container {
        position: relative;
        display: flex;
        justify-content: center;
    }

    .logo-container img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Add Animation */
    .card {
        animation: rolling 4s infinite;
    }

    @keyframes rolling {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    /* Button Styling */
    .btn {
        font-weight: bold;
        letter-spacing: 1px;
        padding: 8px 20px;
    }

    /* Title and Text Styling */
    .card-title {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .card-text {
        font-size: 0.9rem;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        .card {
            margin-bottom: 30px;
        }
    }
</style>

<!-- Courses Section -->
<section id="courses" class="section">
    <div class="container">
        <h2 class="text-center mb-5" style="font-size: 2rem; color: #007bff; font-weight: bold;">Our Course Offers</h2>
        <div class="row justify-content-center">

            <!-- Accounting Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Accounting Image.png" class="rounded-circle" alt="Accounting">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-primary">Accounting</h5>
                        <p class="card-text">Explore a range of accounting courses including:</p>
                        <ul class="list-unstyled">
                            <li>Principles of Accounting</li>
                            <li>Cost Accounting</li>
                            <li>Financial Accounting</li>
                        </ul>
                        <a href="accounting_courses.php" class="btn btn-primary btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Accounting Software -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Peachtree-logo.jpg" class="rounded-circle" alt="Accounting Software">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-success">Accounting Software</h5>
                        <p class="card-text">Get hands-on experience with:</p>
                        <ul class="list-unstyled">
                            <li>Peach Tree</li>
                            <li>Tally</li>
                            <li>QuickBooks</li>
                        </ul>
                        <a href="accounting_software.php" class="btn btn-success btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Electrical Engineering -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/electrical-engineering-logo.avif" class="rounded-circle" alt="Electrical Engineering">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-warning">Electrical Engineering</h5>
                        <p class="card-text">Courses include:</p>
                        <ul class="list-unstyled">
                            <li>Basic Electricity</li>
                            <li>Basic Electronics</li>
                        </ul>
                        <a href="electrical_engineering.php" class="btn btn-warning btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Programming Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/html,css,js.webp" class="rounded-circle" alt="Programming">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-info">Programming</h5>
                        <p class="card-text">Learn the essentials of:</p>
                        <ul class="list-unstyled">
                            <li>Website Development (HTML, CSS, JS)</li>
                            <li>PHP Tutorial</li>
                            <li>Database Management</li>
                        </ul>
                        <a href="programming_courses.php" class="btn btn-info btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


<!-- Testimonials Section -->
<div id="testimonials" class="section text-center bordered"><section id="courses" class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
      
    <h2>What Our Graduates Say</h2>
   <!-- Testimonials Section -->

        
        <!-- First Round Accounting Graduates -->
          <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">First Round Accounting Graduates</h3>
        <div class="row">
        
            <!-- Testimonial 1 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Esaias Amanuel Zeregabir Acc 1st R-3.jpeg" class="rounded-circle mb-3" alt="Eseas Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Eseas Amanuel</h5>
                    <p class="text-muted">"The course deepened my understanding of accounting principles. I’m confident in my skills now!"</p>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Feven Gebremichael" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Feven Gebremichael</h5>
                    <p class="text-muted">"The accounting course is very detailed and easy to understand. Highly recommended!"</p>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Fireselam  bokre Gebresilasia Acc 1st R-2.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Fireselam Bokre</h5>
                    <p class="text-muted">"Clear, concise, and practical. This course gave me the confidence to pursue my accounting career."</p>
                </div>
            </div>

            <!-- Testimonial 4 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Lidia weldu Hayelom Acc 1st R-4.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Lidia weldu</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                </div>
            </div>
        </div>

        <!-- Second Type Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Second Round Accounting Graduates</h3>
        <div class="row">
            <!-- Testimonial 5 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Betelihem.jpg " class="rounded-circle mb-3" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                </div>
            </div>

            <!-- Testimonial 6 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Tsegay.jpg" class="rounded-circle mb-3" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                </div>
            </div>

            <!-- Testimonial 7 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg" class="rounded-circle mb-3" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #ff5733;">Helen Tesfaye</h5>
                    <p class="text-muted">"A fantastic course that greatly improved my accounting knowledge."</p>
                </div>
            </div>

            <!-- Testimonial 8 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg " class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
        </div>
    </div>

 
            <!-- Testimonial 8 -->
            <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Third Round Accounting Graduates</h3>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2aac503c7e.jpg" class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <!-- Testimonial 9 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2beac03e52.jpg" class="rounded-circle mb-3" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Tesfay</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <!-- Testimonial 10 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
 
  
      
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Betelihem</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                  
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-danger" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                  
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-purple" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                  
                </div>
            </div>
        </div>


        <!-- Fourth Round Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Fourth Round Accounting Graduates</h3>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .testimonial-box:hover {
        background-color: #f0f0f0;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(-10px);
    }
</style>
<!<!-- Contact Us Section with Map, Address, and Form -->
<section id="contact" class="section bg-light py-5">
    <div class="container">
        <h2 class="mb-4 text-center" style="font-size: 2.5rem; font-weight: bold; color: #007bff;">Contact Us</h2>
        <div class="row">
            <!-- Contact Form -->
            <div class="col-md-6">
                <p class="text-muted mb-4">For inquiries, feel free to reach out to us using the form below.</p>
                <form action="contact.php" method="POST" class="p-4 rounded shadow-lg bg-white" style="border-radius: 15px;">
                    <div class="form-group">
                        <label for="name" class="font-weight-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Your Full Name" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="email" class="font-weight-bold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Your Email Address" style="border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="telephone" class="font-weight-bold">Telephone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="Your Phone Number" style="border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="message" class="font-weight-bold">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message here..." required style="border-radius: 8px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold" style="border-radius: 25px;">Submit Message</button>
                </form>
            </div>
            
            <!-- Contact Details and Map -->
            <div class="col-md-6">
                <div class="contact-details text-center p-4 rounded shadow-lg bg-white" style="border-radius: 15px;">
                    <h4 class="font-weight-bold mb-3" style="color: #007bff;">Our Location</h4>
                    <p class="mb-1">Gerar Isaac Training Center</p>
                    <p>Meskel Flower, Addis Ababa, Ethiopia</p>
                    <p>Email: <a href="mailto:gerarisaactc@gmail.com" style="color: #28a745;">gerarisaactc@gmail.com</a></p>
                    <p>Phone: <a href="tel:+251111234567" style="color: #28a745;">+251 11 123 4567</a></p>
                    <div class="map-container mt-4">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1272.8425190281702!2d38.76221780279265!3d8.987984198294146!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b844591cefe1f%3A0x4096ef6200a52cca!2sMeskel%20Flower%20Hotel!5e0!3m2!1sen!2set!4v1695140187355!5m2!1sen!2set"
                            width="100%" height="300" frameborder="0" style="border:0; border-radius: 10px;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Footer with Social Icons -->
<?php
// Include footer
include('footer.php');
?>



home page 

kkkkkkkkkk
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac Training Center</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Fixed Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Content Margin after fixed navbar */
        body {
            padding-top: 80px;
            border: 5px solid #ff6347; /* Colorful border for the entire website */
        }

        /* Sidebar Scroll Left */
        .sidebar {
            position: fixed;
            top: 80px;
            left: 0;
            width: 100px;
            height: 100%;
            background-color: #f8f9fa;
            padding: 20px;
            overflow-y: auto;
            border-right: 3px solid #007bff;
        }

        .sidebar a {
            display: block;
            color: #333;
            padding: 10px;
            margin-bottom: 5px;
            border: 2px solid #ddd;
            border-radius: 5px;
            text-align: center;
            background-color: #e9ecef;
        }

        .sidebar a:hover {
            background-color: #007bff;
            color: white;
        }

        /* Colorful Border for each section */
        .section {
            margin-left: 220px;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #007bff;
            border-radius: 10px;
        }

        /* Welcome text styling */
        .welcome-text {
            margin-top: 20px;
            text-align: center;
            color: #333;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .welcome-text span {
            color: #007bff;
        }

        /* Styling for the Carousel */
        .carousel-inner img {
            width: 80%;
            height: 500px;
            object-fit: cover;
        }

        /* Section Heading Styling */
        h2 {
            color: #007bff;
            font-weight: bold;
        }

        /* Testimonials */
        .testimonials {
            background: #f9f9f9;
        }

        .testimonial-item {
            background: white;
            padding: 1px;
            margin: 2px;
            border-radius: 1px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 5px solid #007bff;
        }

        /* Address Section */
        .address-section {
            background-color: #343a40;
            color: white;
            padding: 60px 0;
            border: 4px solid #ff6347;
        }

        .address-section h5, .address-section p {
            color: white;
        }

        .social-icons a {
            color: white;
            margin-right: 15px;
        }
        
       

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <img src="assets/logos/1.png-2.png" alt="GITC Logo" style="max-height: 40px;"> Gerar Isaac Training Center (GITC)
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                <!-- Language Options -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Language
                    </a>
                    <div class="dropdown-menu" aria-labelledby="languageDropdown">
                        <a class="dropdown-item" href="?lang=en">English</a>
                        <a class="dropdown-item" href="?lang=am">Amharic</a>
                        <a class="dropdown-item" href="?lang=ti">Tigrinya</a>
                    </div>
                </li>
                <!-- Conditional Login/Register Links -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar for Scrollable Navigation -->
<div class="sidebar">
    <h4>Navigation</h4>
    <a href="#home">Home</a>
    <a href="#about">About Us</a>
    <a href="#courses">Courses</a>
    <a href="#testimonials">Testimonials</a>
    <a href="#contact">Contact Us</a>
</div>

<!-- Welcome Text -->
<div id="home" class="welcome-text">
    Welcome to <span>Gerar Isaac Training Center</span>
</div>

<!-- Carousel Section -->
<div id="carouselExampleCaptions" class="carousel slide section" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
        <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
        <img src="assets/images_courses/photo_first%20round-final%20exam.jpg" class="d-block w-100" alt="First round Final exam">
            <div class="carousel-caption d-none d-md-block">
                <h5>Empower Your Future</h5>
                <p>Get quality education and skills at GITC.</p>
            </div>
        </div>
        <div class="carousel-item">
        <img src="assets/images_courses/3rd round   students training session .jpg" class="d-block w-100" alt="College 5">
            <div class="carousel-caption d-none d-md-block">
                <h5>Learn From Experts</h5>
                <p>Our courses are designed by professionals.</p>
            </div>
        </div>
        <div class="carousel-item">
        <img src="assets/images_courses/photo_second round-peach tree exam.jpg" class="d-block w-100" alt="Second round graduates">
            <div class="carousel-caption d-none d-md-block">
                <h5>Shape Your Career</h5>
                <p>Join us to unlock your potential.</p>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<!-- Section: About Us -->
<section id="about" class="section">
    <div class="container">
        <h2>About Us</h2>
        <p>At Gerar Isaac Training Center (GITC), we are committed to providing high-quality<br>
         education in various fields, helping students achieve their academic and professional goals.
        
We are in class/Online Courses, your one-stop solution for learning. Our platform offers a wide range of comprehensive courses designed to help you master the principles and practices of accounting.

we understand the importance of quality education, which is why our courses are taught by experienced professionals in the field. Whether you are a beginner or an experienced accountant looking to enhance your skills, we have courses tailored to meet your needs.

Our mission is to make accounting,basic electrical engineering and web development education accessible to everyone, regardless of their location or background. With our user-friendly platform and flexible learning options, you can learn at your own pace and from anywhere in the world.

Join us today and take the first step towards advancing your career!
        </p>
    </div>
</section>

<!-- Section: Courses -->
<section id="courses" style="background-color: #f9f9f9; padding: 10px 0;">
    <div class="container">
        <h2 class="text-center mb-5" style="font-size: 1.5rem; color: #007bff; font-weight: bold;">Our Course Offers</h2>
        <div class="row">

            <!-- Accounting Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <div class="logo-container">
                        <img src="assets/logos/Accounting Image.png" class="card-img-top rounded-circle" alt="Accounting">
                    </div>
                    <div class="card-body uniform-height">
                        <h5 class="card-title font-weight-bold" style="color: #007bff;">Accounting</h5>
                        <p class="card-text">Explore a range of accounting courses including:</p>
                        <ul class="list-unstyled">
                            <li>Principles of Accounting</li>
                            <li>Cost Accounting</li>
                            <li>Financial Accounting</li>
                            <li>Management Accounting</li>
                            <li>Auditing</li>
                            <li>Budgeting</li>
                        </ul>
                        <a href="accounting_courses.php" class="btn btn-primary btn-block mt-3">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Accounting Software -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <div class="logo-container">
                        <img src="assets/logos/Peachtree-logo.jpg" class="card-img-top rounded-circle" alt="Accounting Software">
                    </div>
                    <div class="card-body uniform-height">
                        <h5 class="card-title font-weight-bold" style="color: #28a745;">Accounting Software</h5>
                        <p class="card-text">Get hands-on experience with popular accounting software:</p>
                        <ul class="list-unstyled">
                            <li>Peach Tree</li>
                            <li>Tally</li>
                            <li>QuickBooks</li>
                        </ul>
                        <a href="accounting_software.php" class="btn btn-success btn-block mt-3">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Electrical Engineering -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <div class="logo-container">
                        <img src="assets/logos/electrical-engineering-logo.avif" class="card-img-top rounded-circle" alt="Electrical Engineering">
                    </div>
                    <div class="card-body uniform-height">
                        <h5 class="card-title font-weight-bold" style="color: #ffc107;">Electrical Engineering</h5>
                        <p class="card-text">Courses include:</p>
                        <ul class="list-unstyled">
                            <li>Basic Electricity</li>
                            <li>Basic Electronics</li>
                            <li>House Installation</li>
                            <li>Manufacturing</li>
                            <li>Electric Motors Controlling</li>
                            <li>Electric Motor Rewinding</li>
                        </ul>
                        <a href="electrical_engineering.php" class="btn btn-warning btn-block mt-3">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Programming Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <div class="logo-container">
                        <img src="assets/logos/html,css,js.webp" class="card-img-top rounded-circle" alt="Programming">
                    </div>
                    <div class="card-body uniform-height">
                        <h5 class="card-title font-weight-bold" style="color: #17a2b8;">Programming</h5>
                        <p class="card-text">Learn the essentials of programming with:</p>
                        <ul class="list-unstyled">
                            <li>Website Development (HTML, CSS, JS)</li>
                            <li>PHP Tutorial</li>
                            <li>Database Management</li>
                        </ul>
                        <a href="programming_courses.php" class="btn btn-info btn-block mt-3">Learn More</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Testimonials Section -->
<section id="testimonials" class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <h2 class="text-center mb-5" style="color: #333; font-weight: bold;">What Our Students Say</h2>

        <!-- First Round Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #007bff; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">First Round Accounting Graduates</h3>
        <div class="row">
            <!-- Testimonial 1 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Esaias Amanuel Zeregabir Acc 1st R-3.jpeg" class="rounded-circle mb-3" alt="Eseas Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Eseas Amanuel</h5>
                    <p class="text-muted">"The course deepened my understanding of accounting principles. I’m confident in my skills now!"</p>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Feven Gebremichael" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Feven Gebremichael</h5>
                    <p class="text-muted">"The accounting course is very detailed and easy to understand. Highly recommended!"</p>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Fireselam  bokre Gebresilasia Acc 1st R-2.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Fireselam Bokre</h5>
                    <p class="text-muted">"Clear, concise, and practical. This course gave me the confidence to pursue my accounting career."</p>
                </div>
            </div>

            <!-- Testimonial 4 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-1st Round/Lidia weldu Hayelom Acc 1st R-4.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Lidia weldu</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                </div>
            </div>
        </div>

        <!-- Second Type Accounting Graduates -->
        <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Second Type Accounting Graduates</h3>
        <div class="row">
            <!-- Testimonial 5 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Betelihem.jpg " class="rounded-circle mb-3" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                </div>
            </div>

            <!-- Testimonial 6 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Tsegay.jpg" class="rounded-circle mb-3" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                </div>
            </div>

            <!-- Testimonial 7 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg" class="rounded-circle mb-3" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #ff5733;">Helen Tesfaye</h5>
                    <p class="text-muted">"A fantastic course that greatly improved my accounting knowledge."</p>
                </div>
            </div>

            <!-- Testimonial 8 -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                    <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg " class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
        </div>
    </div>
</section>
 
            <!-- Testimonial 8 -->
             
            <section id="testimonials" class="py-5" style="background-color: #f8f9fa;"><div class="container">
       <h2 class="text-center mb-5" style="color: #333;"><br>'Third Round Accounting Graduates'</h2> 
     
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2aac503c7e.jpg" class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <!-- Testimonial 9 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/66b2beac03e52.jpg" class="rounded-circle mb-3" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <!-- Testimonial 10 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                    <img src="assets/students/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
 
  
      
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Betelihem" style="width: 100px; height: 100px;">
                    <h5 style="color: #17a2b8;">Betelihem</h5>
                    <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-danger" alt="Tsegay" style="width: 100px; height: 100px;">
                    <h5 style="color: #dc3545;">Tsegay</h5>
                    <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-purple" alt="Amanuel" style="width: 100px; height: 100px;">
                    <h5 style="color: #6f42c1;">Amanuel</h5>
                    <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
                    <small class="text-muted">2nd Round Graduate in Accounting</small>
                </div>
            </div>
        </div>

        <!-- Third Round Accounting Graduates -->
        <h2 class="text-center mb-5" style="color: #333;">Fifth Round Accounting Graduates</h2>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                    <h5 style="color: #007bff;">Samuel Bekele</h5>
                    <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                    <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                    <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                    <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                    <h5 style="color: #ffc107;">Marta Haile</h5>
                    <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .testimonial-box:hover {
        background-color: #f0f0f0;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(-10px);
    }
</style>


<!-- Section: Contact -->
<section id="contact" class="section bg-light">
    <div class="container">
        <h2>Contact Us</h2>
        <p>Address: Gerar Isaac Training Center, Addis Ababa, Ethiopia.</p>
        <p>Email: info@gitc.edu.et</p>
        <p>Phone: +251 11 123 4567</p>
    </div>
</section>

<!-- Address Section -->
<section class="address-section">
    <div class="container text-center">
        <h5>Address</h5>
        <p>Gerar Isaac Training Center, Bole Subcity, Addis Ababa, Ethiopia</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
            <a href="#"><i class="fab fa-telegram"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
kkkkkkkkkkkkkkk

fundamental electricity course 


kkkkkkkkkkkkkk
<?php
include('db.php'); // Ensure this file sets up $pdo
include('header_loggedin.php');

// Fetch courses from the database
try {
    $sql = "SELECT * FROM courses";
    $stmt = $pdo->query($sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrical Engineering Courses</title>
    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="assets/css/stylish.css">
    <style>

                
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .module-nav {
            margin: 20px auto;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .module-nav__module-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: #333;
            background: #e0e0e0;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
        }
        .module-nav__module-title h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .icon-thick-chevron-up {
            font-size: 1.2em;
            transition: transform 0.3s;
        }
        .module-nav__module--open .icon-thick-chevron-up {
            transform: rotate(180deg);
        }
        .module-nav__topics {
            padding: 10px 20px;
            display: none;
            background: #f9f9f9;
            border-radius: 0 0 8px 8px;
        }
        .module-nav__topic {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .module-nav__topic:hover {
            background-color: #e0f7fa;
            border-color: #b2ebf2;
        }
        .module-nav__topic.active {
            background-color: #e0f7fa;
            border-color: #b2ebf2;
        }
        .module-nav__topic.current {
            background-color: #b2dfdb;
            border-color: #80cbc4;
        }
        .module-nav__topic.completed_topic {
            background-color: #b9fbc0;
            border-color: #a5d6a7;
        }
        .module-nav__topic h4 {
            margin: 0;
        }
        .module-nav__topic span {
            display: none;
        }
        .video-player {
            display: none;
            margin: 20px auto;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .video-player iframe {
            width: 100%;
            height: 500px;
            border: none;
        }
        .video-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .video-controls button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
        }
        .video-controls button:hover {
            background-color: #0056b3;
        }
        .comments {
            margin-top: 20px;
        }
        .comments h4 {
            margin-bottom: 10px;
        }
        .comments form {
            display: flex;
            flex-direction: column;
        }
        .comments form textarea {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .comments form button {
            align-self: flex-start;
        }
        .comments .comment {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header class="header">
        <section class="flex">
            <a href="home.php" class="logo">View Electrical Engineering Courses</a>
            <form action="search.html" method="post" class="search-form">
                <input type="text" name="search_box" required placeholder="Search courses..." maxlength="100">
                <button type="submit" class="fas fa-search"></button>
            </form>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>
            <div class="profile">
                <img src="assets/tutor/Habtom.jpg" class="image" alt="Tutor Image">
                <h3 class="name">Habtom Araya-ACCA</h3>
                <p class="role">Tutor</p>
                <a href="profile.html" class="btn">View Profile</a>
                <div class="flex-btn">
                    <a href="login.html" class="option-btn">Login</a>
                    <a href="register.html" class="option-btn">Register</a>
                </div>
            </div>
        </section>
    </header>
    <div class="module-nav">
        <div class="module-nav__module module-nav__module--open">
            <a href="#" class="module-nav__module-title">
                <h3>Module 1: Introduction to Basic Electricity 
                    <span class="icon-thick-chevron-up">&#9650;</span>
                </h3>
            </a>
            <div class="module-nav__topics">
                <a data-id="124675" data-video="https://player.vimeo.com/video/583618506" 
                   class="module-nav__topic active completed_topic">
                    <span></span>
                    <h4>Introduction to Basic Electricity - Learning Outcomes</h4>
                </a>
                <a data-id="124676" data-video="https://player.vimeo.com/video/583618506" 
                    class="module-nav__topic active completed_topic">
                    <span></span>
                    <h4>Fundamentals of Electricity</h4>
                </a>
                <a data-id="124677" data-video="https://player.vimeo.com/video/583618506" 
                    class="module-nav__topic current">
                    <span></span>
                    <h4>Electrical Units and Ohm’s Law</h4>
                </a>
                <a data-id="124678" data-video="https://player.vimeo.com/video/583618019" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basics of Direct Current (DC) Circuits</h4>
                </a>
                <a data-id="124679" data-video="https://player.vimeo.com/video/583617288" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basics of Alternating Current</h4>
                </a>
                <a data-id="124680" data-video="https://player.vimeo.com/video/583740013?quality=720p" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Introduction to Basic Electricity - Lesson Summary</h4>
                </a>
            </div>
        </div>
        <div class="module-nav__module module-nav__module--closed">
            <a href="#" class="module-nav__module-title">
                <h3>Module 2: Basic Electricity Components and Precautions 
                    <span class="icon-thick-chevron-up">&#9650;</span>
                </h3>
            </a>
            <div class="module-nav__topics">
                <a data-id="124681" data-video="https://player.vimeo.com/video/583740013?quality=720p" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basic Electricity Components and Precautions - Learning Outcomes</h4>
                </a>
                <a data-id="124682" data-video="https://player.vimeo.com/video/583740013?quality=720p" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Voltage Sources and Resistors</h4>
                </a>
                <a data-id="124683" data-video="https://player.vimeo.com/video/583725560?quality=720p" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Capacitance and Capacitors</h4>
                </a>
                <a data-id="124684" data-video="https://player.vimeo.com/video/583732127?quality=720p" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Magnets and Magnetism</h4>
                </a>
                <a data-id="124685" data-video="https://player.vimeo.com/video/583727241?quality=720p" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Electrical Safety Measures</h4>
                </a>
                <a data-id="124686" data-video="https://player.vimeo.com/video/583738738?quality=720p" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basic Electricity Components and Precautions - Lesson Summary</h4>
                </a>
            </div>
        </div>
    </div>

    <div class="video-player" id="video-player">
        <iframe id="video-frame" src="" allowfullscreen></iframe>
        <div class="video-controls">
            <button id="like-btn"><i class="fas fa-thumbs-up"></i> Like</button>
            <button id="dislike-btn"><i class="fas fa-thumbs-down"></i> Dislike</button>
            <button id="progress-btn">Mark as Complete</button>
        </div>
        <div class="comments">
            <h4>Comments</h4>
            <form id="comment-form">
                <textarea name="comment" rows="4" placeholder="Add a comment..."></textarea>
                <button type="submit">Submit</button>
            </form>
            <div id="comment-list">
                <!-- Comments will be dynamically inserted here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.module-nav__module-title').on('click', function() {
                var $module = $(this).closest('.module-nav__module');
                $module.toggleClass('module-nav__module--open module-nav__module--closed');
                $(this).siblings('.module-nav__topics').slideToggle();
                $(this).find('.icon-thick-chevron-up').toggleClass('icon-thick-chevron-down');
            });

            $('.module-nav__topic').on('click', function() {
                var videoUrl = $(this).data('video');
                $('#video-frame').attr('src', videoUrl);
                $('#video-player').show();
            });

            $('#like-btn').on('click', function() {
                alert('Liked!');
                // Implement like functionality here
            });

            $('#dislike-btn').on('click', function() {
                alert('Disliked!');
                // Implement dislike functionality here
            });

            $('#progress-btn').on('click', function() {
                alert('Marked as Complete!');
                // Implement progress functionality here
            });

            $('#comment-form').on('submit', function(e) {
                e.preventDefault();
                var comment = $(this).find('textarea[name="comment"]').val();
                $('#comment-list').append('<div class="comment">' + comment + '</div>');
                $(this).find('textarea[name="comment"]').val('');
            });
        });

        
    </script>
    <?php include('footer.php'); ?>
</body>
</html>
kkkkkkkkkkkkkkkk
<?php
include('db.php'); // Ensure this file sets up $pdo
include('header_loggedin.php');

// Fetch courses from the database
try {
    $sql = "SELECT * FROM courses";
    $stmt = $pdo->query($sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrical Engineering Courses</title>
    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="assets/css/stylish.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .content-wrapper {
            display: flex;
            justify-content: space-between;
            margin: 20px auto;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .course-sidebar {
            width: 35%;
            padding-right: 20px;
        }
        .course-sidebar h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .module-nav__module-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: #333;
            background: #e0e0e0;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            font-size: 12px; /* Set font size to 12px */
        }
        .module-nav__module-title h3 {
            margin: 0;
            font-size: 1em; /* Adjust font size accordingly */
        }
        .module-nav__module--open .icon-thick-chevron-up {
            transform: rotate(180deg);
        }
        .module-nav__topics {
            padding: 10px 20px;
            display: none;
            background: #f9f9f9;
            border-radius: 0 0 8px 8px;
        }
        .module-nav__topic {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: background-color 0.3s, border-color 0.3s;
            font-size: 12px; /* Set font size to 12px */
        }
        .module-nav__topic:hover {
            background-color: #e0f7fa;
            border-color: #b2ebf2;
        }
        .video-player {
            width: 60%;
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .video-player iframe {
            width: 100%;
            height: 400px;
            border: none;
        }
        .video-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .video-controls button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
        }
        .video-controls button:hover {
            background-color: #0056b3;
        }
        .course-note {
            background-color: #f0f8ff;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .progress-indicator {
            margin-top: 20px;
        }
        .progress-bar {
            background-color: #007bff;
            height: 10px;
            border-radius: 5px;
            transition: width 0.3s;
        }
        .comments {
            margin-top: 20px;
        }
        .comments h4 {
            margin-bottom: 10px;
        }
        .comments form {
            display: flex;
            flex-direction: column;
        }
        .comments form textarea {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .comments form button {
            align-self: flex-start;
        }
        .comments .comment {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header class="header">
        <section class="flex">
            <a href="home.php" class="logo">View Electrical Engineering Courses</a>
            <form action="search.html" method="post" class="search-form">
                <input type="text" name="search_box" required placeholder="Search courses..." maxlength="100">
                <button type="submit" class="fas fa-search"></button>
            </form>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>
            <div class="profile">
                <img src="assets/tutor/Habtom.jpg" class="image" alt="Tutor Image">
                <h3 class="name">Habtom Araya-ACCA</h3>
                <p class="role">Tutor</p>
                <a href="profile.html" class="btn">View Profile</a>
            </div>
        </section>
    </header>
    
    <div class="content-wrapper">
        <!-- Left Sidebar: Course Topics/Subtitles -->
        <div class="course-sidebar">
            <h3>Course Modules</h3>
            <div class="module-nav">
                <!-- Module 1 -->
                <div class="module-nav__module module-nav__module--open">
                    <a href="#" class="module-nav__module-title">
                        <h3>Module 1: Introduction to Basic Electricity</h3>
                        <span class="icon-thick-chevron-up">&#9650;</span>
                    </a>
                    <div class="module-nav__topics">
                        <a data-video="https://player.vimeo.com/video/583618506" class="module-nav__topic">
                            <h4>Introduction to Basic Electricity - Learning Outcomes</h4>
                        </a>
                        <a data-video="https://player.vimeo.com/video/583618506" class="module-nav__topic">
                            <h4>Fundamentals of Electricity</h4>
                        </a>
                        <a data-video="https://player.vimeo.com/video/583618506" class="module-nav__topic">
                            <h4>Electrical Units and Ohm’s Law</h4>
                        </a>
                    </div>
                </div>
                
                <!-- Module 2 -->
                <div class="module-nav__module">
                    <a href="#" class="module-nav__module-title">
                        <h3>Module 2: Electrical Circuit Theory</h3>
                        <span class="icon-thick-chevron-up">&#9650;</span>
                    </a>
                    <div class="module-nav__topics">
                        <a data-video="https://player.vimeo.com/video/583618015" class="module-nav__topic">
                            <h4>Introduction to Circuit Theory</h4>
                        </a>
                        <a data-video="https://player.vimeo.com/video/583618567" class="module-nav__topic">
                            <h4>Series and Parallel Circuits</h4>
                        </a>
                        <a data-video="https://player.vimeo.com/video/583618607" class="module-nav__topic">
                            <h4>Kirchhoff’s Laws</h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Video Player -->
        <div class="video-player">
            <iframe src="" frameborder="0" allowfullscreen></iframe>
            <div class="video-controls">
                <button id="view-notes-btn">View Notes</button>
                <button id="next-btn">Next Module
kkkkkkkkkkkk
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modules = document.querySelectorAll('.module-nav__module');
        const videoPlayer = document.querySelector('.video-player iframe');
        const courseNote = document.getElementById('course-note');
        const progressBar = document.getElementById('progress-bar');
        const nextBtn = document.getElementById('next-btn');
        const notesButton = document.getElementById('view-notes-btn');
        let currentModuleIndex = 0;

        function updateVideo(src, notes) {
            videoPlayer.src = src;
            courseNote.innerHTML = notes;
        }

        function updateProgress(index) {
            const percentage = (index / modules.length) * 100;
            progressBar.style.width = `${percentage}%`;
        }

        function showNextModule() {
            if (currentModuleIndex < modules.length - 1) {
                modules[currentModuleIndex].style.display = 'none';
                currentModuleIndex++;
                modules[currentModuleIndex].style.display = 'block';
                updateProgress(currentModuleIndex);
                const video = modules[currentModuleIndex].querySelector('.module-nav__topic');
                if (video) {
                    updateVideo(video.getAttribute('data-video'), video.innerHTML);
                }
            } else {
                alert('You have reached the end of the course.');
            }
        }

        modules.forEach((module, index) => {
            const topics = module.querySelector('.module-nav__topics');
            if (topics) {
                topics.addEventListener('click', (e) => {
                    if (e.target.classList.contains('module-nav__topic')) {
                        const videoSrc = e.target.getAttribute('data-video');
                        const notes = e.target.innerHTML;
                        updateVideo(videoSrc, notes);
                        updateProgress(index);
                    }
                });
            }
        });

        nextBtn.addEventListener('click', showNextModule);

        notesButton.addEventListener('click', () => {
            const currentTopic = document.querySelector('.module-nav__topic.active');
            if (currentTopic) {
                courseNote.innerHTML = currentTopic.innerHTML;
            }
        });

        // Initial load
        if (modules.length > 0) {
            modules[0].style.display = 'block';
            updateProgress(currentModuleIndex);
            const firstVideo = modules[0].querySelector('.module-nav__topic');
            if (firstVideo) {
                updateVideo(firstVideo.getAttribute('data-video'), firstVideo.innerHTML);
            }
        }
    });
</script>


kkkkkkkkkkkkk watch video-o2

kkkkkkkkkkkkkk
<?php
include('db.php');
include('header.php');

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = "Default Name"; // Temporary fix; ensure this is set correctly during login
}

// Debugging: Check if PDO connection is set
if (!$pdo) {
    die('Database connection failed.');
}

// Fetch video details from the database
$video_id = 101; // video ID is 101 for this example, adjust as needed
try {
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$video) {
        // Debugging: Check if the query returned any results
        die('Video not found for ID: ' . htmlspecialchars($video_id));
    }
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Watch Video</title>

   <!-- Font Awesome CDN Link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS File Link  -->
   <link rel="stylesheet" href="assets/css/style.css">
   <style>
       /* Custom styles as in the previous example */
       body {
           background-color: #f5f5f5;
           font-family: Arial, sans-serif;
       }
       .side-bar {
           position: fixed;
           width: 250px;
           height: 100%;
           background: #333;
           color: #fff;
           padding: 20px;
           box-shadow: 2px 0 5px rgba(0,0,0,0.1);
       }
       .side-bar .profile {
           text-align: center;
           margin-bottom: 20px;
       }
       .side-bar .profile img {
           width: 100px;
           border-radius: 50%;
       }
       .side-bar .profile .name {
           font-size: 18px;
           margin-top: 10px;
       }
       .side-bar .profile .role {
           font-size: 14px;
           color: #aaa;
       }
       .side-bar .navbar a {
           display: block;
           padding: 10px;
           color: #fff;
           text-decoration: none;
           margin-bottom: 10px;
           border-radius: 5px;
           transition: background 0.3s;
       }
       .side-bar .navbar a:hover {
           background: #555;
       }
       .watch-video {
           margin-left: 270px;
           padding: 20px;
       }
       .watch-video .video-container {
           background: #fff;
           padding: 20px;
           box-shadow: 0 0 10px rgba(0,0,0,0.1);
           border-radius: 10px;
       }
       .watch-video .title {
           font-size: 24px;
           margin-top: 10px;
       }
       .comments {
           margin-top: 30px;
       }
       .comments .heading {
           font-size: 24px;
           margin-bottom: 20px;
       }
       .comments .add-comment textarea {
           width: 100%;
           padding: 10px;
           border: 1px solid #ccc;
           border-radius: 5px;
           margin-bottom: 10px;
       }
       .comments .add-comment .inline-btn {
           background: #007bff;
           color: #fff;
           padding: 10px 20px;
           border: none;
           border-radius: 5px;
           cursor: pointer;
           transition: background 0.3s;
       }
       .comments .add-comment .inline-btn:hover {
           background: #0056b3;
       }
       .comments .box-container .box {
           background: #fff;
           padding: 20px;
           box-shadow: 0 0 10px rgba(0,0,0,0.1);
           border-radius: 10px;
           margin-bottom: 20px;
       }
       .comments .box-container .box .user {
           display: flex;
           align-items: center;
           margin-bottom: 10px;
       }
       .comments .box-container .box .user img {
           width: 50px;
           height: 50px;
           border-radius: 50%;
           margin-right: 10px;
       }
       .comments .box-container .box .user h3 {
           margin: 0;
           font-size: 18px;
       }
       .comments .box-container .box .user span {
           font-size: 14px;
           color: #aaa;
       }
       .comments .box-container .box .comment-box {
           font-size: 16px;
           margin-bottom: 10px;
       }
       .comments .box-container .box .flex-btn {
           display: flex;
           gap: 10px;
       }
       .comments .box-container .box .flex-btn .inline-option-btn,
       .comments .box-container .box .flex-btn .inline-delete-btn {
           background: #007bff;
           color: #fff;
           padding: 10px 20px;
           border: none;
           border-radius: 5px;
           cursor: pointer;
           transition: background 0.3s;
       }
       .comments .box-container .box .flex-btn .inline-delete-btn {
           background: #dc3545;
       }
       .comments .box-container .box .flex-btn .inline-option-btn:hover,
       .comments .box-container .box .flex-btn .inline-delete-btn:hover {
           background: #0056b3;
       }
       .comments .box-container .box .flex-btn .inline-delete-btn:hover {
           background: #c82333;
       }
       footer.footer {
           background: #333;
           color: #fff;
           padding: 20px 0;
           text-align: center;
       }
       footer.footer .social-icon {
           margin: 0 10px;
           color: #fff;
           text-decoration: none;
           transition: color 0.3s;
       }
       footer.footer .social-icon:hover {
           color: #007bff;
       }
   </style>
</head>
<body>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="assets/tutor/Professor Marc Badia.jpeg " class="image" alt="">
      <h3 class="name">Prof. Marc Badia</h3>
      <p class="role">Tutor</p>
      <a href="profile.html" class="btn">View Profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>About</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="teacher_profile.html"><i class="fas fa-chalkboard-user"></i><span>Teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="watch-video">
   <div class="video-container">
      <div class="video">
         <video src="assets/Acc-Videos/Accounting Introduction .mp4 " controls poster="images/Accounting Image.png" id="video"></video>
      </div>
      <h3 class="title">Accounting tutorial (part 02)</h3>
      <div class="info">
         <p class="date"><i class="fas fa-calendar"></i><span>22-10-2023</span></p>
         <p class="date"><i class="fas fa-heart"></i><span>44 likes</span></p>
      </div>

      <div class="tutor">
         <img src="assets/images_courses/Accounting Image.png" alt="">
         <div>
            <h3>xxxxx</h3>
            <span>Recording Beginning Balances of a company</span>
         </div>
      </div>
      <p class="description">
         <!-- The video description or notes section can be hidden initially -->
         <button onclick="toggleNotes()" class="inline-btn">View Notes</button>
         <div id="notes" style="display:none;">
            <h3>
               Students’ requirements:
               You will need no background in any of these accounting areas to benefit from this course. This course is designed and prepared for 11th grade complete, university and/or college students and graduates, business and non-business managers. Whether you are a professional person looking to develop new skills, or a person looking to start a new career in accounting or finance, or an auditor and decision maker; this course will equip you with what you need to get started in financial basics skill full.

               Training resources (Training Materials):
               This course is taught in classrooms and online, in English, Tigrinya and Amharic Languages. To get the most from this course, you will be given handouts, short notes, practice exercises, sample video and quiz in English language. And there will be midterm and final exams to complete the course in English language. During and after completion of the course, there will be discussion forums to connect with your peers created on WhatsApp and Telegram.

               Time frame:
               Over all of this principle of accounting course, it took four months to complete for both the theoretical and software peach tree courses. It is scheduled two days per week which is two hours period per day. There will be crash courses that could be able to complete the course within one month and two weeks.

               Future plan:
               In the near future, it is planned to start training on:
               • Financial accounting, financial management, cost accounting, and auditing.
               • Tally and quick book accounting software
               • Basic electricity and electronics that gives skills on maintaining electrical and electronic equipment, mobiles(cellphones), electrical installation and so on. This will go up to maintaining electrical motors and controls of manufacturing, electrical motors windings, etc.
               • Information Technology: programming, Database management, web development etc.

               Congratulations on beginning this exciting journey.
               Good luck!
            </h3>
            <!-- The rest of the notes go here -->
         </div>
      </p>
   </div>
</section>

<section class="comments">
   <h1 class="heading">Comments</h1>

   <form action="" class="add-comment" method="post">
      <h3>Your Comments</h3>
      <textarea name="comment_box" placeholder="Enter your comment" required maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="Add Comment" class="inline-btn" name="add_comment">
   </form>

   <h1 class="heading">User Comments</h1>
   
   <div class="box-container">
      <div class="box">
         <div class="user">
            <img src="images/pic-1.jpg" alt="">
            <div>
               <h3>Name</h3>
               <span>22-10-2023</span>
            </div>
         </div>
         <div class="comment-box">This is a comment from Mr. Desbele</div>
         <form action="" class="flex-btn" method="post">
            <input type="submit" value="Edit Comment" name="edit_comment" class="inline-option-btn">
            <input type="submit" value="Delete Comment" name="delete_comment" class="inline-delete-btn">
         </form>
      </div>

      <!-- Additional comment boxes here -->

   </div>
</section>

<footer class="footer bg-dark text-white">
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center py-4">
        <p>&copy; 2024 Gerar Isaac College Online Courses. All rights reserved.</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-center py-1">
        <a href="https://www.facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
        <a href="https://web.whatsapp.com" target="_blank" class="social-icon"><i class="fab fa-whatsapp"></i></a>
        <a href="https://www.viber.com" target="_blank" class="social-icon"><i class="fab fa-viber"></i></a>
        <a href="https://www.youtube.com" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
        <a href="https://web.telegram.org" target="_blank" class="social-icon"><i class="fab fa-telegram-plane"></i></a>
      </div>
    </div>
  </div>
</footer>

<script>
function toggleNotes() {
   var notes = document.getElementById('notes');
   if (notes.style.display === 'none') {
      notes.style.display = 'block';
   } else {
      notes.style.display = 'none';
   }
}
</script>

</body>
</html>


hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh
<?php
include('db.php');
include('header.php');

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = "Default Name"; // Temporary fix; ensure this is set correctly during login
}

// Fetch courses from the database
try {
    $stmt = $pdo->query("SELECT * FROM courses LIMIT 10"); // Fetch only 10 courses for pagination
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Watch Video</title>

   <!-- Font Awesome CDN Link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS File Link  -->
   <link rel="stylesheet" href="assets/css/style.css">
   <style>
       body {
           background-color: #f5f5f5;
           font-family: Arial, sans-serif;
       }
       .side-bar {
           position: fixed;
           width: 250px;
           height: 100%;
           background: #333;
           color: #fff;
           padding: 20px;
           box-shadow: 2px 0 5px rgba(0,0,0,0.1);
       }
       .side-bar .profile {
           text-align: center;
           margin-bottom: 20px;
       }
       .side-bar .profile img {
           width: 100px;
           border-radius: 50%;
       }
       .side-bar .profile .name {
           font-size: 18px;
           margin-top: 10px;
       }
       .side-bar .profile .role {
           font-size: 14px;
           color: #aaa;
       }
       .side-bar .navbar a {
           display: block;
           padding: 10px;
           color: #fff;
           text-decoration: none;
           margin-bottom: 10px;
           border-radius: 5px;
           transition: background 0.3s;
       }
       .side-bar .navbar a:hover {
           background: #555;
       }
       .watch-video {
           margin-left: 270px;
           padding: 20px;
       }
       .watch-video .video-container {
           background: #fff;
           padding: 20px;
           box-shadow: 0 0 10px rgba(0,0,0,0.1);
           border-radius: 10px;
       }
       .watch-video .title {
           font-size: 24px;
           margin-top: 10px;
       }
       .comments {
           margin-top: 30px;
       }
       .comments .heading {
           font-size: 24px;
           margin-bottom: 20px;
       }
       .comments .add-comment textarea {
           width: 100%;
           padding: 10px;
           border: 1px solid #ccc;
           border-radius: 5px;
           margin-bottom: 10px;
       }
       .comments .add-comment .inline-btn {
           background: #007bff;
           color: #fff;
           padding: 10px 20px;
           border: none;
           border-radius: 5px;
           cursor: pointer;
           transition: background 0.3s;
       }
       .comments .add-comment .inline-btn:hover {
           background: #0056b3;
       }
       .comments .box-container .box {
           background: #fff;
           padding: 20px;
           box-shadow: 0 0 10px rgba(0,0,0,0.1);
           border-radius: 10px;
           margin-bottom: 20px;
       }
       .comments .box-container .box .user {
           display: flex;
           align-items: center;
           margin-bottom: 10px;
       }
       .comments .box-container .box .user img {
           width: 50px;
           height: 50px;
           border-radius: 50%;
           margin-right: 10px;
       }
       .comments .box-container .box .user h3 {
           margin: 0;
           font-size: 18px;
       }
       .comments .box-container .box .user span {
           font-size: 14px;
           color: #aaa;
       }
       .comments .box-container .box .comment-box {
           font-size: 16px;
           margin-bottom: 10px;
       }
       .comments .box-container .box .flex-btn {
           display: flex;
           gap: 10px;
       }
       .comments .box-container .box .flex-btn .inline-option-btn,
       .comments .box-container .box .flex-btn .inline-delete-btn {
           background: #007bff;
           color: #fff;
           padding: 10px 20px;
           border: none;
           border-radius: 5px;
           cursor: pointer;
           transition: background 0.3s;
       }
       .comments .box-container .box .flex-btn .inline-delete-btn {
           background: #dc3545;
       }
       .comments .box-container .box .flex-btn .inline-option-btn:hover,
       .comments .box-container .box .flex-btn .inline-delete-btn:hover {
           background: #0056b3;
       }
       .comments .box-container .box .flex-btn .inline-delete-btn:hover {
           background: #c82333;
       }
       footer.footer {
           background: #333;
           color: #fff;
           padding: 20px 0;
           text-align: center;
       }
       footer.footer .social-icon {
           margin: 0 10px;
           color: #fff;
           text-decoration: none;
           transition: color 0.3s;
       }
       footer.footer .social-icon:hover {
           color: #007bff;
       }
   </style>
</head>
<body>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="images/photo_Tsegay.jpg" class="image" alt="">
      <h3 class="name">Prof. Marc Badia</h3>
      <p class="role">Tutor</p>
      <a href="profile.html" class="btn">View Profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>About</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="teacher_profile.html"><i class="fas fa-chalkboard-user"></i><span>Teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="watch-video">
   <div class="video-container">
      <div class="video">
         <video src="assets/Acc-Videos/Accounting Introduction .mp4 " controls poster="images/Accounting Image.png" id="video"></video>
      </div>
      <h3 class="title">Accounting tutorial (part 02)</h3>
      <div class="info">
         <p class="date"><i class="fas fa-calendar"></i><span>22-10-2023</span></p>
         <p class="date"><i class="fas fa-heart"></i><span>44 likes</span></p>
      </div>

      <div class="tutor">
         <img src="assets/images_courses/Accounting Image.png" alt="">
         <div>
            <h3>xxxxx</h3>
            <span>Recording Beginning Balances of a company</span>
         </div>
      </div>
      <p class="description">
         <!-- The video description or notes section can be hidden initially -->
         <button onclick="toggleNotes()" class="inline-btn">View Notes</button>
         <div id="notes" style="display:none;">
            <h3>
               Students’ requirements:
               You will need no background in any of these accounting areas to benefit from this course. This course is designed and prepared for 11th grade complete, university and/or college students and graduates, business and non-business managers. Whether you are a professional person looking to develop new skills, or a person looking to start a new career in accounting or finance, or an auditor and decision maker; this course will equip you with what you need to get started in financial basics skill full.

               Training resources (Training Materials):
               This course is taught in classrooms and online, in English, Tigrinya and Amharic Languages. To get the most from this course, you will be given handouts, short notes, practice exercises, sample video and quiz in English language. And there will be midterm and final exams to complete the course in English language. During and after completion of the course, there will be discussion forums to connect with your peers created on WhatsApp and Telegram.

               Time frame:
               Over all of this principle of accounting course, it took four months to complete for both the theoretical and software peach tree courses. It is scheduled two days per week which is two hours period per day. There will be crash courses that could be able to complete the course within one month and two weeks.

               Future plan:
               In the near future, it is planned to start training on:
               • Financial accounting, financial management, cost accounting, and auditing.
               • Tally and quick book accounting software
               • Basic electricity and electronics that gives skills on maintaining electrical and electronic equipment, mobiles(cellphones), electrical installation and so on. This will go up to maintaining electrical motors and controls of manufacturing, electrical motors windings, etc.
               • Information Technology: programming, Database management, web development etc.

               Congratulations on beginning this exciting journey.
               Good luck!
            </h3>
            <!-- The rest of the notes go here -->
         </div>
      </p>
   </div>
</section>

<section class="comments">
   <h1 class="heading">Comments</h1>

   <form action="" class="add-comment" method="post">
      <h3>Your Comments</h3>
      <textarea name="comment_box" placeholder="Enter your comment" required maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="Add Comment" class="inline-btn" name="add_comment">
   </form>

   <h1 class="heading">User Comments</h1>
   
   <div class="box-container">
      <div class="box">
         <div class="user">
            <img src="images/pic-1.jpg" alt="">
            <div>
               <h3>Name</h3>
               <span>22-10-2023</span>
            </div>
         </div>
         <div class="comment-box">This is a comment from Mr. Desbele</div>
         <form action="" class="flex-btn" method="post">
            <input type="submit" value="Edit Comment" name="edit_comment" class="inline-option-btn">
            <input type="submit" value="Delete Comment" name="delete_comment" class="inline-delete-btn">
         </form>
      </div>

      <!-- Additional comment boxes here -->

   </div>
</section>

<footer class="footer bg-dark text-white">
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center py-4">
        <p>&copy; 2024 Gerar Isaac College Online Courses. All rights reserved.</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-center py-1">
        <a href="https://www.facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
        <a href="https://web.whatsapp.com" target="_blank" class="social-icon"><i class="fab fa-whatsapp"></i></a>
        <a href="https://www.viber.com" target="_blank" class="social-icon"><i class="fab fa-viber"></i></a>
        <a href="https://www.youtube.com" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
        <a href="https://web.telegram.org" target="_blank" class="social-icon"><i class="fab fa-telegram-plane"></i></a>
      </div>
    </div>
  </div>
</footer>

<script>
function toggleNotes() {
   var notes = document.getElementById('notes');
   if (notes.style.display === 'none') {
      notes.style.display = 'block';
   } else {
      notes.style.display = 'none';
   }
}
</script>

</body>
</html>

hhh
<?php

include('db.php');
include('header.php');


if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = "Default Name"; // Temporary fix; ensure this is set correctly during login
}

// Fetch courses from the database
$sql = "SELECT * FROM courses";




?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Watch Video</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="assets/tutor/Professor Marc Badia.jpeg" class="image" alt="">
      <h3 class="name">Prof. Marc Badia</h3>
      <p class="role">Tutor</p>
      <a href="profile.html" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teacher_profile.html"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>
</div>

<section class="watch-video">
   <div class="video-container">
      <div class="video">
         <video src="assets/Acc-Videos/Accounting Introduction .mp4 " controls poster="images/Accounting Image.png" id="video"></video>
      </div>
      <h3 class="title">Accounting tutorial (part 02)</h3>
      <div class="info">
         <p class="date"><i class="fas fa-calendar"></i><span>22-10-2023</span></p>
         <p class="date"><i class="fas fa-heart"></i><span>44 likes</span></p>
      </div>

      <div class="tutor">
         <img src="assets/images_courses/Accounting Image.png" alt="">
         <div>
            <h3>xxxxx</h3>
            <span>Recording Begining Balances of a company</span>
         </div>
      </div>
      <form action="" method="post" class="flex">
         <a href="playlist.php" class="inline-btn">view playlist</a>
         <button><i class="far fa-heart"></i><span>like</span></button>
      </form>
<p class="description">
   <!-- The video description or notes section can be hidden initially -->
   <button onclick="toggleNotes()" class="inline-btn">View Notes</button>
   <div id="notes" style="display:none;">
  <h3> 
Students’ requirements:
You will need no background in any of these accounting areas to benefit from this course. This course is designed and prepared for 11th grade complete, university and/or college students and graduates, business and non-business managers. Whether you are professional person looking to develop new skills, or a person looking to start a new career in accounting or finance, or auditor and decision maker; this course will equip you with what you need to get started in financial basics skill full.

Training resources (Training Materials):
This course is taught in classrooms and online, in English, Tigrinya and Amharic Languages. To get the most from this course, you will be given handouts, short notes, practice exercises, sample video and quiz in English language. And there will be midterm and final exam to complete the course in English language. During and after completion of the course, there will be discussion forums to connect with your peers created on whatsup and telegram.

Time frame:
Over all of this principle of accounting course, it took four months to complete for both the theoretical and software peach tree courses. It is scheduled two days per week which is two hours period per day. There will be crash courses that could be able to completed the course within one month and two weeks.

Future plan:
In the near future it is planned to start training on:
•	Financial accounting, financial management, cost accounting, and auditing.
•	Tally and quick book accounting software
•	Basic electricity and electronics that gives skill on maintaining electrical and electronic equipments, mobiles(cellphones), electrical installation and so on. This will go up to maintaining electrical motors and controls of manufacturing, electrical motors windings, etc.
•	Information Technology: programming, Database management, web development etc.

Congratulations on beginning of this exciting journey.
Good luck!                                                                                         .

</h3>                                                    
<!-- The rest of the notes go here -->
</div>
</p>
</div>
</section>

<section class="comments">
   <h1 class="heading">comments</h1>

   <form action="" class="add-comment" method="post">
      <h3>Your comments</h3>
      <textarea name="comment_box" placeholder="enter your comment" required maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="add comment" class="inline-btn" name="add_comment">
   </form>

   <h1 class="heading">user comments</h1>
   
   <div class="box-container">
      <div class="box">
         <div class="user">
            <img src="images/pic-1.jpg" alt="">
            <div>
               <h3>name </h3>
               <span>22-10-2023</span>
            </div>
         </div>
         <div class="comment-box">this is a comment form Mr. Desbele</div>
         <form action="" class="flex-btn" method="post">
            <input type="submit" value="edit comment" name="edit_comment" class="inline-option-btn">
            <input type="submit" value="delete comment" name="delete_comment" class="inline-delete-btn">
         </form>
      </div>

      <!-- Additional comment boxes here -->

   </div>
</section>

<footer class="footer">
 

<footer class="footer bg-dark text-white">
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center py-4">
        <p>&copy; 2024 Gerar Isaac College Online Courses. All rights reserved.</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-center py-1">
        <a href="https://www.facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
        <a href="https://web.whatsapp.com" target="_blank" class="social-icon"><i class="fab fa-whatsapp"></i></a>
        <a href="https://www.viber.com" target="_blank" class="social-icon"><i class="fab fa-viber"></i></a>
        <a href="https://www.youtube.com" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
        <a href="https://web.telegram.org" target="_blank" class="social-icon"><i class="fab fa-telegram-plane"></i></a>
      </div>
</footer>

kkkkkkkkkk Header

<?php
include('db.php');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_image FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $full_name = $user['first_name'] . ' ' . $user['last_name'];
        // Set the image path from the database
        $user_image = $user['profile_image'] ? $user['profile_image'] : 'path/to/default/image.jpg';
    } else {
        // Redirect if user is not found
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac College</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Link to your custom CSS -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Gerar Isaac College</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="courses.php">Courses</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="exam_questions.php">Exams</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
      <span class="navbar-text user-info">
        <img src="<?= htmlspecialchars($user_image) ?>" alt="User Image" class="user-image">
        <?= htmlspecialchars($full_name) ?> (ID: <?= htmlspecialchars($user_id) ?>)
      </span>
    </div>
  </div>
</nav>

<style>
.user-info {
    display: flex;
    align-items: center;
    color: #00d1b2; /* Unique color for user info */
    margin-left: auto;
}

.user-info .user-image {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-right: 10px;
}
</style>
</body>
</html>
kkkkkkkkkkkkk Header

<?php

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Gerar Isaac College</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="courses.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="exam_selection.php">Exams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>

            <!-- Display user info if logged in -->
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['full_name']) && isset($_SESSION['user_image'])): ?>
                <span class="navbar-text user-info">
                    <img src="<?= htmlspecialchars($_SESSION['user_image']); ?>" alt="User Image" class="user-image">
                    <?= htmlspecialchars($_SESSION['full_name']); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.user-info {
    display: flex;
    align-items: center;
    color: #00d1b2;
    margin-left: auto;
}

.user-info .user-image {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-right: 10px;
}
</style>




kkkkkkkkkkkkkkk playlist.php
<?php
session_start();
include('db.php'); // Ensure this file sets up $pdo
include('header_loggedin.php');

// Fetch courses from the database
try {
    $sql = "SELECT * FROM courses";
    $stmt = $pdo->query($sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* CSS to ensure header font size is visible on courses.php */
        .courses-page .navbar-brand {
            font-size: 2.95rem; /* Adjust as needed */
        }
        .courses-page .nav-link {
            font-size: 1.45rem; /* Adjust as needed */
        }
        .courses-page .navbar-nav .nav-item {
            padding: 0.5rem 1rem; /* Adjust padding for better spacing */
        }
    </style>
</head>

<body>
    <header class="header">
        <section class="flex">
            <a href="home.php" class="logo">View Acc.101 Courses</a>
            <form action="search.html" method="post" class="search-form">
                <input type="text" name="search_box" required placeholder="Search courses..." maxlength="100">
                <button type="submit" class="fas fa-search"></button>
            </form>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>
            <div class="profile">
                <img src="assets/tutor/Habtom.jpg" class="image" alt="Tutor Image">
                <h3 class="name">Habtom Araya-ACCA</h3>
                <p class="role">Tutor</p>
                <a href="profile.html" class="btn">View Profile</a>
                <div class="flex-btn">
                    <a href="login.html" class="option-btn">Login</a>
                    <a href="register.html" class="option-btn">Register</a>
                </div>
            </div>
        </section>
    </header>

    <div class="side-bar">
        <div id="close-btn">
            <i class="fas fa-times"></i>
        </div>
        <div class="profile">
            <img src="assets/tutor/Habtom.jpg" class="image" alt="Tutor Image">
            <h3 class="name">View Acc.101 Courses</h3>
            <a href="company_profile.php" class="btn">View Profile</a>
        </div>
        <nav class="navbar">
            <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="about.php"><i class="fas fa-question"></i><span>About</span></a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
            <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>Teachers</span></a>
            <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
        </nav>
    </div>

    <section class="playlist-details">
        <h1 class="heading">Playlist Details</h1>
        <div class="row">
            <div class="column">
                <form action="" method="post" class="save-playlist">
                    <button type="submit"><i class="far fa-bookmark"></i> <span>Save Playlist</span></button>
                </form>
                <div class="thumb">
                    <img src="assets/images_courses/Accounting Image.png" alt="Thumbnail">
                    <span>10 videos</span>
                </div>
            </div>
            <div class="column">
                <div class="tutor">
                    <img src="assets/tutor/Habtom.jpg" class="image" alt="Tutor Image">
                    <div>
                        <h3>Tutor Habtom Araya-ACCA</h3>
                        <span>01-02-2023</span>
                    </div>
                </div>
                <div class="details">
                    <h3>Complete Accounting Courses</h3>
                    <h4>
                        Contents and Main Objective of Accounting Courses<br>
                        Contents:<br>
                        * Introduction to Accounting:<br> Basic principles, concepts, and the accounting cycle.<br>
                        * Financial Accounting:<br> Preparation and analysis of financial statements.<br>
                        * Managerial Accounting:<br> Budgeting, cost analysis, and performance evaluation.<br>
                        * Tax Accounting: Tax preparation, compliance, and planning.<br>
                        * Auditing: Internal and external audit procedures, standards, and ethics.<br>
                        * Accounting Information Systems:<br> Use of technology in accounting.<br>
                        * Advanced Accounting: Complex financial transactions and consolidations.<br>
                        * Ethics in Accounting: Professional ethics and regulatory requirements.<br>
                        * Main Objective:<br>
                        To equip students with the knowledge and skills necessary to accurately record,
                        analyze, and report financial information.
                    </h4>
                    <a href="company_profile.php" class="inline-btn">View Profile</a>
                </div>
            </div>
        </div>
    </section>

    <section class="playlist-videos">
        <h1 class="heading">Course Videos</h1>
        <div class="box-container">
            <a class="box" href="watch-video acc-02.php">
                <i class="fas fa-play"></i>
                <img src="assets/images_courses/Accounting Image.png" alt="Video Thumbnail">
                <h2>Accounting Tutorial (Part 01) General Accounting Information</h2>
            </a>
            <a class="box" href="watch-video_acc-05.php">
                <i class="fas fa-play"></i>
                <img src="assets/images_courses/Accounting Image.png" alt="Video Thumbnail">
                <h2>Accounting Principles (Part 02)</h2>
            </a>
            <a class="box" href="watch-video acc-01.php">
                <i class="fas fa-play"></i>
                <img src="assets/images_courses/Accounting Image.png" alt="Video Thumbnail">
                <h3>Accounting Tutorial (Part 03) Recording Beginning Balances</h3>
            </a>
            <a class="box" href="watch-video_acc-04.php">
                <i class="fas fa-play"></i>
                <img src="assets/images_courses/Accounting Image.png" alt="Video Thumbnail">
                <h3>Accounting Tutorial (Part 04) Using PHP Udemy</h3>
            </a>
            <a class="box" href="watch-video.html">
                <i class="fas fa-play"></i>
                <img src="assets/images_courses/Accounting Image.png" alt="Video Thumbnail">
                <h3>Accounting Tutorial (Part 06)</h3>
            </a>
        </div>
    </section>

    <footer class="footer">
        &copy; Copyright @ 2023 by <span>Web Designer Habtom Araya-ACCA</span> | All rights reserved!
    </footer>

    <!-- Custom JS file link -->
    <script src="js/script.js"></script>
</body>
</html>


kkkkkkkkkkkkk
<?php
session_start();
include('db.php');
include('header.php');

// Fetch courses from the database
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);

if ($result === false) {
   die('Error: ' . htmlspecialchars($conn->error));
}

$courses = [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>video playlist</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="assets/css/style.css">
   <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="assets/03-styles.css">
    <style>
        /* CSS to ensure header font size is visible on courses.php */
        .courses-page .navbar-brand {
            font-size: 2.95rem; /* Adjust as needed */
        }

        .courses-page .nav-link {
            font-size: 1.45rem; /* Adjust as needed */
        }
        
        .courses-page .navbar-nav .nav-item {
            padding: 0.5rem 1rem; /* Adjust padding for better spacing */
        }
    </style>
</head> 

</head>

<body>

   <header class="header">

      <section class="flex">

         <a href="home.php" class="logo">view Acc.101 courses</a>

         <form action="search.html" method="post" class="search-form">
            <input type="text" name="search_box" required placeholder="search courses..." maxlength="100">
            <button type="submit" class="fas fa-search"></button>
         </form>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="toggle-btn" class="fas fa-sun"></div>
         </div>

         <div class="profile">
            <img src="assets/tutor/Habtom.jpg" class="image" alt="">
            <h3 class="name">Habtom Araya-ACCA</h3>
            <p class="role">Tutor</p>
            <a href="profile.html" class="btn">view profile</a>
   
            
            <div class="flex-btn">
               <a href="login.html" class="option-btn">login</a>
               <a href="register.html" class="option-btn">register</a>
            </div>
         </div>

      </section>

   </header>

   <div class="side-bar">

      <div id="close-btn">
         <i class="fas fa-times"></i>
      </div>

      <div class="profile">
         
      <img src="assets/tutor/Habtom.jpg" class="image" alt="">
         <h3 class="name">view Acc.101 courses</h3>
         <p class="role"></p>
         <a href="company_profile.php" class="btn">view profile</a>
      </div>
    
   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>

   <section class="playlist-details">

      <h1 class="heading">playlist details</h1>

      <div class="row">

         <div class="column">
            <form action="" method="post" class="save-playlist">
               <button type="submit"><i class="far fa-bookmark"></i> <span>save playlist</span></button>
            </form>

            <div class="thumb">
               <img src="assets/images_courses/Accounting Image.png" alt="">
               <span>10 videos</span>
            </div>
         </div>
         <div class="column">
            <div class="tutor">
            <img src="assets/tutor/Habtom.jpg" class="image" alt="">
               <div>
                  <h3>Tutor Habtom Araya-ACCA</h3>
                  <span>01-02-2023</span>
               </div>
            </div>

            <div class="details">
               <h3>Complete Accounting courses</h3>
               <h4>
                  Contents and Main Objective of Accounting Courses<br>
                  Contents:<br>

                  * Introduction to Accounting: <br>Basic principles, concepts, and the accounting cycle.<br>
                  * Financial Accounting: <br>Preparation and analysis of financial statements, including balance sheets, income statements, and cash flow statements.<br>
                  * Managerial Accounting:<br> Budgeting, cost analysis, and performance evaluation.<br>
                  * Tax Accounting: Tax preparation, compliance, and planning.<br>
                  * Auditing: Internal and external audit procedures, standards, and ethics.<br>
                  *  Accounting Information Systems: <br>Use of technology in accounting, including software and data management.<br>
                  * Advanced Accounting: Complex financial transactions, consolidations, and international accounting standards.<br>
                  * Ethics in Accounting: Professional ethics and regulatory requirements.<br>
                  * Main Objective:<br>
                  To equip students with the knowledge and skills necessary to accurately record,
                  analyze, and report financial information, enabling effective decision-making and
                  ensuring compliance with financial regulations.

               </h4>
               <a href="company_profile.php" class="inline-btn">view profile</a>
            </div>
         </div>
      </div>

   </section>

   <section class="playlist-videos">

      <h1 class="heading">Courses videos</h1>

      <div class="box-container">
     

         <a class="box" href="watch-video acc-02.php">
            <i class="fas fa-play"></i>
            <img src="assets/images_courses/Accounting Image.png" alt="">
            <h2>Accounting tutorial (part 01) <br>General Accounting Information </h2>
         
         <a class="box" href="watch-video acc-05.php">
            <i class="fas fa-play"></i>
            <img src="assets/images_courses/Accounting Image.png" alt="">
            <h2>Accounting principles(part 02)</h2>
             
            <a class="box" href="watch-video acc-01.php">
            <i class="fas fa-play"></i>
            <img src="assets/images_courses/Accounting Image.png" alt="">
            <h3>Accounting tutorial (part 03) Recording Beginning balances of a company</h3>
         </a>
         <a class="box" href="watch-video acc-01.php">
            <i class="fas fa-play"></i>
            <img src="assets/images_courses/Accounting Image.png" alt="">
            <h3>Accounting tutorial (part 03) Recording Beginning balances of a company</h3>
            
         <a class="box" href="watch-videos acc-04.php">
            <i class="fas fa-play"></i>
            <img src="assets/images_courses/Accounting Image.png" alt="">
            <h3>Accounting tutorial (part 04) using php udmy </h3>
         </a>

       
         <a class="box" href="watch-video.html">
            <i class="fas fa-play"></i>
            <img src="assets/images_courses/Accounting Image.png" alt="">
            <h3>Accounting tutorial (part 06)</h3>
         </a>

      </div>

   </section>

   <footer class="footer">

      &copy; copyright @ 2023 by <span>Web Designer Habtom Araya-ACCA</span> | all rights reserved!

   </footer>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>


</body>

</html>
kkkk

<?php
session_start();
include('db.php');
include('header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = "Default Name"; // Temporary fix; ensure this is set correctly during login
}

$video_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($video_id <= 0) {
    echo "Invalid video ID.";
    exit;
}

// Fetch video details from the database
$stmt = $conn->prepare("SELECT * FROM videos WHERE id = :video_id");
$stmt->bindParam(':video_id', $video_id, PDO::PARAM_INT);
$stmt->execute();

$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    echo "Invalid video ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Watch Video</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="assets/css/style.css">
   <style>
        /* CSS to ensure header font size is visible on courses.php */
        .courses-page .navbar-brand {
            font-size: 2.95rem; /* Adjust as needed */
        }

        .courses-page .nav-link {
            font-size: 1.45rem; /* Adjust as needed */
        }
        
        .courses-page .navbar-nav .nav-item {
            padding: 0.5rem 1rem; /* Adjust padding for better spacing */
        }
</head>
<body>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="images/pic-1.jpg" class="image" alt="">
      <h3 class="name">Habtom Araya-ACCA</h3>
      <p class="role">Tutor</p>
      <a href="profile.html" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teacher_profile.html"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>
</div>

<section class="watch-video">
   <div class="video-container">
      <div class="video">
         <video src="<?php echo htmlspecialchars($video['video{']); ?>" controls poster="assets/images_courses/Peach Tree/Peachtree-logo.jpg" id="video"></video>
      </div>
      <h3 class="title"><?php echo htmlspecialchars($video['title']); ?></h3>
      <div class="info">
         <p class="date"><i class="fas fa-calendar"></i><span><?php echo htmlspecialchars($video['date']); ?></span></p>
         <p class="date"><i class="fas fa-heart"></i><span>44 likes</span></p>
      </div>

      <form action="" method="post" class="flex">
         <a href="playlist.html" class="inline-btn">view playlist</a>
         <button><i class="far fa-heart"></i><span>like</span></button>
      </form>
<p class="description">
   <!-- The video description or notes section can be hidden initially -->
   <button onclick="toggleNotes()" class="inline-btn">View Notes</button>
   <div id="notes" style="display:none;">
  <h3> 
Students’ requirements:
You will need no background in any of these accounting areas to benefit from this course. This course is designed and prepared for 11th grade complete, university and/or college students and graduates, business and non-business managers. Whether you are professional person looking to develop new skills, or a person looking to start a new career in accounting or finance, or auditor and decision maker; this course will equip you with what you need to get started in financial basics skill full.

Training resources (Training Materials):
This course is taught in classrooms and online, in English, Tigrinya and Amharic Languages. To get the most from this course, you will be given handouts, short notes, practice exercises, sample video and quiz in English language. And there will be midterm and final exam to complete the course in English language. During and after completion of the course, there will be discussion forums to connect with your peers created on whatsup and telegram.

Time frame:
Over all of this principle of accounting course, it took four months to complete for both the theoretical and software peach tree courses. It is scheduled two days per week which is two hours period per day. There will be crash courses that could be able to completed the course within one month and two weeks.

Future plan:
In the near future it is planned to start training on:
•	Financial accounting, financial management, cost accounting, and auditing.
•	Tally and quick book accounting software
•	Basic electricity and electronics that gives skill on maintaining electrical and electronic equipments, mobiles(cellphones), electrical installation and so on. This will go up to maintaining electrical motors and controls of manufacturing, electrical motors windings, etc.
•	Information Technology: programming, Database management, web development etc.

Congratulations on beginning of this exciting journey.
Good luck!                                                                                         .

</h3>                                                    
<!-- The rest of the notes go here -->
</div>
</p>
</div>
</section>

<section class="comments">
   <h1 class="heading">comments</h1>

   <form action="" class="add-comment" method="post">
      <h3>Your comments</h3>
      <textarea name="comment_box" placeholder="enter your comment" required maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="add comment" class="inline-btn" name="add_comment">
   </form>

   <h1 class="heading">user comments</h1>
   
   <div class="box-container">
      <div class="box">
         <div class="user">
            <img src="images/pic-1.jpg" alt="">
            <div>
               <h3>name </h3>
               <span>22-10-2023</span>
            </div>
         </div>
         <div class="comment-box">this is a comment form Mr. Desbele</div>
         <form action="" class="flex-btn" method="post">
            <input type="submit" value="edit comment" name="edit_comment" class="inline-option-btn">
            <input type="submit" value="delete comment" name="delete_comment" class="inline-delete-btn">
         </form>
      </div>

      <!-- Additional comment boxes here -->

   </div>
</section>

<footer class="footer">
   &copy; copyright @ 2023 by <span>Habtom Araya-ACCA Web designer</span> | all rights reserved!
</footer>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script>
function toggleNotes() {
    var notes = document.getElementById("notes");
    if (notes.style.display === "none") {
        notes.style.display = "block";
    } else {
        notes.style.display = "none";
    }
}
</script>

<?php
if (isset($_POST['add_comment'])) {
    $student_name = $_SESSION['user_name']; // Assuming you have the user's name stored in the session
    $comment = $_POST['comment_box'];
    $playlist_id = 1; // Replace with actual playlist ID if needed

    try {
        $stmt = $conn->prepare("INSERT INTO comments (playlist_id, student_name, comments) VALUES (:playlist_id, :student_name, :comments)");
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->bindParam(':student_name', $student_name);
        $stmt->bindParam(':comments', $comments);
        $stmt->execute();
        echo "Comment submitted successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<section class="comments-display">
    <h2>Comments</h2>
    <?php
    // Ensure $pdo is initialized correctly
    include('db.php');

    // Assuming $playlist_id is set correctly somewhere in your script
    $playlist_id = 1; // Replace with the actual playlist ID if needed

    try {
        $stmtComments = $pdo->prepare("SELECT * FROM comments WHERE playlist_id = :playlist_id ORDER BY created_at DESC");
        $stmtComments->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
        $stmtComments->execute();
        $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comments as $comment) {
            echo "<div class='comment'>";
            echo "<h3>" . htmlspecialchars($comment['student_name']) . "</h3>";
            echo "<p>" . htmlspecialchars($comment['comments']) . "</p>";
            echo "<span>" . htmlspecialchars($comment['created_at']) . "</span>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</section>


kkkkkkkkk
<?php

include('db.php'); // Include your database connection script

// Redirect users who are not logged in
if (!isset($_SESSION['user_id'])) {
  
    exit;
}

// Ensure course_id is provided
if (!isset($_GET['course_id'])) {
    die('Course ID not provided.');
}

$course_id = intval($_GET['course_id']);
$user_id = $_SESSION['user_id'];

// Prepare and execute the SQL statement using PDO
$sql = "SELECT * FROM courses WHERE course_id = :course_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);

if (!$stmt->execute()) {
    die('SQL Execution Error: ' . htmlspecialchars($stmt->errorInfo()[2]));
}

$course = $stmt->fetch(PDO::FETCH_ASSOC);

if ($course === false) {
    die('Course not found.');
}

include('header.php'); // Include the header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['course_title']); ?> - Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/03-styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .container {
            margin-top: 30px;
            max-width: 900px;
        }
        p, ul {
            text-align: justify;
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        ul {
            list-style-type: disc;
            padding-left: 40px;
        }
        iframe {
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .week-content {
            margin-bottom: 40px;
        }
        .toggle-notes {
            margin-top: 20px;
        }
        .notes {
            display: none;
        }
        header, footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
        footer p {
            font-size: 0.9em;
        }
        footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h2>Welcome to the Course Page</h2>
        <p>Explore our weekly breakdown with videos and notes.</p>
    </header>
    
    <div class="container">
        <!-- Display Course Title -->
        <h1><?php echo htmlspecialchars($course['course_title']); ?></h1>

        <!-- Display Course Description -->
        <p><?php echo htmlspecialchars($course['description']); ?></p>
        
        <!-- Example Weekly Breakdown with Videos and Notes -->
        <?php
        // Example of breaking content by week
        $weeks = [
            1 => ['video' => 'https://www.youtube.com/watch?v=uVGln5EiTuU', 'notes' => 'These are the notes for week 1.'],
            2 => ['video' => 'https://www.youtube.com/watch?v=uVGln5EiTuU', 'notes' => 'These are the notes for week 2.'],
            3 => ['video' => 'https://www.youtube.com/watch?v=uVGln5EiTuU', 'notes' => 'These are the notes for week 3.']
        ];
        
        foreach ($weeks as $week => $content): ?>
            <div class="week-content">
                <h2>Week <?php echo $week; ?></h2>
                
                <!-- Display the video -->
                <div id="video-week-<?php echo $week; ?>" class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="<?php echo htmlspecialchars($content['video']); ?>" allowfullscreen></iframe>
                </div>
                
                <!-- Toggle button to view notes -->
                <button class="btn btn-primary toggle-notes" data-week="<?php echo $week; ?>">View Course Notes</button>
                
                <!-- Display notes (hidden by default) -->
                <div id="notes-week-<?php echo $week; ?>" class="notes">
                    <p><?php echo htmlspecialchars($content['notes']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Gerar Isaac Training Center. All rights reserved.</p>
        <p>Contact us: <a href="mailto:info@gitc.com">info@gitc.com</a> | <a href="tel:+1234567890">+123 456 7890</a></p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript to toggle between showing video and notes
        document.querySelectorAll('.toggle-notes').forEach(button => {
            button.addEventListener('click', function() {
                const week = this.getAttribute('data-week');
                const notes = document.getElementById('notes-week-' + week);
                const video = document.getElementById('video-week-' + week);

                // Toggle visibility
                if (notes.style.display === 'none' || notes.style.display === '') {
                    notes.style.display = 'block';
                    this.textContent = 'Hide Course Notes';
                } else {
                    notes.style.display = 'none';
                    this.textContent = 'View Course Notes';
                }
            });
        });
    </script>
</body>
</html>

kkkkkkkkkk
<?php

include('db.php'); // Include your database connection script

// Redirect users who are not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ensure course_id is provided
if (!isset($_GET['course_id'])) {
    die('Course ID not provided.');
}

$course_id = intval($_GET['course_id']);
$user_id = $_SESSION['user_id'];

// Prepare and execute the SQL statement using PDO
$sql = "SELECT * FROM courses WHERE course_id = :course_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);

if (!$stmt->execute()) {
    die('SQL Execution Error: ' . htmlspecialchars($stmt->errorInfo()[2]));
}

$course = $stmt->fetch(PDO::FETCH_ASSOC);

if ($course === false) {
    die('Course not found.');
}

include('header.php'); // Include the header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['course_title']); ?> - Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/03-styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .container {
            margin-top: 30px;
            max-width: 900px;
        }
        p, ul {
            text-align: justify;
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        ul {
            list-style-type: disc;
            padding-left: 40px;
        }
        sub {
            font-size: 0.9em;
        }
        .embed-responsive {
            margin: 20px 0;
        }
        iframe {
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        /* Styling for header and footer */
        header, footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
        footer p {
            font-size: 0.9em;
        }
        footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h2>Welcome to the Course Page</h2>
        <p>Explore our detailed content and videos below.</p>
    </header>
    
    <div class="container">
        <!-- Display Course Title -->
        <h1><?php echo htmlspecialchars($course['course_title']); ?></h1>
        
        <!-- Display Course Description with Subsections -->
        <p><?php echo htmlspecialchars($course['description']); ?></p>
        
        <!-- Example of sub-paragraphs under the description -->
        <ul>
            <li>Key topics covered in this course:</li>
            <li>Comprehensive learning with in-depth modules</li>
            <li>Hands-on video tutorials and practical examples</li>
        </ul>

        <!-- Display Course Video (if available) -->
        <?php if (!empty($course['course_video'])): ?>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="<?php echo htmlspecialchars($course['course_video']); ?>" allowfullscreen></iframe>
            </div>
        <?php endif; ?>
        
        <!-- Display Course Notes -->
        <h2>Course Notes</h2>
        <p><?php echo htmlspecialchars($course['course_note']); ?></p>
    </div>
<!-- footer -->
   <?php include('footer.php');?>
      


</body>
</html>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

course video display 


kkkkkkkkk
<?php
$sql = "SELECT course_title, description, thumb_image, course_videos, course_note FROM courses WHERE course_id = ?";
// Database connection (adjust according to your setup)
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'accounting_course';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$course_id = $_GET['course_id']; // Get the course ID from the URL
$sql = "SELECT course_title, description, thumb_image, course_videos, course_note FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Check if the course exists
if (!$courses) {
    echo "Course not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($courses['course_title']); ?> - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #004080;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header .logo {
            max-width: 50px;
            margin-right: 10px;
        }
        header h1 {
            margin: 0;
            font-size: 1.5rem;
            text-align: left;
        }
        .main-content {
            flex: 1;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        footer {
            background-color: #004080;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: auto;
        }
        .course-details {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .video-container {
            position: relative;
            padding-top: 56.25%;
            background-color: #000;
            margin-bottom: 20px;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .toggle-notes, .like-btn, .add-comment {
            margin-top: 10px;
        }
        .toggle-notes, .like-btn {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            background-color: #0056d2;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .like-btn i {
            margin-right: 5px;
        }
        .add-comment textarea {
            width: 100%;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 1rem;
        }
        .add-comment button {
            display: block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
   
    <header>
        <div class="logo-container">
            <img src="assets/logos/1.png-2.png" alt="Company Logo" class="logo">
            <h1>GerarIsaac Training Center</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="course-container">
        <!-- Course Menu -->
        <div class="course-menu">
            <h3>Course Modules</h3>
            <ul id="moduleList">
                <!-- Modules will be dynamically generated here -->
            </ul>
        </div>

        <!-- Video Content -->
        <div class="video-content">
            <h2 id="moduleTitle">Module 1: Introduction</h2>
            <div class="video-player" id="videoPlayer">
                <!-- Video content here -->
                <iframe id="videoFrame" width="100%" height="100%" src="" frameborder="0" allowfullscreen></iframe>
            </div>
            <p id="moduleNotes">This is the introduction to the course. It covers the basics.</p>
            <div>
                <button id="prevBtn" class="btn-navigation" disabled>Previous</button>
                <button id="nextBtn" class="btn-navigation">Next</button>
                <button id="viewNotes" class="btn-navigation">View Notes</button>
            </div>
            <div class="progress-bar">
                <div id="progress"></div>
            </div>
        </div>
    </div>

    

    <script>
        const modules = [
            { title: "Module 1: Introduction", notes: "This is the introduction to the course. It covers the basics.", video: "https://www.youtube.com/embed/dQw4w9WgXcQ" },
            { title: "Module 2: Core Concepts of Basics Accounting", notes: "This module explores the core concepts in detail.", video: "https://www.youtube.com/embed/3JZ_D3ELwOQ" },
            { title: "Module 3: Accounting Principles", notes: "Advanced topics are covered in this module.", video: "https://www.youtube.com/embed/tgbNymZ7vqY" },
            { title: "Module 4: Accounting Equation", notes: "Real-world applications are demonstrated here.", video: "https://www.youtube.com/embed/aqz-KE-bpKQ" },
            { title: "Module 5: Final Assessment", notes: "This is the final assessment of the course.", video: "https://www.youtube.com/embed/kJQP7kiw5Fk" },
        ];

        let currentIndex = 0;

        const moduleList = document.getElementById('moduleList');
        const moduleTitle = document.getElementById('moduleTitle');
        const moduleNotes = document.getElementById('moduleNotes');
        const videoFrame = document.getElementById('videoFrame');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const viewNotesBtn = document.getElementById('viewNotes');
        const progressBar = document.getElementById('progress');

        // Populate the module list
        modules.forEach((module, index) => {
            const li = document.createElement('li');
            li.innerHTML = `${module.title} <i class="fas fa-check-circle" style="display:none;"></i>`;
            li.addEventListener('click', () => loadModule(index));
            moduleList.appendChild(li);
        });

        function loadModule(index) {
            currentIndex = index;
            const module = modules[currentIndex];
            moduleTitle.textContent = module.title;
            moduleNotes.textContent = module.notes;
            videoFrame.src = module.video;

            // Update progress bar
            progressBar.style.width = ((currentIndex + 1) / modules.length) * 100 + '%';

            // Update navigation buttons
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === modules.length - 1;

            // Mark completed modules
            markCompletedModules();
        }

        function markCompletedModules() {
            const items = moduleList.querySelectorAll('li');
            items.forEach((item, index) => {
                const icon = item.querySelector('i');
                if (index <= currentIndex) {
                    item.classList.add('completed');
                    icon.style.display = 'inline';
                } else {
                    item.classList.remove('completed');
                    icon.style.display = 'none';
                }
            });
        }

        // Event listeners for navigation buttons
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                loadModule(currentIndex - 1);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < modules.length - 1) {
                loadModule(currentIndex + 1);
            }
        });
    </script>

    <main class="main-content">
        <div class="course-details">
            <h2><?php echo htmlspecialchars($courses['course_title']); ?></h2>
            <p><?php echo htmlspecialchars($courses['description']); ?></p>

            <div class="video-container">
                <iframe src="<?php echo htmlspecialchars($courses['course_videos']); ?>" frameborder="0" allowfullscreen></iframe>
            </div>

            <button class="toggle-notes" onclick="toggleNotes()">View Notes</button>
            <div id="courseNotes" style="display:none; margin-top: 10px;">
                <p><?php echo nl2br(htmlspecialchars($courses['course_note'])); ?></p>
            </div>

            <button class="like-btn"><i class="bi bi-hand-thumbs-up"></i> Like</button>

            <div class="add-comment">
                <textarea placeholder="Add a comment..."></textarea>
                <button>Add Comment</button>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 GerarIsaac Training Center. All Rights Reserved.</p>
    </footer>

    <script>
        function toggleNotes() {
            const notes = document.getElementById('courseNotes');
            if (notes.style.display === 'none') {
                notes.style.display = 'block';
            } else {
                notes.style.display = 'none';
            }
        }
    </script>
</body>
</html>

kkkkkkkkk
<?php


$sql = "SELECT course_title, description, thumb_image, course_videos, course_note FROM courses WHERE course_id = ?";

// Debugging: Check course data   
// echo '<pre>';  
// print_r($courses);
// echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Course Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General reset and body styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure full page height */
            background-color: #f9f9f9;
        }

        header {
            background: #004080;
            color: white;
            padding: 20px 0;
            text-align: center;
            font-size: 1.5rem; /* Adjust header size */
        }

        .logo-container img {
            width: 50px;
            height: auto;
            vertical-align: middle;
        }

        .search-container input {
            padding: 10px;
            border: none;
            border-radius: 4px;
            margin-right: 5px;
        }

        .search-container button {
            padding: 10px;
            background-color: #0056d2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #004080;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: left;
            margin-top: 10px;
        }

        nav ul li {
            margin: 0 15px;
            font-size: 1.1rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        .course-container {
            display: flex;
            max-width: 1200px;
            margin: 2rem auto;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }

        .course-menu {
            width: 30%;
            border-right: 1px solid #ddd;
            background: #f4f4f4;
            padding: 1rem;
        }

        .course-menu ul {
            list-style: none;
            padding: 0;
        }

        .course-menu li {
            padding: 0.5rem;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .course-menu li.completed {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .course-menu li.completed i {
            color: green;
        }

        .course-menu li:hover {
            background-color: #eaeaea;
        }

        .video-content {
            width: 70%;
            padding: 1rem;
        }

        .video-player {
            width: 100%;
            height: 400px;
            background-color: #000;
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .btn-navigation {
            padding: 0.5rem 1rem;
            margin: 0 5px;
            border: none;
            color: #fff;
            background-color: #0056d2;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-navigation:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .progress-bar {
            background-color: #ddd;
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .progress-bar div {
            background-color: #0056d2;
            height: 100%;
            width: 0;
        }

        footer {
            background-color: #004080;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
            font-size: 1rem;
        }

        footer a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="logo-container">
            <img src="assets/logos/1.png-2.png" alt="Company Logo" class="logo">
            <h1>GerarIsaac Training Center</h1>
        </div>
        <div class="search-container">
            <input type="text" placeholder="Search...">
            <button type="submit">Search</button>
        </div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="course-container">
        <!-- Course Menu -->
        <div class="course-menu">
            <h3>Course Modules</h3>
            <ul id="moduleList">
                <!-- Modules will be dynamically generated here -->
            </ul>
        </div>

        <!-- Video Content -->
        <div class="video-content">
            <h2 id="moduleTitle">Module 1: Introduction</h2>
            <div class="video-player" id="videoPlayer">
                <!-- Video content here -->
                <iframe id="videoFrame" width="100%" height="100%" src="" frameborder="0" allowfullscreen></iframe>
            </div>
            <p id="moduleNotes">This is the introduction to the course. It covers the basics.</p>
            <div>
                <button id="prevBtn" class="btn-navigation" disabled>Previous</button>
                <button id="nextBtn" class="btn-navigation">Next</button>
                <button id="viewNotes" class="btn-navigation">View Notes</button>
            </div>
            <div class="progress-bar">
                <div id="progress"></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 GerarIsaac Training Center | All rights reserved.</p>
        <p>
            <a href="#facebook" target="_blank">Facebook</a>
            <a href="#youtube" target="_blank">YouTube</a>
            <a href="#telegram" target="_blank">Telegram</a>
            <a href="#whatsapp" target="_blank">WhatsApp</a>
        </p>
    </footer>

    <script>
        const modules = [
            { title: "Module 1: Introduction", notes: "This is the introduction to the course. It covers the basics.", video: "https://www.youtube.com/embed/dQw4w9WgXcQ" },
            { title: "Module 2: Core Concepts of Basics Accounting", notes: "This module explores the core concepts in detail.", video: "https://www.youtube.com/embed/3JZ_D3ELwOQ" },
            { title: "Module 3: Accounting Principles", notes: "Advanced topics are covered in this module.", video: "https://www.youtube.com/embed/tgbNymZ7vqY" },
            { title: "Module 4: Accounting Equation", notes: "Real-world applications are demonstrated here.", video: "https://www.youtube.com/embed/aqz-KE-bpKQ" },
            { title: "Module 5: Final Assessment", notes: "This is the final assessment of the course.", video: "https://www.youtube.com/embed/kJQP7kiw5Fk" },
        ];

        let currentIndex = 0;

        const moduleList = document.getElementById('moduleList');
        const moduleTitle = document.getElementById('moduleTitle');
        const moduleNotes = document.getElementById('moduleNotes');
        const videoFrame = document.getElementById('videoFrame');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const viewNotesBtn = document.getElementById('viewNotes');
        const progressBar = document.getElementById('progress');

        // Populate the module list
        modules.forEach((module, index) => {
            const li = document.createElement('li');
            li.innerHTML = `${module.title} <i class="fas fa-check-circle" style="display:none;"></i>`;
            li.addEventListener('click', () => loadModule(index));
            moduleList.appendChild(li);
        });

        function loadModule(index) {
            currentIndex = index;
            const module = modules[currentIndex];
            moduleTitle.textContent = module.title;
            moduleNotes.textContent = module.notes;
            videoFrame.src = module.video;

            // Update progress bar
            progressBar.style.width = ((currentIndex + 1) / modules.length) * 100 + '%';

            // Update navigation buttons
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === modules.length - 1;

            // Mark completed modules
            markCompletedModules();
        }

        function markCompletedModules() {
            const items = moduleList.querySelectorAll('li');
            items.forEach((item, index) => {
                const icon = item.querySelector('i');
                if (index <= currentIndex) {
                    item.classList.add('completed');
                    icon.style.display = 'inline';
                } else {
                    item.classList.remove('completed');
                    icon.style.display = 'none';
                }
            });
        }

        // Event listeners for navigation buttons
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                loadModule(currentIndex - 1);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < modules.length - 1) {
                loadModule(currentIndex + 1);
            }
        });
    </script>
</body>
</html>

Register
kkkkkkkkkkkkk
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Stylish Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }
        .form-control {
            border: 2px solid #007bff;
            border-radius: 5px;
        }
        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .form-group.required label::after {
            content: "*";
            color: red;
            margin-left: 5px;
        }
        .text-center {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Complete Stylish Form</h2>
        <form>
            <!-- Username -->
            <div class="form-group required">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" placeholder="Enter your username" value="ggggg" required>
            </div>
            <!-- First Name -->
            <div class="form-group required">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" placeholder="Enter your first name" value="ffffgg" required>
            </div>
            <!-- Middle Name -->
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" placeholder="Enter your middle name" value="ygguuu">
            </div>
            <!-- Last Name -->
            <div class="form-group required">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" placeholder="Enter your last name" value="hhg" required>
            </div>
            <!-- Email -->
            <div class="form-group required">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Enter your email" value="bn@gmail.com" required>
            </div>
            <!-- Phone -->
            <div class="form-group required">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number" value="0952681650" required>
            </div>
            <!-- Role -->
            <div class="form-group required">
                <label for="role">Role</label>
                <select class="form-control" id="role" required>
                    <option value="User/Student" selected>User/Student</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <!-- Profile Image -->
            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" class="form-control-file" id="profile_image">
            </div>
            <!-- Age -->
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" class="form-control" id="age" placeholder="Enter your age">
            </div>
            <!-- Gender -->
            <div class="form-group required">
                <label for="gender">Gender</label>
                <select class="form-control" id="gender" required>
                    <option value="Male" selected>Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <!-- Education Level -->
            <div class="form-group">
                <label for="education_level">Education Level</label>
                <select class="form-control" id="education_level">
                    <option value="Diploma" selected>Diploma</option>
                    <option value="Bachelor's">Bachelor's</option>
                    <option value="Master's">Master's</option>
                    <option value="PhD">PhD</option>
                </select>
            </div>
            <!-- Nationality -->
            <div class="form-group">
                <label for="nationality">Nationality</label>
                <select class="form-control" id="nationality">
                    <option value="">Select Country</option>
                    <option value="Ethiopia">Ethiopia</option>
                    <option value="USA">United States</option>
                    <option value="UK">United Kingdom</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <!-- Country of Residence -->
            <div class="form-group">
                <label for="residence">Country of Residence</label>
                <select class="form-control" id="residence">
                    <option value="">Select your country of residence</option>
                    <option value="Ethiopia">Ethiopia</option>
                    <option value="USA">United States</option>
                    <option value="UK">United Kingdom</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
            <!-- Already Have an Account -->
            <div class="text-center">
                <p>Already have an account? <a href="#">Login here</a></p>
            </div>
        </form>
    </div>
</body>
</html>




