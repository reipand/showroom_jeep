<?php
session_start();
include 'koneksi.php';

// Fetch vehicles from database
$vehicles_query = "SELECT * FROM vehicles WHERE is_active = 1 ORDER BY id ASC";
$vehicles_result = mysqli_query($conn, $vehicles_query);
$vehicles = [];
if ($vehicles_result) {
    while ($row = mysqli_fetch_assoc($vehicles_result)) {
        $vehicles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeep Showroom - Main Page</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Header Styles */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: 700;
            color: #000 !important;
            text-decoration: none;
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #007bff !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 4rem 0;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }

        .hero-content {
            z-index: 2;
            position: relative;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 2rem;
            line-height: 1.2;
        }

        .hero-description {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
            max-width: 600px;
        }

        .btn-explore {
            background: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-explore:hover {
            background: #34495e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* 3D Car Container */
        .car-3d-container {
            position: relative;
            height: 500px;
            background: linear-gradient(45deg, #2c3e50 0%, #34495e 100%);
            border-radius: 20px;
            overflow: hidden;
        }

        #car-canvas {
            width: 100%;
            height: 100%;
            border-radius: 20px;
        }

        .car-label {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: rgba(255,255,255,0.9);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Vehicles Section */
        .vehicles-section {
            padding: 5rem 0;
            background: #fff;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 3rem;
        }

        .vehicle-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
            border-bottom: 2px solid #e9ecef;
        }

        .vehicle-tab {
            padding: 1rem 2rem;
            background: none;
            border: none;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .vehicle-tab.active {
            color: #2c3e50;
            border-bottom-color: #2c3e50;
        }

        .vehicle-tab:hover {
            color: #2c3e50;
        }

        /* Vehicle Cards */
        .vehicle-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .vehicle-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .vehicle-image {
            height: 250px;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .vehicle-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .vehicle-card:hover .vehicle-image img {
            transform: scale(1.1);
        }

        .vehicle-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #28a745;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .vehicle-info {
            padding: 1.5rem;
        }

        .vehicle-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .vehicle-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 1rem;
        }

        .vehicle-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .feature-tag {
            background: #e9ecef;
            color: #6c757d;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        /* Feature Cards */
        .feature-cards {
            padding: 5rem 0;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        .feature-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 3rem 2rem;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255,255,255,0.2);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #fff;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .feature-description {
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-feature {
            background: white;
            color: #2c3e50;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-feature:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        /* 360 Section */
        .wrangler-360 {
            padding: 5rem 0;
            background: #f8f9fa;
            text-align: center;
        }

        .wrangler-360-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 2rem;
        }

        .wrangler-360-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            height: 400px;
            background: linear-gradient(45deg, #2c3e50, #34495e);
            border-radius: 20px;
            overflow: hidden;
        }

        #wrangler-360-canvas {
            width: 100%;
            height: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .car-3d-container {
                height: 300px;
                margin-top: 2rem;
            }
            
            .vehicle-tabs {
                flex-direction: column;
                align-items: center;
            }
            
            .vehicle-tab {
                margin-bottom: 1rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #2c3e50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="main.php">JEEP</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="main.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="vehiclesDropdown" role="button" data-bs-toggle="dropdown">
                            Vehicles
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#all-vehicles">All Vehicles</a></li>
                            <li><a class="dropdown-item" href="#electric">Electric & Hybrid</a></li>
                            <li><a class="dropdown-item" href="#limited">Limited Editions</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#shopping">Shopping</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#blog">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex">
                    <a class="nav-link me-3" href="#search">
                        <i class="fas fa-search"></i>
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">WRANGLER</h1>
                        <p class="hero-description">
                            From its debut on the fields of WWII to its segment-defining capability today, 
                            Jeep® Wrangler has always brought incredible strength and power to hard-to-reach terrain.
                        </p>
                        <p class="hero-description">
                            The Best-in-Class maximum 470 horsepower of the Class-Exclusive 6.4L HEMI® V8 engine 
                            on 392 models is just one example of our tireless commitment to driving power. 
                            It's what we were made for. Limited quantities available.
                        </p>
                        <a href="#vehicles" class="btn-explore">Explore Car</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="car-3d-container">
                        <canvas id="car-canvas"></canvas>
                        <div class="car-label">Wrangler Unlimited</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vehicles Section -->
    <section class="vehicles-section" id="vehicles">
        <div class="container">
            <h2 class="section-title">JEEP® Vehicles</h2>
            
            <div class="vehicle-tabs">
                <button class="vehicle-tab active" data-filter="all">ALL VEHICLE</button>
                <button class="vehicle-tab" data-filter="electric">ELECTRIC & HYBRID</button>
                <button class="vehicle-tab" data-filter="limited">LIMITED EDITIONS</button>
            </div>

            <div class="row" id="vehicles-container">
                <?php if (!empty($vehicles)): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <div class="col-lg-4 col-md-6 vehicle-item" data-category="all">
                            <div class="vehicle-card">
                                <div class="vehicle-image">
                                    <?php if (!empty($vehicle['image_file'])): ?>
                                        <img src="assets/images/<?php echo htmlspecialchars($vehicle['image_file']); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
                                    <?php else: ?>
                                        <div class="loading">
                                            <div class="spinner"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="vehicle-info">
                                    <h3 class="vehicle-name"><?php echo htmlspecialchars($vehicle['name']); ?></h3>
                                    <p class="vehicle-price">MSRP Starting at<br>Rp. <?php echo number_format($vehicle['price'], 0, ',', '.'); ?></p>
                                    
                                    <div class="vehicle-features">
                                        <span class="feature-tag">Stock: <?php echo $vehicle['stock']; ?></span>
                                        <?php if ($vehicle['model_year']): ?>
                                            <span class="feature-tag"><?php echo $vehicle['model_year']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button class="btn btn-outline-primary w-100" onclick="viewVehicle(<?php echo $vehicle['id']; ?>)">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No vehicles available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Feature Cards -->
    <section class="feature-cards">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Defense Wheel</h3>
                        <p class="feature-description">
                            Advanced safety features and robust construction for ultimate protection.
                        </p>
                        <button class="btn-feature">Explore Now</button>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="feature-title">Latest & Beauty</h3>
                        <p class="feature-description">
                            Cutting-edge design and stunning aesthetics that turn heads everywhere.
                        </p>
                        <button class="btn-feature">Explore Now</button>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <h3 class="feature-title">Car Interior</h3>
                        <p class="feature-description">
                            Luxurious and comfortable interiors designed for the ultimate driving experience.
                        </p>
                        <button class="btn-feature">Explore Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Wrangler 360 Section -->
    <section class="wrangler-360">
        <div class="container">
            <h2 class="wrangler-360-title">Wrangler 360°</h2>
            <div class="wrangler-360-container">
                <canvas id="wrangler-360-canvas"></canvas>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <script>
        // Vehicle filtering
        document.querySelectorAll('.vehicle-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.vehicle-tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                const vehicles = document.querySelectorAll('.vehicle-item');
                
                vehicles.forEach(vehicle => {
                    if (filter === 'all' || vehicle.getAttribute('data-category') === filter) {
                        vehicle.style.display = 'block';
                    } else {
                        vehicle.style.display = 'none';
                    }
                });
            });
        });

        // Three.js Car 3D Model
        let scene, camera, renderer, car;
        
        function initCar3D() {
            const canvas = document.getElementById('car-canvas');
            
            // Scene
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x2c3e50);
            
            // Camera
            camera = new THREE.PerspectiveCamera(75, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
            camera.position.set(0, 2, 5);
            
            // Renderer
            renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true });
            renderer.setSize(canvas.clientWidth, canvas.clientHeight);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            
            // Lighting
            const ambientLight = new THREE.AmbientLight(0x404040, 0.6);
            scene.add(ambientLight);
            
            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(10, 10, 5);
            directionalLight.castShadow = true;
            scene.add(directionalLight);
            
            // Create a simple car-like shape
            const carGeometry = new THREE.BoxGeometry(3, 1, 1.5);
            const carMaterial = new THREE.MeshLambertMaterial({ color: 0x2c3e50 });
            car = new THREE.Mesh(carGeometry, carMaterial);
            car.position.y = 0.5;
            car.castShadow = true;
            scene.add(car);
            
            // Add wheels
            const wheelGeometry = new THREE.CylinderGeometry(0.3, 0.3, 0.2, 16);
            const wheelMaterial = new THREE.MeshLambertMaterial({ color: 0x333333 });
            
            const wheelPositions = [
                [-1, 0.3, 0.8],
                [1, 0.3, 0.8],
                [-1, 0.3, -0.8],
                [1, 0.3, -0.8]
            ];
            
            wheelPositions.forEach(pos => {
                const wheel = new THREE.Mesh(wheelGeometry, wheelMaterial);
                wheel.position.set(pos[0], pos[1], pos[2]);
                wheel.rotation.z = Math.PI / 2;
                wheel.castShadow = true;
                scene.add(wheel);
            });
            
            // Ground
            const groundGeometry = new THREE.PlaneGeometry(20, 20);
            const groundMaterial = new THREE.MeshLambertMaterial({ color: 0x34495e });
            const ground = new THREE.Mesh(groundGeometry, groundMaterial);
            ground.rotation.x = -Math.PI / 2;
            ground.receiveShadow = true;
            scene.add(ground);
            
            // Animation
            function animate() {
                requestAnimationFrame(animate);
                
                // Rotate car
                car.rotation.y += 0.01;
                
                renderer.render(scene, camera);
            }
            
            animate();
            
            // Handle resize
            window.addEventListener('resize', () => {
                camera.aspect = canvas.clientWidth / canvas.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(canvas.clientWidth, canvas.clientHeight);
            });
        }

        // Wrangler 360 View
        function initWrangler360() {
            const canvas = document.getElementById('wrangler-360-canvas');
            
            const scene360 = new THREE.Scene();
            const camera360 = new THREE.PerspectiveCamera(75, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
            const renderer360 = new THREE.WebGLRenderer({ canvas: canvas, antialias: true });
            
            renderer360.setSize(canvas.clientWidth, canvas.clientHeight);
            scene360.background = new THREE.Color(0x2c3e50);
            
            // Create a more detailed car for 360 view
            const carGroup = new THREE.Group();
            
            // Car body
            const bodyGeometry = new THREE.BoxGeometry(4, 1.5, 2);
            const bodyMaterial = new THREE.MeshLambertMaterial({ color: 0x2c3e50 });
            const body = new THREE.Mesh(bodyGeometry, bodyMaterial);
            body.position.y = 0.75;
            carGroup.add(body);
            
            // Car roof
            const roofGeometry = new THREE.BoxGeometry(3, 0.5, 1.8);
            const roofMaterial = new THREE.MeshLambertMaterial({ color: 0x34495e });
            const roof = new THREE.Mesh(roofGeometry, roofMaterial);
            roof.position.set(0, 1.5, 0);
            carGroup.add(roof);
            
            // Add lighting
            const ambientLight360 = new THREE.AmbientLight(0x404040, 0.8);
            scene360.add(ambientLight360);
            
            const directionalLight360 = new THREE.DirectionalLight(0xffffff, 1);
            directionalLight360.position.set(5, 5, 5);
            scene360.add(directionalLight360);
            
            scene360.add(carGroup);
            camera360.position.set(0, 2, 6);
            
            // Mouse controls for 360 rotation
            let mouseX = 0, mouseY = 0;
            let isMouseDown = false;
            
            canvas.addEventListener('mousedown', (e) => {
                isMouseDown = true;
            });
            
            canvas.addEventListener('mouseup', () => {
                isMouseDown = false;
            });
            
            canvas.addEventListener('mousemove', (e) => {
                if (isMouseDown) {
                    mouseX = (e.clientX / canvas.clientWidth) * 2 - 1;
                    mouseY = -(e.clientY / canvas.clientHeight) * 2 + 1;
                    
                    carGroup.rotation.y = mouseX * Math.PI;
                    carGroup.rotation.x = mouseY * Math.PI * 0.3;
                }
            });
            
            // Auto rotation
            function animate360() {
                requestAnimationFrame(animate360);
                
                if (!isMouseDown) {
                    carGroup.rotation.y += 0.005;
                }
                
                renderer360.render(scene360, camera360);
            }
            
            animate360();
        }

        // Initialize when page loads
        window.addEventListener('load', () => {
            initCar3D();
            initWrangler360();
        });

        // Vehicle view function
        function viewVehicle(vehicleId) {
            // You can implement vehicle detail view here
            alert('Viewing vehicle ID: ' + vehicleId);
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
