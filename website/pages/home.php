<?php
// website/pages/home.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRC Load Platform - Multi-Tenant SaaS for Business Risk Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .hero-section {
            background: var(--primary-gradient);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,100 1000,0"/></svg>');
            background-size: cover;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }
        
        .btn-gradient {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-gradient {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-gradient:hover {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }
        
        .stats-section {
            background: #f8f9fc;
            padding: 80px 0;
        }
        
        .stat-item {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .navbar {
            background: rgba(255,255,255,0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .cta-section {
            background: var(--primary-gradient);
            color: white;
            padding: 80px 0;
        }
        
        footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt text-primary"></i>
                BRC Load Platform
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary ms-2" href="?page=admin">
                            <i class="fas fa-crown"></i> Admin Panel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-4">
                            Multi-Tenant SaaS Platform for 
                            <span class="text-warning">Business Risk Management</span>
                        </h1>
                        <p class="lead mb-4">
                            Empower your organization with enterprise-grade risk assessment, 
                            compliance management, and modular business solutions. 
                            Built for scalability, designed for efficiency.
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="#demo" class="btn btn-light btn-gradient text-primary">
                                <i class="fas fa-play"></i> View Demo
                            </a>
                            <a href="?page=pricing" class="btn btn-outline-light btn-outline-gradient">
                                <i class="fas fa-rocket"></i> Get Started
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <img src="https://via.placeholder.com/600x400/667eea/ffffff?text=BRC+Platform+Dashboard" 
                             alt="BRC Platform Dashboard" 
                             class="img-fluid rounded shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Companies</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Modules</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Powerful Features</h2>
                    <p class="lead text-muted">Everything you need to manage business risks and compliance</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="text-center mb-3">BRC Risk Assessment</h4>
                        <p class="text-muted text-center">
                            Comprehensive 5x5 risk matrix with automated calculations, 
                            compliance workflows, and detailed reporting capabilities.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-puzzle-piece"></i>
                        </div>
                        <h4 class="text-center mb-3">Module Builder</h4>
                        <p class="text-muted text-center">
                            Professional drag-and-drop interface to create custom modules 
                            with components, workflows, and business logic.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h4 class="text-center mb-3">Multi-Tenant Architecture</h4>
                        <p class="text-muted text-center">
                            Isolated company data, individual theming, 
                            subscription management, and scalable infrastructure.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="text-center mb-3">Advanced Analytics</h4>
                        <p class="text-muted text-center">
                            Real-time dashboards, KPI tracking, custom reports, 
                            and data visualization for informed decision making.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h4 class="text-center mb-3">API Integration</h4>
                        <p class="text-muted text-center">
                            RESTful APIs, webhooks, rate limiting, 
                            and comprehensive documentation for seamless integrations.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4 class="text-center mb-3">Enterprise Security</h4>
                        <p class="text-muted text-center">
                            CSRF protection, rate limiting, input sanitization, 
                            secure sessions, and audit trails.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-6 fw-bold mb-4">Try Our Demo</h2>
                    <p class="lead mb-4">
                        Experience the power of BRC Load Platform with our interactive demo. 
                        See how easy it is to manage risks, build modules, and scale your business.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/customer/demo" class="btn btn-gradient">
                            <i class="fas fa-eye"></i> Customer Demo
                        </a>
                        <a href="?page=admin" class="btn btn-outline-gradient">
                            <i class="fas fa-crown"></i> Admin Demo
                        </a>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted">
                            <strong>Demo Credentials:</strong><br>
                            Admin: admin / admin123<br>
                            Customer: demo@example.com / demo123
                        </small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <img src="https://via.placeholder.com/500x350/f8f9fc/667eea?text=Interactive+Demo" 
                             alt="Demo Preview" 
                             class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-4">Ready to Get Started?</h2>
                    <p class="lead mb-4">
                        Join hundreds of companies already using BRC Load Platform 
                        to streamline their risk management and compliance processes.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="?page=pricing" class="btn btn-light btn-lg">
                            <i class="fas fa-rocket"></i> Start Free Trial
                        </a>
                        <a href="?page=contact" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-phone"></i> Contact Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h5><i class="fas fa-shield-alt"></i> BRC Load Platform</h5>
                    <p class="text-light">
                        Enterprise-grade multi-tenant SaaS platform for business risk management and compliance.
                    </p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <p class="mb-0">&copy; 2025 BRC Load Platform. All rights reserved.</p>
                    <div class="mt-2">
                        <a href="?page=pricing" class="text-light me-3">Pricing</a>
                        <a href="?page=contact" class="text-light me-3">Contact</a>
                        <a href="?page=admin" class="text-light">Admin Panel</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
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

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255,255,255,0.95)';
            } else {
                navbar.style.background = 'rgba(255,255,255,0.95)';
            }
        });

        function safeQuerySelector(elementId) {
            // ID boş kontrolü
            if (!elementId || elementId.trim() === '') {
                console.warn('querySelector: Empty ID provided');
                return null;
            }
            
            // # karakteri zaten varsa tekrar ekleme
            const selector = elementId.startsWith('#') ? elementId : '#' + elementId;
            
            try {
                return document.querySelector(selector);
            } catch (error) {
                console.error('querySelector error:', error);
                return null;
            }
        }

        // Link click handler'ları için:
        document.addEventListener('DOMContentLoaded', function() {
            
            // Admin panel linki
            const adminLink = document.querySelector('a[href*="admin"]');
            if (adminLink) {
                adminLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Direkt admin paneline yönlendir
                    window.location.href = '/brcproject/admin';
                });
            }
            
            // BRC Load Platform linki
            const platformLink = document.querySelector('a[href*="platform"]');
            if (platformLink) {
                platformLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Platform paneline yönlendir
                    window.location.href = '/brcproject/platform';
                });
            }
            
            // Genel navigation linkleri
            const navLinks = document.querySelectorAll('.nav-link, .menu-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    // Eğer href boşsa veya # ile başlıyorsa
                    if (!href || href === '#' || href.startsWith('#')) {
                        e.preventDefault();
                        console.warn('Invalid link href:', href);
                        return false;
                    }
                });
            });
        });
    </script>
</body>
</html>