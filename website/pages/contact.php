<?php
// website/pages/contact.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - BRC Load Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .contact-section {
            padding: 100px 0;
            background: #f8f9fc;
        }
        
        .contact-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
        }
        
        .contact-info-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            height: 100%;
        }
        
        .btn-gradient {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 600;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e1e5e9;
            padding: 1rem;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="?page=home">
                <i class="fas fa-shield-alt text-primary"></i>
                BRC Load Platform
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="?page=home">Home</a>
                <a class="nav-link" href="?page=pricing">Pricing</a>
                <a class="nav-link active" href="?page=contact">Contact</a>
                <a class="nav-link btn btn-outline-primary ms-2" href="?page=admin">Admin Panel</a>
            </div>
        </div>
    </nav>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h1 class="display-4 fw-bold">Get in Touch</h1>
                    <p class="lead text-muted">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="contact-card">
                        <form id="contactForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company</label>
                                        <input type="text" class="form-control" name="company">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subject *</label>
                                <select class="form-select" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="demo">Request Demo</option>
                                    <option value="pricing">Pricing Information</option>
                                    <option value="support">Technical Support</option>
                                    <option value="partnership">Partnership</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Message *</label>
                                <textarea class="form-control" name="message" rows="6" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-gradient text-white w-100">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="contact-info-card">
                        <h4 class="mb-4">Contact Information</h4>
                        
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt me-3"></i>
                                <strong>Address</strong>
                            </div>
                            <p class="mb-0 ps-4">
                                123 Business District<br>
                                Istanbul, Turkey 34000
                            </p>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone me-3"></i>
                                <strong>Phone</strong>
                            </div>
                            <p class="mb-0 ps-4">+90 (212) 123-4567</p>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope me-3"></i>
                                <strong>Email</strong>
                            </div>
                            <p class="mb-0 ps-4">
                                Sales: sales@brcload.com<br>
                                Support: support@brcload.com
                            </p>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock me-3"></i>
                                <strong>Business Hours</strong>
                            </div>
                            <p class="mb-0 ps-4">
                                Monday - Friday: 9:00 AM - 6:00 PM<br>
                                Saturday: 10:00 AM - 4:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                        
                        <hr class="my-4" style="border-color: rgba(255,255,255,0.3);">
                        
                        <div class="text-center">
                            <h5>Follow Us</h5>
                            <div class="social-links">
                                <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                                <a href="#" class="text-white me-3"><i class="fab fa-linkedin fa-2x"></i></a>
                                <a href="#" class="text-white"><i class="fab fa-github fa-2x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simulate form submission
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> Message Sent!';
                btn.classList.remove('btn-gradient');
                btn.classList.add('btn-success');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-gradient');
                    this.reset();
                }, 2000);
            }, 2000);
        });
    </script>
</body>
</html>