<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Company - BRC Load</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff; /* Company theme color */
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --dark-color: #1a1d20;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Inter', sans-serif;
        }

        /* Login Page Styles */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 1.5rem;
        }

        .login-form .form-control {
            border-radius: 12px;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .login-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            border: none;
            border-radius: 12px;
            padding: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }

        /* Dashboard Styles */
        .sidebar {
            background: linear-gradient(135deg, var(--dark-color) 0%, #2c3034 100%);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar .company-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .company-logo-small {
            width: 48px;
            height: 48px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin: 0 auto 0.5rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.875rem 1.5rem;
            margin: 0.25rem 0;
            border-radius: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
        }

        .top-header {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .welcome-header h2 {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card .icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card.primary .icon {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
        }

        .stat-card.success .icon {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
        }

        .stat-card.warning .icon {
            background: linear-gradient(135deg, var(--warning-color), #fd7e14);
            color: white;
        }

        .stat-card.info .icon {
            background: linear-gradient(135deg, #17a2b8, #20c997);
            color: white;
        }

        .activity-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1rem;
        }

        .quick-action-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .user-dropdown {
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Login Page (if not authenticated) -->
    <div class="login-container" id="loginPage" style="display: none;">
        <div class="login-card">
            <div class="text-center mb-4">
                <div class="company-logo">DC</div>
                <h3>Demo Company</h3>
                <p class="text-muted">Welcome back! Please sign in to your account.</p>
            </div>
            
            <form class="login-form">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" class="form-control" placeholder="Enter your username or email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100">Sign In</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="#" class="text-decoration-none">Forgot your password?</a>
            </div>
            
            <div class="text-center mt-4 pt-3 border-top">
                <small class="text-muted">
                    Powered by <strong>BRC Load</strong> Platform
                </small>
            </div>
        </div>
    </div>

    <!-- Dashboard (if authenticated) -->
    <div id="dashboardPage">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="company-header">
                <div class="company-logo-small">DC</div>
                <h6 class="text-white mb-0">Demo Company</h6>
                <small class="text-white-50">demo.brcload.com</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link active" href="?page=dashboard">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a class="nav-link" href="?page=marketplace">
                    <i class="fas fa-store"></i>Module Marketplace
                </a>
                <a class="nav-link" href="?page=modules">
                    <i class="fas fa-puzzle-piece"></i>My Modules
                </a>
                
                <!-- Dynamic Module Links -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Active Modules</small>
                    <a class="nav-link" href="?module=brc_risk_assessment">
                        <i class="fas fa-shield-alt"></i>Risk Assessment
                    </a>
                    <a class="nav-link" href="?module=quality_mgmt">
                        <i class="fas fa-award"></i>Quality Management
                    </a>
                </div>
                
                <hr class="my-3 text-white-50">
                <a class="nav-link" href="?page=users">
                    <i class="fas fa-users"></i>Team Members
                </a>
                <a class="nav-link" href="?page=settings">
                    <i class="fas fa-cog"></i>Settings
                </a>
                <a class="nav-link" href="?page=support">
                    <i class="fas fa-headset"></i>Support
                </a>
                <a class="nav-link" href="?page=logout">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <div class="top-header">
                <div class="welcome-header">
                    <h2>Good morning, John! 👋</h2>
                    <p class="text-muted mb-0">Here's what's happening with your compliance today.</p>
                </div>
                <div class="d-flex align-items-center">
                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><a class="dropdown-item" href="#">Risk assessment due tomorrow</a></li>
                            <li><a class="dropdown-item" href="#">New BRC module available</a></li>
                            <li><a class="dropdown-item" href="#">Team member added</a></li>
                        </ul>
                    </div>
                    
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">JD</div>
                            <div class="text-start">
                                <div class="fw-semibold">John Doe</div>
                                <small class="text-muted">Admin</small>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="?page=profile">My Profile</a></li>
                            <li><a class="dropdown-item" href="?page=settings">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?page=logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card primary">
                        <div class="icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="mb-1">28</h3>
                        <p class="text-muted mb-0">Active Risk Assessments</p>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +12% from last month</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card success">
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="mb-1">94%</h3>
                        <p class="text-muted mb-0">Compliance Score</p>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +2% this week</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card warning">
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="mb-1">3</h3>
                        <p class="text-muted mb-0">Pending Reviews</p>
                        <small class="text-warning"><i class="fas fa-clock"></i> Due this week</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card info">
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="mb-1">12</h3>
                        <p class="text-muted mb-0">Team Members</p>
                        <small class="text-info"><i class="fas fa-plus"></i> 2 new this month</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Activity -->
                <div class="col-md-8">
                    <div class="activity-card">
                        <h5 class="mb-3">Recent Activity</h5>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-primary text-white">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Risk Assessment Completed</h6>
                                <p class="text-muted mb-1">Kitchen Area - Risk Assessment 2024-07-23</p>
                                <small class="text-muted">2 hours ago by Sarah Wilson</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-success text-white">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Compliance Review Approved</h6>
                                <p class="text-muted mb-1">Monthly BRC Compliance Check</p>
                                <small class="text-muted">4 hours ago by John Doe</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-info text-white">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">New Team Member Added</h6>
                                <p class="text-muted mb-1">Mike Johnson joined as Quality Inspector</p>
                                <small class="text-muted">1 day ago by John Doe</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-warning text-white">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Module Update Available</h6>
                                <p class="text-muted mb-1">BRC Risk Assessment v2.1 released</p>
                                <small class="text-muted">2 days ago by BRC Load Platform</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-md-4">
                    <h5 class="mb-3">Quick Actions</h5>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="quick-action-card">
                                <div class="icon bg-primary text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <h6>New Risk Assessment</h6>
                                <p class="text-muted small">Start a new risk assessment</p>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="quick-action-card">
                                <div class="icon bg-success text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <h6>View Reports</h6>
                                <p class="text-muted small">Generate compliance reports</p>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="quick-action-card">
                                <div class="icon bg-warning text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <h6>Invite Team</h6>
                                <p class="text-muted small">Add new team members</p>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="quick-action-card">
                                <div class="icon bg-info text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-store"></i>
                                </div>
                                <h6>Browse Modules</h6>
                                <p class="text-muted small">Explore module marketplace</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulate authentication state
        const isAuthenticated = true; // Change to false to see login page
        
        if (isAuthenticated) {
            document.getElementById('loginPage').style.display = 'none';
            document.getElementById('dashboardPage').style.display = 'block';
        } else {
            document.getElementById('loginPage').style.display = 'flex';
            document.getElementById('dashboardPage').style.display = 'none';
        }

        // Login form handler
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simulate login process
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                // Redirect to dashboard
                document.getElementById('loginPage').style.display = 'none';
                document.getElementById('dashboardPage').style.display = 'block';
            }, 2000);
        });

        // Quick action handlers
        document.querySelectorAll('.quick-action-card').forEach(card => {
            card.addEventListener('click', function() {
                const title = this.querySelector('h6').textContent;
                console.log('Quick action clicked:', title);
                
                // Here you would navigate to the appropriate page/module
                // For example:
                // if (title === 'New Risk Assessment') {
                //     window.location.href = '?module=brc_risk_assessment&action=new';
                // }
            });
        });

        // Auto-refresh activity feed (simulate real-time updates)
        setInterval(() => {
            // In a real app, you would fetch new activities via AJAX
            console.log('Checking for new activities...');
        }, 30000);
    </script>
</body>
</html>