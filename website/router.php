<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRC Load Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <div class="logo">
            <i class="fas fa-cubes"></i>
        </div>
        <h1>BRC Load Platform</h1>
        <p class="text-muted mb-4">Multi-Tenant SaaS for BRC Compliance Management</p>
        
        <div class="d-grid gap-3">
            <a href="?page=admin" class="btn btn-primary btn-lg">
                <i class="fas fa-cog me-2"></i>Platform Admin Panel
            </a>
            <a href="/brcproject/demo" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-building me-2"></i>Demo Company Panel
            </a>
        </div>
        
        <div class="mt-4 pt-3 border-top">
            <small class="text-muted">
                <strong>Test Credentials:</strong><br>
                Admin: admin@brcload.com / admin123<br>
                Demo Company: admin / password123
            </small>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>